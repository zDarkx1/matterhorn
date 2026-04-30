<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RentalResource;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Services\PaydigitalService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Cache;

class CheckoutController extends Controller
{
    /**
     * GET /api/store-status
     *
     * Returns the current open/closed status of the store
     * based on server time (Asia/Jakarta timezone).
     * Store hours: 09:00 – 21:45 WIB
     */
    public function storeStatus(): JsonResponse
    {
        $now = Carbon::now('Asia/Jakarta');
        $openTime  = $now->copy()->setTime(9, 0, 0);
        $closeTime = $now->copy()->setTime(21, 45, 0);

        $isOpen = $now->between($openTime, $closeTime);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'is_open'      => $isOpen,
                'current_time' => $now->format('H:i'),
                'open_time'    => '09:00',
                'close_time'   => '21:45',
                'message'      => $isOpen
                    ? 'Open · Closes 9.45 pm'
                    : 'Closed · Opens 9.00 am',
            ],
        ]);
    }

    /**
     * POST /api/checkout
     *
     * User creates a rental from their cart.
     *
     * Request body:
     * {
     *   "payment_method": "cash" | "qris",
     *   "start_date": "2026-04-20 09:00:00",
     *   "end_date": "2026-04-22 09:00:00"
     * }
     */
    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'payment_method' => ['required', 'in:qris,cash'],
            'start_date'     => ['required', 'date', 'after_or_equal:today'],
            'end_date'       => ['required', 'date', 'after:start_date'],
            'item_ids'       => ['sometimes', 'array'],
            'item_ids.*'     => ['string'],
        ]);

        $user = $request->user();

        // Load user's cart from cache (same as CartController)
        $fullCart = Cache::get("cart_{$user->id}", []);

        if (empty($fullCart)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Keranjang kosong.',
            ], 422);
        }

        // Filter cart by item_ids if provided
        $itemIds = $request->input('item_ids', []);
        if (!empty($itemIds)) {
            $cart = array_values(array_filter($fullCart, fn($item) => in_array($item['id'], $itemIds)));
            if (empty($cart)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Tidak ada item terpilih yang valid.',
                ], 422);
            }
        } else {
            $cart = $fullCart;
        }

        // Check user has an address
        if ($user->addresses()->count() === 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda harus menambahkan alamat terlebih dahulu.',
            ], 422);
        }

        // Hydrate cart items with product data (including sizes)
        $productIds = collect($cart)->pluck('product_id')->unique()->toArray();
        $products   = Product::with('sizes')->whereIn('id', $productIds)->get()->keyBy('id');

        return DB::transaction(function () use ($request, $user, $cart, $fullCart, $itemIds, $products) {
            $startDate = Carbon::parse($request->start_date);
            $endDate   = Carbon::parse($request->end_date);
            $days      = max(1, $startDate->diffInDays($endDate));

            // Validate stock & calculate total
            $totalPrice  = 0;
            $itemDetails = [];

            foreach ($cart as $cartItem) {
                $product = $products->get($cartItem['product_id']);

                if (!$product) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Produk tidak ditemukan.',
                    ], 422);
                }

                $size = $cartItem['size'] ?? null;

                // Per-size stock check
                if ($size) {
                    $sizeModel = $product->sizes->firstWhere('size', $size);
                    if (!$sizeModel || $sizeModel->stock < $cartItem['quantity']) {
                        $available = $sizeModel ? $sizeModel->stock : 0;
                        return response()->json([
                            'status'  => 'error',
                            'message' => "Stok {$product->name} ({$size}) tidak cukup. Tersedia: {$available}.",
                        ], 422);
                    }
                } else {
                    if ($product->stock_available < $cartItem['quantity']) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => "Stok {$product->name} tidak cukup. Tersedia: {$product->stock_available}.",
                        ], 422);
                    }
                }

                $subtotal = $product->price_24h * $cartItem['quantity'] * $days;
                $totalPrice += $subtotal;

                $itemDetails[] = [
                    'product'  => $product,
                    'quantity' => $cartItem['quantity'],
                    'price'    => $product->price_24h,
                    'size'     => $size,
                ];
            }

            // Generate invoice number
            $today = Carbon::today()->format('Ymd');
            $lastInvoice = Rental::where('invoice_no', 'like', "INV-{$today}-%")
                                  ->orderByDesc('invoice_no')
                                  ->first();
            $sequence = 1;
            if ($lastInvoice) {
                $lastSeq  = (int) substr($lastInvoice->invoice_no, -3);
                $sequence = $lastSeq + 1;
            }
            $invoiceNo = sprintf('INV-%s-%03d', $today, $sequence);

            // Create rental
            $rental = Rental::create([
                'user_id'        => $user->id,
                'invoice_no'     => $invoiceNo,
                'start_date'     => $startDate,
                'end_date'       => $endDate,
                'total_price'    => $totalPrice,
                'fine_amount'    => 0,
                'status'         => 'booked',
            ]);

            // Create rental items & decrease stock
            foreach ($itemDetails as $detail) {
                RentalItem::create([
                    'rental_id'       => $rental->id,
                    'product_id'      => $detail['product']->id,
                    'quantity'        => $detail['quantity'],
                    'price_at_rental' => $detail['price'],
                ]);

                // Per-size stock decrement
                if ($detail['size']) {
                    $detail['product']->sizes()
                        ->where('size', $detail['size'])
                        ->decrement('stock', $detail['quantity']);
                } else {
                    $detail['product']->decrement('stock_available', $detail['quantity']);
                }
            }

            // Create payment record
            $paymentData = [
                'rental_id'      => $rental->id,
                'amount'         => $totalPrice,
                'payment_method' => $request->payment_method,
                'status'         => $request->payment_method === 'cash' ? 'verified' : 'pending',
            ];

            // If QRIS, call Paydigital API
            if ($request->payment_method === 'qris') {
                $paydigital = new PaydigitalService();
                $result = $paydigital->createPayment(
                    (int) $totalPrice,
                    "Sewa peralatan - {$invoiceNo}",
                    $user->name
                );

                if (!$result['success']) {
                    // Rollback will happen automatically via DB::transaction
                    throw new \Exception($result['message'] ?? 'Gagal membuat pembayaran QRIS.');
                }

                $qrisData = $result['data'];
                $paymentData['qris_invoice'] = $qrisData['invoice'] ?? $qrisData['reference'] ?? null;
                $paymentData['qris_url']     = $qrisData['qr_url'] ?? $qrisData['pay_url'] ?? $qrisData['checkout_url'] ?? null;
                $paymentData['expired_at']   = isset($qrisData['expired_at'])
                    ? Carbon::parse($qrisData['expired_at'])
                    : Carbon::now()->addHours(24);
            }

            Payment::create($paymentData);

            // Remove only checked-out items from cache, keep the rest
            if (!empty($itemIds)) {
                $remainingCart = array_values(array_filter($fullCart, fn($item) => !in_array($item['id'], $itemIds)));
                if (empty($remainingCart)) {
                    Cache::forget("cart_{$user->id}");
                } else {
                    Cache::put("cart_{$user->id}", $remainingCart, now()->addMinutes(60 * 24));
                }
            } else {
                Cache::forget("cart_{$user->id}");
            }

            $rental->load(['user', 'items.product', 'payment']);

            return response()->json([
                'status'  => 'success',
                'message' => "Pesanan berhasil dibuat. Invoice: {$invoiceNo}",
                'data'    => new RentalResource($rental),
            ], 201);
        });
    }

    /**
     * GET /api/rentals
     *
     * User views their own rental history.
     * Query params:
     *   - status   : filter by status
     *   - per_page : items per page (default 10)
     */
    public function myRentals(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Rental::with(['items.product', 'payment'])
                       ->where('user_id', $user->id);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $query->orderByDesc('created_at');

        $perPage = min((int) $request->input('per_page', 10), 50);
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
     * GET /api/rentals/{id}
     *
     * User views a single rental detail (must own it).
     */
    public function showRental(Request $request, int $id): JsonResponse
    {
        $rental = Rental::with(['user', 'items.product', 'payment'])
                        ->where('user_id', $request->user()->id)
                        ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => new RentalResource($rental),
        ]);
    }

    /**
     * POST /api/rentals/{id}/check-payment
     *
     * Poll QRIS payment status from Paydigital.
     * If paid, update payment status to 'verified'.
     */
    public function checkPayment(Request $request, int $id): JsonResponse
    {
        $rental = Rental::with('payment')
                        ->where('user_id', $request->user()->id)
                        ->findOrFail($id);

        $payment = $rental->payment;

        if (!$payment) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data pembayaran tidak ditemukan.',
            ], 404);
        }

        // Already verified
        if ($payment->status === 'verified') {
            return response()->json([
                'status' => 'success',
                'data'   => [
                    'payment_status' => 'verified',
                    'message'        => 'Pembayaran sudah terverifikasi.',
                ],
            ]);
        }

        // Only QRIS payments can be polled
        if ($payment->payment_method !== 'qris' || !$payment->qris_invoice) {
            return response()->json([
                'status' => 'success',
                'data'   => [
                    'payment_status' => $payment->status,
                    'message'        => 'Pembayaran bukan QRIS.',
                ],
            ]);
        }

        // Check with Paydigital
        $paydigital = new PaydigitalService();
        $result = $paydigital->checkStatus($payment->qris_invoice);

        if ($result['success']) {
            $payData = $result['data'];
            $pgStatus = $payData['status'] ?? $payData['payment_status'] ?? 'pending';

            // Check if paid/success/settled
            if (in_array(strtolower($pgStatus), ['paid', 'success', 'settled', 'completed'])) {
                $payment->update(['status' => 'verified']);

                return response()->json([
                    'status' => 'success',
                    'data'   => [
                        'payment_status' => 'verified',
                        'message'        => 'Pembayaran QRIS berhasil diverifikasi!',
                    ],
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'payment_status' => 'pending',
                'message'        => 'Menunggu pembayaran...',
            ],
        ]);
    }
}
