<?php

use App\Http\Controllers\AiChatController;
use App\Http\Controllers\AuthController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $categories = Product::select('category')->distinct()->limit(12)->pluck('category');
    return view('landing', compact('categories'));
});

Route::post('/ai-chat/send', [AiChatController::class, 'sendMessage'])->name('ai.chat.send');

// ─── Product Detail (Web) ────────────────────────────────────
Route::get('/product/{id}', function ($id) {
    $product = Product::with('sizes')->findOrFail($id);
    return view('product-detail', compact('product'));
})->name('product.detail');

// ─── Profile Page (Web) ──────────────────────────────────────
Route::get('/profile', function () {
    return view('profile');
})->middleware('auth')->name('profile');

// ─── Auth Routes (Web) ──────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
