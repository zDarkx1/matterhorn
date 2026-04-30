<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'rental_id'      => $this->rental_id,
            'amount'         => (float) $this->amount,
            'payment_method' => $this->payment_method,
            'payment_proof'  => $this->payment_proof ? asset('storage/' . $this->payment_proof) : null,
            'status'         => $this->status,
            'qris_invoice'   => $this->qris_invoice,
            'qris_url'       => $this->qris_url,
            'expired_at'     => $this->expired_at?->toISOString(),
            'created_at'     => $this->created_at?->toISOString(),
            'updated_at'     => $this->updated_at?->toISOString(),
            'rental'         => $this->whenLoaded('rental', function () {
                return [
                    'id'         => $this->rental->id,
                    'invoice_no' => $this->rental->invoice_no,
                    'user'       => $this->rental->user ? [
                        'id'   => $this->rental->user->id,
                        'name' => $this->rental->user->name,
                    ] : null,
                ];
            }),
        ];
    }
}
