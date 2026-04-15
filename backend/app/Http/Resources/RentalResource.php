<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'invoice_no'     => $this->invoice_no,
            'user'           => new UserResource($this->whenLoaded('user')),
            'admin'          => new UserResource($this->whenLoaded('admin')),
            'status'         => $this->status,
            'start_date'     => $this->start_date?->toISOString(),
            'end_date'       => $this->end_date?->toISOString(),
            'return_date'    => $this->return_date?->toISOString(),
            'total_price'    => (float) $this->total_price,
            'fine_amount'    => (float) $this->fine_amount,
            'guarantee_info' => $this->guarantee_info,
            'items'          => RentalItemResource::collection($this->whenLoaded('items')),
            'payment'        => new PaymentResource($this->whenLoaded('payment')),
            'created_at'     => $this->created_at?->toISOString(),
            'updated_at'     => $this->updated_at?->toISOString(),
        ];
    }
}
