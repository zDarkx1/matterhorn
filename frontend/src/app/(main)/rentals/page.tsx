'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { useRouter } from 'next/navigation';
import { useAuthStore } from '@/stores/useAuthStore';
import { checkoutService, type RentalListMeta } from '@/services/checkout.service';
import { formatRupiah } from '@/utils/format';
import { Button } from '@/components/ui/button';
import type { Rental, RentalStatus } from '@/types';
import {
  Package, ChevronRight, FileText, Filter,
  Loader2, CalendarDays, CreditCard,
} from 'lucide-react';

const STATUS_CONFIG: Record<RentalStatus, { label: string; color: string; bg: string }> = {
  booked:   { label: 'Dipesan',      color: 'text-blue-700',  bg: 'bg-blue-100' },
  active:   { label: 'Aktif',        color: 'text-green-700', bg: 'bg-green-100' },
  returned: { label: 'Dikembalikan', color: 'text-gray-700',  bg: 'bg-gray-100' },
  canceled: { label: 'Dibatalkan',   color: 'text-red-700',   bg: 'bg-red-100' },
  overdue:  { label: 'Terlambat',    color: 'text-amber-700', bg: 'bg-amber-100' },
};

const FILTER_OPTIONS: { value: string; label: string }[] = [
  { value: '',         label: 'Semua' },
  { value: 'booked',   label: 'Dipesan' },
  { value: 'active',   label: 'Aktif' },
  { value: 'returned', label: 'Dikembalikan' },
  { value: 'canceled', label: 'Dibatalkan' },
  { value: 'overdue',  label: 'Terlambat' },
];

function formatDateShort(iso: string | null | undefined) {
  if (!iso) return '-';
  return new Date(iso).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'short', year: 'numeric',
  });
}

export default function RentalsPage() {
  const router = useRouter();
  const { user } = useAuthStore();
  const [rentals, setRentals] = useState<Rental[]>([]);
  const [meta, setMeta] = useState<RentalListMeta | null>(null);
  const [loading, setLoading] = useState(true);
  const [statusFilter, setStatusFilter] = useState('');
  const [page, setPage] = useState(1);

  useEffect(() => {
    if (!user) {
      router.push('/login');
      return;
    }
    loadRentals();
  }, [user, statusFilter, page]);

  const loadRentals = async () => {
    setLoading(true);
    try {
      const result = await checkoutService.getMyRentals({
        status: statusFilter || undefined,
        page,
        per_page: 10,
      });
      setRentals(result.data);
      setMeta(result.meta);
    } catch {
      setRentals([]);
    } finally {
      setLoading(false);
    }
  };

  if (!user) return null;

  return (
    <section className="py-8 bg-white min-h-screen">
      <div className="max-w-4xl mx-auto px-4">
        <h1 className="font-display font-bold text-3xl uppercase mb-8 flex items-center gap-3">
          <span className="w-1 h-8 bg-brand-orange block" />
          Riwayat Sewa
        </h1>

        {/* Filter */}
        <div className="mb-6">
          <div className="flex items-center gap-2 mb-3">
            <Filter className="w-4 h-4 text-gray-500" />
            <span className="text-xs font-bold uppercase text-gray-600">Filter Status</span>
          </div>
          <div className="flex flex-wrap gap-2">
            {FILTER_OPTIONS.map((opt) => (
              <button
                key={opt.value}
                onClick={() => { setStatusFilter(opt.value); setPage(1); }}
                className={`px-4 py-2 text-sm font-medium rounded-lg border transition-all ${
                  statusFilter === opt.value
                    ? 'bg-brand-orange text-white border-brand-orange'
                    : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400'
                }`}
              >
                {opt.label}
              </button>
            ))}
          </div>
        </div>

        {/* Content */}
        {loading ? (
          <div className="flex items-center justify-center py-20">
            <Loader2 className="w-8 h-8 animate-spin text-brand-orange" />
          </div>
        ) : rentals.length === 0 ? (
          <div className="text-center py-20">
            <FileText className="w-16 h-16 text-gray-200 mx-auto mb-4" />
            <h3 className="font-display font-bold text-xl uppercase mb-2 text-gray-400">
              {statusFilter ? 'Tidak Ada Rental' : 'Belum Ada Riwayat Sewa'}
            </h3>
            <p className="text-gray-400 text-sm mb-6">
              {statusFilter
                ? `Tidak ada rental dengan status "${FILTER_OPTIONS.find(o => o.value === statusFilter)?.label}".`
                : 'Mulai sewa peralatan adventure pertama Anda!'}
            </p>
            {!statusFilter && (
              <Link href="/products">
                <Button className="bg-brand-orange hover:bg-orange-700 text-white">
                  Jelajahi Produk
                </Button>
              </Link>
            )}
          </div>
        ) : (
          <div className="space-y-4">
            {rentals.map((rental) => {
              const statusCfg = STATUS_CONFIG[rental.status] || STATUS_CONFIG.booked;
              const isPending = rental.payment?.status === 'pending';
              const isQris = rental.payment?.payment_method === 'qris';

              return (
                <Link
                  key={rental.id}
                  href={`/payment/${rental.id}`}
                  className="block border border-gray-200 rounded-lg hover:border-brand-orange hover:shadow-md transition-all group"
                >
                  <div className="p-5">
                    {/* Top Row */}
                    <div className="flex items-center justify-between mb-3">
                      <div className="flex items-center gap-3">
                        <span className="font-mono font-bold text-sm">{rental.invoice_no}</span>
                        <span className={`text-[11px] font-bold px-2 py-0.5 rounded ${statusCfg.bg} ${statusCfg.color}`}>
                          {statusCfg.label}
                        </span>
                        {isPending && (
                          <span className="text-[11px] font-bold px-2 py-0.5 rounded bg-amber-100 text-amber-700">
                            Belum Bayar
                          </span>
                        )}
                      </div>
                      <ChevronRight className="w-5 h-5 text-gray-300 group-hover:text-brand-orange transition-colors" />
                    </div>

                    {/* Items Preview */}
                    <div className="flex items-center gap-3 mb-3">
                      <div className="flex -space-x-2">
                        {rental.items?.slice(0, 3).map((item, idx) => (
                          <div key={item.id} className="w-10 h-10 bg-gray-100 rounded border-2 border-white relative overflow-hidden" style={{ zIndex: 3 - idx }}>
                            {item.product?.image_url ? (
                              <Image
                                src={item.product.image_url}
                                alt={item.product.name || ''}
                                fill
                                className="object-cover"
                                sizes="40px"
                              />
                            ) : (
                              <Package className="w-4 h-4 text-gray-400 absolute inset-0 m-auto" />
                            )}
                          </div>
                        ))}
                        {(rental.items?.length || 0) > 3 && (
                          <div className="w-10 h-10 bg-gray-200 rounded border-2 border-white flex items-center justify-center text-xs font-bold text-gray-500">
                            +{(rental.items?.length || 0) - 3}
                          </div>
                        )}
                      </div>
                      <div className="flex-1 min-w-0">
                        <p className="text-sm text-gray-600 truncate">
                          {rental.items?.map(i => i.product?.name || `#${i.product_id}`).join(', ')}
                        </p>
                      </div>
                    </div>

                    {/* Bottom Row */}
                    <div className="flex items-center justify-between text-sm">
                      <div className="flex items-center gap-4 text-gray-500">
                        <span className="flex items-center gap-1">
                          <CalendarDays className="w-3.5 h-3.5" />
                          {formatDateShort(rental.start_date)} - {formatDateShort(rental.end_date)}
                        </span>
                        <span className="flex items-center gap-1">
                          <CreditCard className="w-3.5 h-3.5" />
                          {isQris ? 'QRIS' : 'Cash'}
                        </span>
                      </div>
                      <span className="font-bold text-brand-orange">{formatRupiah(rental.total_price)}</span>
                    </div>
                  </div>
                </Link>
              );
            })}
          </div>
        )}

        {/* Pagination */}
        {meta && meta.last_page > 1 && (
          <div className="flex items-center justify-center gap-2 mt-8">
            <Button
              variant="outline"
              size="sm"
              disabled={page <= 1}
              onClick={() => setPage(p => p - 1)}
            >
              Sebelumnya
            </Button>
            <span className="text-sm text-gray-500 px-4">
              Halaman {meta.current_page} dari {meta.last_page}
            </span>
            <Button
              variant="outline"
              size="sm"
              disabled={page >= meta.last_page}
              onClick={() => setPage(p => p + 1)}
            >
              Selanjutnya
            </Button>
          </div>
        )}
      </div>
    </section>
  );
}
