<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Resources\CartItemResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * GET /api/cart
     */
    public function index(Request $request): JsonResponse
    {
        $cart  = session('cart', []);
        $items = $this->hydrateCart($cart);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'items'       => CartItemResource::collection($items),
                'total_items' => collect($cart)->sum('quantity'),
                'total_price' => collect($items)->sum(fn($i) => ($i['product']?->price_24h ?? 0) * $i['quantity']),
            ],
        ]);
    }

    /**
     * POST /api/cart
     */
    public function store(AddToCartRequest $request): JsonResponse
    {
        $cart = session('cart', []);
        $productId = $request->product_id;
        $quantity  = $request->quantity;

        // Verify stock
        $product = Product::findOrFail($productId);
        $existing = collect($cart)->firstWhere('product_id', $productId);
        $currentQty = $existing ? $existing['quantity'] : 0;

        if (($currentQty + $quantity) > $product->stock_available) {
            return response()->json([
                'status'  => 'error',
                'message' => "Stok tidak cukup. Tersedia: {$product->stock_available}.",
            ], 422);
        }

        // Update or add
        $found = false;
        foreach ($cart as &$item) {
            if ($item['product_id'] === $productId) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        unset($item);

        if (!$found) {
            $cart[] = [
                'id'         => uniqid('cart_'),
                'product_id' => $productId,
                'quantity'   => $quantity,
            ];
        }

        session(['cart' => $cart]);

        return response()->json([
            'status'  => 'success',
            'message' => "{$product->name} ditambahkan ke keranjang.",
            'data'    => [
                'total_items' => collect($cart)->sum('quantity'),
            ],
        ], 201);
    }

    /**
     * DELETE /api/cart/{itemId}
     */
    public function destroy(string $itemId): JsonResponse
    {
        $cart = session('cart', []);

        $cart = array_values(array_filter($cart, fn($item) => $item['id'] !== $itemId));

        session(['cart' => $cart]);

        $items = $this->hydrateCart($cart);

        return response()->json([
            'status'  => 'success',
            'message' => 'Item dihapus dari keranjang.',
            'data'    => [
                'total_items' => collect($cart)->sum('quantity'),
                'total_price' => collect($items)->sum(fn($i) => ($i['product']?->price_24h ?? 0) * $i['quantity']),
            ],
        ]);
    }

    /**
     * PUT /api/cart/{itemId}
     */
    public function update(Request $request, string $itemId): JsonResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $cart = session('cart', []);

        foreach ($cart as &$item) {
            if ($item['id'] === $itemId) {
                $product = Product::find($item['product_id']);
                if ($product && $request->quantity > $product->stock_available) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => "Stok tidak cukup. Tersedia: {$product->stock_available}.",
                    ], 422);
                }
                $item['quantity'] = $request->quantity;
                break;
            }
        }
        unset($item);

        session(['cart' => $cart]);

        $items = $this->hydrateCart($cart);

        return response()->json([
            'status'  => 'success',
            'message' => 'Jumlah item diperbarui.',
            'data'    => [
                'total_items' => collect($cart)->sum('quantity'),
                'total_price' => collect($items)->sum(fn($i) => ($i['product']?->price_24h ?? 0) * $i['quantity']),
            ],
        ]);
    }

    /**
     * Hydrate cart items with Product models.
     */
    private function hydrateCart(array $cart): array
    {
        $productIds = collect($cart)->pluck('product_id')->unique()->toArray();
        $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');

        return collect($cart)->map(function ($item) use ($products) {
            $item['product'] = $products->get($item['product_id']);
            return $item;
        })->toArray();
    }
}
