<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Rental;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    /**
     * GET /api/admin/reports/rentals
     *
     * Generate PDF report of rentals.
     *
     * Query params:
     *   - status    : filter by status
     *   - date_from : start date (YYYY-MM-DD)
     *   - date_to   : end date (YYYY-MM-DD)
     */
    public function rentalReport(Request $request)
    {
        $query = Rental::with(['user', 'items.product', 'payment']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $query->orderByDesc('created_at');
        $rentals = $query->get();

        // Calculate summary
        $totalRevenue    = $rentals->where('status', '!=', 'canceled')->sum('total_price');
        $totalFines      = $rentals->sum('fine_amount');
        $totalRentals    = $rentals->count();
        $verifiedPayments = Payment::whereIn('rental_id', $rentals->pluck('id'))
                                    ->where('status', 'verified')
                                    ->sum('amount');

        $statusBreakdown = $rentals->groupBy('status')->map->count();

        $data = [
            'rentals'           => $rentals,
            'total_revenue'     => $totalRevenue,
            'total_fines'       => $totalFines,
            'total_rentals'     => $totalRentals,
            'verified_payments' => $verifiedPayments,
            'status_breakdown'  => $statusBreakdown,
            'date_from'         => $dateFrom ?? 'Semua',
            'date_to'           => $dateTo ?? 'Semua',
            'status_filter'     => $status ?? 'Semua',
            'generated_at'      => Carbon::now()->format('d M Y H:i'),
        ];

        $pdf = Pdf::loadView('reports.rentals', $data)
                   ->setPaper('a4', 'landscape');

        $filename = 'laporan-rental-' . Carbon::now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }
}
