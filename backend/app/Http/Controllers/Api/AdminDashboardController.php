<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * GET /api/admin/dashboard
     *
     * Returns aggregated statistics for the admin dashboard:
     * - total_users: jumlah customer terdaftar + persentase perubahan bulan ini vs bulan lalu
     * - active_rentals: jumlah rental yang sedang aktif (active + overdue) + persentase perubahan
     * - total_products: jumlah total item produk + persentase perubahan
     * - total_revenue: total pendapatan dari rental yang verified + persentase perubahan
     * - status_breakdown: jumlah rental per status
     * - monthly_revenue: revenue per bulan (6 bulan terakhir)
     * - recent_rentals: 5 rental terbaru
     */
    public function index(Request $request): JsonResponse
    {
        $now            = Carbon::now();
        $startThisMonth = $now->copy()->startOfMonth();
        $startLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endLastMonth   = $now->copy()->subMonth()->endOfMonth();

        // ─── Total Users (customers) ───────────────────────────────
        $totalUsers = User::where('role', 'customer')->count();

        $usersThisMonth = User::where('role', 'customer')
            ->where('created_at', '>=', $startThisMonth)
            ->count();

        $usersLastMonth = User::where('role', 'customer')
            ->whereBetween('created_at', [$startLastMonth, $endLastMonth])
            ->count();

        $usersChange = $this->percentageChange($usersLastMonth, $usersThisMonth);

        // ─── Active Rentals (sedang disewa) ────────────────────────
        $activeRentals = Rental::whereIn('status', ['active', 'overdue'])->count();

        $activeThisMonth = Rental::whereIn('status', ['active', 'overdue'])
            ->where('created_at', '>=', $startThisMonth)
            ->count();

        $activeLastMonth = Rental::whereIn('status', ['active', 'overdue'])
            ->whereBetween('created_at', [$startLastMonth, $endLastMonth])
            ->count();

        $activeChange = $this->percentageChange($activeLastMonth, $activeThisMonth);

        // ─── Total Products ────────────────────────────────────────
        $totalProducts = Product::sum('stock_total');

        $productsThisMonth = Product::where('created_at', '>=', $startThisMonth)
            ->sum('stock_total');

        $productsLastMonth = Product::whereBetween('created_at', [$startLastMonth, $endLastMonth])
            ->sum('stock_total');

        $productsChange = $this->percentageChange($productsLastMonth, $productsThisMonth);

        // ─── Total Revenue ─────────────────────────────────────────
        $totalRevenue = Payment::where('status', 'verified')->sum('amount');

        $revenueThisMonth = Payment::where('status', 'verified')
            ->where('created_at', '>=', $startThisMonth)
            ->sum('amount');

        $revenueLastMonth = Payment::where('status', 'verified')
            ->whereBetween('created_at', [$startLastMonth, $endLastMonth])
            ->sum('amount');

        $revenueChange = $this->percentageChange($revenueLastMonth, $revenueThisMonth);

        // ─── Status Breakdown ──────────────────────────────────────
        $statusBreakdown = Rental::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // ─── Recent Rentals ────────────────────────────────────────
        $recentRentals = Rental::with(['user', 'items.product', 'payment'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ─── Monthly Revenue Chart (last 6 months) ─────────────────
        $monthlyRevenue = Payment::where('status', 'verified')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total")
            ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m')")
            ->orderBy('month')
            ->limit(6)
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => [
                // Card stats with percentage changes
                'total_users'      => $totalUsers,
                'users_change'     => $usersChange,

                'active_rentals'   => $activeRentals,
                'rentals_change'   => $activeChange,

                'total_products'   => $totalProducts,
                'products_change'  => $productsChange,

                'total_revenue'    => (float) $totalRevenue,
                'revenue_change'   => $revenueChange,

                // Breakdown & charts
                'status_breakdown' => $statusBreakdown,
                'monthly_revenue'  => $monthlyRevenue,
                'recent_rentals'   => \App\Http\Resources\RentalResource::collection($recentRentals),
            ],
        ]);
    }

    /**
     * Hitung persentase perubahan antara periode lama dan baru.
     * Jika periode lama = 0 dan baru > 0 → +100%
     * Jika keduanya 0 → 0%
     */
    private function percentageChange($old, $new): float
    {
        if ($old == 0) {
            return $new > 0 ? 100.0 : 0.0;
        }

        return round((($new - $old) / $old) * 100, 2);
    }
}
