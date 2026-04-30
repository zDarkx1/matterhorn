<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Resources\CartItemResource;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * CartController — Sanctum token-scoped cart stored in cache.
 *
 * Setiap user punya cart sendiri yang disimpan di cache (key: "cart_{userId}").
 * Cart persist selama cache TTL (24 jam default).
 *
 * Size-aware: product_id + size = unique cart entry.
 * If a product has sizes, size is required when adding to cart.
 */
class CartController extends Controller
{
    private const CACHE_TTL = 60 * 24; // 24 hours in minutes

    /**
     * GET /api/cart
     */
    public function index(Request $request): JsonResponse
    {
        $cart  = $this->getCart($request->user()->id);
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
     *
     * Body: { "product_id": 1, "quantity": 2, "size": "M" }
     * Size is optional — required only if the product has sizes.
     */
    public function store(AddToCartRequest $request): JsonResponse
    {
        $userId    = $request->user()->id;
        $cart      = $this->getCart($userId);
        $productId = $request->product_id;
        $quantity  = $request->quantity;
        $size      = $request->size; // nullable

        // Verify product exists
        $product = Product::with('sizes')->findOrFail($productId);

        // If product has sizes, size is required
        if ($product->sizes->isNotEmpty() && empty($size)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pilih ukuran terlebih dahulu.',
            ], 422);
        }

        // Stock check: per-size if size provided, otherwise product-level
        if ($size) {
            $sizeModel = $product->sizes->firstWhere('size', $size);
            if (!$sizeModel) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Ukuran '{$size}' tidak tersedia.",
                ], 422);
            }

            // Check existing qty in cart for this product+size
            $existing = collect($cart)->first(fn($i) => $i['product_id'] === $productId && ($i['size'] ?? null) === $size);
            $currentQty = $existing ? $existing['quantity'] : 0;

            if (($currentQty + $quantity) > $sizeModel->stock) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Stok ukuran {$size} tidak cukup. Tersedia: {$sizeModel->stock}.",
                ], 422);
            }
        } else {
            $existing = collect($cart)->firstWhere('product_id', $productId);
            $currentQty = $existing ? $existing['quantity'] : 0;

            if (($currentQty + $quantity) > $product->stock_available) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Stok tidak cukup. Tersedia: {$product->stock_available}.",
                ], 422);
            }
        }

        // Update or add — uniqueness = product_id + size
        $found = false;
        foreach ($cart as &$item) {
            if ($item['product_id'] === $productId && ($item['size'] ?? null) === $size) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        unset($item);

        if (!$found) {
            $entry = [
                'id'         => uniqid('cart_'),
                'product_id' => $productId,
                'quantity'   => $quantity,
            ];
            if ($size) {
                $entry['size'] = $size;
            }
            $cart[] = $entry;
        }

        $this->saveCart($userId, $cart);

        return response()->json([
            'status'  => 'success',
            'message' => "{$product->name}" . ($size ? " ({$size})" : '') . " ditambahkan ke keranjang.",
            'data'    => [
                'total_items' => collect($cart)->sum('quantity'),
            ],
        ], 201);
    }

    /**
     * PUT /api/cart/{itemId}
     */
    public function update(Request $request, string $itemId): JsonResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $userId = $request->user()->id;
        $cart   = $this->getCart($userId);

        foreach ($cart as &$item) {
            if ($item['id'] === $itemId) {
                $product = Product::with('sizes')->find($item['product_id']);
                $size = $item['size'] ?? null;

                if ($product) {
                    if ($size) {
                        $sizeModel = $product->sizes->firstWhere('size', $size);
                        if ($sizeModel && $request->quantity > $sizeModel->stock) {
                            return response()->json([
                                'status'  => 'error',
                                'message' => "Stok ukuran {$size} tidak cukup. Tersedia: {$sizeModel->stock}.",
                            ], 422);
                        }
                    } elseif ($request->quantity > $product->stock_available) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => "Stok tidak cukup. Tersedia: {$product->stock_available}.",
                        ], 422);
                    }
                }
                $item['quantity'] = $request->quantity;
                break;
            }
        }
        unset($item);

        $this->saveCart($userId, $cart);
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
     * DELETE /api/cart/{itemId}
     */
    public function destroy(Request $request, string $itemId): JsonResponse
    {
        $userId = $request->user()->id;
        $cart   = $this->getCart($userId);

        $cart = array_values(array_filter($cart, fn($item) => $item['id'] !== $itemId));

        $this->saveCart($userId, $cart);
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
     * DELETE /api/cart
     */
    public function clear(Request $request): JsonResponse
    {
        $this->saveCart($request->user()->id, []);

        return response()->json([
            'status'  => 'success',
            'message' => 'Keranjang dikosongkan.',
            'data'    => [
                'total_items' => 0,
                'total_price' => 0,
            ],
        ]);
    }

    // ── Private Helpers ──────────────────────────────────────

    private function getCart(int $userId): array
    {
        return Cache::get("cart_{$userId}", []);
    }

    private function saveCart(int $userId, array $cart): void
    {
        Cache::put("cart_{$userId}", $cart, now()->addMinutes(self::CACHE_TTL));
    }

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
