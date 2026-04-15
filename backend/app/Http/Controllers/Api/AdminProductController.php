<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    /**
     * GET /api/admin/products
     *
     * Query params:
     *   - search   : keyword pencarian (nama / deskripsi / category)
     *   - category : filter by category
     *   - per_page : jumlah per halaman (default 15)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('sizes');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $query->orderBy('created_at', 'desc');
        $perPage = min((int) $request->input('per_page', 15), 50);

        $products = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data'   => ProductResource::collection($products),
            'meta'   => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    /**
     * GET /api/admin/products/{id}
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::with('sizes')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => new ProductResource($product),
        ]);
    }

    /**
     * POST /api/admin/products
     *
     * Request body (multipart/form-data):
     * {
     *   "name": "Produk Baru",
     *   "category": "Carrier",
     *   "gender": "unisex",
     *   "description": "Deskripsi produk",
     *   "image": <file>,
     *   "price_24h": 50000,
     *   "stock_total": 10,
     *   "stock_available": 10,
     *   "sizes": [{"size": "M", "stock": 5}, {"size": "L", "stock": 5}]
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'category'        => ['required', 'string', 'max:100'],
            'gender'          => ['sometimes', 'in:unisex,pria,wanita,anak'],
            'description'     => ['nullable', 'string'],
            'image'           => ['nullable', 'image', 'max:2048'],
            'price_24h'       => ['required', 'numeric', 'min:0'],
            'stock_total'     => ['required', 'integer', 'min:0'],
            'stock_available' => ['required', 'integer', 'min:0'],
            'sizes'           => ['nullable', 'array'],
            'sizes.*.size'    => ['required_with:sizes', 'string'],
            'sizes.*.stock'   => ['required_with:sizes', 'integer', 'min:0'],
        ]);

        return DB::transaction(function () use ($request) {
            $data = $request->only(['name', 'category', 'gender', 'description', 'price_24h', 'stock_total', 'stock_available']);

            // Handle image upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $data['image'] = 'storage/' . $path;
            }

            $product = Product::create($data);

            // Create sizes if provided
            if ($request->has('sizes')) {
                $sizes = is_string($request->sizes) ? json_decode($request->sizes, true) : $request->sizes;
                if (is_array($sizes)) {
                    foreach ($sizes as $size) {
                        ProductSize::create([
                            'product_id' => $product->id,
                            'size'       => $size['size'],
                            'stock'      => $size['stock'] ?? 0,
                        ]);
                    }
                }
            }

            $product->load('sizes');

            return response()->json([
                'status'  => 'success',
                'message' => 'Produk berhasil ditambahkan.',
                'data'    => new ProductResource($product),
            ], 201);
        });
    }

    /**
     * PUT /api/admin/products/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'            => ['sometimes', 'string', 'max:255'],
            'category'        => ['sometimes', 'string', 'max:100'],
            'gender'          => ['sometimes', 'in:unisex,pria,wanita,anak'],
            'description'     => ['nullable', 'string'],
            'image'           => ['nullable', 'image', 'max:2048'],
            'price_24h'       => ['sometimes', 'numeric', 'min:0'],
            'stock_total'     => ['sometimes', 'integer', 'min:0'],
            'stock_available' => ['sometimes', 'integer', 'min:0'],
            'sizes'           => ['nullable', 'array'],
            'sizes.*.size'    => ['required_with:sizes', 'string'],
            'sizes.*.stock'   => ['required_with:sizes', 'integer', 'min:0'],
        ]);

        $data = $request->only(['name', 'category', 'gender', 'description', 'price_24h', 'stock_total', 'stock_available']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                $oldPath = str_replace('storage/', '', $product->image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = 'storage/' . $path;
        }

        $product->update($data);

        // Update sizes if provided
        if ($request->has('sizes')) {
            $sizes = is_string($request->sizes) ? json_decode($request->sizes, true) : $request->sizes;
            if (is_array($sizes)) {
                $product->sizes()->delete();
                foreach ($sizes as $size) {
                    ProductSize::create([
                        'product_id' => $product->id,
                        'size'       => $size['size'],
                        'stock'      => $size['stock'] ?? 0,
                    ]);
                }
            }
        }

        $product->load('sizes');

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil diperbarui.',
            'data'    => new ProductResource($product),
        ]);
    }

    /**
     * DELETE /api/admin/products/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        // Check if product is in active rentals
        $activeRentals = $product->rentalItems()
            ->whereHas('rental', function ($q) {
                $q->whereIn('status', ['active', 'booked', 'overdue']);
            })
            ->count();

        if ($activeRentals > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak bisa dihapus karena sedang dalam rental aktif.',
            ], 422);
        }

        // Delete image
        if ($product->image) {
            $path = str_replace('storage/', '', $product->image);
            Storage::disk('public')->delete($path);
        }

        $product->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil dihapus.',
        ]);
    }
}
