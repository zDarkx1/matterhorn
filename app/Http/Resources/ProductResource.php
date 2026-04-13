<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'category'        => $this->category,
            'gender'          => $this->gender ?? 'unisex',
            'description'     => $this->description,
            'image_url'       => $this->image ? asset($this->image) : null,
            'price_24h'       => (float) $this->price_24h,
            'stock_total'     => $this->stock_total,
            'stock_available' => $this->stock_available,
            'sizes'           => $this->whenLoaded('sizes', function () {
                return $this->sizes->map(fn($s) => [
                    'size'  => $s->size,
                    'stock' => $s->stock,
                ]);
            }),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
