<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * GET /api/products
     *
     * Query params:
     *   - search    : keyword pencarian (nama / deskripsi)
     *   - category  : filter by category
     *   - gender    : filter by gender (pria, wanita, unisex, anak)
     *   - min_price : harga minimum
     *   - max_price : harga maksimum
     *   - sort      : price_asc, price_desc, newest, name_asc
     *   - per_page  : jumlah per halaman (default 12)
     */
    public function index(Request $request)
    {
        $query = Product::with('sizes');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter: category
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        // Filter: gender
        if ($gender = $request->input('gender')) {
            $query->where('gender', $gender);
        }

        // Filter: price range
        if ($min = $request->input('min_price')) {
            $query->where('price_24h', '>=', $min);
        }
        if ($max = $request->input('max_price')) {
            $query->where('price_24h', '<=', $max);
        }

        // Filter: available only
        if ($request->boolean('available_only')) {
            $query->where('stock_available', '>', 0);
        }

        // Sort
        switch ($request->input('sort', 'newest')) {
            case 'price_asc':
                $query->orderBy('price_24h', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price_24h', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $perPage = min((int) $request->input('per_page', 12), 48);

        return new ProductCollection($query->paginate($perPage));
    }

    /**
     * GET /api/products/categories
     */
    public function categories(): JsonResponse
    {
        $categories = Product::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return response()->json([
            'status' => 'success',
            'data'   => $categories,
        ]);
    }

    /**
     * GET /api/products/{id}
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::with('sizes')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => new ProductResource($product),
        ]);
    }
}
