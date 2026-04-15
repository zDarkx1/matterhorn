<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Only allow users with role 'admin' to proceed.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->role !== 'admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akses ditolak. Hanya admin yang diizinkan.',
            ], 403);
        }

        return $next($request);
    }
}
