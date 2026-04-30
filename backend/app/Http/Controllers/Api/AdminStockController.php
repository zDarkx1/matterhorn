<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminStockController extends Controller
{
    /**
     * GET /api/admin/products/{id}/sizes
     *
     * List all sizes and their stock for a product.
     */
    public function index(int $id): JsonResponse
    {
        $product = Product::with('sizes')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'sizes'        => $product->sizes->map(fn($s) => [
                    'id'    => $s->id,
                    'size'  => $s->size,
                    'stock' => $s->stock,
                ]),
            ],
        ]);
    }

    /**
     * POST /api/admin/products/{id}/sizes
     *
     * Add a new size variant to a product.
     * Body: { "size": "XXL", "stock": 5 }
     */
    public function store(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'size'  => ['required', 'string', 'max:20'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        // Check if size already exists
        $existing = $product->sizes()->where('size', $request->size)->first();
        if ($existing) {
            return response()->json([
                'status'  => 'error',
                'message' => "Ukuran '{$request->size}' sudah ada untuk produk ini.",
            ], 422);
        }

        $size = ProductSize::create([
            'product_id' => $product->id,
            'size'       => $request->size,
            'stock'      => $request->stock,
        ]);

        // Update product total stock
        $product->update([
            'stock_total'     => $product->sizes()->sum('stock'),
            'stock_available' => $product->sizes()->sum('stock'),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => "Ukuran '{$request->size}' berhasil ditambahkan.",
            'data'    => [
                'id'    => $size->id,
                'size'  => $size->size,
                'stock' => $size->stock,
            ],
        ], 201);
    }

    /**
     * PUT /api/admin/products/{id}/sizes/{sizeId}/restock
     *
     * Add stock to an existing size (increment).
     * Body: { "quantity": 10 }
     */
    public function restock(Request $request, int $id, int $sizeId): JsonResponse
    {
        $product = Product::findOrFail($id);
        $size = $product->sizes()->findOrFail($sizeId);

        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $size->increment('stock', $request->quantity);

        // Update product total stock
        $product->update([
            'stock_total'     => $product->sizes()->sum('stock'),
            'stock_available' => $product->sizes()->sum('stock'),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => "Stok ukuran '{$size->size}' berhasil ditambah {$request->quantity} unit.",
            'data'    => [
                'id'    => $size->id,
                'size'  => $size->size,
                'stock' => $size->stock,
            ],
        ]);
    }

    /**
     * PUT /api/admin/products/{id}/sizes/{sizeId}
     *
     * Update size name and/or stock value.
     * Body: { "size": "XL", "stock": 8 }
     */
    public function update(Request $request, int $id, int $sizeId): JsonResponse
    {
        $product = Product::findOrFail($id);
        $size = $product->sizes()->findOrFail($sizeId);

        $request->validate([
            'size'  => ['sometimes', 'string', 'max:20'],
            'stock' => ['sometimes', 'integer', 'min:0'],
        ]);

        // Check for duplicate name if renaming
        if ($request->has('size') && $request->size !== $size->size) {
            $duplicate = $product->sizes()
                ->where('size', $request->size)
                ->where('id', '!=', $sizeId)
                ->first();
            if ($duplicate) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Ukuran '{$request->size}' sudah ada.",
                ], 422);
            }
        }

        $size->update($request->only(['size', 'stock']));

        // Update product total stock
        $product->update([
            'stock_total'     => $product->sizes()->sum('stock'),
            'stock_available' => $product->sizes()->sum('stock'),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Ukuran berhasil diperbarui.',
            'data'    => [
                'id'    => $size->id,
                'size'  => $size->size,
                'stock' => $size->stock,
            ],
        ]);
    }

    /**
     * DELETE /api/admin/products/{id}/sizes/{sizeId}
     *
     * Remove a size variant.
     */
    public function destroy(int $id, int $sizeId): JsonResponse
    {
        $product = Product::findOrFail($id);
        $size = $product->sizes()->findOrFail($sizeId);

        $sizeName = $size->size;
        $size->delete();

        // Update product total stock
        $product->update([
            'stock_total'     => $product->sizes()->sum('stock'),
            'stock_available' => $product->sizes()->sum('stock'),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => "Ukuran '{$sizeName}' berhasil dihapus.",
        ]);
    }
}
