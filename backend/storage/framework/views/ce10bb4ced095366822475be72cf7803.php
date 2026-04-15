<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Matterhorn.co | Adventure Gear Rental</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Roboto"', 'sans-serif'],
                        display: ['"Oswald"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            black: '#1A1A1A',
                            orange: '#D64500',
                            red: '#D32F2F',
                            dark: '#2d2d2d',
                            gray: '#F5F5F5'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-white text-brand-black antialiased font-sans">

    <div class="bg-black text-gray-300 text-xs py-2 px-4 hidden md:block">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex gap-4">
                <span>Garansi Alat Steril</span>
                <span>|</span>
                <span>Pengiriman Seluruh Bandung</span>
            </div>
            <div class="flex gap-4">
                <a href="#" class="hover:text-white transition">Tentang Kami</a>
                <a href="#" class="hover:text-white transition">Bantuan</a>
                <a href="#" class="hover:text-brand-orange transition text-brand-orange font-bold">Download App</a>
            </div>
        </div>
    </div>

    <nav class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">

                <div class="flex-shrink-0 flex items-center cursor-pointer">
                    <div class="flex flex-col leading-none">
                        <span class="font-display font-bold text-2xl tracking-tighter text-black">MATTERHORN</span>
                        <span class="text-[0.6rem] tracking-[0.2em] text-gray-500 uppercase">Adventure Rental</span>
                    </div>
                </div>

                <div class="hidden md:flex space-x-8 items-center h-full">
                    <!-- Mega Menu Group -->
                    <div class="group h-full flex items-center">
                        <a href="#" class="text-sm font-bold uppercase tracking-wide hover:text-brand-orange transition border-b-2 border-transparent group-hover:border-brand-orange py-7 h-full flex items-center">Pria</a>

                        <!-- Mega Menu Dropdown -->
                        <div class="absolute top-full left-0 w-full bg-white border-b border-gray-200 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                                <h4 class="font-display font-bold text-lg mb-6 text-brand-black uppercase">Kategori</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide"><?php echo e($category); ?></span>
                                    </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <p class="text-sm text-gray-400 col-span-4">Belum ada kategori.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wanita Mega Menu Group -->
                    <div class="group h-full flex items-center">
                        <a href="#" class="text-sm font-bold uppercase tracking-wide hover:text-brand-orange transition border-b-2 border-transparent group-hover:border-brand-orange py-7 h-full flex items-center">Wanita</a>

                        <!-- Wanita Mega Menu Dropdown -->
                        <div class="absolute top-full left-0 w-full bg-white border-b border-gray-200 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                                <h4 class="font-display font-bold text-lg mb-6 text-brand-black uppercase">Koleksi Wanita</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">Jaket Wanita</span>
                                    </a>
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">Carrier Wanita</span>
                                    </a>
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">Sepatu Trail</span>
                                    </a>
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">Sleeping Bag</span>
                                    </a>
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">Celana Hiking</span>
                                    </a>
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">Topi & Aksesoris</span>
                                    </a>
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">Sarung Tangan</span>
                                    </a>
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">Kacamata Gunung</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Equipment Mega Menu Group -->
                    <div class="group h-full flex items-center">
                        <a href="#katalog" class="text-sm font-bold uppercase tracking-wide hover:text-brand-orange transition border-b-2 border-transparent group-hover:border-brand-orange py-7 h-full flex items-center">Equipment</a>

                        <!-- Equipment Mega Menu Dropdown -->
                        <div class="absolute top-full left-0 w-full bg-white border-b border-gray-200 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                                <h4 class="font-display font-bold text-lg mb-6 text-brand-black uppercase">Semua Equipment</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">Tenda</span>
                                    </a>
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">Carrier & Daypack</span>
                                    </a>
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">Sleeping Bag</span>
                                    </a>
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">Kompor & Memasak</span>
                                    </a>
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">Navigasi & GPS</span>
                                    </a>
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">Headlamp & Senter</span>
                                    </a>
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">Trekking Pole</span>
                                    </a>
                                    <a href="#" class="group/item flex items-center gap-2 hover:bg-gray-50 p-2 rounded transition-all">
                                        <span class="w-1 h-8 bg-gray-200 group-hover/item:bg-brand-orange transition-colors"></span>
                                        <span class="text-sm font-medium uppercase text-gray-600 group-hover/item:text-brand-black tracking-wide">Water Filter</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="#" class="text-sm font-bold uppercase tracking-wide text-brand-red hover:text-red-700 transition border-b-2 border-transparent hover:border-brand-red py-7 h-full flex items-center">Sale</a>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center relative group">
                        <input type="text" id="searchInput" class="w-0 opacity-0 transition-all duration-300 bg-gray-100 border-none focus:ring-1 focus:ring-brand-black text-sm h-10 px-0 outline-none" placeholder="Cari Carrier 60L...">
                        <button id="searchBtn" class="p-2 hover:text-brand-orange transition z-10 bg-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>

                    <button id="openCartBtn" class="relative p-2 hover:text-brand-orange transition" aria-label="Buka keranjang belanja">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span id="cartBadge" class="absolute top-0 right-0 h-4 w-4 bg-brand-orange text-white text-[10px] flex items-center justify-center font-bold rounded-full hidden">0</span>
                    </button>

                    <?php if(auth()->guard()->check()): ?>
                        <div class="hidden md:flex items-center gap-3">
                            <a href="<?php echo e(route('profile')); ?>" class="text-sm text-gray-600 hover:text-brand-orange transition">Halo, <strong class="text-brand-black"><?php echo e(Auth::user()->name); ?></strong></a>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="flex items-center gap-2 p-2 hover:text-brand-orange transition font-display font-medium uppercase text-sm border border-gray-300 px-4 py-2 hover:border-brand-orange">
                                    Logout
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="hidden md:flex items-center gap-2 p-2 hover:text-brand-orange transition font-display font-medium uppercase text-sm border border-gray-300 px-4 py-2 hover:border-brand-orange">
                            Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <section class="relative bg-gray-100 overflow-hidden group">
        <div class="relative w-full h-[300px] md:h-[500px] overflow-hidden">
            <div id="sliderTrack" class="flex w-full h-full slider-track">

                <div class="min-w-full relative">
                    <img src="https://images.unsplash.com/photo-1516939884455-1445c8652f83?q=80&w=2000&auto=format&fit=crop" class="w-full h-full object-cover brightness-75" alt="Camping Banner">
                    <div class="absolute inset-0 flex flex-col justify-center px-8 md:px-20 max-w-7xl mx-auto">
                        <span class="bg-brand-orange text-white text-xs font-bold px-2 py-1 w-fit mb-4 uppercase tracking-widest">Adventure Ready</span>
                        <h2 class="text-4xl md:text-6xl font-display font-bold text-white uppercase mb-4 leading-none">Taklukkan<br>Setiap Puncak</h2>
                        <p class="text-gray-200 mb-8 max-w-lg font-light">Sewa peralatan standar ekspedisi dengan harga terjangkau. Mulai dari Rp 25.000/hari.</p>
                        <a href="#katalog" class="bg-brand-orange text-white px-8 py-3 w-fit font-display font-bold uppercase tracking-wider hover:bg-orange-700 transition">Sewa Sekarang</a>
                    </div>
                </div>

                <div class="min-w-full relative">
                    <img src="images/man2.webp" class="w-full h-full object-cover brightness-75" alt="Sleeping Bag">
                    <div class="absolute inset-0 flex flex-col justify-center items-end px-8 md:px-20 max-w-7xl mx-auto text-right">
                        <h2 class="text-4xl md:text-6xl font-display font-bold text-white uppercase mb-4 leading-none">Nyaman<br>Di Alam Bebas</h2>
                        <p class="text-gray-200 mb-8 max-w-lg font-light">Koleksi Sleeping Bag & Tenda Ultralight terbaru tahun 2026.</p>
                        <a href="#katalog" class="bg-white text-black px-8 py-3 w-fit font-display font-bold uppercase tracking-wider hover:bg-gray-200 transition">Lihat Koleksi</a>
                    </div>
                </div>

            </div>

            <button id="prevBtn" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-brand-orange/90 text-white p-3 backdrop-blur-sm transition duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <button id="nextBtn" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-brand-orange/90 text-white p-3 backdrop-blur-sm transition duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="font-display font-bold text-2xl uppercase mb-6 flex items-center gap-2">
                <span class="w-1 h-6 bg-brand-orange block"></span>
                Kategori Populer
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <a href="#" class="group relative block h-40 overflow-hidden border border-gray-200">
                    <img src="images/tas_thumbnail.jpg" class="w-full h-full object-cover transition duration-500 group-hover:scale-110" alt="Bags">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex items-end justify-center p-3">
                        <span class="text-white font-display font-bold uppercase tracking-wider text-sm">Bags</span>
                    </div>
                </a>

                <a href="#" class="group relative block h-40 overflow-hidden border border-gray-200">
                    <img src="images/sepatu_thumbnail.webp" class="w-full h-full object-cover transition duration-500 group-hover:scale-110" alt="Sepatu">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex items-end justify-center p-3">
                        <span class="text-white font-display font-bold uppercase tracking-wider text-sm">Sepatu</span>
                    </div>
                </a>

                <a href="#" class="group relative block h-40 overflow-hidden border border-gray-200">
                    <img src="images/tenda_thumbnail.webp" class="w-full h-full object-cover transition duration-500 group-hover:scale-110" alt="Tenda">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex items-end justify-center p-3">
                        <span class="text-white font-display font-bold uppercase tracking-wider text-sm">Tenda</span>
                    </div>
                </a>

                <a href="#" class="group relative block h-40 overflow-hidden border border-gray-200">
                    <img src="images/jaket_thumbnail.webp" class="w-full h-full object-cover transition duration-500 group-hover:scale-110" alt="Apparel">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex items-end justify-center p-3">
                        <span class="text-white font-display font-bold uppercase tracking-wider text-sm">Apparel</span>
                    </div>
                </a>

                <a href="#" class="group relative block h-40 overflow-hidden border border-gray-200">
                    <img src="https://images.unsplash.com/photo-1583578768826-b8c281747805?auto=format&fit=crop&w=300&q=80" class="w-full h-full object-cover transition duration-500 group-hover:scale-110" alt="Cooking">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex items-end justify-center p-3">
                        <span class="text-white font-display font-bold uppercase tracking-wider text-sm">Cooking</span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <section class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-brand-orange to-red-600 rounded-sm p-6 md:p-10 text-white relative overflow-hidden flex flex-col md:flex-row items-center justify-between">
                <div class="absolute inset-0 opacity-10" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, #000 10px, #000 20px);"></div>

                <div class="relative z-10 mb-6 md:mb-0">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-white text-brand-orange font-bold text-xs px-2 py-1 uppercase tracking-widest">Flash Sale</span>
                        <span class="text-xs font-mono">Berakhir dalam 02:30:15</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-display font-bold uppercase leading-none">Diskon Member<br>Hingga 50%</h2>
                </div>

                <div class="relative z-10">
                    <a href="#" class="bg-black text-white px-8 py-3 border border-white font-display font-bold uppercase tracking-wider hover:bg-white hover:text-black transition">Cek Promo</a>
                </div>
            </div>
        </div>
    </section>

    <section id="katalog" class="py-12 bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filter -->
                <aside class="w-full lg:w-64 flex-shrink-0 hidden lg:block">
                    <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
                        <h3 class="font-display font-bold text-lg uppercase">Filter</h3>
                        <button class="text-xs text-brand-orange hover:underline font-bold" id="resetFilters">Reset</button>
                    </div>

                    <!-- Filter Groups -->
                    <div class="space-y-6">

                        <!-- Gender Filter -->
                        <div class="border-b border-gray-100 pb-6 filter-group">
                            <button class="flex justify-between items-center w-full group mb-4">
                                <span class="font-bold text-sm uppercase">Gender</span>
                                <svg class="w-4 h-4 transform transition duration-300 group-hover:text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="space-y-2 pl-2">
                                <label class="flex items-center space-x-3 cursor-pointer group">
                                    <input type="checkbox" class="form-checkbox h-4 w-4 text-brand-orange rounded border-gray-300 focus:ring-brand-orange">
                                    <span class="text-sm text-gray-600 group-hover:text-brand-orange transition">Pria (1065)</span>
                                </label>
                                <label class="flex items-center space-x-3 cursor-pointer group">
                                    <input type="checkbox" class="form-checkbox h-4 w-4 text-brand-orange rounded border-gray-300 focus:ring-brand-orange">
                                    <span class="text-sm text-gray-600 group-hover:text-brand-orange transition">Wanita (0)</span>
                                </label>
                                <label class="flex items-center space-x-3 cursor-pointer group">
                                    <input type="checkbox" class="form-checkbox h-4 w-4 text-brand-orange rounded border-gray-300 focus:ring-brand-orange">
                                    <span class="text-sm text-gray-600 group-hover:text-brand-orange transition">Unisex (74)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Ukuran Filter -->
                        <div class="border-b border-gray-100 pb-6 filter-group">
                            <button class="flex justify-between items-center w-full group mb-4">
                                <span class="font-bold text-sm uppercase">Ukuran</span>
                                <svg class="w-4 h-4 transform transition duration-300 group-hover:text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="grid grid-cols-2 gap-2">
                                <button class="border border-gray-200 text-xs py-2 hover:border-brand-orange hover:text-brand-orange transition">S</button>
                                <button class="border border-gray-200 text-xs py-2 hover:border-brand-orange hover:text-brand-orange transition">M</button>
                                <button class="border border-gray-200 text-xs py-2 hover:border-brand-orange hover:text-brand-orange transition">L</button>
                                <button class="border border-gray-200 text-xs py-2 hover:border-brand-orange hover:text-brand-orange transition">XL</button>
                                <button class="border border-gray-200 text-xs py-2 hover:border-brand-orange hover:text-brand-orange transition">XXL</button>
                                <button class="border border-gray-200 text-xs py-2 hover:border-brand-orange hover:text-brand-orange transition">38</button>
                                <button class="border border-gray-200 text-xs py-2 hover:border-brand-orange hover:text-brand-orange transition">40</button>
                                <button class="border border-gray-200 text-xs py-2 hover:border-brand-orange hover:text-brand-orange transition">42</button>
                            </div>
                        </div>

                        <!-- Harga Filter -->
                        <div class="border-b border-gray-100 pb-6 filter-group">
                            <button class="flex justify-between items-center w-full group mb-4">
                                <span class="font-bold text-sm uppercase">Harga</span>
                                <svg class="w-4 h-4 transform transition duration-300 group-hover:text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="space-y-3">
                                <div class="flex items-center gap-2">
                                    <input type="number" placeholder="Min" class="w-full border border-gray-200 p-2 text-xs outline-none focus:border-brand-orange transition">
                                    <span class="text-gray-400">-</span>
                                    <input type="number" placeholder="Max" class="w-full border border-gray-200 p-2 text-xs outline-none focus:border-brand-orange transition">
                                </div>
                                <button class="w-full bg-black text-white py-2 text-xs font-bold uppercase hover:bg-brand-orange transition">Terapkan</button>
                            </div>
                        </div>

                    </div>
                </aside>

                <!-- Product Grid Area -->
                <div class="flex-1">
                    <div class="flex justify-between items-end mb-8 border-b border-gray-300 pb-4">
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-500 mb-1">Menampilkan 5 Produk</span>
                            <h3 class="font-display font-bold text-2xl uppercase text-brand-black">Produk Terlaris</h3>
                        </div>
                        <div class="flex items-center gap-4">
                            <!-- Mobile Filter Toggle -->
                            <button class="lg:hidden flex items-center gap-2 text-sm font-bold uppercase border border-gray-300 px-4 py-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                </svg>
                                Filter
                            </button>

                            <select class="border-none bg-transparent text-sm font-bold uppercase cursor-pointer outline-none focus:ring-0 text-right">
                                <option>Terbaru</option>
                                <option>Termurah</option>
                                <option>Termahal</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <!-- Existing Product Skeleton & Container Logic will be injected here by JS -->
                        <div id="skeleton-container" class="contents">
                            <div class="bg-white p-3 border border-gray-200 animate-pulse">
                                <div class="bg-gray-200 h-48 w-full mb-3"></div>
                                <div class="bg-gray-200 h-4 w-3/4 mb-2"></div>
                                <div class="bg-gray-200 h-3 w-1/2 mb-4"></div>
                                <div class="bg-gray-200 h-6 w-1/2"></div>
                            </div>
                            <div class="bg-white p-3 border border-gray-200 animate-pulse">
                                <div class="bg-gray-200 h-48 w-full mb-3"></div>
                                <div class="bg-gray-200 h-4 w-3/4 mb-2"></div>
                                <div class="bg-gray-200 h-3 w-1/2 mb-4"></div>
                                <div class="bg-gray-200 h-6 w-1/2"></div>
                            </div>
                            <div class="bg-white p-3 border border-gray-200 animate-pulse">
                                <div class="bg-gray-200 h-48 w-full mb-3"></div>
                                <div class="bg-gray-200 h-4 w-3/4 mb-2"></div>
                                <div class="bg-gray-200 h-3 w-1/2 mb-4"></div>
                                <div class="bg-gray-200 h-6 w-1/2"></div>
                            </div>
                            <div class="bg-white p-3 border border-gray-200 animate-pulse">
                                <div class="bg-gray-200 h-48 w-full mb-3"></div>
                                <div class="bg-gray-200 h-4 w-3/4 mb-2"></div>
                                <div class="bg-gray-200 h-3 w-1/2 mb-4"></div>
                                <div class="bg-gray-200 h-6 w-1/2"></div>
                            </div>
                        </div>

                        <div id="product-container" class="contents hidden">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========= TESTIMONIAL SECTION ========= -->
    <section id="testimoni" class="py-20 bg-brand-black text-white relative overflow-hidden">
        <!-- Background texture -->
        <div class="absolute inset-0 opacity-5" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 30px, #FF5500 30px, #FF5500 31px);"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

            <!-- Section Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-brand-orange text-xs font-bold uppercase tracking-widest mb-2 block">Real Stories</span>
                    <h2 class="font-display font-bold text-4xl md:text-5xl uppercase leading-none">
                        Petualang <br class="hidden md:block"> Bicara
                    </h2>
                </div>
                <p class="text-gray-400 max-w-xs text-sm leading-relaxed">
                    Ribuan adventurer telah mempercayakan gear mereka ke Matterhorn. Ini kisah mereka.
                </p>
            </div>

            <!-- Testimonial Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Card 1 -->
                <div class="group relative bg-brand-dark border border-gray-700 hover:border-brand-orange transition-all duration-300 overflow-hidden flex flex-col">
                    <div class="relative h-56 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1533130061792-64b345e4a833?auto=format&fit=crop&w=600&q=80"
                            alt="Pendaki di Puncak"
                            class="w-full h-full object-cover brightness-75 group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark via-transparent to-transparent"></div>
                        <!-- Quote Icon -->
                        <div class="absolute top-4 right-4 w-10 h-10 bg-brand-orange flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                            </svg>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <!-- Stars -->
                        <div class="flex gap-1 mb-3">
                            <span class="text-brand-orange text-sm">★★★★★</span>
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed mb-5 flex-grow italic">
                            "Carrier Eiger yang aku sewa di Matterhorn beneran top. Nyaman banget pas summit attack ke Rinjani, dan kondisinya masih mulus. Harga sewa jauh lebih masuk akal daripada beli sendiri!"
                        </p>
                        <div class="flex items-center gap-3 border-t border-gray-700 pt-4">
                            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=80&q=80"
                                alt="Rizky A."
                                class="w-10 h-10 object-cover border-2 border-brand-orange">
                            <div>
                                <p class="font-display font-bold text-sm uppercase text-white">Rizky Aditya</p>
                                <p class="text-xs text-gray-500">Pendaki • Rinjani Expedition 2025</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2 (Featured - spans 2 rows on lg) -->
                <div class="group relative bg-brand-dark border border-brand-orange overflow-hidden flex flex-col lg:row-span-1">
                    <div class="relative h-56 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=600&q=80"
                            alt="Tenda di Gunung"
                            class="w-full h-full object-cover brightness-75 group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark via-transparent to-transparent"></div>
                        <span class="absolute top-4 left-4 bg-brand-orange text-white text-xs font-bold px-2 py-1 uppercase tracking-widest">Top Review</span>
                        <div class="absolute top-4 right-4 w-10 h-10 bg-brand-orange flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                            </svg>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex gap-1 mb-3">
                            <span class="text-brand-orange text-sm">★★★★★</span>
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed mb-5 flex-grow italic">
                            "Sudah 3x sewa tenda di Matterhorn buat trip ke Papandayan, Prau, sampai Sindoro. Kualitasnya nggak pernah mengecewakan. Pelayanannya cepat, alat steril, dan harga transparan. Definitely my go-to outdoor rental!"
                        </p>
                        <div class="flex items-center gap-3 border-t border-gray-700 pt-4">
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=80&q=80"
                                alt="Sari W."
                                class="w-10 h-10 object-cover border-2 border-brand-orange">
                            <div>
                                <p class="font-display font-bold text-sm uppercase text-white">Sari Wulandari</p>
                                <p class="text-xs text-gray-500">Hiker • Triple Summit 2025</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="group relative bg-brand-dark border border-gray-700 hover:border-brand-orange transition-all duration-300 overflow-hidden flex flex-col">
                    <div class="relative h-56 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=600&q=80"
                            alt="Hiking Trail"
                            class="w-full h-full object-cover brightness-75 group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark via-transparent to-transparent"></div>
                        <div class="absolute top-4 right-4 w-10 h-10 bg-brand-orange flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                            </svg>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex gap-1 mb-3">
                            <span class="text-brand-orange text-sm">★★★★★</span>
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed mb-5 flex-grow italic">
                            "Trekking pole carbon-nya ringan abis dan kokoh. Perfect buat terrain berbatu di jalur Semeru. Proses sewa online-nya mudah, dan barang datang tepat waktu sebelum keberangkatan."
                        </p>
                        <div class="flex items-center gap-3 border-t border-gray-700 pt-4">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=80&q=80"
                                alt="Bima R."
                                class="w-10 h-10 object-cover border-2 border-brand-orange">
                            <div>
                                <p class="font-display font-bold text-sm uppercase text-white">Bima Raharja</p>
                                <p class="text-xs text-gray-500">Backpacker • Semeru 3676m</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Stats Row -->
            <div class="mt-14 grid grid-cols-2 md:grid-cols-4 gap-0 border border-gray-700">
                <div class="flex flex-col items-center justify-center py-8 px-4 border-r border-gray-700 text-center">
                    <span class="font-display font-bold text-4xl text-brand-orange">4.9</span>
                    <span class="text-xs text-gray-500 uppercase tracking-wider mt-1">Rating Rata-rata</span>
                </div>
                <div class="flex flex-col items-center justify-center py-8 px-4 md:border-r border-gray-700 text-center">
                    <span class="font-display font-bold text-4xl text-white">2.4K+</span>
                    <span class="text-xs text-gray-500 uppercase tracking-wider mt-1">Pelanggan Puas</span>
                </div>
                <div class="flex flex-col items-center justify-center py-8 px-4 border-r border-gray-700 text-center border-t md:border-t-0">
                    <span class="font-display font-bold text-4xl text-white">98%</span>
                    <span class="text-xs text-gray-500 uppercase tracking-wider mt-1">Repeat Order</span>
                </div>
                <div class="flex flex-col items-center justify-center py-8 px-4 text-center border-t md:border-t-0">
                    <span class="font-display font-bold text-4xl text-white">5+</span>
                    <span class="text-xs text-gray-500 uppercase tracking-wider mt-1">Tahun Beroperasi</span>
                </div>
            </div>

        </div>
    </section>
    <!-- ========= END TESTIMONIAL ========= -->

    <footer class="bg-brand-black text-gray-400 py-16 border-t border-gray-800 border-t-4 border-brand-orange">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">

                <div class="col-span-1">
                    <span class="font-display font-bold text-2xl text-white tracking-tighter uppercase mb-4 block">Matterhorn</span>
                    <p class="text-sm leading-relaxed mb-6">
                        Penyedia peralatan outdoor premium no. 1 di Bandung. Kami menjamin kualitas, kebersihan, dan keamanan setiap alat yang Anda sewa.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-8 h-8 bg-gray-700 hover:bg-brand-orange flex items-center justify-center text-white transition"><i class="text-xs">IG</i></a>
                        <a href="#" class="w-8 h-8 bg-gray-700 hover:bg-brand-orange flex items-center justify-center text-white transition"><i class="text-xs">FB</i></a>
                        <a href="#" class="w-8 h-8 bg-gray-700 hover:bg-brand-orange flex items-center justify-center text-white transition"><i class="text-xs">YT</i></a>
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-display font-bold uppercase tracking-wider mb-6 text-sm">Bantuan</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="hover:text-brand-orange transition">Cara Menyewa</a></li>
                        <li><a href="#" class="hover:text-brand-orange transition">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-brand-orange transition">Kebijakan Denda</a></li>
                        <li><a href="#" class="hover:text-brand-orange transition">Konfirmasi Pembayaran</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-display font-bold uppercase tracking-wider mb-6 text-sm">Tentang Kami</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="hover:text-brand-orange transition">Lokasi Toko</a></li>
                        <li><a href="#" class="hover:text-brand-orange transition">Program Member</a></li>
                        <li><a href="#" class="hover:text-brand-orange transition">Karir</a></li>
                        <li><a href="#" class="hover:text-brand-orange transition">Hubungi Kami</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-display font-bold uppercase tracking-wider mb-6 text-sm">Berlangganan</h4>
                    <p class="text-xs mb-4">Dapatkan info promo terbaru.</p>
                    <div class="flex">
                        <input type="email" placeholder="Email Anda" class="w-full bg-brand-dark text-white px-4 py-2 text-sm outline-none border border-gray-700 focus:border-brand-orange">
                        <button class="bg-brand-orange text-white px-4 font-bold hover:bg-orange-700 transition">GO</button>
                    </div>
                </div>

            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center text-xs">
                <p>&copy; 2026 Matterhorn Outdoor Rental. All Rights Reserved.</p>
                <div class="flex gap-4 mt-4 md:mt-0">
                    <span>Privacy Policy</span>
                    <span>Terms of Service</span>
                </div>
            </div>
        </div>
    </footer>


    <button id="backToTop" class="fixed bottom-6 right-6 z-50 bg-brand-black text-white border border-brand-orange p-3 shadow-lg opacity-0 invisible transition-all duration-300 hover:bg-brand-orange hover:text-white transform translate-y-4 group">
        <svg class="w-6 h-6 group-hover:-translate-y-1 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            /* --- 1. SEARCH EXPAND LOGIC --- */
            const searchBtn = document.getElementById('searchBtn');
            const searchInput = document.getElementById('searchInput');
            let isSearchOpen = false;

            searchBtn.addEventListener('click', (e) => {
                if (!isSearchOpen) {
                    searchInput.classList.remove('w-0', 'opacity-0', 'px-0');
                    searchInput.classList.add('w-48', 'opacity-100', 'px-3');
                    searchInput.focus();
                    isSearchOpen = true;
                } else if (searchInput.value === '') {
                    searchInput.classList.add('w-0', 'opacity-0', 'px-0');
                    searchInput.classList.remove('w-48', 'opacity-100', 'px-3');
                    isSearchOpen = false;
                } else {
                    console.log('Searching for:', searchInput.value);
                }
            });

            /* --- 2. SLIDER LOGIC --- */
            const track = document.getElementById('sliderTrack');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            let currentSlide = 0;
            const totalSlides = 2; // Sesuaikan jumlah slide

            const updateSlide = () => {
                track.style.transform = `translateX(-${currentSlide * 100}%)`;
            };

            nextBtn.addEventListener('click', () => {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlide();
            });

            prevBtn.addEventListener('click', () => {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                updateSlide();
            });

            // Auto Slide every 5 seconds
            setInterval(() => {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlide();
            }, 5000);

            /* --- 3. PRODUCT LISTING via API --- */
            const skeletonContainer = document.getElementById('skeleton-container');
            const productContainer = document.getElementById('product-container');
            const formatRupiah = (num) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(num);

            const createCard = (p) => `
                <div class="group bg-white border border-gray-200 hover:border-brand-orange transition duration-300 relative flex flex-col">
                    ${p.tag ? `<span class="absolute top-0 left-0 bg-brand-orange text-white text-xs font-bold px-2 py-1 uppercase tracking-wider z-10">${p.tag}</span>` : ''}
                    
                    <a href="/product/${p.id}" class="w-full h-48 overflow-hidden bg-gray-100 relative block">
                        <img src="${p.image}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="${p.name}">
                        <div class="absolute bottom-0 left-0 w-full p-2 bg-white/90 translate-y-full group-hover:translate-y-0 transition duration-300 flex justify-center">
                            <span class="bg-black text-white text-xs uppercase font-bold px-4 py-2 hover:bg-brand-orange w-full text-center">Lihat Detail</span>
                        </div>
                    </a>
                    
                    <div class="p-4 flex flex-col flex-grow">
                        <span class="text-gray-500 text-xs font-bold uppercase mb-1">${p.category}</span>
                        <a href="/product/${p.id}" class="font-display font-bold text-sm text-brand-black uppercase leading-tight mb-2 group-hover:text-brand-orange transition cursor-pointer line-clamp-2">${p.name}</a>
                        <div class="mt-auto pt-2 border-t border-gray-100">
                            <p class="text-brand-orange font-bold text-sm">${formatRupiah(p.price)} <span class="text-gray-500 font-normal text-xs">/hari</span></p>
                        </div>
                    </div>
                </div>
            `;

            async function loadProducts() {
                try {
                    const res = await fetch('/api/products?per_page=12', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const json = await res.json();
                    const products = (json.data || []).map(p => ({
                        id: p.id,
                        name: p.name,
                        category: p.category,
                        price: p.price_24h,
                        image: p.image_url || 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=500&q=80',
                        tag: p.stock_available < 5 && p.stock_available > 0 ? 'STOK TERBATAS' : ''
                    }));

                    skeletonContainer.classList.add('hidden');
                    productContainer.classList.remove('hidden');

                    if (products.length === 0) {
                        productContainer.innerHTML = '<p class="col-span-full text-center text-gray-500 py-12">Belum ada produk tersedia.</p>';
                    } else {
                        products.forEach(p => productContainer.innerHTML += createCard(p));
                    }
                } catch (err) {
                    skeletonContainer.classList.add('hidden');
                    productContainer.classList.remove('hidden');
                    productContainer.innerHTML = '<p class="col-span-full text-center text-red-500 py-12">Gagal memuat produk.</p>';
                }
            }

            loadProducts();

            /* --- 4. SIDEBAR ACCORDION LOGIC --- */
            const filterGroups = document.querySelectorAll('.filter-group');

            filterGroups.forEach(group => {
                const button = group.querySelector('button');
                const content = group.querySelector('div');
                const icon = group.querySelector('svg');

                if (button && content) {
                    button.addEventListener('click', () => {
                        content.classList.toggle('hidden');
                        icon.classList.toggle('rotate-180');
                    });
                }
            });

            /* --- 5. RESET FILTERS --- */
            const resetBtn = document.getElementById('resetFilters');
            if (resetBtn) {
                resetBtn.addEventListener('click', () => {
                    // Uncheck checkboxes
                    document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
                    // Reset inputs
                    document.querySelectorAll('input[type="number"]').forEach(inpt => inpt.value = '');
                });
            }
        });

        /* --- 4. BACK TO TOP LOGIC --- */
        const backToTopBtn = document.getElementById('backToTop');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                // Munculkan Tombol
                backToTopBtn.classList.remove('opacity-0', 'invisible', 'translate-y-4');
                backToTopBtn.classList.add('opacity-100', 'visible', 'translate-y-0');
            } else {
                // Sembunyikan Tombol
                backToTopBtn.classList.add('opacity-0', 'invisible', 'translate-y-4');
                backToTopBtn.classList.remove('opacity-100', 'visible', 'translate-y-0');
            }
        });

        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>

    <div class="fixed bottom-6 right-6 md:right-24 z-50 flex flex-col items-end gap-4 font-sans" id="aiChatWidget">

        <div id="chatBox" class="hidden w-[340px] md:w-[380px] bg-white rounded-xl shadow-2xl flex flex-col transition-all duration-500 ease-in-out transform translate-y-10 opacity-0 overflow-hidden border border-gray-200" style="height: 550px; max-height: 80vh;">

            <div class="bg-brand-black text-white p-4 flex justify-between items-center select-none flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center overflow-hidden">
                        <img src="<?php echo e(asset('images/matterhorn.png')); ?>" alt="M" class="w-5 h-5 object-contain">
                    </div>
                    <div>
                        <h4 class="font-bold text-sm tracking-wide">Matterhorn Care</h4>
                        <div class="flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                            <p class="text-xs text-gray-500">Online</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button class="text-gray-400 hover:text-white transition" id="closeChatBtn">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div id="chatRegistration" class="flex-1 p-6 md:p-8 flex flex-col justify-center bg-white overflow-y-auto">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                    </div>
                    <h5 class="font-bold text-black text-lg mb-1">Halo, Petualang!</h5>
                    <p class="text-xs text-gray-500 leading-relaxed">Isi data diri sebentar ya, biar kami tahu harus panggil Kakak siapa.</p>
                </div>

                <form id="chatRegForm" class="space-y-3">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" id="regFirstName" placeholder="Nama Depan" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs focus:bg-white focus:border-black focus:ring-0 outline-none transition" required>
                        <input type="text" id="regLastName" placeholder="Belakang" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs focus:bg-white focus:border-black focus:ring-0 outline-none transition" required>
                    </div>
                    <input type="email" id="regEmail" placeholder="Email Aktif" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs focus:bg-white focus:border-black focus:ring-0 outline-none transition" required>
                    <button type="submit" class="w-full bg-brand-black text-white font-bold py-3 rounded-lg text-xs uppercase tracking-wider hover:bg-gray-800 transition shadow-lg mt-2">
                        Mulai Chatting
                    </button>
                </form>
            </div>

            <div id="chatInterface" class="hidden flex-1 flex flex-col bg-gray-50 relative min-h-0">

                <div id="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-4 min-h-0">
                    <div class="text-center mt-2">
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Hari ini</span>
                    </div>

                    <div class="flex items-end gap-2">
                        <div class="w-6 h-6 bg-brand-black rounded-full flex-shrink-0 flex items-center justify-center text-white text-[10px] font-bold">M</div>
                        <div class="bg-white p-3 rounded-2xl rounded-bl-none shadow-sm text-sm text-gray-700 max-w-[85%] border border-gray-100">
                            Halo! Selamat datang di Matterhorn Care. Ada yang bisa dibantu soal peralatan camping?
                        </div>
                    </div>
                </div>

                <div id="chatLoading" class="hidden px-5 pb-2 pl-10">
                    <div class="bg-gray-200 p-2 rounded-2xl rounded-bl-none w-10 flex items-center justify-center gap-1">
                        <span class="w-1 h-1 bg-gray-400 rounded-full animate-bounce"></span>
                        <span class="w-1 h-1 bg-gray-400 rounded-full animate-bounce delay-100"></span>
                        <span class="w-1 h-1 bg-gray-400 rounded-full animate-bounce delay-200"></span>
                    </div>
                </div>

                <div class="p-3 bg-brand-black flex-shrink-0">
                    <div class="relative">
                        <input type="text" id="userMessageInput" class="w-full bg-white text-black pl-4 pr-12 py-3 rounded-full text-sm outline-none focus:ring-2 focus:ring-gray-500 transition" placeholder="Tulis pesan..." autocomplete="off">
                        <button id="sendMessageBtn" class="absolute right-1 top-1/2 -translate-y-1/2 w-8 h-8 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition">
                            <svg class="w-4 h-4 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="text-center mt-1">
                        <p class="text-[9px] text-gray-500">Powered by Matterhorn AI</p>
                    </div>
                </div>
            </div>
        </div>

        <button id="toggleChatBtn" class="bg-brand-black text-white flex items-center gap-3 px-6 py-3.5 rounded-full shadow-2xl hover:bg-gray-900 transition-all duration-300 transform hover:scale-105 group border border-gray-800">
            <div class="relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 border-2 border-brand-black rounded-full animate-pulse"></span>
            </div>
            <span class="font-medium text-sm tracking-wide">Chat</span>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const toggleBtn = document.getElementById('toggleChatBtn');
            const chatBox = document.getElementById('chatBox');
            const closeBtn = document.getElementById('closeChatBtn');
            const regForm = document.getElementById('chatRegForm');
            const regView = document.getElementById('chatRegistration');
            const chatView = document.getElementById('chatInterface');
            const messageInput = document.getElementById('userMessageInput');
            const sendBtn = document.getElementById('sendMessageBtn');
            const messageArea = document.getElementById('chatMessages');
            const loadingIndicator = document.getElementById('chatLoading');

            // State & LocalStorage
            let isChatOpen = false;
            let userData = null;
            let chatHistory = [];

            try {
                userData = JSON.parse(localStorage.getItem('matterhorn_user'));
                chatHistory = JSON.parse(localStorage.getItem('matterhorn_history')) || [];
            } catch (e) {
                console.log('Resetting local storage due to error');
                localStorage.removeItem('matterhorn_user');
                localStorage.removeItem('matterhorn_history');
            }

            // Helper: Scroll ke bawah
            const scrollToBottom = () => {
                if (messageArea) messageArea.scrollTop = messageArea.scrollHeight;
            }

            // 1. CEK USER DATA
            // Jika sudah pernah isi nama, langsung ke kolom chat
            if (userData) {
                regView.classList.add('hidden');
                chatView.classList.remove('hidden');
                renderHistory();
            }

            // 2. TOGGLE BUKA/TUTUP
            toggleBtn.addEventListener('click', () => {
                chatBox.classList.remove('hidden');
                setTimeout(() => {
                    chatBox.classList.remove('translate-y-10', 'opacity-0');
                }, 10);
                toggleBtn.classList.add('hidden'); // Sembunyikan tombol trigger
                isChatOpen = true;
                if (userData) setTimeout(scrollToBottom, 100);
            });

            closeBtn.addEventListener('click', () => {
                chatBox.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => {
                    chatBox.classList.add('hidden');
                    toggleBtn.classList.remove('hidden'); // Munculkan tombol trigger lagi
                }, 300);
                isChatOpen = false;
            });

            // 3. LOGIC REGISTRASI (Isi Form -> Muncul Chat)
            regForm.addEventListener('submit', (e) => {
                e.preventDefault();
                userData = {
                    firstName: document.getElementById('regFirstName').value,
                    lastName: document.getElementById('regLastName').value,
                    email: document.getElementById('regEmail').value,
                };
                localStorage.setItem('matterhorn_user', JSON.stringify(userData));

                // Efek ganti halaman
                regView.classList.add('hidden');
                chatView.classList.remove('hidden');
                scrollToBottom();
            });

            // 4. KIRIM PESAN KE LARAVEL
            const sendMessage = async () => {
                const text = messageInput.value.trim();
                if (!text) return;

                // UI: Tampilkan pesan user
                appendMessage('user', text);
                messageInput.value = '';

                chatHistory.push({
                    role: 'user',
                    content: text
                });
                localStorage.setItem('matterhorn_history', JSON.stringify(chatHistory));

                loadingIndicator.classList.remove('hidden');
                scrollToBottom();

                try {
                    // Ambil CSRF Token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    if (!csrfToken) {
                        throw new Error('CSRF Token not found');
                    }

                    const response = await fetch("<?php echo e(route('ai.chat.send')); ?>", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            message: text,
                            history: chatHistory.slice(-5)
                        })
                    });

                    const data = await response.json();
                    loadingIndicator.classList.add('hidden');

                    if (data.choices && data.choices[0]) {
                        const aiText = data.choices[0].message.content;
                        appendMessage('ai', aiText);
                        chatHistory.push({
                            role: 'assistant',
                            content: aiText
                        });
                        localStorage.setItem('matterhorn_history', JSON.stringify(chatHistory));
                    } else {
                        appendMessage('system', 'Maaf, server AI sedang sibuk.');
                    }

                } catch (error) {
                    loadingIndicator.classList.add('hidden');
                    console.error(error);
                    appendMessage('system', 'Gagal terhubung. Cek koneksi / Refresh halaman.');
                }
            };

            sendBtn.addEventListener('click', sendMessage);
            messageInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') sendMessage();
            });

            // RENDER BUBBLE CHAT
            function appendMessage(role, text) {
                const div = document.createElement('div');

                if (role === 'user') {
                    div.className = 'flex justify-end animate-fade-in-up';
                    div.innerHTML = `
                    <div class="bg-brand-black text-white px-4 py-3 rounded-2xl rounded-br-none text-sm max-w-[80%] shadow-md">
                        ${text}
                    </div>
                `;
                } else if (role === 'ai') {
                    div.className = 'flex items-end gap-2 animate-fade-in-up';
                    div.innerHTML = `
                    <div class="w-6 h-6 bg-white border border-gray-200 rounded-full flex-shrink-0 flex items-center justify-center overflow-hidden">
                        <img src="<?php echo e(asset('images/matterhorn.png')); ?>" class="w-4 h-4 object-contain">
                    </div>
                    <div class="bg-white p-3.5 rounded-2xl rounded-bl-none shadow-sm text-sm text-gray-800 max-w-[85%] border border-gray-100 leading-relaxed">
                        ${text}
                    </div>
                `;
                } else {
                    div.className = 'text-center text-xs text-red-600 my-2';
                    div.innerText = text;
                }

                messageArea.appendChild(div);
                scrollToBottom();
            }

            function renderHistory() {
                chatHistory.forEach(msg => {
                    if (msg.role === 'user') appendMessage('user', msg.content);
                    else if (msg.role === 'assistant') appendMessage('ai', msg.content);
                });
                scrollToBottom();
            }
        });

        const style = document.createElement('style');
        style.innerHTML = `
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-up { animation: fadeInUp 0.3s ease-out forwards; }
    `;
        document.head.appendChild(style);
    </script>

    <!-- Cart Drawer Overlay -->
    <div id="cartOverlay" class="fixed inset-0 bg-black/50 z-[60] hidden opacity-0 transition-opacity duration-300" aria-hidden="true"></div>

    <!-- Cart Drawer -->
    <aside id="cartDrawer" class="fixed top-0 right-0 h-full w-full max-w-md bg-white z-[60] shadow-2xl transform translate-x-full transition-transform duration-300 flex flex-col" role="dialog" aria-label="Keranjang Belanja" aria-hidden="true">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h2 class="font-display font-bold text-lg uppercase tracking-wide">Keranjang Belanja</h2>
            <button id="closeCartDrawer" class="text-gray-400 hover:text-brand-black transition p-1" aria-label="Tutup keranjang">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div id="cartContent" class="flex-1 overflow-y-auto px-6 py-4">
            <div id="cartLoading" class="flex items-center justify-center py-12">
                <svg class="w-8 h-8 animate-spin text-brand-orange" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            </div>
            <div id="cartEmpty" class="hidden text-center py-16">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                <p class="text-gray-500 font-medium mb-2">Keranjang Kosong</p>
                <p class="text-gray-400 text-sm">Belum ada peralatan yang ditambahkan.</p>
            </div>
            <div id="cartItems" class="space-y-4 hidden"></div>
        </div>
        <div id="cartFooter" class="hidden border-t border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm text-gray-600">Total Harga</span>
                <span id="cartTotal" class="font-display font-bold text-xl text-brand-black">Rp 0</span>
            </div>
            <a href="/cart" class="block w-full bg-brand-black text-white font-display font-bold uppercase tracking-wider py-3.5 hover:bg-brand-orange transition-colors duration-300 text-center">
                Lihat Keranjang
            </a>
        </div>
    </aside>

    <!-- Toast -->
    <div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[70] bg-brand-black text-white px-6 py-3 rounded shadow-xl flex items-center gap-3 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
        <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span id="toastMsg" class="text-sm font-medium"></span>
    </div>

    <script>
        // ─── Cart Drawer Logic ────────────────────────────
        (function() {
            const overlay = document.getElementById('cartOverlay');
            const drawer = document.getElementById('cartDrawer');
            const openBtn = document.getElementById('openCartBtn');
            const closeBtn = document.getElementById('closeCartDrawer');

            function openCart() {
                overlay.classList.remove('hidden');
                drawer.setAttribute('aria-hidden', 'false');
                requestAnimationFrame(() => {
                    overlay.classList.remove('opacity-0');
                    drawer.classList.remove('translate-x-full');
                });
                loadCart();
                document.body.style.overflow = 'hidden';
            }

            function closeCart() {
                overlay.classList.add('opacity-0');
                drawer.classList.add('translate-x-full');
                drawer.setAttribute('aria-hidden', 'true');
                setTimeout(() => overlay.classList.add('hidden'), 300);
                document.body.style.overflow = '';
            }

            openBtn.addEventListener('click', openCart);
            closeBtn.addEventListener('click', closeCart);
            overlay.addEventListener('click', closeCart);
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !overlay.classList.contains('hidden')) closeCart(); });

            window.loadCart = async function() {
                const loading = document.getElementById('cartLoading');
                const empty = document.getElementById('cartEmpty');
                const items = document.getElementById('cartItems');
                const footer = document.getElementById('cartFooter');
                loading.classList.remove('hidden'); empty.classList.add('hidden'); items.classList.add('hidden'); footer.classList.add('hidden');
                try {
                    const res = await fetch('/api/cart', { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    loading.classList.add('hidden');
                    if (!data.data.items || data.data.items.length === 0) { empty.classList.remove('hidden'); return; }
                    items.innerHTML = data.data.items.map(item => `
                        <div class="flex gap-4 p-4 border border-gray-200">
                            <div class="w-20 h-20 bg-gray-100 flex-shrink-0 overflow-hidden">
                                ${item.product?.image_url ? `<img src="${item.product.image_url}" alt="${item.product?.name}" class="w-full h-full object-cover">` : '<div class="w-full h-full flex items-center justify-center text-gray-300"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>'}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-display font-bold text-sm uppercase truncate">${item.product?.name || 'Produk'}</h4>
                                <p class="text-brand-orange font-bold text-sm mt-1">Rp ${Number(item.subtotal).toLocaleString('id-ID')}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <div class="flex items-center border border-gray-300 text-xs">
                                        <button onclick="updateCartQty('${item.id}', ${item.quantity - 1})" class="px-2 py-1 hover:bg-gray-100">−</button>
                                        <span class="px-3 py-1 border-x border-gray-300 font-medium">${item.quantity}</span>
                                        <button onclick="updateCartQty('${item.id}', ${item.quantity + 1})" class="px-2 py-1 hover:bg-gray-100">+</button>
                                    </div>
                                    <button onclick="removeCartItem('${item.id}')" class="text-gray-400 hover:text-brand-red transition text-xs flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('');
                    items.classList.remove('hidden');
                    footer.classList.remove('hidden');
                    document.getElementById('cartTotal').textContent = 'Rp ' + Number(data.data.total_price).toLocaleString('id-ID');
                    updateCartBadge(data.data.total_items);
                } catch(e) { loading.classList.add('hidden'); }
            };

            window.updateCartQty = async function(itemId, qty) {
                if (qty < 1) return removeCartItem(itemId);
                await fetch(`/api/cart/${itemId}`, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: JSON.stringify({ quantity: qty }) });
                loadCart();
            };

            window.removeCartItem = async function(itemId) {
                await fetch(`/api/cart/${itemId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
                loadCart();
            };

            window.updateCartBadge = function(count) {
                const badge = document.getElementById('cartBadge');
                if (count > 0) { badge.textContent = count; badge.classList.remove('hidden'); } else { badge.classList.add('hidden'); }
            };

            window.showToast = function(msg) {
                const toast = document.getElementById('toast');
                document.getElementById('toastMsg').textContent = msg;
                toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
                setTimeout(() => toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none'), 3000);
            };

            // Init badge
            fetch('/api/cart', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json()).then(d => updateCartBadge(d.data?.total_items || 0)).catch(() => {});

            // Show flash messages from login/register
            const _flashMsg = '<?php echo e(session("success") ?? ""); ?>';
            if (_flashMsg) showToast(_flashMsg);
        })();
    </script>
</body>

</html><?php /**PATH C:\MyWork\Jurusan\WEBSITE\code\laravel\tent\resources\views/landing.blade.php ENDPATH**/ ?>