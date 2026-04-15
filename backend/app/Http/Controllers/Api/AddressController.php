<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * GET /api/addresses
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $addresses,
        ]);
    }

    /**
     * POST /api/addresses
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'label'          => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:20'],
            'province'       => ['required', 'string', 'max:255'],
            'city'           => ['required', 'string', 'max:255'],
            'district'       => ['required', 'string', 'max:255'],
            'postal_code'    => ['required', 'string', 'max:10'],
            'full_address'   => ['required', 'string'],
            'notes'          => ['nullable', 'string'],
            'latitude'       => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'      => ['nullable', 'numeric', 'between:-180,180'],
            'is_default'     => ['nullable', 'boolean'],
        ]);

        $validated['user_id'] = $request->user()->id;

        // If setting as default, unset other defaults first
        if (!empty($validated['is_default'])) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        // If this is the first address, make it default
        if ($request->user()->addresses()->count() === 0) {
            $validated['is_default'] = true;
        }

        $address = UserAddress::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Alamat berhasil ditambahkan.',
            'data'    => $address,
        ], 201);
    }

    /**
     * PUT /api/addresses/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $validated = $request->validate([
            'label'          => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:20'],
            'province'       => ['required', 'string', 'max:255'],
            'city'           => ['required', 'string', 'max:255'],
            'district'       => ['required', 'string', 'max:255'],
            'postal_code'    => ['required', 'string', 'max:10'],
            'full_address'   => ['required', 'string'],
            'notes'          => ['nullable', 'string'],
            'latitude'       => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'      => ['nullable', 'numeric', 'between:-180,180'],
            'is_default'     => ['nullable', 'boolean'],
        ]);

        if (!empty($validated['is_default'])) {
            $request->user()->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Alamat berhasil diperbarui.',
            'data'    => $address->fresh(),
        ]);
    }

    /**
     * DELETE /api/addresses/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $wasDefault = $address->is_default;
        $address->delete();

        // If deleted address was default, set the first remaining as default
        if ($wasDefault) {
            $next = $request->user()->addresses()->first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Alamat berhasil dihapus.',
        ]);
    }

    /**
     * PUT /api/addresses/{id}/default
     */
    public function setDefault(Request $request, int $id): JsonResponse
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $request->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Alamat utama berhasil diubah.',
        ]);
    }
}
