<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPaymentController extends Controller
{
    /**
     * GET /api/admin/payments
     *
     * Query params:
     *   - status   : filter by status (pending, verified)
     *   - search   : search by invoice number
     *   - per_page : items per page (default 15)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['rental.user']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->whereHas('rental', function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%");
            });
        }

        $query->orderByDesc('created_at');
        $perPage = min((int) $request->input('per_page', 15), 50);

        $payments = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data'   => PaymentResource::collection($payments),
            'meta'   => [
                'current_page' => $payments->currentPage(),
                'last_page'    => $payments->lastPage(),
                'per_page'     => $payments->perPage(),
                'total'        => $payments->total(),
            ],
        ]);
    }

    /**
     * PUT /api/admin/payments/{id}/verify
     *
     * Verify a pending payment.
     */
    public function verify(int $id): JsonResponse
    {
        $payment = Payment::with('rental')->findOrFail($id);

        if ($payment->status === 'verified') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pembayaran sudah terverifikasi.',
            ], 422);
        }

        $payment->update(['status' => 'verified']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Pembayaran berhasil diverifikasi.',
            'data'    => new PaymentResource($payment),
        ]);
    }

    /**
     * POST /api/admin/payments/{rentalId}/upload-proof
     *
     * Upload bukti pembayaran (QRIS screenshot).
     * Request: multipart/form-data with 'payment_proof' file.
     */
    public function uploadProof(Request $request, int $rentalId): JsonResponse
    {
        $request->validate([
            'payment_proof' => ['required', 'image', 'max:2048'],
        ]);

        $payment = Payment::where('rental_id', $rentalId)->firstOrFail();

        // Delete old proof if exists
        if ($payment->payment_proof) {
            Storage::disk('public')->delete($payment->payment_proof);
        }

        $path = $request->file('payment_proof')->store('payments', 'public');
        $payment->update(['payment_proof' => $path]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Bukti pembayaran berhasil diupload.',
            'data'    => new PaymentResource($payment),
        ]);
    }
}
