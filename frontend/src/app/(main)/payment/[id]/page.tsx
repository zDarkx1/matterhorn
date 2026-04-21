'use client';

import { use, useEffect, useState, useRef, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import { Suspense } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { checkoutService } from '@/services/checkout.service';
import { formatRupiah } from '@/utils/format';
import { Button } from '@/components/ui/button';
import type { Rental } from '@/types';
import {
  CheckCircle, Copy, QrCode, Banknote, ArrowLeft,
  Clock, Package, AlertTriangle, Loader2, RefreshCw,
} from 'lucide-react';
import { toast } from 'sonner';

const STATUS_MAP: Record<string, { label: string; color: string }> = {
  booked:   { label: 'Dipesan',       color: 'bg-blue-100 text-blue-700' },
  active:   { label: 'Aktif',         color: 'bg-green-100 text-green-700' },
  returned: { label: 'Dikembalikan',  color: 'bg-gray-100 text-gray-700' },
  canceled: { label: 'Dibatalkan',    color: 'bg-red-100 text-red-700' },
  overdue:  { label: 'Terlambat',     color: 'bg-amber-100 text-amber-700' },
};

const PAYMENT_STATUS_MAP: Record<string, { label: string; color: string }> = {
  pending:  { label: 'Menunggu Pembayaran', color: 'bg-amber-100 text-amber-700' },
  verified: { label: 'Sudah Dibayar',       color: 'bg-green-100 text-green-700' },
};

function formatDateLocal(iso: string | null | undefined) {
  if (!iso) return '-';
  return new Date(iso).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric',
  });
}

function formatTimeLocal(iso: string | null | undefined) {
  if (!iso) return '';
  return new Date(iso).toLocaleTimeString('id-ID', {
    hour: '2-digit', minute: '2-digit',
  });
}

function PaymentContent({ rentalId }: { rentalId: number }) {
  const router = useRouter();
  const [rental, setRental] = useState<Rental | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [paymentVerified, setPaymentVerified] = useState(false);
  const [polling, setPolling] = useState(false);
  const pollingRef = useRef<ReturnType<typeof setInterval> | null>(null);

  const fetchRental = useCallback(async () => {
    try {
      const data = await checkoutService.getRentalDetail(rentalId);
      setRental(data);
      if (data.payment?.status === 'verified') {
        setPaymentVerified(true);
      }
      setError(null);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Gagal memuat data rental.');
    } finally {
      setLoading(false);
    }
  }, [rentalId]);

  // Initial load
  useEffect(() => {
    fetchRental();
  }, [fetchRental]);

  // Auto-poll for QRIS payments
  useEffect(() => {
    if (!rental || paymentVerified) return;
    if (rental.payment?.payment_method !== 'qris' || rental.payment?.status === 'verified') return;

    setPolling(true);
    pollingRef.current = setInterval(async () => {
      try {
        const result = await checkoutService.checkPaymentStatus(rentalId);
        if (result.payment_status === 'verified') {
          setPaymentVerified(true);
          setPolling(false);
          if (pollingRef.current) clearInterval(pollingRef.current);
          // Refresh rental data
          fetchRental();
          toast.success('Pembayaran QRIS berhasil diverifikasi!');
        }
      } catch {
        // Silently ignore polling errors
      }
    }, 5000);

    return () => {
      if (pollingRef.current) clearInterval(pollingRef.current);
    };
  }, [rental, paymentVerified, rentalId, fetchRental]);

  const handleCopyInvoice = () => {
    if (!rental) return;
    navigator.clipboard.writeText(rental.invoice_no);
    toast.success('Nomor invoice disalin!');
  };

  const handleManualCheck = async () => {
    if (!rental) return;
    setPolling(true);
    try {
      const result = await checkoutService.checkPaymentStatus(rentalId);
      if (result.payment_status === 'verified') {
        setPaymentVerified(true);
        fetchRental();
        toast.success('Pembayaran berhasil diverifikasi!');
      } else {
        toast.info('Pembayaran belum diterima. Tetap menunggu...');
      }
    } catch {
      toast.error('Gagal mengecek status pembayaran.');
    } finally {
      setPolling(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <Loader2 className="w-8 h-8 animate-spin text-brand-orange" />
      </div>
    );
  }

  if (error || !rental) {
    return (
      <div className="min-h-screen flex flex-col items-center justify-center gap-4 px-4">
        <AlertTriangle className="w-12 h-12 text-red-400" />
        <p className="text-gray-600">{error || 'Data tidak ditemukan.'}</p>
        <Link href="/rentals">
          <Button variant="outline">Lihat Riwayat Sewa</Button>
        </Link>
      </div>
    );
  }

  const payment = rental.payment;
  const isQris = payment?.payment_method === 'qris';
  const isPaid = paymentVerified || payment?.status === 'verified';
  const rentalStatus = STATUS_MAP[rental.status] || { label: rental.status, color: 'bg-gray-100 text-gray-700' };
  const payStatus = PAYMENT_STATUS_MAP[isPaid ? 'verified' : 'pending'];

  return (
    <section className="py-8 bg-gray-50 min-h-screen">
      <div className="max-w-lg mx-auto px-4">
        {/* Header */}
        <div className="text-center mb-8">
          <div className={`w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 ${isPaid ? 'bg-green-100' : 'bg-amber-100'}`}>
            {isPaid ? (
              <CheckCircle className="w-8 h-8 text-green-600" />
            ) : (
              <Clock className="w-8 h-8 text-amber-600" />
            )}
          </div>
          <h1 className="font-display font-bold text-2xl uppercase mb-2">
            {isPaid ? 'Pembayaran Berhasil' : 'Menunggu Pembayaran'}
          </h1>
          <p className="text-sm text-muted-foreground">
            {isPaid ? 'Pesanan Anda telah dikonfirmasi.' : 'Silakan lakukan pembayaran sesuai metode yang dipilih.'}
          </p>
        </div>

        {/* Invoice Card */}
        <div className="bg-white border border-gray-200 rounded-lg overflow-hidden mb-6">
          {/* Invoice Info */}
          <div className="p-6 border-b border-gray-100">
            <div className="flex items-center justify-between mb-3">
              <span className="text-xs text-muted-foreground uppercase tracking-wide">No. Invoice</span>
              <button onClick={handleCopyInvoice} className="flex items-center gap-1 text-brand-orange text-xs font-bold hover:underline">
                <Copy className="w-3 h-3" /> Salin
              </button>
            </div>
            <p className="font-mono font-bold text-lg mb-3">{rental.invoice_no}</p>
            <div className="flex items-center gap-2 flex-wrap">
              <span className={`text-xs font-bold px-2 py-1 rounded ${rentalStatus.color}`}>{rentalStatus.label}</span>
              <span className={`text-xs font-bold px-2 py-1 rounded ${payStatus.color}`}>{payStatus.label}</span>
            </div>
          </div>

          {/* Rental Period */}
          <div className="p-6 border-b border-gray-100 bg-gray-50/50">
            <span className="text-xs text-muted-foreground uppercase tracking-wide block mb-2">Periode Sewa</span>
            <div className="flex items-center gap-2 text-sm">
              <span className="font-medium">{formatDateLocal(rental.start_date)}</span>
              <span className="text-gray-400">&rarr;</span>
              <span className="font-medium">{formatDateLocal(rental.end_date)}</span>
            </div>
          </div>

          {/* Items */}
          <div className="p-6 border-b border-gray-100">
            <span className="text-xs text-muted-foreground uppercase tracking-wide block mb-3">Item Sewa</span>
            <div className="space-y-3">
              {rental.items?.map((item) => (
                <div key={item.id} className="flex items-center gap-3">
                  <div className="w-10 h-10 bg-gray-100 rounded relative flex-shrink-0 overflow-hidden">
                    {item.product?.image_url ? (
                      <Image
                        src={item.product.image_url}
                        alt={item.product.name || ''}
                        fill
                        className="object-cover"
                        sizes="40px"
                      />
                    ) : (
                      <Package className="w-5 h-5 text-gray-400 absolute inset-0 m-auto" />
                    )}
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium truncate">{item.product?.name || `Produk #${item.product_id}`}</p>
                    <p className="text-xs text-muted-foreground">x{item.quantity} &middot; {formatRupiah(item.price_at_rental)}/hari</p>
                  </div>
                  <span className="font-bold text-sm">{formatRupiah(item.price_at_rental * item.quantity)}</span>
                </div>
              ))}
            </div>
          </div>

          {/* Amount */}
          <div className="p-6 border-b border-gray-100 bg-gray-50">
            <span className="text-xs text-muted-foreground uppercase tracking-wide block mb-1">Total Pembayaran</span>
            <p className="font-display font-bold text-3xl text-brand-orange">{formatRupiah(rental.total_price)}</p>
            {rental.fine_amount > 0 && (
              <p className="text-sm text-red-600 mt-1">Denda: {formatRupiah(rental.fine_amount)}</p>
            )}
          </div>

          {/* Payment Method */}
          <div className="p-6">
            <span className="text-xs text-muted-foreground uppercase tracking-wide block mb-3">Metode Pembayaran</span>

            {isQris ? (
              <div>
                <div className="flex items-center gap-3 mb-4">
                  <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <QrCode className="w-5 h-5 text-blue-600" />
                  </div>
                  <div>
                    <p className="font-bold text-sm">QRIS</p>
                    <p className="text-xs text-muted-foreground">Scan QR Code untuk pembayaran</p>
                  </div>
                </div>

                {isPaid ? (
                  <div className="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                    <CheckCircle className="w-12 h-12 text-green-500 mx-auto mb-3" />
                    <p className="font-bold text-green-800">Pembayaran Diterima</p>
                    <p className="text-sm text-green-600 mt-1">Silakan datang ke toko untuk mengambil barang.</p>
                  </div>
                ) : (
                  <>
                    {/* QR Code Display */}
                    {payment?.qris_url ? (
                      <div className="bg-white border-2 border-gray-200 rounded-lg p-4 flex flex-col items-center">
                        <img
                          src={payment.qris_url}
                          alt="QRIS QR Code"
                          className="w-64 h-64 object-contain mb-3"
                        />
                        <p className="text-xs text-muted-foreground text-center">Scan dengan aplikasi e-wallet atau mobile banking</p>
                      </div>
                    ) : (
                      <div className="bg-white border-2 border-dashed border-gray-200 rounded-lg p-8 flex flex-col items-center justify-center">
                        <QrCode className="w-32 h-32 text-gray-300 mb-4" />
                        <p className="text-sm text-muted-foreground text-center">QR Code sedang dimuat...</p>
                      </div>
                    )}

                    {/* Polling indicator */}
                    <div className="mt-4 flex items-center justify-between">
                      <div className="flex items-center gap-2 text-amber-600 text-xs">
                        {polling ? (
                          <Loader2 className="w-4 h-4 animate-spin" />
                        ) : (
                          <Clock className="w-4 h-4" />
                        )}
                        <span>
                          {payment?.expired_at
                            ? `Berlaku hingga ${formatDateLocal(payment.expired_at)} ${formatTimeLocal(payment.expired_at)}`
                            : 'Menunggu konfirmasi pembayaran...'}
                        </span>
                      </div>
                      <button
                        onClick={handleManualCheck}
                        className="flex items-center gap-1 text-brand-orange text-xs font-bold hover:underline"
                      >
                        <RefreshCw className="w-3 h-3" /> Cek Status
                      </button>
                    </div>
                  </>
                )}
              </div>
            ) : (
              <div>
                <div className="flex items-center gap-3 mb-4">
                  <div className="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <Banknote className="w-5 h-5 text-green-600" />
                  </div>
                  <div>
                    <p className="font-bold text-sm">Bayar di Toko (Cash)</p>
                    <p className="text-xs text-muted-foreground">Bayar saat pengambilan barang</p>
                  </div>
                </div>
                <div className="bg-green-50 border border-green-200 rounded-lg p-4">
                  <h4 className="font-bold text-sm text-green-800 mb-2">Instruksi Pembayaran</h4>
                  <ol className="text-sm text-green-700 space-y-2 list-decimal list-inside">
                    <li>Datang ke toko Matterhorn di jam operasional</li>
                    <li>Tunjukkan nomor invoice: <span className="font-mono font-bold">{rental.invoice_no}</span></li>
                    <li>Lakukan pembayaran secara tunai sebesar <span className="font-bold">{formatRupiah(rental.total_price)}</span></li>
                    <li>Terima barang dan bukti sewa</li>
                  </ol>
                </div>
              </div>
            )}
          </div>
        </div>

        {/* Actions */}
        <div className="space-y-3">
          <Link href="/rentals" className="block">
            <Button variant="outline" className="w-full" size="lg">
              <Package className="w-4 h-4 mr-2" /> Lihat Riwayat Sewa
            </Button>
          </Link>
          <Link href="/" className="block">
            <Button variant="outline" className="w-full" size="lg">
              <ArrowLeft className="w-4 h-4 mr-2" /> Kembali ke Beranda
            </Button>
          </Link>
        </div>
      </div>
    </section>
  );
}

export default function PaymentPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = use(params);

  return (
    <Suspense fallback={<div className="min-h-screen flex items-center justify-center"><Loader2 className="w-8 h-8 animate-spin text-brand-orange" /></div>}>
      <PaymentContent rentalId={Number(id)} />
    </Suspense>
  );
}
