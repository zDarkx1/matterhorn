<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // If image is already a full URL (CDN), return as-is; otherwise use asset()
        $imageUrl = null;
        if ($this->image) {
            $imageUrl = str_starts_with($this->image, 'http')
                ? $this->image
                : asset($this->image);
        }

        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'category'        => $this->category,
            'gender'          => $this->gender ?? 'unisex',
            'description'     => $this->description,
            'image_url'       => $imageUrl,
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
