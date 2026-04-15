<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($product->name); ?> | Matterhorn Adventure Rental</title>

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
</head>

<body class="bg-white text-brand-black antialiased font-sans" data-product-stock="<?php echo e($product->stock_available); ?>">

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="flex flex-col leading-none">
                    <span class="font-display font-bold text-xl tracking-tighter text-black">MATTERHORN</span>
                    <span class="text-[0.5rem] tracking-[0.2em] text-gray-500 uppercase">Adventure Rental</span>
                </a>
                <div class="flex items-center gap-4">
                    <a href="/" class="text-sm font-medium text-gray-600 hover:text-brand-orange transition">← Kembali</a>
                    <button id="openCartBtn" class="relative p-2 hover:text-brand-orange transition" aria-label="Buka keranjang belanja">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span id="cartBadge" class="absolute -top-1 -right-1 h-5 w-5 bg-brand-orange text-white text-xs flex items-center justify-center font-bold rounded-full hidden">0</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!-- Breadcrumb -->
        <nav class="mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2 text-sm text-gray-500">
                <li><a href="/" class="hover:text-brand-orange transition">Beranda</a></li>
                <li><span class="text-gray-300">/</span></li>
                <li><a href="/#katalog" class="hover:text-brand-orange transition"><?php echo e($product->category); ?></a></li>
                <li><span class="text-gray-300">/</span></li>
                <li class="text-brand-black font-medium truncate max-w-[200px]" aria-current="page"><?php echo e($product->name); ?></li>
            </ol>
        </nav>

        <!-- Product Detail Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16">

            <!-- LEFT: Product Image -->
            <div>
                <div class="bg-gray-100 aspect-square flex items-center justify-center overflow-hidden group relative">
                    <?php if($product->image): ?>
                        <img id="mainImage" src="<?php echo e(asset($product->image)); ?>" alt="<?php echo e($product->name); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <?php else: ?>
                        <div class="text-gray-400 text-center">
                            <svg class="w-20 h-20 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm">Gambar tidak tersedia</p>
                        </div>
                    <?php endif; ?>

                    <?php if($product->stock_available < 5 && $product->stock_available > 0): ?>
                        <span class="absolute top-4 left-4 bg-brand-orange text-white text-xs font-bold px-3 py-1 uppercase tracking-wider">Stok Terbatas</span>
                    <?php elseif($product->stock_available <= 0): ?>
                        <span class="absolute top-4 left-4 bg-brand-red text-white text-xs font-bold px-3 py-1 uppercase tracking-wider">Habis</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- RIGHT: Product Info -->
            <div class="flex flex-col">

                <!-- Tags -->
                <div class="flex items-center gap-2 mb-3">
                    <span class="border border-gray-300 text-gray-600 text-xs font-bold uppercase tracking-wider px-3 py-1"><?php echo e(ucfirst($product->gender ?? 'Unisex')); ?></span>
                    <span class="border border-gray-300 text-gray-600 text-xs font-bold uppercase tracking-wider px-3 py-1"><?php echo e($product->category); ?></span>
                </div>

                <!-- Name -->
                <h1 class="font-display font-bold text-2xl sm:text-3xl uppercase text-brand-black leading-tight mb-4"><?php echo e($product->name); ?></h1>

                <!-- Price -->
                <div class="mb-4">
                    <p class="text-brand-orange font-bold text-2xl sm:text-3xl font-display">
                        Rp <?php echo e(number_format($product->price_24h, 0, ',', '.')); ?>

                        <span class="text-gray-500 font-normal text-sm font-sans">/hari</span>
                    </p>
                </div>

                <!-- Stock -->
                <div class="flex items-center gap-2 mb-6">
                    <span>Stok:</span>
                    <?php if($product->stock_available > 0): ?>
                        <span class="flex items-center gap-1 text-green-600 font-medium text-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                            <?php echo e($product->stock_available); ?> tersedia
                        </span>
                    <?php else: ?>
                        <span class="flex items-center gap-1 text-red-500 font-medium text-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                            Habis
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Sizes -->
                <?php if($product->sizes->count() > 0): ?>
                <div class="mb-6">
                    <label class="block text-sm font-bold uppercase tracking-wider text-gray-700 mb-3">Ukuran</label>
                    <div class="flex flex-wrap gap-2" id="sizeSelector" role="radiogroup" aria-label="Pilih ukuran">
                        <?php $__currentLoopData = $product->sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button
                                type="button"
                                data-size="<?php echo e($size->size); ?>"
                                data-stock="<?php echo e($size->stock); ?>"
                                class="size-btn border-2 border-gray-300 text-sm font-medium px-5 py-2.5 hover:border-brand-orange hover:text-brand-orange transition focus:outline-none focus:ring-2 focus:ring-brand-orange focus:ring-offset-1 <?php echo e($size->stock <= 0 ? 'opacity-40 cursor-not-allowed line-through' : 'cursor-pointer'); ?>"
                                role="radio"
                                aria-checked="false"
                                aria-label="Ukuran <?php echo e($size->size); ?><?php echo e($size->stock <= 0 ? ' - Habis' : ''); ?>"
                                <?php echo e($size->stock <= 0 ? 'disabled' : ''); ?>

                            >
                                <?php echo e($size->size); ?>

                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Quantity -->
                <div class="mb-6">
                    <label class="block text-sm font-bold uppercase tracking-wider text-gray-700 mb-3">Jumlah</label>
                    <div class="flex items-center border border-gray-300 w-fit">
                        <button id="qtyMinus" class="px-4 py-2.5 text-gray-600 hover:text-brand-orange hover:bg-gray-50 transition text-lg font-bold" aria-label="Kurangi jumlah">−</button>
                        <input id="qtyInput" type="number" value="1" min="1" max="<?php echo e($product->stock_available); ?>" class="w-14 text-center text-sm font-medium outline-none border-x border-gray-300 py-2.5" aria-label="Jumlah item">
                        <button id="qtyPlus" class="px-4 py-2.5 text-gray-600 hover:text-brand-orange hover:bg-gray-50 transition text-lg font-bold" aria-label="Tambah jumlah">+</button>
                    </div>
                </div>

                <!-- Add to Cart -->
                <button
                    id="addToCartBtn"
                    class="w-full bg-brand-black text-white font-display font-bold uppercase tracking-wider py-4 hover:bg-brand-orange transition-colors duration-300 flex items-center justify-center gap-3 relative overflow-hidden group disabled:opacity-50 disabled:cursor-not-allowed"
                    <?php echo e($product->stock_available <= 0 ? 'disabled' : ''); ?>

                    data-product-id="<?php echo e($product->id); ?>"
                    aria-label="Tambahkan <?php echo e($product->name); ?> ke keranjang"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <span id="addToCartText">Tambah ke Keranjang</span>
                    <div class="absolute inset-0 bg-white/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-500 skew-x-12"></div>
                </button>

                <!-- Info Badges -->
                <div class="mt-6 space-y-3 border-t border-gray-200 pt-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Pengiriman Area Bandung</p>
                            <p class="text-xs text-gray-500">Gratis antar-jemput minimal 3 hari sewa</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Garansi Alat Steril</p>
                            <p class="text-xs text-gray-500">Semua peralatan dicuci & disterilkan sebelum dikirim</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Product Description -->
        <section class="mt-12 border-t border-gray-200 pt-10">
            <h2 class="font-display font-bold text-xl uppercase mb-6 flex items-center gap-3">
                <span class="w-1 h-6 bg-brand-orange block"></span>
                Tentang Produk
            </h2>
            <?php if($product->description): ?>
                <div class="text-gray-600 text-sm leading-relaxed max-w-3xl whitespace-pre-line"><?php echo e($product->description); ?></div>
            <?php else: ?>
                <p class="text-gray-400 text-sm italic">Deskripsi belum tersedia untuk produk ini.</p>
            <?php endif; ?>

            <!-- Product Meta -->
            <div class="mt-8 border-t border-gray-200 pt-6 max-w-lg">
                <table class="w-full text-sm" role="presentation">
                    <tbody>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 text-gray-500 font-medium w-1/3">Kategori</td>
                            <td class="py-3 text-brand-black font-medium text-right"><?php echo e($product->category); ?></td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 text-gray-500 font-medium">Gender</td>
                            <td class="py-3 text-brand-black font-medium text-right"><?php echo e(ucfirst($product->gender ?? 'Unisex')); ?></td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 text-gray-500 font-medium">Stok Total</td>
                            <td class="py-3 text-brand-black font-medium text-right"><?php echo e($product->stock_total); ?> unit</td>
                        </tr>
                        <tr>
                            <td class="py-3 text-gray-500 font-medium">ID Produk</td>
                            <td class="py-3 text-brand-black font-medium text-right">#<?php echo e($product->id); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <!-- Cart Drawer Overlay -->
    <div id="cartOverlay" class="fixed inset-0 bg-black/50 z-50 hidden opacity-0 transition-opacity duration-300" aria-hidden="true"></div>

    <!-- Cart Drawer -->
    <aside id="cartDrawer" class="fixed top-0 right-0 h-full w-full max-w-md bg-white z-50 shadow-2xl transform translate-x-full transition-transform duration-300 flex flex-col" role="dialog" aria-label="Keranjang Belanja" aria-hidden="true">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h2 class="font-display font-bold text-lg uppercase tracking-wide">Keranjang Belanja</h2>
            <button id="closeCartBtn" class="text-gray-400 hover:text-brand-black transition p-1" aria-label="Tutup keranjang">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Cart Content -->
        <div id="cartContent" class="flex-1 overflow-y-auto px-6 py-4">
            <!-- Loading -->
            <div id="cartLoading" class="flex items-center justify-center py-12">
                <svg class="w-8 h-8 animate-spin text-brand-orange" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
            <!-- Empty State -->
            <div id="cartEmpty" class="hidden text-center py-16">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <p class="text-gray-500 font-medium mb-2">Keranjang Kosong</p>
                <p class="text-gray-400 text-sm mb-6">Belum ada peralatan yang ditambahkan.</p>
                <a href="/#katalog" class="text-brand-orange font-bold text-sm hover:underline">Lihat Katalog →</a>
            </div>
            <!-- Items list injected here -->
            <div id="cartItems" class="space-y-4 hidden"></div>
        </div>

        <!-- Footer -->
        <div id="cartFooter" class="hidden border-t border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm text-gray-600">Total Harga</span>
                <span id="cartTotal" class="font-display font-bold text-xl text-brand-black">Rp 0</span>
            </div>
            <button class="w-full bg-brand-black text-white font-display font-bold uppercase tracking-wider py-3.5 hover:bg-brand-orange transition-colors duration-300 flex items-center justify-center gap-2" disabled>
                <span>Checkout</span>
                <span id="cartCheckoutCount" class="bg-white/20 text-xs px-2 py-0.5 rounded">(0)</span>
            </button>
            <p class="text-xs text-gray-500 text-center mt-2">Fitur checkout sedang dalam pengembangan</p>
        </div>
    </aside>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-brand-black text-white px-6 py-3 rounded shadow-xl flex items-center gap-3 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
        <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span id="toastMsg" class="text-sm font-medium"></span>
    </div>

    <script>
        // ─── Cart Drawer ───────────────────────────────────
        const overlay = document.getElementById('cartOverlay');
        const drawer = document.getElementById('cartDrawer');
        const openBtn = document.getElementById('openCartBtn');
        const closeBtn = document.getElementById('closeCartBtn');

        function openCart() {
            overlay.classList.remove('hidden');
            drawer.setAttribute('aria-hidden', 'false');
            overlay.setAttribute('aria-hidden', 'false');
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
            overlay.setAttribute('aria-hidden', 'true');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
            document.body.style.overflow = '';
        }

        openBtn.addEventListener('click', openCart);
        closeBtn.addEventListener('click', closeCart);
        overlay.addEventListener('click', closeCart);

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !overlay.classList.contains('hidden')) closeCart();
        });

        // ─── Size Selector ──────────────────────────────────
        document.querySelectorAll('.size-btn:not([disabled])').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.size-btn').forEach(b => {
                    b.classList.remove('border-brand-orange', 'text-brand-orange', 'bg-orange-50');
                    b.setAttribute('aria-checked', 'false');
                });
                this.classList.add('border-brand-orange', 'text-brand-orange', 'bg-orange-50');
                this.setAttribute('aria-checked', 'true');
            });
        });

        // ─── Quantity Controls ───────────────────────────────
        const qtyInput = document.getElementById('qtyInput');
        const maxQty = parseInt(document.body.dataset.productStock) || 0;

        document.getElementById('qtyMinus').addEventListener('click', () => {
            const v = parseInt(qtyInput.value);
            if (v > 1) qtyInput.value = v - 1;
        });

        document.getElementById('qtyPlus').addEventListener('click', () => {
            const v = parseInt(qtyInput.value);
            if (v < maxQty) qtyInput.value = v + 1;
        });

        // ─── Add to Cart ─────────────────────────────────────
        document.getElementById('addToCartBtn').addEventListener('click', async function() {
            const btn = this;
            const text = document.getElementById('addToCartText');
            const productId = btn.dataset.productId;
            const quantity = parseInt(qtyInput.value);

            btn.disabled = true;
            text.textContent = 'Menambahkan...';

            try {
                const res = await fetch('/api/cart', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ product_id: parseInt(productId), quantity }),
                });

                const data = await res.json();

                if (res.ok) {
                    showToast(data.message || 'Ditambahkan ke keranjang!');
                    updateCartBadge(data.data.total_items);
                    text.textContent = '✓ Ditambahkan!';
                    setTimeout(() => {
                        text.textContent = 'Tambah ke Keranjang';
                        btn.disabled = false;
                    }, 1500);
                } else {
                    showToast(data.message || 'Gagal menambahkan', true);
                    text.textContent = 'Tambah ke Keranjang';
                    btn.disabled = false;
                }
            } catch (err) {
                showToast('Terjadi kesalahan jaringan.', true);
                text.textContent = 'Tambah ke Keranjang';
                btn.disabled = false;
            }
        });

        // ─── Load Cart ─────────────────────────────────────
        async function loadCart() {
            const loading = document.getElementById('cartLoading');
            const empty = document.getElementById('cartEmpty');
            const items = document.getElementById('cartItems');
            const footer = document.getElementById('cartFooter');

            loading.classList.remove('hidden');
            empty.classList.add('hidden');
            items.classList.add('hidden');
            footer.classList.add('hidden');

            try {
                const res = await fetch('/api/cart', {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();

                loading.classList.add('hidden');

                if (!data.data.items || data.data.items.length === 0) {
                    empty.classList.remove('hidden');
                    return;
                }

                items.innerHTML = data.data.items.map(item => `
                    <div class="flex gap-4 p-4 border border-gray-200 hover:border-gray-300 transition" data-item-id="${item.id}">
                        <div class="w-20 h-20 bg-gray-100 flex-shrink-0 overflow-hidden">
                            ${item.product?.image_url
                                ? `<img src="${item.product.image_url}" alt="${item.product?.name}" class="w-full h-full object-cover">`
                                : `<div class="w-full h-full flex items-center justify-center text-gray-300"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>`
                            }
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-display font-bold text-sm uppercase leading-tight truncate">${item.product?.name || 'Produk'}</h4>
                            <p class="text-xs text-gray-500 mt-0.5">${item.product?.category || ''}</p>
                            <p class="text-brand-orange font-bold text-sm mt-1">Rp ${Number(item.subtotal).toLocaleString('id-ID')}</p>
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center border border-gray-300 text-xs">
                                    <button onclick="updateQty('${item.id}', ${item.quantity - 1})" class="px-2 py-1 hover:bg-gray-100 transition" aria-label="Kurangi">−</button>
                                    <span class="px-3 py-1 border-x border-gray-300 font-medium">${item.quantity}</span>
                                    <button onclick="updateQty('${item.id}', ${item.quantity + 1})" class="px-2 py-1 hover:bg-gray-100 transition" aria-label="Tambah">+</button>
                                </div>
                                <button onclick="removeItem('${item.id}')" class="text-gray-400 hover:text-brand-red transition flex items-center gap-1 text-xs" aria-label="Hapus ${item.product?.name}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');

                items.classList.remove('hidden');
                footer.classList.remove('hidden');

                document.getElementById('cartTotal').textContent = 'Rp ' + Number(data.data.total_price).toLocaleString('id-ID');
                document.getElementById('cartCheckoutCount').textContent = `(${data.data.total_items})`;
                updateCartBadge(data.data.total_items);

            } catch (err) {
                loading.classList.add('hidden');
                items.innerHTML = '<p class="text-center text-red-500 text-sm py-8">Gagal memuat keranjang.</p>';
                items.classList.remove('hidden');
            }
        }

        async function updateQty(itemId, newQty) {
            if (newQty < 1) return removeItem(itemId);
            try {
                await fetch(`/api/cart/${itemId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ quantity: newQty }),
                });
                loadCart();
            } catch (e) {
                showToast('Gagal update jumlah.', true);
            }
        }

        async function removeItem(itemId) {
            try {
                await fetch(`/api/cart/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                loadCart();
            } catch (e) {
                showToast('Gagal menghapus item.', true);
            }
        }

        function updateCartBadge(count) {
            const badge = document.getElementById('cartBadge');
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        function showToast(msg, isError = false) {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMsg');
            toastMsg.textContent = msg;
            toast.querySelector('svg').classList.toggle('text-green-400', !isError);
            toast.querySelector('svg').classList.toggle('text-red-400', isError);
            toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
            }, 3000);
        }

        // Init: load cart count
        fetch('/api/cart', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(d => updateCartBadge(d.data?.total_items || 0))
            .catch(() => {});
    </script>

</body>
</html>
<?php /**PATH C:\MyWork\Jurusan\WEBSITE\code\laravel\tent\resources\views/product-detail.blade.php ENDPATH**/ ?>