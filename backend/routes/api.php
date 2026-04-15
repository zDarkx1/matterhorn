<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminPaymentController;
use App\Http\Controllers\Api\AdminProductController;
use App\Http\Controllers\Api\AdminRentalController;
use App\Http\Controllers\Api\AdminReportController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Matterhorn.co
|--------------------------------------------------------------------------
|
| Semua endpoint REST API untuk platform Matterhorn.co.
| Frontend: Next.js (port 3000) | Backend: Laravel (port 8000)
| Auth: Laravel Sanctum (Bearer token)
|
*/

// ═════════════════════════════════════════════════════════════
// ─── PUBLIC ENDPOINTS (No Auth Required) ─────────────────────
// ═════════════════════════════════════════════════════════════

// Products
Route::prefix('products')->group(function () {
    Route::get('/',           [ProductController::class, 'index']);
    Route::get('/categories', [ProductController::class, 'categories']);
    Route::get('/{id}',       [ProductController::class, 'show']);
});

// Store Status
Route::get('/store-status', [CheckoutController::class, 'storeStatus']);

// ═════════════════════════════════════════════════════════════
// ─── AUTH ENDPOINTS ──────────────────────────────────────────
// ═════════════════════════════════════════════════════════════

Route::prefix('auth')->group(function () {
    // Public
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    // Authenticated
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me',      [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// ═════════════════════════════════════════════════════════════
// ─── AUTHENTICATED USER ENDPOINTS ────────────────────────────
// ═════════════════════════════════════════════════════════════

Route::middleware('auth:sanctum')->group(function () {

    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('/',            [CartController::class, 'index']);
        Route::post('/',           [CartController::class, 'store']);
        Route::put('/{itemId}',    [CartController::class, 'update']);
        Route::delete('/{itemId}', [CartController::class, 'destroy']);
        Route::delete('/',         [CartController::class, 'clear']);
    });

    // Profile
    Route::get('/profile',          [ProfileController::class, 'show']);
    Route::put('/profile',          [ProfileController::class, 'updateProfile']);
    Route::put('/profile/password', [ProfileController::class, 'changePassword']);

    // Addresses
    Route::prefix('addresses')->group(function () {
        Route::get('/',              [AddressController::class, 'index']);
        Route::post('/',             [AddressController::class, 'store']);
        Route::put('/{id}',          [AddressController::class, 'update']);
        Route::delete('/{id}',       [AddressController::class, 'destroy']);
        Route::put('/{id}/default',  [AddressController::class, 'setDefault']);
    });
});

// ═════════════════════════════════════════════════════════════
// ─── ADMIN ENDPOINTS (Sanctum + Admin Role) ──────────────────
// ═════════════════════════════════════════════════════════════

Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {

    // Dashboard & Reports
    Route::get('/dashboard',        [AdminDashboardController::class, 'index']);
    Route::get('/reports/rentals',  [AdminReportController::class, 'rentalReport']);

    // Rentals Management
    Route::get('/rentals',              [AdminRentalController::class, 'index']);
    Route::post('/rentals',             [AdminRentalController::class, 'store']);
    Route::get('/rentals/{id}',         [AdminRentalController::class, 'show']);
    Route::put('/rentals/{id}',         [AdminRentalController::class, 'update']);
    Route::put('/rentals/{id}/status',  [AdminRentalController::class, 'updateStatus']);
    Route::delete('/rentals/{id}',      [AdminRentalController::class, 'destroy']);

    // Products CRUD
    Route::get('/products',         [AdminProductController::class, 'index']);
    Route::post('/products',        [AdminProductController::class, 'store']);
    Route::get('/products/{id}',    [AdminProductController::class, 'show']);
    Route::put('/products/{id}',    [AdminProductController::class, 'update']);
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy']);

    // Payments
    Route::get('/payments',                          [AdminPaymentController::class, 'index']);
    Route::put('/payments/{id}/verify',              [AdminPaymentController::class, 'verify']);
    Route::post('/payments/{rentalId}/upload-proof', [AdminPaymentController::class, 'uploadProof']);

    // Users CRUD
    Route::get('/users',          [AdminUserController::class, 'index']);
    Route::post('/users',         [AdminUserController::class, 'store']);
    Route::get('/users/{id}',     [AdminUserController::class, 'show']);
    Route::put('/users/{id}',     [AdminUserController::class, 'update']);
    Route::delete('/users/{id}',  [AdminUserController::class, 'destroy']);
});

// ── AI Chat (public – no auth required) ─────────────────
Route::post('/ai/chat', [\App\Http\Controllers\AiChatController::class, 'sendMessage']);
