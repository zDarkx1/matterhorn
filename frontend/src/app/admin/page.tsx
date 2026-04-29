'use client';

import { useState, useEffect } from 'react';
import { adminService, type DashboardData } from '@/services/admin.service';
import { formatRupiah, formatDate } from '@/utils/format';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Skeleton } from '@/components/ui/skeleton';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import {
  Users,
  ShoppingCart,
  Package,
  DollarSign,
  TrendingUp,
  TrendingDown,
} from 'lucide-react';

const statusColors: Record<string, string> = {
  booked: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
  active: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
  returned: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
  canceled: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
  overdue: 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
};

export default function AdminDashboardPage() {
  const [data, setData] = useState<DashboardData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    adminService
      .getDashboard()
      .then(setData)
      .catch(console.error)
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="space-y-6">
        <div>
          <h1 className="font-display text-2xl font-bold uppercase tracking-tight">Dashboard</h1>
          <p className="text-sm text-muted-foreground mt-1">Ringkasan performa toko</p>
        </div>
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
          {[...Array(4)].map((_, i) => (
            <Card key={i}>
              <CardHeader className="pb-2"><Skeleton className="h-4 w-24" /></CardHeader>
              <CardContent><Skeleton className="h-8 w-32" /></CardContent>
            </Card>
          ))}
        </div>
        <Card>
          <CardHeader><Skeleton className="h-5 w-40" /></CardHeader>
          <CardContent><Skeleton className="h-48 w-full" /></CardContent>
        </Card>
      </div>
    );
  }

  if (!data) return <p className="text-muted-foreground">Gagal memuat data dashboard.</p>;

  const stats = [
    {
      title: 'Total Pelanggan',
      value: data.total_users.toLocaleString('id-ID'),
      change: data.users_change,
      icon: Users,
      color: 'text-blue-600 dark:text-blue-400',
      bg: 'bg-blue-50 dark:bg-blue-900/20',
    },
    {
      title: 'Sewa Aktif',
      value: data.active_rentals.toLocaleString('id-ID'),
      change: data.rentals_change,
      icon: ShoppingCart,
      color: 'text-green-600 dark:text-green-400',
      bg: 'bg-green-50 dark:bg-green-900/20',
    },
    {
      title: 'Total Produk',
      value: data.total_products.toLocaleString('id-ID'),
      change: data.products_change,
      icon: Package,
      color: 'text-purple-600 dark:text-purple-400',
      bg: 'bg-purple-50 dark:bg-purple-900/20',
    },
    {
      title: 'Pendapatan',
      value: formatRupiah(data.total_revenue),
      change: data.revenue_change,
      icon: DollarSign,
      color: 'text-brand-orange',
      bg: 'bg-orange-50 dark:bg-orange-900/20',
    },
  ];

  // Revenue chart — simple bar chart
  const maxRevenue = Math.max(...(data.monthly_revenue?.map((m) => m.total) || [1]));

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="font-display text-2xl font-bold uppercase tracking-tight">Dashboard</h1>
        <p className="text-sm text-muted-foreground mt-1">Ringkasan performa toko</p>
      </div>

      {/* Stats Cards */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        {stats.map((stat) => (
          <Card key={stat.title} className="relative overflow-hidden">
            <CardHeader className="flex flex-row items-center justify-between pb-2">
              <CardTitle className="text-sm font-medium text-muted-foreground">{stat.title}</CardTitle>
              <div className={`rounded-lg p-2 ${stat.bg}`}>
                <stat.icon className={`h-4 w-4 ${stat.color}`} />
              </div>
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{stat.value}</div>
              <div className="flex items-center gap-1 mt-1">
                {stat.change >= 0 ? (
                  <TrendingUp className="h-3 w-3 text-green-600" />
                ) : (
                  <TrendingDown className="h-3 w-3 text-red-600" />
                )}
                <span className={`text-xs font-medium ${stat.change >= 0 ? 'text-green-600' : 'text-red-600'}`}>
                  {stat.change >= 0 ? '+' : ''}{stat.change}%
                </span>
                <span className="text-xs text-muted-foreground">vs bulan lalu</span>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      <div className="grid gap-6 lg:grid-cols-5">
        {/* Revenue Chart */}
        <Card className="lg:col-span-3">
          <CardHeader>
            <CardTitle className="font-display text-lg uppercase tracking-tight">Pendapatan Bulanan</CardTitle>
          </CardHeader>
          <CardContent>
            {data.monthly_revenue && data.monthly_revenue.length > 0 ? (
              <div className="flex items-end gap-2 h-48">
                {data.monthly_revenue.map((m) => (
                  <div key={m.month} className="flex-1 flex flex-col items-center gap-1">
                    <span className="text-[10px] font-medium text-muted-foreground">
                      {formatRupiah(m.total)}
                    </span>
                    <div
                      className="w-full bg-brand-orange/80 rounded-t-md transition-all duration-500 hover:bg-brand-orange min-h-[4px]"
                      style={{ height: `${Math.max(4, (m.total / maxRevenue) * 160)}px` }}
                    />
                    <span className="text-[10px] text-muted-foreground">{m.month}</span>
                  </div>
                ))}
              </div>
            ) : (
              <p className="text-sm text-muted-foreground text-center py-12">Belum ada data pendapatan.</p>
            )}
          </CardContent>
        </Card>

        {/* Status Breakdown */}
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="font-display text-lg uppercase tracking-tight">Status Pesanan</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            {Object.entries(data.status_breakdown || {}).map(([status, count]) => (
              <div key={status} className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                  <div className={`h-2 w-2 rounded-full ${
                    status === 'active' ? 'bg-green-500' :
                    status === 'booked' ? 'bg-blue-500' :
                    status === 'overdue' ? 'bg-orange-500' :
                    status === 'canceled' ? 'bg-red-500' : 'bg-gray-400'
                  }`} />
                  <span className="text-sm capitalize">{status}</span>
                </div>
                <Badge variant="secondary" className="text-xs">{count as number}</Badge>
              </div>
            ))}
            {Object.keys(data.status_breakdown || {}).length === 0 && (
              <p className="text-sm text-muted-foreground text-center py-4">Tidak ada data.</p>
            )}
          </CardContent>
        </Card>
      </div>

      {/* Recent Rentals */}
      <Card>
        <CardHeader>
          <CardTitle className="font-display text-lg uppercase tracking-tight">Pesanan Terbaru</CardTitle>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Invoice</TableHead>
                <TableHead>Customer</TableHead>
                <TableHead>Total</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Tanggal</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {data.recent_rentals && data.recent_rentals.length > 0 ? (
                data.recent_rentals.map((rental) => (
                  <TableRow key={rental.id}>
                    <TableCell className="font-mono text-sm">{rental.invoice_no}</TableCell>
                    <TableCell>{rental.user?.name || '-'}</TableCell>
                    <TableCell>{formatRupiah(rental.total_price)}</TableCell>
                    <TableCell>
                      <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${statusColors[rental.status] || ''}`}>
                        {rental.status}
                      </span>
                    </TableCell>
                    <TableCell className="text-muted-foreground text-sm">{formatDate(rental.created_at)}</TableCell>
                  </TableRow>
                ))
              ) : (
                <TableRow>
                  <TableCell colSpan={5} className="text-center text-muted-foreground py-8">
                    Belum ada pesanan.
                  </TableCell>
                </TableRow>
              )}
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  );
}
