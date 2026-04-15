<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * GET /api/admin/dashboard
     *
     * Returns aggregated statistics for the admin dashboard:
     * - total_users: jumlah customer terdaftar
     * - active_rentals: jumlah rental yang sedang aktif (active + overdue)
     * - total_products: jumlah total item produk
     * - total_revenue: total pendapatan dari rental yang verified
     * - status_breakdown: jumlah rental per status
     * - recent_rentals: 5 rental terbaru
     */
    public function index(Request $request): JsonResponse
    {
        $totalUsers      = User::where('role', 'customer')->count();
        $activeRentals   = Rental::whereIn('status', ['active', 'overdue'])->count();
        $totalProducts   = Product::sum('stock_total');
        $totalRevenue    = Payment::where('status', 'verified')
                                  ->sum('amount');

        $statusBreakdown = Rental::selectRaw('status, COUNT(*) as count')
                                  ->groupBy('status')
                                  ->pluck('count', 'status');

        $recentRentals   = Rental::with(['user', 'items.product', 'payment'])
                                  ->orderByDesc('created_at')
                                  ->limit(5)
                                  ->get();

        // Monthly revenue for chart (last 6 months)
        $monthlyRevenue = Payment::where('status', 'verified')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total")
            ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m')")
            ->orderBy('month')
            ->limit(6)
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'total_users'      => $totalUsers,
                'active_rentals'   => $activeRentals,
                'total_products'   => $totalProducts,
                'total_revenue'    => (float) $totalRevenue,
                'status_breakdown' => $statusBreakdown,
                'monthly_revenue'  => $monthlyRevenue,
                'recent_rentals'   => \App\Http\Resources\RentalResource::collection($recentRentals),
            ],
        ]);
    }
}
