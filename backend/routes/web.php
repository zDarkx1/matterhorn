<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Matterhorn.co
|--------------------------------------------------------------------------
|
| Laravel hanya berfungsi sebagai API backend.
| Semua frontend di-handle oleh Next.js (port 3000).
| File ini hanya menyediakan health-check endpoint.
|
*/

Route::get('/', function () {
    return response()->json([
        'status'  => 'success',
        'message' => 'Matterhorn.co API is running.',
        'version' => '1.0.0',
    ]);
});
