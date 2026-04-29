'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { useCartStore } from '@/stores/useCartStore';
import { useAuthStore } from '@/stores/useAuthStore';
import { formatRupiah } from '@/utils/format';
import { toast } from 'sonner';
import { Minus, Plus, Trash2, ShoppingBag, ArrowRight, Check } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';

export default function CartPage() {
  const { items, totalItems, loading, fetchCart, updateItem, removeItem } = useCartStore();
  const { user } = useAuthStore();
  const [selectedIds, setSelectedIds] = useState<Set<string>>(new Set());

  useEffect(() => {
    if (user) fetchCart();
  }, [user, fetchCart]);

  // Auto-select all items when they load
  useEffect(() => {
    if (items.length > 0) {
      setSelectedIds(new Set(items.map((i) => i.id)));
    }
  }, [items]);

  const toggleItem = (id: string) => {
    setSelectedIds((prev) => {
      const next = new Set(prev);
      if (next.has(id)) next.delete(id);
      else next.add(id);
      return next;
    });
  };

  const toggleAll = () => {
    if (selectedIds.size === items.length) {
      setSelectedIds(new Set());
    } else {
      setSelectedIds(new Set(items.map((i) => i.id)));
    }
  };

  const selectedItems = items.filter((i) => selectedIds.has(i.id));
  const selectedTotal = selectedItems.reduce(
    (sum, i) => sum + (i.product?.price_24h || 0) * i.quantity,
    0
  );
  const selectedCount = selectedItems.reduce((sum, i) => sum + i.quantity, 0);

  if (!user) {
    return (
      <div className="max-w-4xl mx-auto px-4 py-20 text-center">
        <ShoppingBag className="w-16 h-16 text-gray-300 mx-auto mb-6" />
        <h2 className="font-display font-bold text-3xl uppercase mb-4">Login Diperlukan</h2>
        <p className="text-gray-500 mb-8">Silakan login untuk melihat keranjang Anda.</p>
        <Link href="/login">
          <Button className="bg-brand-orange hover:bg-orange-700 text-white">Login</Button>
        </Link>
      </div>
    );
  }

  if (!loading && items.length === 0) {
    return (
      <div className="max-w-4xl mx-auto px-4 py-20 text-center">
        <ShoppingBag className="w-16 h-16 text-gray-300 mx-auto mb-6" />
        <h2 className="font-display font-bold text-3xl uppercase mb-4">Keranjang Kosong</h2>
        <p className="text-gray-500 mb-8">Belum ada barang di keranjang Anda.</p>
        <Link href="/products">
          <Button className="bg-brand-orange hover:bg-orange-700 text-white">Jelajahi Produk</Button>
        </Link>
      </div>
    );
  }

  const handleQty = async (itemId: string, newQty: number) => {
    try { await updateItem(itemId, newQty); } catch { toast.error('Gagal memperbarui.'); }
  };

  const handleRemove = async (itemId: string) => {
    try {
      await removeItem(itemId);
      setSelectedIds((prev) => { const n = new Set(prev); n.delete(itemId); return n; });
      toast.info('Item dihapus dari keranjang.');
    } catch { toast.error('Gagal menghapus.'); }
  };

  return (
    <section className="py-8 bg-white min-h-screen">
      <div className="max-w-5xl mx-auto px-4">
        <h1 className="font-display font-bold text-3xl uppercase mb-8 flex items-center gap-3">
          <span className="w-1 h-8 bg-brand-orange block" />
          Keranjang Sewa
        </h1>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Items */}
          <div className="lg:col-span-2 space-y-0">
            {/* Select All Header */}
            <div className="flex items-center gap-3 py-3 px-4 bg-gray-50 border border-gray-200 mb-2">
              <Checkbox
                checked={selectedIds.size === items.length && items.length > 0}
                onCheckedChange={toggleAll}
                id="select-all"
              />
              <label htmlFor="select-all" className="text-sm font-bold uppercase cursor-pointer select-none">
                Pilih Semua ({items.length} produk)
              </label>
            </div>

            {items.map((item) => (
              <div
                key={item.id}
                className={`border p-4 flex gap-4 transition-colors ${
                  selectedIds.has(item.id)
                    ? 'border-brand-orange bg-orange-50/30'
                    : 'border-gray-200'
                }`}
              >
                {/* Checkbox */}
                <div className="flex items-center">
                  <Checkbox
                    checked={selectedIds.has(item.id)}
                    onCheckedChange={() => toggleItem(item.id)}
                    id={`item-${item.id}`}
                  />
                </div>

                {/* Image */}
                <div className="w-24 h-24 bg-gray-100 flex-shrink-0 relative">
                  <Image
                    src={item.product?.image_url || 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=200&q=80'}
                    alt={item.product?.name || 'Product'}
                    fill
                    className="object-cover"
                    sizes="96px"
                  />
                </div>

                {/* Details */}
                <div className="flex-1 flex flex-col">
                  <Link href={`/products/${item.product_id}`} className="font-display font-bold text-sm uppercase hover:text-brand-orange transition">
                    {item.product?.name || `Product #${item.product_id}`}
                  </Link>
                  <div className="flex items-center gap-2">
                    <span className="text-xs text-gray-500 uppercase">{item.product?.category}</span>
                    {item.size && (
                      <span className="text-[10px] font-bold bg-brand-orange/10 text-brand-orange px-2 py-0.5 rounded uppercase">
                        Size: {item.size}
                      </span>
                    )}
                  </div>
                  <p className="text-brand-orange font-bold text-sm mt-1">
                    {formatRupiah(item.product?.price_24h || 0)} <span className="text-gray-500 font-normal">/hari</span>
                  </p>
                  <div className="flex items-center gap-4 mt-auto">
                    <div className="flex items-center border border-gray-300">
                      <button onClick={() => handleQty(item.id, Math.max(1, item.quantity - 1))} className="p-1.5 hover:bg-gray-100"><Minus className="w-3 h-3" /></button>
                      <span className="px-3 text-sm font-bold">{item.quantity}</span>
                      <button onClick={() => handleQty(item.id, item.quantity + 1)} className="p-1.5 hover:bg-gray-100"><Plus className="w-3 h-3" /></button>
                    </div>
                    <button onClick={() => handleRemove(item.id)} className="text-red-500 hover:text-red-700 transition text-xs flex items-center gap-1">
                      <Trash2 className="w-4 h-4" /> Hapus
                    </button>
                  </div>
                </div>

                {/* Subtotal */}
                <div className="text-right flex-shrink-0">
                  <p className="font-bold text-sm">{formatRupiah((item.product?.price_24h || 0) * item.quantity)}</p>
                </div>
              </div>
            ))}
          </div>

          {/* Summary */}
          <div className="lg:col-span-1">
            <div className="border border-gray-200 p-6 sticky top-28">
              <h3 className="font-display font-bold text-lg uppercase mb-6 border-b border-gray-200 pb-4">Ringkasan</h3>
              <div className="space-y-3 text-sm mb-6">
                <div className="flex justify-between">
                  <span className="text-gray-500">Item Terpilih</span>
                  <span className="font-bold">{selectedCount} dari {totalItems}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-500">Subtotal</span>
                  <span className="font-bold">{formatRupiah(selectedTotal)}</span>
                </div>
              </div>
              <div className="border-t border-gray-200 pt-4 mb-6">
                <div className="flex justify-between text-lg">
                  <span className="font-display font-bold uppercase">Total</span>
                  <span className="font-bold text-brand-orange">{formatRupiah(selectedTotal)}</span>
                </div>
              </div>

              {selectedIds.size === 0 ? (
                <div className="text-center">
                  <p className="text-sm text-gray-400 mb-2">Pilih item yang ingin di-checkout</p>
                  <Button disabled className="w-full" variant="outline">Checkout</Button>
                </div>
              ) : (
                <Link
                  href={`/checkout?items=${Array.from(selectedIds).join(',')}`}
                  className="w-full bg-brand-orange text-white font-display font-bold uppercase tracking-wider py-3 hover:bg-orange-700 transition flex items-center justify-center gap-2 block text-center"
                >
                  Checkout ({selectedCount}) <ArrowRight className="w-4 h-4" />
                </Link>
              )}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
