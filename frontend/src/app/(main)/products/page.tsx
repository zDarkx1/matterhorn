'use client';

import { Suspense, useState, useEffect } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { useSearchParams } from 'next/navigation';
import { productService } from '@/services/product.service';
import { formatRupiah } from '@/utils/format';
import type { Product, PaginationMeta } from '@/types';
import { SlidersHorizontal, Search } from 'lucide-react';

function ProductsCatalog() {
  const searchParams = useSearchParams();
  const [products, setProducts] = useState<Product[]>([]);
  const [meta, setMeta] = useState<PaginationMeta | null>(null);
  const [loading, setLoading] = useState(true);
  const [categories, setCategories] = useState<string[]>([]);
  const [search, setSearch] = useState(searchParams.get('search') || '');
  const [selectedCategory, setSelectedCategory] = useState(searchParams.get('category') || '');
  const [sort, setSort] = useState('newest');
  const [page, setPage] = useState(1);
  const [showFilters, setShowFilters] = useState(false);

  useEffect(() => {
    productService.getCategories().then(setCategories).catch(() => {});
  }, []);

  useEffect(() => {
    async function load() {
      setLoading(true);
      try {
        const res = await productService.getProducts({
          search: search || undefined,
          category: selectedCategory || undefined,
          sort: sort as 'newest',
          page,
          per_page: 12,
        });
        setProducts(res.data || []);
        setMeta(res.meta);
      } catch {
        setProducts([]);
      } finally {
        setLoading(false);
      }
    }
    load();
  }, [search, selectedCategory, sort, page]);

  return (
    <div className="flex flex-col lg:flex-row gap-8">

      {/* Sidebar Filter */}
      <aside className={`w-full lg:w-64 flex-shrink-0 ${showFilters ? 'block' : 'hidden lg:block'}`}>
        <div className="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
          <h3 className="font-display font-bold text-lg uppercase">Filter</h3>
          <button onClick={() => { setSelectedCategory(''); setSearch(''); setSort('newest'); }} className="text-xs text-brand-orange hover:underline font-bold">
            Reset
          </button>
        </div>

        {/* Search */}
        <div className="mb-6">
          <label className="font-bold text-sm uppercase mb-3 block">Pencarian</label>
          <div className="relative">
            <input
              type="text"
              value={search}
              onChange={(e) => { setSearch(e.target.value); setPage(1); }}
              className="w-full border border-gray-200 p-2 pr-8 text-sm outline-none focus:border-brand-orange transition"
              placeholder="Cari produk..."
            />
            <Search className="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
          </div>
        </div>

        {/* Category Filter */}
        <div className="border-b border-gray-100 pb-6 mb-6">
          <span className="font-bold text-sm uppercase mb-3 block">Kategori</span>
          <div className="flex flex-wrap gap-2">
            <button
              type="button"
              onClick={() => { setSelectedCategory(''); setPage(1); }}
              className={`px-3 py-1.5 text-xs font-bold uppercase tracking-wide border rounded transition-all duration-200 ${
                !selectedCategory
                  ? 'bg-brand-orange text-white border-brand-orange shadow-sm'
                  : 'bg-white text-gray-600 border-gray-200 hover:border-brand-orange hover:text-brand-orange'
              }`}
            >
              Semua
            </button>
            {categories.map((cat) => (
              <button
                key={cat}
                type="button"
                onClick={() => { setSelectedCategory(cat); setPage(1); }}
                className={`px-3 py-1.5 text-xs font-bold uppercase tracking-wide border rounded transition-all duration-200 ${
                  selectedCategory === cat
                    ? 'bg-brand-orange text-white border-brand-orange shadow-sm'
                    : 'bg-white text-gray-600 border-gray-200 hover:border-brand-orange hover:text-brand-orange'
                }`}
              >
                {cat}
              </button>
            ))}
          </div>
        </div>
      </aside>

      {/* Product Grid */}
      <div className="flex-1">
        <div className="flex justify-between items-end mb-8 border-b border-gray-300 pb-4">
          <div>
            <span className="text-xs text-gray-500 mb-1 block">{meta ? `${meta.total} Produk ditemukan` : 'Memuat...'}</span>
            <h3 className="font-display font-bold text-2xl uppercase text-brand-black">
              {selectedCategory || 'Semua Produk'}
            </h3>
          </div>
          <div className="flex items-center gap-4">
            <button onClick={() => setShowFilters(!showFilters)} className="lg:hidden flex items-center gap-2 text-sm font-bold uppercase border border-gray-300 px-4 py-2">
              <SlidersHorizontal className="w-4 h-4" /> Filter
            </button>
            <select value={sort} onChange={(e) => { setSort(e.target.value); setPage(1); }} className="border-none bg-transparent text-sm font-bold uppercase cursor-pointer outline-none text-right">
              <option value="newest">Terbaru</option>
              <option value="price_asc">Termurah</option>
              <option value="price_desc">Termahal</option>
              <option value="name_asc">Nama A-Z</option>
            </select>
          </div>
        </div>

        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          {loading
            ? Array.from({ length: 8 }).map((_, i) => (
                <div key={i} className="bg-white p-3 border border-gray-200 animate-pulse">
                  <div className="bg-gray-200 h-48 w-full mb-3" />
                  <div className="bg-gray-200 h-4 w-3/4 mb-2" />
                  <div className="bg-gray-200 h-3 w-1/2 mb-4" />
                  <div className="bg-gray-200 h-6 w-1/2" />
                </div>
              ))
            : products.length === 0
            ? <p className="col-span-full text-center text-gray-500 py-12">Tidak ada produk ditemukan.</p>
            : products.map((p) => (
                <div key={p.id} className="group bg-white border border-gray-200 hover:border-brand-orange transition duration-300 relative flex flex-col">
                  {p.stock_available < 5 && p.stock_available > 0 && (
                    <span className="absolute top-0 left-0 bg-brand-orange text-white text-xs font-bold px-2 py-1 uppercase tracking-wider z-10">Stok Terbatas</span>
                  )}
                  <Link href={`/products/${p.id}`} className="w-full h-48 overflow-hidden bg-gray-100 relative block">
                    <Image src={p.image_url || 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=500&q=80'} alt={p.name} fill className="object-cover group-hover:scale-105 transition duration-500" sizes="(max-width:768px) 50vw, 25vw" />
                  </Link>
                  <div className="p-4 flex flex-col flex-grow">
                    <span className="text-gray-500 text-xs font-bold uppercase mb-1">{p.category}</span>
                    <Link href={`/products/${p.id}`} className="font-display font-bold text-sm text-brand-black uppercase leading-tight mb-2 group-hover:text-brand-orange transition line-clamp-2">{p.name}</Link>
                    <div className="mt-auto pt-2 border-t border-gray-100">
                      <p className="text-brand-orange font-bold text-sm">{formatRupiah(p.price_24h)} <span className="text-gray-500 font-normal text-xs">/hari</span></p>
                    </div>
                  </div>
                </div>
              ))}
        </div>

        {/* Pagination */}
        {meta && meta.last_page > 1 && (
          <div className="flex justify-center gap-2 mt-8">
            {Array.from({ length: meta.last_page }).map((_, i) => (
              <button
                key={i}
                onClick={() => setPage(i + 1)}
                className={`w-10 h-10 text-sm font-bold transition ${
                  page === i + 1 ? 'bg-brand-orange text-white' : 'border border-gray-300 hover:border-brand-orange'
                }`}
              >
                {i + 1}
              </button>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}

export default function ProductsPage() {
  return (
    <section className="py-8 bg-white min-h-screen">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <Suspense fallback={
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            {Array.from({ length: 8 }).map((_, i) => (
              <div key={i} className="bg-white p-3 border border-gray-200 animate-pulse">
                <div className="bg-gray-200 h-48 w-full mb-3" />
                <div className="bg-gray-200 h-4 w-3/4 mb-2" />
                <div className="bg-gray-200 h-6 w-1/2" />
              </div>
            ))}
          </div>
        }>
          <ProductsCatalog />
        </Suspense>
      </div>
    </section>
  );
}
