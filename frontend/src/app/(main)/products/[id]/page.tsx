'use client';

import { useState, useEffect, use } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import { productService } from '@/services/product.service';
import { useCartStore } from '@/stores/useCartStore';
import { useAuthStore } from '@/stores/useAuthStore';
import { formatRupiah } from '@/utils/format';
import { toast } from 'sonner';
import type { Product } from '@/types';
import { ShoppingBag, ArrowLeft, Package, Shield, Truck, Minus, Plus } from 'lucide-react';

export default function ProductDetailPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = use(params);
  const [product, setProduct] = useState<Product | null>(null);
  const [loading, setLoading] = useState(true);
  const [quantity, setQuantity] = useState(1);
  const [adding, setAdding] = useState(false);
  const { addItem } = useCartStore();
  const { user } = useAuthStore();

  useEffect(() => {
    async function load() {
      try {
        const data = await productService.getProduct(Number(id));
        setProduct(data);
      } catch {
        setProduct(null);
      } finally {
        setLoading(false);
      }
    }
    load();
  }, [id]);

  const handleAddToCart = async () => {
    if (!user) {
      toast.error('Silakan login terlebih dahulu.');
      return;
    }
    if (!product) return;

    setAdding(true);
    try {
      await addItem({ product_id: product.id, quantity });
      toast.success(`${product.name} ditambahkan ke keranjang!`);
    } catch {
      toast.error('Gagal menambahkan ke keranjang.');
    } finally {
      setAdding(false);
    }
  };

  if (loading) {
    return (
      <div className="max-w-7xl mx-auto px-4 py-12 animate-pulse">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-12">
          <div className="bg-gray-200 h-96" />
          <div className="space-y-4">
            <div className="bg-gray-200 h-6 w-1/3" />
            <div className="bg-gray-200 h-10 w-2/3" />
            <div className="bg-gray-200 h-8 w-1/4" />
            <div className="bg-gray-200 h-32 w-full" />
          </div>
        </div>
      </div>
    );
  }

  if (!product) {
    return (
      <div className="max-w-7xl mx-auto px-4 py-20 text-center">
        <h2 className="font-display font-bold text-3xl uppercase mb-4">Produk Tidak Ditemukan</h2>
        <Link href="/products" className="text-brand-orange font-bold hover:underline">← Kembali ke Katalog</Link>
      </div>
    );
  }

  return (
    <div className="bg-white min-h-screen">
      {/* Breadcrumb */}
      <div className="max-w-7xl mx-auto px-4 py-4">
        <Link href="/products" className="text-sm text-gray-500 hover:text-brand-orange transition flex items-center gap-1">
          <ArrowLeft className="w-4 h-4" /> Kembali ke Katalog
        </Link>
      </div>

      <div className="max-w-7xl mx-auto px-4 pb-16">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-12">
          {/* Image */}
          <div className="relative h-96 md:h-[500px] bg-gray-100 border border-gray-200 overflow-hidden">
            <Image
              src={product.image_url || 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=800&q=80'}
              alt={product.name}
              fill
              className="object-cover"
              sizes="(max-width:768px) 100vw, 50vw"
              priority
            />
            {product.stock_available < 5 && product.stock_available > 0 && (
              <span className="absolute top-4 left-4 bg-brand-orange text-white text-xs font-bold px-3 py-1.5 uppercase tracking-wider">
                Stok Terbatas
              </span>
            )}
          </div>

          {/* Details */}
          <div className="flex flex-col">
            <span className="text-gray-500 text-xs font-bold uppercase tracking-wide mb-1">{product.category}</span>
            <h1 className="font-display font-bold text-3xl md:text-4xl uppercase leading-tight mb-4">{product.name}</h1>

            <div className="flex items-baseline gap-2 mb-6">
              <span className="text-brand-orange font-bold text-3xl">{formatRupiah(product.price_24h)}</span>
              <span className="text-gray-500 text-sm">/hari</span>
            </div>

            {/* Stock */}
            <div className="flex items-center gap-2 mb-6 text-sm">
              <Package className="w-4 h-4 text-gray-400" />
              <span className={product.stock_available > 0 ? 'text-green-600' : 'text-red-600'}>
                {product.stock_available > 0 ? `${product.stock_available} unit tersedia` : 'Stok habis'}
              </span>
            </div>

            {/* Description */}
            {product.description && (
              <div className="border-t border-gray-200 pt-6 mb-6">
                <h3 className="font-bold text-sm uppercase mb-3">Deskripsi</h3>
                <p className="text-gray-600 text-sm leading-relaxed whitespace-pre-line">{product.description}</p>
              </div>
            )}

            {/* Sizes */}
            {product.sizes && product.sizes.length > 0 && (
              <div className="mb-6">
                <h3 className="font-bold text-sm uppercase mb-3">Ukuran Tersedia</h3>
                <div className="flex gap-2 flex-wrap">
                  {product.sizes.map((s) => (
                    <span key={s.size} className="border border-gray-200 text-xs py-2 px-4 font-medium">
                      {s.size} <span className="text-gray-400">({s.stock})</span>
                    </span>
                  ))}
                </div>
              </div>
            )}

            {/* Quantity + Add to Cart */}
            {product.stock_available > 0 && (
              <div className="border-t border-gray-200 pt-6 mt-auto">
                <div className="flex items-center gap-4 mb-4">
                  <span className="text-sm font-bold uppercase">Jumlah</span>
                  <div className="flex items-center border border-gray-300">
                    <button onClick={() => setQuantity(Math.max(1, quantity - 1))} className="p-2 hover:bg-gray-100 transition">
                      <Minus className="w-4 h-4" />
                    </button>
                    <span className="px-4 py-2 text-sm font-bold min-w-[48px] text-center">{quantity}</span>
                    <button onClick={() => setQuantity(Math.min(product.stock_available, quantity + 1))} className="p-2 hover:bg-gray-100 transition">
                      <Plus className="w-4 h-4" />
                    </button>
                  </div>
                </div>
                <button
                  onClick={handleAddToCart}
                  disabled={adding}
                  className="w-full bg-brand-orange text-white font-display font-bold uppercase tracking-wider py-4 hover:bg-orange-700 transition disabled:opacity-50 flex items-center justify-center gap-2 text-lg"
                >
                  <ShoppingBag className="w-5 h-5" />
                  {adding ? 'Menambahkan...' : 'Tambah ke Keranjang'}
                </button>
              </div>
            )}

            {/* Trust Badges */}
            <div className="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-200">
              {[
                { icon: Shield, label: 'Alat Steril' },
                { icon: Package, label: 'Kondisi Terjamin' },
                { icon: Truck, label: 'Ambil di Toko' },
              ].map(({ icon: Icon, label }) => (
                <div key={label} className="flex flex-col items-center text-center gap-1">
                  <Icon className="w-5 h-5 text-brand-orange" />
                  <span className="text-xs text-gray-500">{label}</span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
