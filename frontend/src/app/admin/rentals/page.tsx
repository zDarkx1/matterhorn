'use client';

import { useState, useEffect, useCallback } from 'react';
import { adminService } from '@/services/admin.service';
import { formatRupiah, formatDate } from '@/utils/format';
import type { Rental } from '@/types/rental.types';
import type { PaginationMeta } from '@/types/api.types';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import {
  Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
} from '@/components/ui/table';
import {
  AlertDialog, AlertDialogContent,
  AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import {
  Dialog, DialogBody, DialogContent, DialogHeader,
} from '@/components/ui/dialog';
import { Search, PlayCircle, RotateCcw, XCircle, AlertTriangle, Eye } from 'lucide-react';
import { toast } from 'sonner';

const statusTabs = [
  { value: '', label: 'Semua' },
  { value: 'booked', label: 'Booked' },
  { value: 'active', label: 'Active' },
  { value: 'returned', label: 'Returned' },
  { value: 'canceled', label: 'Canceled' },
  { value: 'overdue', label: 'Overdue' },
];

const statusColors: Record<string, string> = {
  booked: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
  active: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
  returned: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
  canceled: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
  overdue: 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
};

const statusActions: Record<string, { label: string; target: string; icon: React.ElementType; variant: string }[]> = {
  booked: [
    { label: 'Aktivasi', target: 'active', icon: PlayCircle, variant: 'default' },
    { label: 'Batalkan', target: 'canceled', icon: XCircle, variant: 'destructive' },
  ],
  active: [
    { label: 'Dikembalikan', target: 'returned', icon: RotateCcw, variant: 'default' },
    { label: 'Overdue', target: 'overdue', icon: AlertTriangle, variant: 'destructive' },
  ],
  overdue: [
    { label: 'Dikembalikan', target: 'returned', icon: RotateCcw, variant: 'default' },
  ],
};

export default function AdminRentalsPage() {
  const [rentals, setRentals] = useState<Rental[]>([]);
  const [meta, setMeta] = useState<PaginationMeta | null>(null);
  const [loading, setLoading] = useState(true);
  const [statusFilter, setStatusFilter] = useState('');
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);

  // Action confirmation
  const [actionTarget, setActionTarget] = useState<{ rental: Rental; newStatus: string } | null>(null);
  const [acting, setActing] = useState(false);

  // Detail drawer
  const [detailRental, setDetailRental] = useState<Rental | null>(null);

  const fetchRentals = useCallback(async () => {
    setLoading(true);
    try {
      const res = await adminService.getRentals({
        status: statusFilter || undefined,
        search: search || undefined,
        page,
        per_page: 10,
      });
      setRentals(res.data);
      setMeta(res.meta);
    } catch (e: unknown) {
      toast.error((e as Error).message);
    } finally {
      setLoading(false);
    }
  }, [statusFilter, search, page]);

  useEffect(() => { fetchRentals(); }, [fetchRentals]);

  const handleStatusChange = async () => {
    if (!actionTarget) return;
    setActing(true);
    try {
      await adminService.updateRentalStatus(actionTarget.rental.id, actionTarget.newStatus);
      toast.success(`Status diubah ke "${actionTarget.newStatus}".`);
      setActionTarget(null);
      fetchRentals();
    } catch (e: unknown) {
      toast.error((e as Error).message);
    } finally {
      setActing(false);
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="font-display text-2xl font-bold uppercase tracking-tight">Kelola Pesanan</h1>
        <p className="text-sm text-muted-foreground mt-1">
          {meta ? `${meta.total} pesanan` : 'Memuat...'}
        </p>
      </div>

      {/* Filters */}
      <div className="flex flex-col sm:flex-row gap-4">
        <Tabs value={statusFilter} onValueChange={({ value }) => { setStatusFilter(value); setPage(1); }} className="flex-1">
          <TabsList className="flex-wrap h-auto">
            {statusTabs.map((tab) => (
              <TabsTrigger key={tab.value} value={tab.value} className="text-xs">
                {tab.label}
              </TabsTrigger>
            ))}
          </TabsList>
        </Tabs>
        <div className="relative max-w-xs">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input
            placeholder="Cari invoice/customer..."
            value={search}
            onChange={(e) => { setSearch(e.target.value); setPage(1); }}
            className="pl-10"
          />
        </div>
      </div>

      {/* Table */}
      <Card>
        <CardContent className="p-0">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Invoice</TableHead>
                <TableHead>Customer</TableHead>
                <TableHead>Tanggal Sewa</TableHead>
                <TableHead>Total</TableHead>
                <TableHead>Denda</TableHead>
                <TableHead>Status</TableHead>
                <TableHead className="text-right">Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {loading ? (
                [...Array(5)].map((_, i) => (
                  <TableRow key={i}>
                    {[...Array(7)].map((_, j) => (
                      <TableCell key={j}><Skeleton className="h-4 w-full" /></TableCell>
                    ))}
                  </TableRow>
                ))
              ) : rentals.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={7} className="text-center py-12 text-muted-foreground">
                    Tidak ada pesanan ditemukan.
                  </TableCell>
                </TableRow>
              ) : (
                rentals.map((rental) => (
                  <TableRow key={rental.id}>
                    <TableCell className="font-mono text-sm">{rental.invoice_no}</TableCell>
                    <TableCell>{rental.user?.name || '-'}</TableCell>
                    <TableCell className="text-sm">
                      {formatDate(rental.start_date)} — {formatDate(rental.end_date)}
                    </TableCell>
                    <TableCell>{formatRupiah(rental.total_price)}</TableCell>
                    <TableCell>
                      {rental.fine_amount > 0 ? (
                        <span className="text-destructive font-semibold">{formatRupiah(rental.fine_amount)}</span>
                      ) : (
                        <span className="text-muted-foreground">—</span>
                      )}
                    </TableCell>
                    <TableCell>
                      <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${statusColors[rental.status] || ''}`}>
                        {rental.status}
                      </span>
                    </TableCell>
                    <TableCell className="text-right">
                      <div className="flex items-center justify-end gap-1">
                        <Button variant="ghost" size="sm" onClick={() => setDetailRental(rental)} title="Detail">
                          <Eye className="h-4 w-4" />
                        </Button>
                        {statusActions[rental.status]?.map((action) => (
                          <Button
                            key={action.target}
                            variant="ghost"
                            size="sm"
                            className={action.variant === 'destructive' ? 'text-destructive hover:text-destructive' : 'text-brand-orange hover:text-brand-orange'}
                            onClick={() => setActionTarget({ rental, newStatus: action.target })}
                            title={action.label}
                          >
                            <action.icon className="h-4 w-4" />
                          </Button>
                        ))}
                      </div>
                    </TableCell>
                  </TableRow>
                ))
              )}
            </TableBody>
          </Table>
        </CardContent>
      </Card>

      {/* Pagination */}
      {meta && meta.last_page > 1 && (
        <div className="flex items-center justify-center gap-2">
          <Button variant="outline" size="sm" disabled={page <= 1} onClick={() => setPage((p) => p - 1)}>
            Sebelumnya
          </Button>
          <span className="text-sm text-muted-foreground">
            Hal {meta.current_page} dari {meta.last_page}
          </span>
          <Button variant="outline" size="sm" disabled={page >= meta.last_page} onClick={() => setPage((p) => p + 1)}>
            Selanjutnya
          </Button>
        </div>
      )}

      {/* ─── Status Change Confirmation ─── */}
      <AlertDialog open={!!actionTarget} onOpenChange={({ open }) => { if (!open) setActionTarget(null); }}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Ubah Status Pesanan?</AlertDialogTitle>
            <AlertDialogDescription>
              Invoice <strong>{actionTarget?.rental.invoice_no}</strong> akan diubah dari
              <strong> {actionTarget?.rental.status}</strong> menjadi
              <strong> {actionTarget?.newStatus}</strong>.
              {actionTarget?.newStatus === 'canceled' && ' Stok akan dikembalikan.'}
              {actionTarget?.newStatus === 'returned' && ' Stok akan dikembalikan.'}
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <Button variant="outline" onClick={() => setActionTarget(null)}>Batal</Button>
            <Button onClick={handleStatusChange} disabled={acting} className="bg-brand-orange hover:bg-brand-orange/90 text-white">
              {acting ? 'Memproses...' : 'Konfirmasi'}
            </Button>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>

      {/* ─── Detail Dialog ─── */}
      <Dialog open={!!detailRental} onOpenChange={({ open }) => { if (!open) setDetailRental(null); }}>
        <DialogContent size="lg">
          <DialogHeader title={`Detail Pesanan — ${detailRental?.invoice_no || ''}`} />
          <DialogBody>
            {detailRental && (
              <div className="space-y-4">
                <div className="grid grid-cols-2 gap-3 text-sm">
                  <div>
                    <span className="text-muted-foreground">Customer</span>
                    <p className="font-medium">{detailRental.user?.name || '-'}</p>
                  </div>
                  <div>
                    <span className="text-muted-foreground">Status</span>
                    <p>
                      <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${statusColors[detailRental.status] || ''}`}>
                        {detailRental.status}
                      </span>
                    </p>
                  </div>
                  <div>
                    <span className="text-muted-foreground">Tanggal Mulai</span>
                    <p className="font-medium">{formatDate(detailRental.start_date)}</p>
                  </div>
                  <div>
                    <span className="text-muted-foreground">Tanggal Selesai</span>
                    <p className="font-medium">{formatDate(detailRental.end_date)}</p>
                  </div>
                  <div>
                    <span className="text-muted-foreground">Total Harga</span>
                    <p className="font-medium">{formatRupiah(detailRental.total_price)}</p>
                  </div>
                  <div>
                    <span className="text-muted-foreground">Denda</span>
                    <p className={`font-medium ${detailRental.fine_amount > 0 ? 'text-destructive' : ''}`}>
                      {detailRental.fine_amount > 0 ? formatRupiah(detailRental.fine_amount) : '—'}
                    </p>
                  </div>
                </div>

                {/* Items */}
                {detailRental.items && detailRental.items.length > 0 && (
                  <div>
                    <h4 className="text-sm font-medium mb-2">Item Pesanan</h4>
                    <div className="space-y-2">
                      {detailRental.items.map((item) => (
                        <div key={item.id} className="flex items-center justify-between p-2 bg-muted/50 rounded-md text-sm">
                          <span>{item.product?.name || `Produk #${item.product_id}`}</span>
                          <span className="text-muted-foreground">
                            {item.quantity}x @ {formatRupiah(item.price_at_rental)}
                          </span>
                        </div>
                      ))}
                    </div>
                  </div>
                )}

                {/* Payment info */}
                {detailRental.payment && (
                  <div>
                    <h4 className="text-sm font-medium mb-2">Pembayaran</h4>
                    <div className="grid grid-cols-2 gap-2 text-sm p-3 bg-muted/50 rounded-md">
                      <div>
                        <span className="text-muted-foreground">Metode</span>
                        <p className="font-medium uppercase">{detailRental.payment.payment_method}</p>
                      </div>
                      <div>
                        <span className="text-muted-foreground">Status</span>
                        <p>
                          <Badge variant={detailRental.payment.status === 'verified' ? 'default' : 'secondary'}>
                            {detailRental.payment.status}
                          </Badge>
                        </p>
                      </div>
                    </div>
                  </div>
                )}
              </div>
            )}
          </DialogBody>
        </DialogContent>
      </Dialog>
    </div>
  );
}
