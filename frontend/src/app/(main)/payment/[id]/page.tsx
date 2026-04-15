'use client';

import { use } from 'react';
import { useSearchParams } from 'next/navigation';
import { Suspense } from 'react';
import Link from 'next/link';
import { formatRupiah } from '@/utils/format';
import { Button } from '@/components/ui/button';
import { CheckCircle, Copy, QrCode, Banknote, ArrowLeft, Clock } from 'lucide-react';
import { toast } from 'sonner';

function PaymentContent({ invoiceId }: { invoiceId: string }) {
  const searchParams = useSearchParams();
  const method = searchParams.get('method') || 'cash';
  const amount = Number(searchParams.get('amount') || 0);

  const handleCopyInvoice = () => {
    navigator.clipboard.writeText(invoiceId);
    toast.success('Nomor invoice disalin!');
  };

  return (
    <section className="py-8 bg-gray-50 min-h-screen">
      <div className="max-w-lg mx-auto px-4">
        {/* Success Header */}
        <div className="text-center mb-8">
          <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <CheckCircle className="w-8 h-8 text-green-600" />
          </div>
          <h1 className="font-display font-bold text-2xl uppercase mb-2">Pesanan Dibuat</h1>
          <p className="text-sm text-muted-foreground">Silakan lakukan pembayaran sesuai metode yang dipilih.</p>
        </div>

        {/* Invoice Card */}
        <div className="bg-white border border-gray-200 rounded-lg overflow-hidden mb-6">
          {/* Invoice Info */}
          <div className="p-6 border-b border-gray-100">
            <div className="flex items-center justify-between mb-4">
              <span className="text-xs text-muted-foreground uppercase tracking-wide">No. Invoice</span>
              <button onClick={handleCopyInvoice} className="flex items-center gap-1 text-brand-orange text-xs font-bold hover:underline">
                <Copy className="w-3 h-3" /> Salin
              </button>
            </div>
            <p className="font-mono font-bold text-lg">{invoiceId}</p>
          </div>

          {/* Amount */}
          <div className="p-6 border-b border-gray-100 bg-gray-50">
            <span className="text-xs text-muted-foreground uppercase tracking-wide block mb-1">Total Pembayaran</span>
            <p className="font-display font-bold text-3xl text-brand-orange">{formatRupiah(amount)}</p>
          </div>

          {/* Payment Method */}
          <div className="p-6">
            <span className="text-xs text-muted-foreground uppercase tracking-wide block mb-3">Metode Pembayaran</span>

            {method === 'qris' ? (
              <div>
                <div className="flex items-center gap-3 mb-4">
                  <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <QrCode className="w-5 h-5 text-blue-600" />
                  </div>
                  <div>
                    <p className="font-bold text-sm">QRIS</p>
                    <p className="text-xs text-muted-foreground">Scan QR Code di bawah</p>
                  </div>
                </div>
                {/* Placeholder QR */}
                <div className="bg-white border-2 border-dashed border-gray-200 rounded-lg p-8 flex flex-col items-center justify-center">
                  <QrCode className="w-32 h-32 text-gray-300 mb-4" />
                  <p className="text-sm text-muted-foreground text-center">QR Code akan ditampilkan saat konfirmasi admin</p>
                </div>
                <div className="mt-4 flex items-center gap-2 text-amber-600 text-xs">
                  <Clock className="w-4 h-4" />
                  <span>Berlaku selama 24 jam sejak pesanan dibuat</span>
                </div>
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
                    <li>Tunjukkan nomor invoice: <span className="font-mono font-bold">{invoiceId}</span></li>
                    <li>Lakukan pembayaran secara tunai</li>
                    <li>Terima barang dan bukti sewa</li>
                  </ol>
                </div>
              </div>
            )}
          </div>
        </div>

        {/* Actions */}
        <div className="space-y-3">
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
    <Suspense fallback={<div className="min-h-screen flex items-center justify-center">Memuat...</div>}>
      <PaymentContent invoiceId={id} />
    </Suspense>
  );
}
