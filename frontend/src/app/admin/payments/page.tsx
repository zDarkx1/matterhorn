'use client';

import { useState, useEffect, useCallback } from 'react';
import { adminService } from '@/services/admin.service';
import { formatRupiah, formatDate } from '@/utils/format';
import type { Payment } from '@/types/rental.types';
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
import { Search, CheckCircle2 } from 'lucide-react';
import { toast } from 'sonner';

interface PaymentWithRental extends Payment {
  rental?: {
    id: number;
    invoice_no: string;
    user?: { name: string };
  };
}

export default function AdminPaymentsPage() {
  const [payments, setPayments] = useState<PaymentWithRental[]>([]);
  const [meta, setMeta] = useState<PaginationMeta | null>(null);
  const [loading, setLoading] = useState(true);
  const [statusFilter, setStatusFilter] = useState('');
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [verifyTarget, setVerifyTarget] = useState<PaymentWithRental | null>(null);
  const [verifying, setVerifying] = useState(false);

  const fetchPayments = useCallback(async () => {
    setLoading(true);
    try {
      const res = await adminService.getPayments({
        status: statusFilter || undefined,
        search: search || undefined,
        page,
        per_page: 10,
      });
      setPayments(res.data as unknown as PaymentWithRental[]);
      setMeta(res.meta);
    } catch (e: unknown) {
      toast.error((e as Error).message);
    } finally {
      setLoading(false);
    }
  }, [statusFilter, search, page]);

  useEffect(() => { fetchPayments(); }, [fetchPayments]);

  const handleVerify = async () => {
    if (!verifyTarget) return;
    setVerifying(true);
    try {
      await adminService.verifyPayment(verifyTarget.id);
      toast.success('Pembayaran berhasil diverifikasi.');
      setVerifyTarget(null);
      fetchPayments();
    } catch (e: unknown) {
      toast.error((e as Error).message);
    } finally {
      setVerifying(false);
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="font-display text-2xl font-bold uppercase tracking-tight">Verifikasi Pembayaran</h1>
        <p className="text-sm text-muted-foreground mt-1">
          {meta ? `${meta.total} pembayaran` : 'Memuat...'}
        </p>
      </div>

      {/* Filters */}
      <div className="flex flex-col sm:flex-row gap-4">
        <Tabs value={statusFilter} onValueChange={({ value }) => { setStatusFilter(value); setPage(1); }}>
          <TabsList>
            <TabsTrigger value="">Semua</TabsTrigger>
            <TabsTrigger value="pending">Pending</TabsTrigger>
            <TabsTrigger value="verified">Verified</TabsTrigger>
          </TabsList>
        </Tabs>
        <div className="relative max-w-xs">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input
            placeholder="Cari invoice..."
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
                <TableHead>Jumlah</TableHead>
                <TableHead>Metode</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Tanggal</TableHead>
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
              ) : payments.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={7} className="text-center py-12 text-muted-foreground">
                    Tidak ada pembayaran ditemukan.
                  </TableCell>
                </TableRow>
              ) : (
                payments.map((payment) => (
                  <TableRow key={payment.id}>
                    <TableCell className="font-mono text-sm">{payment.rental?.invoice_no || '-'}</TableCell>
                    <TableCell>{payment.rental?.user?.name || '-'}</TableCell>
                    <TableCell className="font-medium">{formatRupiah(payment.amount)}</TableCell>
                    <TableCell>
                      <Badge variant="outline" className="text-xs uppercase">{payment.payment_method}</Badge>
                    </TableCell>
                    <TableCell>
                      <Badge
                        variant={payment.status === 'verified' ? 'default' : 'secondary'}
                        className={payment.status === 'verified' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'}
                      >
                        {payment.status}
                      </Badge>
                    </TableCell>
                    <TableCell className="text-sm text-muted-foreground">{formatDate(payment.created_at)}</TableCell>
                    <TableCell className="text-right">
                      {payment.status === 'pending' && (
                        <Button
                          variant="ghost"
                          size="sm"
                          className="text-green-600 hover:text-green-700"
                          onClick={() => setVerifyTarget(payment)}
                          title="Verifikasi"
                        >
                          <CheckCircle2 className="h-4 w-4 mr-1" />
                          Verify
                        </Button>
                      )}
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

      {/* Verify Confirmation */}
      <AlertDialog open={!!verifyTarget} onOpenChange={({ open }) => { if (!open) setVerifyTarget(null); }}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Verifikasi Pembayaran?</AlertDialogTitle>
            <AlertDialogDescription>
              Pembayaran untuk invoice <strong>{verifyTarget?.rental?.invoice_no}</strong> sebesar{' '}
              <strong>{verifyTarget ? formatRupiah(verifyTarget.amount) : ''}</strong> akan diverifikasi.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <Button variant="outline" onClick={() => setVerifyTarget(null)}>Batal</Button>
            <Button onClick={handleVerify} disabled={verifying} className="bg-green-600 hover:bg-green-700 text-white">
              {verifying ? 'Memverifikasi...' : 'Verifikasi'}
            </Button>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  );
}
