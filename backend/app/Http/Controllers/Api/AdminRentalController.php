<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RentalResource;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRentalController extends Controller
{
    /**
     * GET /api/admin/rentals
     *
     * Query params:
     *   - status    : filter by status (active, booked, returned, canceled, overdue)
     *   - search    : search by invoice_no or customer name
     *   - user_id   : filter by customer
     *   - date_from : start date filter (YYYY-MM-DD)
     *   - date_to   : end date filter (YYYY-MM-DD)
     *   - sort      : newest (default), oldest, price_asc, price_desc
     *   - per_page  : items per page (default 15)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Rental::with(['user', 'items.product', 'payment']);

        // Filter: status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter: search (invoice_no or customer name)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter: specific user
        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        // Filter: date range
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('start_date', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('end_date', '<=', $dateTo);
        }

        // Sort
        switch ($request->input('sort', 'newest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_asc':
                $query->orderBy('total_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('total_price', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $perPage = min((int) $request->input('per_page', 15), 50);

        $rentals = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data'   => RentalResource::collection($rentals),
            'meta'   => [
                'current_page' => $rentals->currentPage(),
                'last_page'    => $rentals->lastPage(),
                'per_page'     => $rentals->perPage(),
                'total'        => $rentals->total(),
            ],
        ]);
    }

    /**
     * GET /api/admin/rentals/{id}
     */
    public function show(int $id): JsonResponse
    {
        $rental = Rental::with(['user', 'admin', 'items.product', 'payment'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => new RentalResource($rental),
        ]);
    }

    /**
     * POST /api/admin/rentals
     *
     * Admin creates a rental on behalf of a customer.
     *
     * Request body:
     * {
     *   "user_id": 2,
     *   "items": [
     *     { "product_id": 1, "quantity": 1 },
     *     { "product_id": 6, "quantity": 2 }
     *   ],
     *   "start_date": "2026-04-15 08:00:00",
     *   "end_date": "2026-04-17 08:00:00",
     *   "guarantee_info": "KTP - 320115xxxx",
     *   "payment_method": "qris" | "cash"
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id'        => ['required', 'exists:users,id'],
            'items'          => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'start_date'     => ['required', 'date'],
            'end_date'       => ['required', 'date', 'after:start_date'],
            'guarantee_info' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:qris,cash'],
        ]);

        return DB::transaction(function () use ($request) {
            $startDate = Carbon::parse($request->start_date);
            $endDate   = Carbon::parse($request->end_date);
            $days      = max(1, $startDate->diffInDays($endDate));

            // Validate stock & calculate total
            $totalPrice = 0;
            $itemDetails = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock_available < $item['quantity']) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => "Stok {$product->name} tidak cukup. Tersedia: {$product->stock_available}.",
                    ], 422);
                }

                $subtotal = $product->price_24h * $item['quantity'] * $days;
                $totalPrice += $subtotal;

                $itemDetails[] = [
                    'product'  => $product,
                    'quantity' => $item['quantity'],
                    'price'    => $product->price_24h,
                ];
            }

            // Generate invoice number
            $today = Carbon::today()->format('Ymd');
            $lastInvoice = Rental::where('invoice_no', 'like', "INV-{$today}-%")
                                  ->orderByDesc('invoice_no')
                                  ->first();
            $sequence = 1;
            if ($lastInvoice) {
                $lastSeq = (int) substr($lastInvoice->invoice_no, -3);
                $sequence = $lastSeq + 1;
            }
            $invoiceNo = sprintf('INV-%s-%03d', $today, $sequence);

            // Create rental
            $rental = Rental::create([
                'user_id'        => $request->user_id,
                'admin_id'       => $request->user()->id,
                'invoice_no'     => $invoiceNo,
                'start_date'     => $startDate,
                'end_date'       => $endDate,
                'total_price'    => $totalPrice,
                'fine_amount'    => 0,
                'status'         => 'booked',
                'guarantee_info' => $request->guarantee_info,
            ]);

            // Create rental items & decrease stock
            foreach ($itemDetails as $detail) {
                RentalItem::create([
                    'rental_id'      => $rental->id,
                    'product_id'     => $detail['product']->id,
                    'quantity'       => $detail['quantity'],
                    'price_at_rental' => $detail['price'],
                ]);

                $detail['product']->decrement('stock_available', $detail['quantity']);
            }

            // Create payment record
            Payment::create([
                'rental_id'      => $rental->id,
                'amount'         => $totalPrice,
                'payment_method' => $request->payment_method,
                'status'         => $request->payment_method === 'cash' ? 'verified' : 'pending',
            ]);

            $rental->load(['user', 'admin', 'items.product', 'payment']);

            return response()->json([
                'status'  => 'success',
                'message' => "Rental berhasil dibuat. Invoice: {$invoiceNo}",
                'data'    => new RentalResource($rental),
            ], 201);
        });
    }

    /**
     * PUT /api/admin/rentals/{id}
     *
     * Update rental details (dates, guarantee_info, etc.)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $rental = Rental::findOrFail($id);

        $request->validate([
            'start_date'     => ['sometimes', 'date'],
            'end_date'       => ['sometimes', 'date', 'after:start_date'],
            'guarantee_info' => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);

        $rental->update($request->only(['start_date', 'end_date', 'guarantee_info']));
        $rental->load(['user', 'admin', 'items.product', 'payment']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Rental berhasil diperbarui.',
            'data'    => new RentalResource($rental),
        ]);
    }

    /**
     * PUT /api/admin/rentals/{id}/status
     *
     * Update rental status with business logic:
     * - booked → active  : Admin confirms item picked up
     * - active → returned: Admin confirms items returned, restore stock
     * - active → overdue : Mark as overdue, calculate fine
     * - booked → canceled: Cancel booking, restore stock
     *
     * Request body: { "status": "active" | "returned" | "overdue" | "canceled" }
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:active,returned,overdue,canceled'],
        ]);

        $rental = Rental::with('items')->findOrFail($id);
        $newStatus = $request->status;
        $oldStatus = $rental->status;

        // Validate transition
        $allowedTransitions = [
            'booked'  => ['active', 'canceled'],
            'active'  => ['returned', 'overdue'],
            'overdue' => ['returned'],
        ];

        if (!isset($allowedTransitions[$oldStatus]) || !in_array($newStatus, $allowedTransitions[$oldStatus])) {
            return response()->json([
                'status'  => 'error',
                'message' => "Tidak bisa mengubah status dari '{$oldStatus}' ke '{$newStatus}'.",
            ], 422);
        }

        return DB::transaction(function () use ($rental, $newStatus, $oldStatus, $request) {
            $updateData = ['status' => $newStatus];

            // Status-specific logic
            switch ($newStatus) {
                case 'active':
                    $updateData['admin_id'] = $request->user()->id;
                    break;

                case 'returned':
                    $updateData['return_date'] = Carbon::now();
                    $updateData['admin_id']    = $request->user()->id;

                    // Calculate fine if returned late
                    if (Carbon::now()->gt($rental->end_date)) {
                        $daysLate = Carbon::now()->diffInDays($rental->end_date);
                        $fineAmount = $daysLate * 0.10 * $rental->total_price;
                        $updateData['fine_amount'] = $fineAmount;
                    }

                    // Restore stock
                    foreach ($rental->items as $item) {
                        Product::find($item->product_id)?->increment('stock_available', $item->quantity);
                    }
                    break;

                case 'overdue':
                    $daysLate = Carbon::now()->diffInDays($rental->end_date);
                    $fineAmount = max(1, $daysLate) * 0.10 * $rental->total_price;
                    $updateData['fine_amount'] = $fineAmount;
                    break;

                case 'canceled':
                    // Restore stock for booked items
                    foreach ($rental->items as $item) {
                        Product::find($item->product_id)?->increment('stock_available', $item->quantity);
                    }

                    // Remove pending payment
                    $rental->payment()->where('status', 'pending')->delete();
                    break;
            }

            $rental->update($updateData);
            $rental->load(['user', 'admin', 'items.product', 'payment']);

            return response()->json([
                'status'  => 'success',
                'message' => "Status rental berhasil diubah ke '{$newStatus}'.",
                'data'    => new RentalResource($rental),
            ]);
        });
    }

    /**
     * DELETE /api/admin/rentals/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $rental = Rental::with('items')->findOrFail($id);

        // Restore stock if rental is active/booked/overdue
        if (in_array($rental->status, ['active', 'booked', 'overdue'])) {
            foreach ($rental->items as $item) {
                Product::find($item->product_id)?->increment('stock_available', $item->quantity);
            }
        }

        $rental->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Rental berhasil dihapus.',
        ]);
    }
}
