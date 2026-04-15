'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { useRouter } from 'next/navigation';
import { useAuthStore } from '@/stores/useAuthStore';
import { useCartStore } from '@/stores/useCartStore';
import { storeService, type StoreStatus } from '@/services/store.service';
import { formatRupiah } from '@/utils/format';
import { apiFetch } from '@/lib/api';
import { toast } from 'sonner';
import { PaymentMethodModal, type PaymentMethod } from '@/components/modals/PaymentMethodModal';
import type { UserAddress } from '@/types';
import { MapPin, Clock, ShieldCheck, AlertTriangle, ChevronRight } from 'lucide-react';
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

  const canCheckout = storeStatus?.is_open !== false && defaultAddress !== null;

  const handlePaymentSelect = async (method: PaymentMethod) => {
    setSubmitting(true);
    try {
      // For now, simulate order creation
      toast.success('Pesanan berhasil dibuat!', {
        description: `Metode pembayaran: ${method === 'cash' ? 'Bayar di Toko' : 'QRIS'}`,
      });

      // Navigate to payment page (simulated invoice)
      const invoiceId = `INV-${Date.now()}`;
      clearCart();
      router.push(`/payment/${invoiceId}?method=${method}&amount=${totalPrice}`);
    } catch {
      toast.error('Gagal membuat pesanan.');
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
                    <p className="text-xs text-muted-foreground">x{item.quantity}</p>
                  </div>
                  <span className="font-bold text-sm text-nowrap">{formatRupiah((item.product?.price_24h || 0) * item.quantity)}</span>
                </div>
              ))}
              <div className="border-t border-gray-200 pt-3 flex justify-between text-lg">
                <span className="font-display font-bold uppercase">Total</span>
                <span className="font-bold text-brand-orange">{formatRupiah(totalPrice)}</span>
              </div>
            </div>
          </div>

          {/* Store Status */}
          <div className={`border p-4 rounded-lg flex items-center gap-3 ${storeStatus?.is_open ? 'border-green-200 bg-green-50' : 'border-amber-200 bg-amber-50'}`}>
            <Clock className="w-5 h-5 flex-shrink-0" />
            <div>
              <p className="font-bold text-sm">Pengambilan & Pengembalian di Toko</p>
              <p className="text-sm text-gray-600">
                {storeStatus ? storeStatus.message : 'Memuat status toko...'}
                {storeStatus && ` (${storeStatus.open_time} - ${storeStatus.close_time} WIB)`}
              </p>
            </div>
          </div>

          {/* Warnings */}
          {!storeStatus?.is_open && storeStatus !== null && (
            <div className="border border-red-200 bg-red-50 p-4 rounded-lg flex items-center gap-3 text-red-700">
              <AlertTriangle className="w-5 h-5 flex-shrink-0" />
              <p className="text-sm">Checkout hanya bisa dilakukan saat toko buka.</p>
            </div>
          )}

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
