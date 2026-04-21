'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { useRouter } from 'next/navigation';
import { useAuthStore } from '@/stores/useAuthStore';
import { useCartStore } from '@/stores/useCartStore';
import { storeService, type StoreStatus } from '@/services/store.service';
import { checkoutService } from '@/services/checkout.service';
import { formatRupiah } from '@/utils/format';
import { apiFetch } from '@/lib/api';
import { toast } from 'sonner';
import { PaymentMethodModal, type PaymentMethod } from '@/components/modals/PaymentMethodModal';
import type { UserAddress } from '@/types';
import { MapPin, Clock, ShieldCheck, AlertTriangle, ChevronRight, CalendarDays } from 'lucide-react';
import { Button } from '@/components/ui/button';

export default function CheckoutPage() {
  const router = useRouter();
  const { user } = useAuthStore();
  const { items, totalPrice, totalItems, fetchCart, clearCart } = useCartStore();
  const [storeStatus, setStoreStatus] = useState<StoreStatus | null>(null);
  const [addresses, setAddresses] = useState<UserAddress[]>([]);
  const [defaultAddress, setDefaultAddress] = useState<UserAddress | null>(null);
  const [loadingAddresses, setLoadingAddresses] = useState(true);
  const [paymentModalOpen, setPaymentModalOpen] = useState(false);
  const [submitting, setSubmitting] = useState(false);

  // Rental date state
  const today = new Date();
  const tomorrow = new Date(today);
  tomorrow.setDate(tomorrow.getDate() + 1);
  const formatDate = (d: Date) => d.toISOString().split('T')[0];

  const [startDate, setStartDate] = useState(formatDate(today));
  const [endDate, setEndDate] = useState(formatDate(tomorrow));

  // Calculate rental days
  const rentalDays = Math.max(1, Math.ceil((new Date(endDate).getTime() - new Date(startDate).getTime()) / (1000 * 60 * 60 * 24)));
  const estimatedTotal = totalPrice * rentalDays;

  useEffect(() => {
    if (user) {
      fetchCart();
      loadAddresses();
    }
    storeService.getStatus().then(setStoreStatus).catch(() => {});
  }, [user, fetchCart]);

  const loadAddresses = async () => {
    setLoadingAddresses(true);
    try {
      const res = await apiFetch<UserAddress[]>('/addresses');
      const addrs = res.data || [];
      setAddresses(addrs);
      setDefaultAddress(addrs.find((a) => a.is_default) || addrs[0] || null);
    } catch {
      setAddresses([]);
    } finally {
      setLoadingAddresses(false);
    }
  };

  if (!user) {
    return (
      <div className="max-w-4xl mx-auto px-4 py-20 text-center">
        <h2 className="font-display font-bold text-3xl uppercase mb-4">Login Diperlukan</h2>
        <p className="text-gray-500 mb-6">Silakan login untuk melanjutkan checkout.</p>
        <Link href="/login">
          <Button className="bg-brand-orange hover:bg-orange-700 text-white">Login</Button>
        </Link>
      </div>
    );
  }

  if (!items.length) {
    return (
      <div className="max-w-4xl mx-auto px-4 py-20 text-center">
        <h2 className="font-display font-bold text-3xl uppercase mb-4">Keranjang Kosong</h2>
        <p className="text-gray-500 mb-6">Tambahkan produk ke keranjang terlebih dahulu.</p>
        <Link href="/products">
          <Button className="bg-brand-orange hover:bg-orange-700 text-white">Jelajahi Produk</Button>
        </Link>
      </div>
    );
  }

  const canCheckout = defaultAddress !== null;

  const handlePaymentSelect = async (method: PaymentMethod) => {
    setSubmitting(true);
    try {
      const rental = await checkoutService.createOrder({
        payment_method: method,
        start_date: `${startDate} 09:00:00`,
        end_date: `${endDate} 09:00:00`,
      });

      toast.success('Pesanan berhasil dibuat!', {
        description: `Invoice: ${rental.invoice_no}`,
      });

      clearCart();
      router.push(`/payment/${rental.id}`);
    } catch (err) {
      toast.error(err instanceof Error ? err.message : 'Gagal membuat pesanan.');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <section className="py-8 bg-white min-h-screen">
      <div className="max-w-4xl mx-auto px-4">
        <h1 className="font-display font-bold text-3xl uppercase mb-8 flex items-center gap-3">
          <span className="w-1 h-8 bg-brand-orange block" />
          Checkout
        </h1>

        <div className="space-y-6">
          {/* Address Section */}
          <div className="border border-gray-200 p-6 rounded-lg">
            <h3 className="font-display font-bold uppercase text-sm mb-4 flex items-center gap-2">
              <ShieldCheck className="w-5 h-5 text-brand-orange" />
              Alamat Verifikasi Identitas
            </h3>
            <p className="text-sm text-gray-600 mb-4">
              Alamat digunakan untuk verifikasi identitas. Pengambilan barang dilakukan langsung di toko.
            </p>

            {loadingAddresses ? (
              <div className="bg-gray-50 p-4 rounded-lg animate-pulse">
                <div className="h-4 bg-gray-200 w-1/3 mb-2 rounded" />
                <div className="h-3 bg-gray-200 w-2/3 rounded" />
              </div>
            ) : defaultAddress ? (
              <div className="bg-gray-50 p-4 rounded-lg flex items-start gap-3">
                <MapPin className="w-5 h-5 text-brand-orange flex-shrink-0 mt-0.5" />
                <div className="flex-1">
                  <div className="flex items-center gap-2 mb-1">
                    <span className="font-bold text-sm">{defaultAddress.label}</span>
                    {defaultAddress.is_default && (
                      <span className="bg-brand-orange text-white text-[10px] px-1.5 py-0.5 font-bold rounded">Utama</span>
                    )}
                  </div>
                  <p className="text-sm">{defaultAddress.recipient_name} &bull; {defaultAddress.phone_number}</p>
                  <p className="text-sm text-gray-500">{defaultAddress.address_line}, {defaultAddress.city}, {defaultAddress.province} {defaultAddress.postal_code}</p>
                </div>
                <Link href="/profile" className="text-brand-orange hover:underline text-xs font-bold flex items-center gap-1 flex-shrink-0">
                  Ubah <ChevronRight className="w-3 h-3" />
                </Link>
              </div>
            ) : (
              <div className="bg-amber-50 border border-amber-200 p-4 rounded-lg">
                <p className="text-sm text-amber-800 mb-2 font-medium">Alamat belum diatur</p>
                <p className="text-xs text-amber-600 mb-3">Anda perlu menambahkan alamat sebagai verifikasi identitas sebelum checkout.</p>
                <Link href="/profile">
                  <Button size="sm" className="bg-brand-orange hover:bg-orange-700 text-white">
                    Tambah Alamat
                  </Button>
                </Link>
              </div>
            )}
          </div>

          {/* Rental Period */}
          <div className="border border-gray-200 p-6 rounded-lg">
            <h3 className="font-display font-bold uppercase text-sm mb-4 flex items-center gap-2">
              <CalendarDays className="w-5 h-5 text-brand-orange" />
              Periode Sewa
            </h3>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="text-xs font-bold uppercase text-gray-600 block mb-2">Tanggal Mulai</label>
                <input
                  type="date"
                  value={startDate}
                  min={formatDate(today)}
                  onChange={(e) => {
                    setStartDate(e.target.value);
                    // Auto-adjust end date if needed
                    if (e.target.value >= endDate) {
                      const next = new Date(e.target.value);
                      next.setDate(next.getDate() + 1);
                      setEndDate(formatDate(next));
                    }
                  }}
                  className="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-brand-orange focus:border-brand-orange outline-none transition"
                />
              </div>
              <div>
                <label className="text-xs font-bold uppercase text-gray-600 block mb-2">Tanggal Selesai</label>
                <input
                  type="date"
                  value={endDate}
                  min={startDate}
                  onChange={(e) => setEndDate(e.target.value)}
                  className="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-brand-orange focus:border-brand-orange outline-none transition"
                />
              </div>
            </div>
            <div className="mt-3 bg-orange-50 border border-orange-200 rounded-lg px-4 py-2">
              <p className="text-sm text-orange-800">
                Durasi sewa: <span className="font-bold">{rentalDays} hari</span>
              </p>
            </div>
          </div>

          {/* Order Summary */}
          <div className="border border-gray-200 p-6 rounded-lg">
            <h3 className="font-display font-bold uppercase text-sm mb-4">Ringkasan Pesanan</h3>
            <div className="space-y-3">
              {items.map((item) => (
                <div key={item.id} className="flex items-center gap-3 py-2 border-b border-gray-100 last:border-0">
                  <div className="w-12 h-12 bg-gray-100 rounded relative flex-shrink-0 overflow-hidden">
                    <Image
                      src={item.product?.image_url || 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=100&q=80'}
                      alt={item.product?.name || 'Product'}
                      fill
                      className="object-cover"
                      sizes="48px"
                    />
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium truncate">{item.product?.name}</p>
                    <p className="text-xs text-muted-foreground">x{item.quantity} &middot; {rentalDays} hari</p>
                  </div>
                  <span className="font-bold text-sm text-nowrap">{formatRupiah((item.product?.price_24h || 0) * item.quantity * rentalDays)}</span>
                </div>
              ))}
              <div className="border-t border-gray-200 pt-3">
                <div className="flex justify-between text-sm text-gray-500 mb-1">
                  <span>Subtotal ({totalItems} item x {rentalDays} hari)</span>
                  <span>{formatRupiah(estimatedTotal)}</span>
                </div>
                <div className="flex justify-between text-lg">
                  <span className="font-display font-bold uppercase">Total</span>
                  <span className="font-bold text-brand-orange">{formatRupiah(estimatedTotal)}</span>
                </div>
              </div>
            </div>
          </div>

          {/* Store Status */}
          <div className={`border p-4 rounded-lg flex items-center gap-3 ${
            storeStatus?.is_open
              ? 'border-green-200 bg-green-50'
              : 'border-amber-200 bg-amber-50'
          }`}>
            <Clock className={`w-5 h-5 flex-shrink-0 ${storeStatus?.is_open ? 'text-green-600' : 'text-amber-600'}`} />
            <div className="flex-1">
              <p className={`font-bold text-sm ${storeStatus?.is_open ? 'text-green-800' : 'text-amber-800'}`}>
                Pengambilan & Pengembalian di Toko
              </p>
              <p className={`text-sm ${storeStatus?.is_open ? 'text-green-600' : 'text-amber-600'}`}>
                {storeStatus ? storeStatus.message : 'Memuat status toko...'}
                {storeStatus && ` (${storeStatus.open_time} - ${storeStatus.close_time} WIB)`}
              </p>
            </div>
          </div>

          {!storeStatus?.is_open && storeStatus !== null && (
            <div className="border border-amber-300 bg-amber-50 p-4 rounded-lg flex items-start gap-3 text-amber-800">
              <AlertTriangle className="w-5 h-5 flex-shrink-0 mt-0.5 text-amber-500" />
              <div>
                <p className="text-sm font-medium">Anda memesan di luar jam operasional toko.</p>
                <p className="text-xs text-amber-600 mt-1">Pengambilan barang hanya bisa dilakukan saat toko buka (09:00 - 21:45 WIB).</p>
              </div>
            </div>
          )}

          {/* Warnings */}

          {!defaultAddress && !loadingAddresses && (
            <div className="border border-red-200 bg-red-50 p-4 rounded-lg flex items-center gap-3 text-red-700">
              <AlertTriangle className="w-5 h-5 flex-shrink-0" />
              <p className="text-sm">Lengkapi alamat di halaman profil sebelum melanjutkan.</p>
            </div>
          )}

          {/* CTA */}
          <Button
            disabled={!canCheckout || submitting}
            onClick={() => setPaymentModalOpen(true)}
            className="w-full bg-brand-orange hover:bg-orange-700 text-white font-display font-bold uppercase tracking-wider py-6 text-lg disabled:opacity-50"
            size="lg"
          >
            {submitting ? 'Memproses...' : `Pilih Pembayaran (${totalItems} item)`}
          </Button>
        </div>
      </div>

      {/* Payment Method Modal */}
      <PaymentMethodModal
        open={paymentModalOpen}
        onOpenChange={setPaymentModalOpen}
        onSelect={handlePaymentSelect}
      />
    </section>
  );
}
