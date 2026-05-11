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
use Laravel\Socialite\Facades\Socialite;

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
        // Check if user exists and is Google-only (no password)
        $user = User::where('email', $request->email)->first();
        if ($user && $user->isGoogleOnly()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akun ini terdaftar melalui Google. Silakan gunakan tombol "Lanjutkan dengan Google" untuk masuk.',
            ], 401);
        }

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
     * GET /api/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => [
                'user' => new UserResource($request->user()),
            ],
        ]);
    }

    /**
     * GET /auth/google/redirect (web route)
     *
     * Redirect user to Google OAuth consent screen.
     */
    public function googleRedirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * GET /auth/google/callback (web route)
     *
     * Handle Google callback, find/create user, and redirect to frontend.
     *
     * Rules:
     * - If user exists with matching email → link google_id and login
     * - If user exists with matching google_id → login
     * - If no user found → create new account (password = null, google-only)
     * - Google-only users CANNOT login by email/password and CANNOT re-register
     */
    public function googleCallback()
    {
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            \Log::error('Google OAuth failed: ' . $e->getMessage());
            return redirect($frontendUrl . '/login?error=' . urlencode('Google login gagal. Silakan coba lagi.'));
        }

        try {
            // Try to find user by google_id first, then by email
            $user = User::where('google_id', $googleUser->getId())->first();

            if (!$user) {
                $user = User::where('email', $googleUser->getEmail())->first();
            }

            if ($user) {
                // Existing user — link google_id if not yet linked
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar'    => $googleUser->getAvatar(),
                    ]);
                }
            } else {
                // New user — create google-only account (no password)
                $user = new User();
                $user->name      = $googleUser->getName();
                $user->email     = $googleUser->getEmail();
                $user->google_id = $googleUser->getId();
                $user->avatar    = $googleUser->getAvatar();
                $user->save();
            }

            // Generate Sanctum token
            $token = $user->createToken('google-auth')->plainTextToken;

            // Redirect to frontend with token and user data
            $userData = urlencode(json_encode(new UserResource($user)));

            return redirect($frontendUrl . '/auth/google/callback?token=' . $token . '&user=' . $userData);
        } catch (\Exception $e) {
            \Log::error('Google OAuth user creation/login failed: ' . $e->getMessage());
            return redirect($frontendUrl . '/login?error=' . urlencode('Gagal memproses akun Google. Silakan coba lagi atau daftar terlebih dahulu.'));
        }
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
