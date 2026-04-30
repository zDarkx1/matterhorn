'use client';

import { useState, useEffect, useRef } from 'react';
import Link from 'next/link';
import { useAuthStore } from '@/stores/useAuthStore';
import { useCartStore } from '@/stores/useCartStore';
import { useUIStore } from '@/stores/useUIStore';
import { ShoppingBag, Search, Menu, X, User, LogOut, ChevronDown, ChevronRight } from 'lucide-react';

export default function Navbar() {
  const { user, isAdmin, logout } = useAuthStore();
  const { totalItems, fetchCart } = useCartStore();
  const { isSearchOpen, setSearchOpen, toggleMobileMenu, isMobileMenuOpen, setMobileMenu } = useUIStore();
  const [searchQuery, setSearchQuery] = useState('');
  const [showUserMenu, setShowUserMenu] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const [categories, setCategories] = useState<string[]>([]);
  const [mobileEquipOpen, setMobileEquipOpen] = useState(false);
  const searchRef = useRef<HTMLInputElement>(null);
  const userMenuRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (user) fetchCart();
  }, [user, fetchCart]);

  // Load categories from API
  useEffect(() => {
    const loadCategories = async () => {
      try {
        const res = await fetch(
          `${process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api'}/products/categories`,
          { headers: { Accept: 'application/json' } }
        );
        const data = await res.json();
        if (data.status === 'success' && Array.isArray(data.data)) {
          setCategories(data.data);
        }
      } catch { /* Silently fail — mega menu just stays empty */ }
    };
    loadCategories();
  }, []);

  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 10);
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  useEffect(() => {
    if (isSearchOpen && searchRef.current) searchRef.current.focus();
  }, [isSearchOpen]);

  useEffect(() => {
    const handleClickOutside = (e: MouseEvent) => {
      if (userMenuRef.current && !userMenuRef.current.contains(e.target as Node)) {
        setShowUserMenu(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const handleLogout = async () => {
    await logout();
    setShowUserMenu(false);
  };

  return (
    <>
      {/* Top Info Bar */}
      <div className="bg-black text-gray-300 text-xs py-2 px-4 hidden md:block">
        <div className="max-w-7xl mx-auto flex justify-between items-center">
          <div className="flex gap-4">
            <span>Garansi Alat Steril</span>
            <span aria-hidden="true">|</span>
            <span>Pengiriman Seluruh Bandung</span>
          </div>
          <div className="flex gap-4">
            <Link href="/about" className="hover:text-white transition">Tentang Kami</Link>
            <Link href="#" className="hover:text-white transition">Bantuan</Link>
          </div>
        </div>
      </div>

      {/* Main Navbar */}
      <nav
        className={`sticky top-0 z-50 bg-white border-b border-gray-200 transition-shadow duration-300 ${scrolled ? 'shadow-md' : 'shadow-sm'}`}
        role="navigation"
        aria-label="Navigasi utama"
      >
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-20">

            {/* Logo */}
            <Link href="/" className="flex-shrink-0 flex items-center cursor-pointer">
              <img src="/logo.png" alt="Matterhorn — Adventure Rental" className="h-14 w-auto object-contain" />
            </Link>

            {/* Desktop Nav */}
            <div className="hidden md:flex space-x-8 items-center h-full">
              {/* Equipment Mega Menu */}
              <div className="group h-full flex items-center">
                <button
                  className="text-sm font-bold uppercase tracking-wide hover:text-brand-orange transition border-b-2 border-transparent group-hover:border-brand-orange py-7 h-full flex items-center gap-1"
                  aria-expanded="false"
                  aria-haspopup="true"
                >
                  Equipment <ChevronDown className="w-3 h-3" aria-hidden="true" />
                </button>
                <div className="absolute top-full left-0 w-full bg-white border-b border-gray-200 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                  <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                    <h4 className="font-display font-bold text-lg mb-6 text-brand-black uppercase">Semua Equipment</h4>
                    {categories.length > 0 ? (
                      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        {categories.map((cat) => (
                          <Link key={cat} href={`/products?category=${encodeURIComponent(cat)}`} className="group/item flex items-center gap-2 hover:bg-gray-50 p-2 transition-all">
                            <span className="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors" aria-hidden="true" />
                            <span className="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">{cat}</span>
                          </Link>
                        ))}
                      </div>
                    ) : (
                      <div className="text-center py-4">
                        <Link href="/products" className="text-sm text-brand-orange font-medium hover:underline">Lihat Semua Produk →</Link>
                      </div>
                    )}
                  </div>
                </div>
              </div>

              <Link href="/products" className="text-sm font-bold uppercase tracking-wide hover:text-brand-orange transition border-b-2 border-transparent hover:border-brand-orange py-7 h-full flex items-center">
                Katalog
              </Link>

              <Link href="/about" className="text-sm font-bold uppercase tracking-wide hover:text-brand-orange transition border-b-2 border-transparent hover:border-brand-orange py-7 h-full flex items-center">
                Tentang
              </Link>
            </div>

            {/* Right Actions */}
            <div className="flex items-center gap-3">
              {/* Search */}
              <div className="flex items-center relative">
                <label htmlFor="navbar-search" className="sr-only">Cari alat outdoor</label>
                <input
                  id="navbar-search"
                  ref={searchRef}
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className={`transition-all duration-300 bg-gray-100 border-none text-sm h-10 ${
                    isSearchOpen ? 'w-48 opacity-100 px-3' : 'w-0 opacity-0 px-0'
                  }`}
                  placeholder="Cari alat..."
                  onKeyDown={(e) => {
                    if (e.key === 'Enter' && searchQuery) {
                      window.location.href = `/products?search=${encodeURIComponent(searchQuery)}`;
                    }
                  }}
                />
                <button
                  onClick={() => setSearchOpen(!isSearchOpen)}
                  className="p-2 hover:text-brand-orange transition z-10 bg-white"
                  aria-label={isSearchOpen ? 'Tutup pencarian' : 'Buka pencarian'}
                >
                  <Search className="w-5 h-5" aria-hidden="true" />
                </button>
              </div>

              {/* Cart */}
              <Link href="/cart" className="relative p-2 hover:text-brand-orange transition" aria-label={`Keranjang belanja${totalItems > 0 ? `, ${totalItems} item` : ''}`}>
                <ShoppingBag className="w-5 h-5" aria-hidden="true" />
                {totalItems > 0 && (
                  <span className="absolute top-0 right-0 h-4 w-4 bg-brand-orange text-white text-[10px] flex items-center justify-center font-bold rounded-full" aria-hidden="true">
                    {totalItems}
                  </span>
                )}
              </Link>

              {/* User / Auth */}
              {user ? (
                <div className="hidden md:block relative" ref={userMenuRef}>
                  <button
                    onClick={() => setShowUserMenu(!showUserMenu)}
                    className="flex items-center gap-2 p-2 hover:text-brand-orange transition text-sm"
                    aria-expanded={showUserMenu}
                    aria-haspopup="true"
                    aria-label="Menu akun"
                  >
                    <User className="w-5 h-5" aria-hidden="true" />
                    <span className="font-medium max-w-[100px] truncate">{user.name}</span>
                    <ChevronDown className="w-3 h-3" aria-hidden="true" />
                  </button>

                  {showUserMenu && (
                    <div className="absolute right-0 top-full mt-2 w-48 bg-white border border-gray-200 shadow-lg z-50" role="menu">
                      <Link href="/profile" className="block px-4 py-3 text-sm hover:bg-gray-50 transition border-b border-gray-100" role="menuitem" onClick={() => setShowUserMenu(false)}>
                        Profil Saya
                      </Link>
                      <Link href="/rentals" className="block px-4 py-3 text-sm hover:bg-gray-50 transition border-b border-gray-100" role="menuitem" onClick={() => setShowUserMenu(false)}>
                        Riwayat Sewa
                      </Link>
                      {isAdmin && (
                        <Link href="/admin" className="block px-4 py-3 text-sm hover:bg-gray-50 transition border-b border-gray-100 text-brand-orange font-medium" role="menuitem" onClick={() => setShowUserMenu(false)}>
                          Admin Panel
                        </Link>
                      )}
                      <button onClick={handleLogout} className="w-full text-left px-4 py-3 text-sm hover:bg-gray-50 transition text-red-600 flex items-center gap-2" role="menuitem">
                        <LogOut className="w-4 h-4" aria-hidden="true" /> Logout
                      </button>
                    </div>
                  )}
                </div>
              ) : (
                <Link href="/login" className="hidden md:flex items-center gap-2 p-2 hover:text-brand-orange transition font-display font-medium uppercase text-sm border border-gray-300 px-4 py-2 hover:border-brand-orange">
                  Login
                </Link>
              )}

              {/* Mobile Menu Toggle */}
              <button
                onClick={toggleMobileMenu}
                className="md:hidden p-2 hover:text-brand-orange transition"
                aria-label={isMobileMenuOpen ? 'Tutup menu' : 'Buka menu'}
                aria-expanded={isMobileMenuOpen}
              >
                {isMobileMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
              </button>
            </div>
          </div>
        </div>

        {/* Mobile Menu */}
        {isMobileMenuOpen && (
          <div className="md:hidden bg-white border-t border-gray-200 shadow-lg">
            <div className="px-4 py-4 space-y-1">
              {/* Equipment Accordion */}
              <div>
                <button
                  onClick={() => setMobileEquipOpen(!mobileEquipOpen)}
                  className="w-full flex justify-between items-center py-3 text-sm font-bold uppercase tracking-wide border-b border-gray-100"
                  aria-expanded={mobileEquipOpen}
                >
                  Equipment
                  <ChevronRight className={`w-4 h-4 transition-transform ${mobileEquipOpen ? 'rotate-90' : ''}`} aria-hidden="true" />
                </button>
                {mobileEquipOpen && (
                  <div className="pl-4 py-2 space-y-1 bg-gray-50 rounded-b-lg mb-1">
                    {categories.length > 0 ? categories.map((cat) => (
                      <Link
                        key={cat}
                        href={`/products?category=${encodeURIComponent(cat)}`}
                        className="block py-2 text-sm text-gray-600 hover:text-brand-orange transition"
                        onClick={() => setMobileMenu(false)}
                      >
                        {cat}
                      </Link>
                    )) : (
                      <Link href="/products" className="block py-2 text-sm text-brand-orange" onClick={() => setMobileMenu(false)}>Lihat Semua Produk</Link>
                    )}
                  </div>
                )}
              </div>

              <Link href="/products" className="block py-3 text-sm font-bold uppercase tracking-wide border-b border-gray-100" onClick={() => setMobileMenu(false)}>Katalog</Link>
              <Link href="/about" className="block py-3 text-sm font-bold uppercase tracking-wide border-b border-gray-100" onClick={() => setMobileMenu(false)}>Tentang Kami</Link>
              <Link href="/cart" className="block py-3 text-sm font-bold uppercase tracking-wide border-b border-gray-100" onClick={() => setMobileMenu(false)}>
                Keranjang {totalItems > 0 && <span className="text-brand-orange">({totalItems})</span>}
              </Link>
              {user ? (
                <>
                  <Link href="/profile" className="block py-3 text-sm font-bold uppercase tracking-wide border-b border-gray-100" onClick={() => setMobileMenu(false)}>Profil</Link>
                  <Link href="/rentals" className="block py-3 text-sm font-bold uppercase tracking-wide border-b border-gray-100" onClick={() => setMobileMenu(false)}>Riwayat Sewa</Link>
                  {isAdmin && <Link href="/admin" className="block py-3 text-sm font-bold uppercase tracking-wide border-b border-gray-100 text-brand-orange" onClick={() => setMobileMenu(false)}>Admin Panel</Link>}
                  <button onClick={() => { handleLogout(); setMobileMenu(false); }} className="w-full text-left py-3 text-sm font-bold uppercase tracking-wide text-red-600">Logout</button>
                </>
              ) : (
                <>
                  <Link href="/login" className="block py-3 text-sm font-bold uppercase tracking-wide border-b border-gray-100" onClick={() => setMobileMenu(false)}>Login</Link>
                  <Link href="/register" className="block py-3 text-sm font-bold uppercase tracking-wide text-brand-orange" onClick={() => setMobileMenu(false)}>Daftar</Link>
                </>
              )}
            </div>
          </div>
        )}
      </nav>
    </>
  );
}
