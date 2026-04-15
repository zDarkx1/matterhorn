<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * GET /api/profile
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('addresses');

        return response()->json([
            'status' => 'success',
            'data'   => new UserResource($user),
        ]);
    }

    /**
     * PUT /api/profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'name'         => ['sometimes', 'string', 'max:255'],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:20'],
        ]);

        $request->user()->update($request->only(['name', 'phone_number']));

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil berhasil diperbarui.',
            'data'    => new UserResource($request->user()->fresh()),
        ]);
    }

    /**
     * PUT /api/profile/password
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $request->user()->update([
            'password' => $request->new_password, // Auto-hashed via model cast
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Password berhasil diubah.',
        ]);
    }
}
