<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rental - Matterhorn</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }

        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 3px solid #1a1a1a;
            margin-bottom: 20px;
        }
        .header h1 { font-size: 22px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
        .header p { font-size: 12px; color: #666; margin-top: 4px; }

        .filter-info {
            background: #f5f5f5;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .filter-info span { font-weight: 600; }

        .summary {
            display: flex;
            margin-bottom: 20px;
        }
        .summary-box {
            display: inline-block;
            width: 24%;
            background: #fafafa;
            border: 1px solid #e0e0e0;
            padding: 12px;
            text-align: center;
            margin-right: 1%;
        }
        .summary-box .label { font-size: 9px; text-transform: uppercase; color: #888; letter-spacing: 1px; }
        .summary-box .value { font-size: 18px; font-weight: 700; margin-top: 4px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #1a1a1a;
            color: #fff;
            padding: 8px 6px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            padding: 7px 6px;
            border-bottom: 1px solid #e8e8e8;
            font-size: 10px;
        }
        tr:nth-child(even) { background: #fafafa; }

        .status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-active { background: #e8f5e9; color: #2e7d32; }
        .status-booked { background: #fff3e0; color: #ef6c00; }
        .status-returned { background: #e3f2fd; color: #1565c0; }
        .status-canceled { background: #fce4ec; color: #c62828; }
        .status-overdue { background: #fbe9e7; color: #d84315; }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #999;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Matterhorn Outdoor Rental</h1>
        <p>Laporan Data Rental</p>
    </div>

    <div class="filter-info">
        Filter: <span>Status:</span> {{ $status_filter }} |
        <span>Periode:</span> {{ $date_from }} s/d {{ $date_to }} |
        <span>Dicetak:</span> {{ $generated_at }}
    </div>

    <!-- Summary Cards -->
    <table style="margin-bottom: 20px;">
        <tr>
            <td style="width: 25%; text-align: center; background: #f0f0f0; border: 1px solid #ddd; padding: 10px;">
                <div style="font-size: 9px; text-transform: uppercase; color: #888;">Total Rental</div>
                <div style="font-size: 20px; font-weight: 700;">{{ $total_rentals }}</div>
            </td>
            <td style="width: 25%; text-align: center; background: #f0f0f0; border: 1px solid #ddd; padding: 10px;">
                <div style="font-size: 9px; text-transform: uppercase; color: #888;">Total Revenue</div>
                <div style="font-size: 20px; font-weight: 700;">Rp {{ number_format($total_revenue, 0, ',', '.') }}</div>
            </td>
            <td style="width: 25%; text-align: center; background: #f0f0f0; border: 1px solid #ddd; padding: 10px;">
                <div style="font-size: 9px; text-transform: uppercase; color: #888;">Pembayaran Verified</div>
                <div style="font-size: 20px; font-weight: 700;">Rp {{ number_format($verified_payments, 0, ',', '.') }}</div>
            </td>
            <td style="width: 25%; text-align: center; background: #f0f0f0; border: 1px solid #ddd; padding: 10px;">
                <div style="font-size: 9px; text-transform: uppercase; color: #888;">Total Denda</div>
                <div style="font-size: 20px; font-weight: 700;">Rp {{ number_format($total_fines, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <!-- Status Breakdown -->
    <table style="margin-bottom: 15px; width: auto;">
        <tr>
            @foreach ($status_breakdown as $status => $count)
                <td style="padding: 4px 12px; border: 1px solid #ddd;">
                    <span class="status status-{{ $status }}">{{ ucfirst($status) }}</span>: {{ $count }}
                </td>
            @endforeach
        </tr>
    </table>

    <!-- Rental Data Table -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Invoice</th>
                <th>Customer</th>
                <th>Produk</th>
                <th>Tgl Mulai</th>
                <th>Tgl Selesai</th>
                <th>Tgl Kembali</th>
                <th class="text-right">Total</th>
                <th class="text-right">Denda</th>
                <th class="text-center">Status</th>
                <th>Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rentals as $index => $rental)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $rental->invoice_no }}</td>
                    <td>{{ $rental->user->name ?? '-' }}</td>
                    <td>
                        @foreach ($rental->items as $item)
                            {{ $item->product->name ?? 'N/A' }} (×{{ $item->quantity }})<br>
                        @endforeach
                    </td>
                    <td>{{ $rental->start_date->format('d/m/Y') }}</td>
                    <td>{{ $rental->end_date->format('d/m/Y') }}</td>
                    <td>{{ $rental->return_date ? $rental->return_date->format('d/m/Y') : '-' }}</td>
                    <td class="text-right">Rp {{ number_format($rental->total_price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($rental->fine_amount, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="status status-{{ $rental->status }}">{{ ucfirst($rental->status) }}</span>
                    </td>
                    <td>
                        @if ($rental->payment)
                            {{ strtoupper($rental->payment->payment_method) }}
                            ({{ $rental->payment->status }})
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px; color: #999;">
                        Tidak ada data rental untuk filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Matterhorn Outdoor Rental &mdash; Laporan digenerate otomatis pada {{ $generated_at }}
    </div>
</body>
</html>
