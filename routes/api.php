<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

// ─── Public Endpoints ────────────────────────────────────────
Route::prefix('products')->group(function () {
    Route::get('/',           [ProductController::class, 'index']);
    Route::get('/categories', [ProductController::class, 'categories']);
    Route::get('/{id}',       [ProductController::class, 'show']);
});

// ─── Auth Endpoints ──────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// ─── Simple Login API (standalone, no token) ─────────────────
Route::post('/login', [AuthController::class, 'simpleLogin']);

// ─── Session-based Endpoints (uses web middleware for cookie/session) ─
Route::middleware('web')->group(function () {

    // Cart (accessible to everyone via session)
    Route::prefix('cart')->group(function () {
        Route::get('/',           [CartController::class, 'index']);
        Route::post('/',          [CartController::class, 'store']);
        Route::put('/{itemId}',   [CartController::class, 'update']);
        Route::delete('/{itemId}',[CartController::class, 'destroy']);
    });

    // Profile (requires session login)
    Route::middleware('auth')->group(function () {
        Route::get('/profile',          [ProfileController::class, 'show']);
        Route::put('/profile/password', [ProfileController::class, 'changePassword']);
    });
});

// ─── Token-based Endpoints (Sanctum API tokens) ─────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});
