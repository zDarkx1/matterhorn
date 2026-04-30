<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Matterhorn.co
|--------------------------------------------------------------------------
|
| Laravel hanya berfungsi sebagai API backend.
| Semua frontend di-handle oleh Next.js (port 3000).
| File ini menyediakan health-check endpoint dan Google OAuth routes.
|
*/

Route::get('/', function () {
    return response()->json([
        'status'  => 'success',
        'message' => 'Matterhorn.co API is running.',
        'version' => '1.0.0',
    ]);
});

// Google OAuth routes (must be web routes for Socialite session-based redirect)
Route::get('/auth/google/redirect', [AuthController::class, 'googleRedirect']);
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback']);
