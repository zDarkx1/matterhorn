<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    /**
     * GET /api/store-status
     *
     * Returns the current open/closed status of the store
     * based on server time (Asia/Jakarta timezone).
     * Store hours: 09:00 – 21:45 WIB
     */
    public function storeStatus(): JsonResponse
    {
        $now = Carbon::now('Asia/Jakarta');
        $openTime  = $now->copy()->setTime(9, 0, 0);
        $closeTime = $now->copy()->setTime(21, 45, 0);

        $isOpen = $now->between($openTime, $closeTime);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'is_open'      => $isOpen,
                'current_time' => $now->format('H:i'),
                'open_time'    => '09:00',
                'close_time'   => '21:45',
                'message'      => $isOpen
                    ? 'Open · Closes 9.45 pm'
                    : 'Closed · Opens 9.00 am',
            ],
        ]);
    }
}
