<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'product_id'      => $this->product_id,
            'product'         => new ProductResource($this->whenLoaded('product')),
            'quantity'        => $this->quantity,
            'price_at_rental' => (float) $this->price_at_rental,
            'subtotal'        => (float) ($this->price_at_rental * $this->quantity),
        ];
    }
}
