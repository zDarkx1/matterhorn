<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this['product'] ?? null;

        return [
            'id'         => $this['id'] ?? null,
            'product_id' => $this['product_id'],
            'quantity'   => $this['quantity'],
            'product'    => $product ? [
                'id'              => $product->id,
                'name'            => $product->name,
                'category'        => $product->category,
                'image_url'       => $product->image ? asset($product->image) : null,
                'price_24h'       => (float) $product->price_24h,
                'stock_available' => $product->stock_available,
            ] : null,
            'subtotal' => $product ? (float) $product->price_24h * $this['quantity'] : 0,
        ];
    }
}
