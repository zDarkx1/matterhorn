'use client';

import { useState, useEffect } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import { motion, useInView } from 'motion/react';
import { useRef } from 'react';
import { ArrowUp } from 'lucide-react';
import { HeroCarousel, type HeroSlide } from '@/components/hero/HeroCarousel';
import { productService } from '@/services/product.service';
import { formatRupiah } from '@/utils/format';
import type { Product } from '@/types';

// ─── Reusable Animation Wrappers ─────────────────────────────

const bouncy = {
  type: 'spring' as const,
  stiffness: 260,
  damping: 20,
  mass: 0.8,
};

const smooth = {
  type: 'spring' as const,
  stiffness: 120,
  damping: 18,
  mass: 0.6,
};

function FadeInUp({ children, delay = 0, className = '' }: { children: React.ReactNode; delay?: number; className?: string }) {
  const ref = useRef(null);
  const isInView = useInView(ref, { once: true, margin: '-60px' });
  return (
    <motion.div
      ref={ref}
      initial={{ opacity: 0, y: 40 }}
      animate={isInView ? { opacity: 1, y: 0 } : {}}
      transition={{ ...smooth, delay }}
      className={className}
    >
      {children}
    </motion.div>
  );
}

function ScaleIn({ children, delay = 0, className = '' }: { children: React.ReactNode; delay?: number; className?: string }) {
  const ref = useRef(null);
  const isInView = useInView(ref, { once: true, margin: '-40px' });
  return (
    <motion.div
      ref={ref}
      initial={{ opacity: 0, scale: 0.85 }}
      animate={isInView ? { opacity: 1, scale: 1 } : {}}
      transition={{ ...bouncy, delay }}
      className={className}
    >
      {children}
    </motion.div>
  );
}

// ─── Hero Slides (3) ─────────────────────────────────────────
const HERO_SLIDES: HeroSlide[] = [
  {
    image: 'https://images.unsplash.com/photo-1516939884455-1445c8652f83?q=80&w=2000&auto=format&fit=crop',
    badge: 'Adventure Ready',
    title: <>Taklukkan<br />Setiap Puncak</>,
    desc: 'Sewa peralatan standar ekspedisi dengan harga terjangkau. Mulai dari Rp 25.000/hari.',
    cta: 'Sewa Sekarang',
    ctaHref: '/products',
    align: 'left',
  },
  {
    image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?q=80&w=2000&auto=format&fit=crop',
    badge: 'New Arrivals',
    title: <>Nyaman<br />Di Alam Bebas</>,
    desc: 'Koleksi Sleeping Bag & Tenda Ultralight terbaru tahun 2026.',
    cta: 'Lihat Koleksi',
    ctaHref: '/products?category=Tenda',
    align: 'right',
  },
  {
    image: 'https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=2000&auto=format&fit=crop',
    badge: 'Premium Gear',
    title: <>Carrier<br />Expedition</>,
    desc: 'Carrier 60L–80L untuk pendakian multi-day. Nyaman & tahan banting.',
    cta: 'Jelajahi',
    ctaHref: '/products?category=Carrier+%26+Daypack',
    align: 'left',
  },
];

// ─── Category Data ───────────────────────────────────────────
const CATEGORIES = [
  { name: 'Bags', image: 'https://images.unsplash.com/photo-1622260614153-03223fb72052?auto=format&fit=crop&w=300&q=80' },
  { name: 'Sepatu', image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=300&q=80' },
  { name: 'Tenda', image: 'https://images.unsplash.com/photo-1504851149312-7a075b496cc7?auto=format&fit=crop&w=300&q=80' },
  { name: 'Apparel', image: 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=300&q=80' },
  { name: 'Cooking', image: 'https://images.unsplash.com/photo-1583578768826-b8c281747805?auto=format&fit=crop&w=300&q=80' },
];

// ─── Testimonials ────────────────────────────────────────────
const TESTIMONIALS = [
  {
    image: 'https://images.unsplash.com/photo-1533130061792-64b345e4a833?auto=format&fit=crop&w=600&q=80',
    avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=80&q=80',
    name: 'Rizky Aditya',
    role: 'Pendaki • Rinjani Expedition 2025',
    text: '"Carrier Eiger yang aku sewa di Matterhorn beneran top. Nyaman banget pas summit attack ke Rinjani."',
    featured: false,
  },
  {
    image: 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=600&q=80',
    avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=80&q=80',
    name: 'Sari Wulandari',
    role: 'Hiker • Triple Summit 2025',
    text: '"Sudah 3x sewa tenda di Matterhorn buat trip ke Papandayan, Prau, sampai Sindoro. Kualitasnya nggak pernah mengecewakan."',
    featured: true,
  },
  {
    image: 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=600&q=80',
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=80&q=80',
    name: 'Bima Raharja',
    role: 'Backpacker • Semeru 3676m',
    text: '"Trekking pole carbon-nya ringan abis dan kokoh. Perfect buat terrain berbatu di jalur Semeru."',
    featured: false,
  },
];

// ─── Landing Page ────────────────────────────────────────────
export default function LandingPage() {
  const [products, setProducts] = useState<Product[]>([]);
  const [loadingProducts, setLoadingProducts] = useState(true);
  const [showBackToTop, setShowBackToTop] = useState(false);

  useEffect(() => {
    async function load() {
      try {
        const res = await productService.getProducts({ per_page: 8 });
        setProducts(res.data || []);
      } catch {
        setProducts([]);
      } finally {
        setLoadingProducts(false);
      }
    }
    load();
  }, []);

  useEffect(() => {
    const handleScroll = () => setShowBackToTop(window.scrollY > 400);
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  return (
    <>
      {/* ═══ HERO — 3-Slide MotionCarousel ═══ */}
      <HeroCarousel slides={HERO_SLIDES} />

      {/* ═══ KATEGORI POPULER ═══ */}
      <section className="py-14 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <FadeInUp>
            <h3 className="font-display font-bold text-2xl uppercase mb-8 flex items-center gap-2">
              <motion.span
                className="w-1 h-6 bg-brand-orange block"
                initial={{ scaleY: 0 }}
                whileInView={{ scaleY: 1 }}
                transition={bouncy}
                viewport={{ once: true }}
                style={{ originY: 1 }}
              />
              Kategori Populer
            </h3>
          </FadeInUp>

          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            {CATEGORIES.map((cat, i) => (
              <ScaleIn key={cat.name} delay={i * 0.08}>
                <Link
                  href={`/products?category=${encodeURIComponent(cat.name)}`}
                  className="group relative block h-44 overflow-hidden border border-gray-200"
                >
                  <Image
                    src={cat.image}
                    alt={cat.name}
                    fill
                    className="object-cover transition-transform duration-700 group-hover:scale-110"
                    sizes="(max-width:768px) 50vw, 20vw"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex items-end justify-center p-3">
                    <motion.span
                      className="text-white font-display font-bold uppercase tracking-wider text-sm"
                      whileHover={{ y: -4 }}
                      transition={bouncy}
                    >
                      {cat.name}
                    </motion.span>
                  </div>
                </Link>
              </ScaleIn>
            ))}
          </div>
        </div>
      </section>

      {/* ═══ FLASH SALE BANNER ═══ */}
      <section className="py-4">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <FadeInUp>
            <motion.div
              className="bg-gradient-to-r from-brand-orange to-red-600 p-6 md:p-10 text-white relative overflow-hidden flex flex-col md:flex-row items-center justify-between"
              whileHover={{ scale: 1.01 }}
              transition={smooth}
            >
              <div
                className="absolute inset-0 opacity-10"
                style={{
                  backgroundImage:
                    'repeating-linear-gradient(45deg, transparent, transparent 10px, #000 10px, #000 20px)',
                }}
              />
              <div className="relative z-10 mb-6 md:mb-0">
                <motion.div
                  className="flex items-center gap-3 mb-2"
                  initial={{ x: -40, opacity: 0 }}
                  whileInView={{ x: 0, opacity: 1 }}
                  transition={{ ...bouncy, delay: 0.1 }}
                  viewport={{ once: true }}
                >
                  <span className="bg-white text-brand-orange font-bold text-xs px-2 py-1 uppercase tracking-widest">
                    Flash Sale
                  </span>
                </motion.div>
                <motion.h2
                  className="text-3xl md:text-5xl font-display font-bold uppercase leading-none"
                  initial={{ x: -60, opacity: 0 }}
                  whileInView={{ x: 0, opacity: 1 }}
                  transition={{ ...smooth, delay: 0.2 }}
                  viewport={{ once: true }}
                >
                  Diskon Member
                  <br />
                  Hingga 50%
                </motion.h2>
              </div>
              <motion.div
                className="relative z-10"
                initial={{ scale: 0.7, opacity: 0 }}
                whileInView={{ scale: 1, opacity: 1 }}
                transition={{ ...bouncy, delay: 0.3 }}
                viewport={{ once: true }}
              >
                <Link
                  href="/products"
                  className="bg-black text-white px-8 py-3 border border-white font-display font-bold uppercase tracking-wider hover:bg-white hover:text-black transition-colors duration-300 inline-block"
                >
                  Cek Promo
                </Link>
              </motion.div>
            </motion.div>
          </FadeInUp>
        </div>
      </section>

      {/* ═══ PRODUCT CATALOG ═══ */}
      <section id="katalog" className="py-14 bg-white border-t border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <FadeInUp>
            <div className="flex justify-between items-end mb-8 border-b border-gray-300 pb-4">
              <div className="flex flex-col">
                <span className="text-xs text-gray-500 mb-1">
                  Menampilkan {products.length} Produk
                </span>
                <h3 className="font-display font-bold text-2xl uppercase text-brand-black">
                  Produk Terlaris
                </h3>
              </div>
              <Link
                href="/products"
                className="text-sm font-bold uppercase text-brand-orange hover:underline tracking-wide"
              >
                Lihat Semua →
              </Link>
            </div>
          </FadeInUp>

          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            {loadingProducts
              ? Array.from({ length: 8 }).map((_, i) => (
                  <div key={i} className="bg-white p-3 border border-gray-200 animate-pulse">
                    <div className="bg-gray-200 h-48 w-full mb-3" />
                    <div className="bg-gray-200 h-4 w-3/4 mb-2" />
                    <div className="bg-gray-200 h-3 w-1/2 mb-4" />
                    <div className="bg-gray-200 h-6 w-1/2" />
                  </div>
                ))
              : products.map((p, i) => (
                  <ScaleIn key={p.id} delay={i * 0.06}>
                    <motion.div
                      className="group bg-white border border-gray-200 hover:border-brand-orange transition-colors duration-300 relative flex flex-col"
                      whileHover={{ y: -6 }}
                      transition={bouncy}
                    >
                      {p.stock_available < 5 && p.stock_available > 0 && (
                        <motion.span
                          className="absolute top-0 left-0 bg-brand-orange text-white text-xs font-bold px-2 py-1 uppercase tracking-wider z-10"
                          initial={{ x: -20, opacity: 0 }}
                          animate={{ x: 0, opacity: 1 }}
                          transition={{ ...bouncy, delay: 0.3 }}
                        >
                          Stok Terbatas
                        </motion.span>
                      )}
                      <Link
                        href={`/products/${p.id}`}
                        className="w-full h-48 overflow-hidden bg-gray-100 relative block"
                      >
                        <Image
                          src={
                            p.image_url ||
                            'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=500&q=80'
                          }
                          alt={p.name}
                          fill
                          className="object-cover group-hover:scale-105 transition-transform duration-500"
                          sizes="(max-width:768px) 50vw, 25vw"
                        />
                        <div className="absolute bottom-0 left-0 w-full p-2 bg-white/90 translate-y-full group-hover:translate-y-0 transition-transform duration-300 flex justify-center">
                          <span className="bg-black text-white text-xs uppercase font-bold px-4 py-2 hover:bg-brand-orange w-full text-center">
                            Lihat Detail
                          </span>
                        </div>
                      </Link>
                      <div className="p-4 flex flex-col flex-grow">
                        <span className="text-gray-500 text-xs font-bold uppercase mb-1">
                          {p.category}
                        </span>
                        <Link
                          href={`/products/${p.id}`}
                          className="font-display font-bold text-sm text-brand-black uppercase leading-tight mb-2 group-hover:text-brand-orange transition-colors cursor-pointer line-clamp-2"
                        >
                          {p.name}
                        </Link>
                        <div className="mt-auto pt-2 border-t border-gray-100">
                          <p className="text-brand-orange font-bold text-sm">
                            {formatRupiah(p.price_24h)}{' '}
                            <span className="text-gray-500 font-normal text-xs">/hari</span>
                          </p>
                        </div>
                      </div>
                    </motion.div>
                  </ScaleIn>
                ))}
          </div>
        </div>
      </section>

      {/* ═══ TESTIMONIAL ═══ */}
      <section className="py-20 bg-brand-black text-white relative overflow-hidden">
        <div
          className="absolute inset-0 opacity-5"
          style={{
            backgroundImage:
              'repeating-linear-gradient(45deg, transparent, transparent 30px, #FF5500 30px, #FF5500 31px)',
          }}
        />
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <FadeInUp>
            <div className="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
              <div>
                <motion.span
                  className="text-brand-orange text-xs font-bold uppercase tracking-widest mb-2 block"
                  initial={{ x: -30, opacity: 0 }}
                  whileInView={{ x: 0, opacity: 1 }}
                  transition={{ ...bouncy, delay: 0.1 }}
                  viewport={{ once: true }}
                >
                  Real Stories
                </motion.span>
                <motion.h2
                  className="font-display font-bold text-4xl md:text-5xl uppercase leading-none"
                  initial={{ x: -50, opacity: 0 }}
                  whileInView={{ x: 0, opacity: 1 }}
                  transition={{ ...smooth, delay: 0.2 }}
                  viewport={{ once: true }}
                >
                  Petualang
                  <br className="hidden md:block" /> Bicara
                </motion.h2>
              </div>
              <motion.p
                className="text-gray-400 max-w-xs text-sm leading-relaxed"
                initial={{ opacity: 0 }}
                whileInView={{ opacity: 1 }}
                transition={{ duration: 0.6, delay: 0.3 }}
                viewport={{ once: true }}
              >
                Ribuan adventurer telah mempercayakan gear mereka ke Matterhorn. Ini kisah
                mereka.
              </motion.p>
            </div>
          </FadeInUp>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {TESTIMONIALS.map((t, i) => (
              <ScaleIn key={i} delay={i * 0.12}>
                <motion.div
                  className={`group relative bg-brand-dark border ${
                    t.featured
                      ? 'border-brand-orange'
                      : 'border-gray-700 hover:border-brand-orange'
                  } transition-all duration-300 overflow-hidden flex flex-col`}
                  whileHover={{ y: -6, scale: 1.02 }}
                  transition={bouncy}
                >
                  <div className="relative h-56 overflow-hidden">
                    <Image
                      src={t.image}
                      alt={t.name}
                      fill
                      className="object-cover brightness-75 group-hover:scale-105 transition-transform duration-500"
                      sizes="(max-width:768px) 100vw, 33vw"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-brand-dark via-transparent to-transparent" />
                    {t.featured && (
                      <span className="absolute top-4 left-4 bg-brand-orange text-white text-xs font-bold px-2 py-1 uppercase tracking-widest">
                        Top Review
                      </span>
                    )}
                    <div className="absolute top-4 right-4 w-10 h-10 bg-brand-orange flex items-center justify-center">
                      <svg
                        className="w-5 h-5 text-white"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                      </svg>
                    </div>
                  </div>
                  <div className="p-6 flex flex-col flex-grow">
                    <div className="flex gap-1 mb-3">
                      <span className="text-brand-orange text-sm">★★★★★</span>
                    </div>
                    <p className="text-gray-300 text-sm leading-relaxed mb-5 flex-grow italic">
                      {t.text}
                    </p>
                    <div className="flex items-center gap-3 border-t border-gray-700 pt-4">
                      <Image
                        src={t.avatar}
                        alt={t.name}
                        width={40}
                        height={40}
                        className="object-cover border-2 border-brand-orange"
                      />
                      <div>
                        <p className="font-display font-bold text-sm uppercase text-white">
                          {t.name}
                        </p>
                        <p className="text-xs text-gray-500">{t.role}</p>
                      </div>
                    </div>
                  </div>
                </motion.div>
              </ScaleIn>
            ))}
          </div>

          {/* Stats Row */}
          <FadeInUp delay={0.2} className="mt-14">
            <div className="grid grid-cols-2 md:grid-cols-4 gap-0 border border-gray-700">
              {[
                { value: '4.9', label: 'Rating Rata-rata', highlight: true },
                { value: '2.4K+', label: 'Pelanggan Puas' },
                { value: '98%', label: 'Repeat Order' },
                { value: '5+', label: 'Tahun Beroperasi' },
              ].map((stat, i) => (
                <motion.div
                  key={i}
                  className={`flex flex-col items-center justify-center py-8 px-4 text-center ${
                    i < 3 ? 'border-r border-gray-700' : ''
                  } ${i >= 2 ? 'border-t md:border-t-0 border-gray-700' : ''}`}
                  initial={{ opacity: 0, y: 30 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ ...bouncy, delay: i * 0.1 }}
                  viewport={{ once: true }}
                >
                  <span
                    className={`font-display font-bold text-4xl ${
                      stat.highlight ? 'text-brand-orange' : 'text-white'
                    }`}
                  >
                    {stat.value}
                  </span>
                  <span className="text-xs text-gray-500 uppercase tracking-wider mt-1">
                    {stat.label}
                  </span>
                </motion.div>
              ))}
            </div>
          </FadeInUp>
        </div>
      </section>

      {/* ═══ Back to Top ═══ */}
      <motion.button
        onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })}
        className="fixed bottom-6 right-6 z-40 bg-brand-black text-white border border-brand-orange p-3 shadow-lg group"
        initial={{ opacity: 0, y: 20 }}
        animate={showBackToTop ? { opacity: 1, y: 0 } : { opacity: 0, y: 20, pointerEvents: 'none' as const }}
        whileHover={{ scale: 1.15 }}
        whileTap={{ scale: 0.9 }}
        transition={bouncy}
      >
        <ArrowUp className="w-6 h-6 group-hover:-translate-y-1 transition-transform duration-300" />
      </motion.button>
    </>
  );
}
