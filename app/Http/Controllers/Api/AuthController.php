<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * POST /api/auth/register
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'password'     => $request->password, // Auto-hashed via model cast
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Pendaftaran berhasil.',
            'data'    => [
                'user'  => new UserResource($user),
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * POST /api/auth/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email atau password salah.',
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil.',
            'data'    => [
                'user'  => new UserResource($user),
                'token' => $token,
            ],
        ]);
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * POST /api/login
     *
     * Simple login API — kirim email & password, return detail user lengkap.
     * Tidak generate token, murni untuk validasi kredensial.
     */
    public function simpleLogin(Request $request): JsonResponse
    {
        // ── Ambil input (support JSON, form-data, x-www-form-urlencoded) ──
        $email    = $request->input('email') ?? $request->json('email');
        $password = $request->input('password') ?? $request->json('password');

        // ── Validasi manual ──────────────────────────────────
        $errors = [];
        if (empty($email)) {
            $errors['email'] = ['Email wajib diisi.'];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = ['Format email tidak valid.'];
        }
        if (empty($password)) {
            $errors['password'] = ['Password wajib diisi.'];
        }

        if (!empty($errors)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $errors,
                'data'    => null,
            ], 422);
        }

        // ── Cek kredensial ────────────────────────────────
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email atau password salah.',
                'data'    => null,
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ── Return semua detail user dari database ────────
        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil.',
            'data'    => [
                'user' => [
                    'id'                => $user->id,
                    'name'              => $user->name,
                    'email'             => $user->email,
                    'role'              => $user->role ?? 'customer',
                    'phone_number'      => $user->phone_number,
                    'address'           => $user->address,
                    'email_verified_at' => $user->email_verified_at?->toISOString(),
                    'created_at'        => $user->created_at?->toISOString(),
                    'updated_at'        => $user->updated_at?->toISOString(),
                ],
            ],
        ]);
    }
}
