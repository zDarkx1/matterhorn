<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Keranjang Belanja | Matterhorn Adventure Rental</title>

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
        .cart-item { transition: all 0.2s ease; }
        .cart-item:hover { border-color: #D64500; }
        .cart-item.selected { background-color: #FFF7ED; border-color: #D64500; }
        .qty-btn { transition: all 0.15s ease; }
        .qty-btn:hover { background-color: #D64500; color: white; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        .fade-in { animation: fadeIn 0.3s ease forwards; }
    </style>
</head>

<body class="bg-brand-gray text-brand-black antialiased font-sans min-h-screen">

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="flex flex-col leading-none">
                    <span class="font-display font-bold text-xl tracking-tighter text-black">MATTERHORN</span>
                    <span class="text-[0.5rem] tracking-[0.2em] text-gray-500 uppercase">Adventure Rental</span>
                </a>
                <div class="flex items-center gap-4">
                    <a href="/" class="text-sm font-medium text-gray-600 hover:text-brand-orange transition">← Lanjut Belanja</a>
                    <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('profile')); ?>" class="text-sm text-gray-600 hover:text-brand-orange transition">
                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">

            <!-- ═══ LEFT: Cart Items ═══ -->
            <div class="flex-1">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="font-display font-bold text-2xl uppercase flex items-center gap-3">
                        <span class="w-1 h-7 bg-brand-orange block"></span>
                        Keranjang Belanja
                    </h1>
                    <span id="cartCount" class="text-sm text-gray-500">0 item</span>
                </div>

                <!-- Select All -->
                <div id="selectAllBar" class="hidden bg-white border border-gray-200 px-5 py-3 mb-4 flex items-center justify-between">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" id="selectAll" class="form-checkbox h-5 w-5 text-brand-orange rounded border-gray-300 focus:ring-brand-orange cursor-pointer">
                        <span class="text-sm font-bold uppercase group-hover:text-brand-orange transition">Pilih Semua</span>
                    </label>
                    <button id="deleteSelected" class="hidden text-xs text-brand-red hover:text-red-700 font-bold transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Hapus Terpilih
                    </button>
                </div>

                <!-- Cart Loading -->
                <div id="cartLoading" class="bg-white border border-gray-200 p-8">
                    <div class="flex items-center justify-center py-12">
                        <svg class="w-8 h-8 animate-spin text-brand-orange" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </div>
                </div>

                <!-- Cart Empty -->
                <div id="cartEmpty" class="hidden bg-white border border-gray-200 p-8 text-center">
                    <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <p class="text-gray-500 font-display font-bold text-lg uppercase mb-2">Keranjang Kosong</p>
                    <p class="text-gray-400 text-sm mb-6">Belum ada peralatan yang ditambahkan ke keranjang.</p>
                    <a href="/" class="inline-block bg-brand-black text-white font-display font-bold uppercase tracking-wider px-8 py-3 hover:bg-brand-orange transition-colors duration-300">Mulai Belanja</a>
                </div>

                <!-- Cart Items -->
                <div id="cartItems" class="hidden space-y-3"></div>
            </div>

            <!-- ═══ RIGHT: Summary Sidebar ═══ -->
            <div class="w-full lg:w-96 lg:flex-shrink-0">
                <!-- Store Info Card -->
                <div class="bg-white border border-gray-200 p-5 mb-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-brand-black rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-display font-bold text-sm">M</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-display font-bold text-sm uppercase">Matterhorn.co</h4>
                            <p class="text-xs text-gray-500 mt-0.5">Jl. Cihampelas No.22 · 0811-2184-109</p>
                            <div class="flex items-center gap-1.5 mt-1.5" id="storeStatusBadge">
                                <span class="w-2 h-2 rounded-full bg-gray-400 animate-pulse"></span>
                                <span class="text-xs font-medium text-gray-500">Memuat...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div id="summaryCard" class="bg-white border border-gray-200 p-6 sticky top-24">
                    <h3 class="font-display font-bold text-lg uppercase mb-5 flex items-center gap-3">
                        <span class="w-1 h-5 bg-brand-orange block"></span>
                        Ringkasan Belanja
                    </h3>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Item Terpilih</span>
                            <span id="selectedCount" class="text-sm font-medium">0 barang</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Total Harga</span>
                            <span id="selectedTotal" class="text-sm font-medium">Rp 0</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3 flex items-center justify-between">
                            <span class="font-display font-bold uppercase text-sm">Total Tagihan</span>
                            <span id="grandTotal" class="font-display font-bold text-xl text-brand-orange">Rp 0</span>
                        </div>
                    </div>

                    <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('checkout')); ?>" id="checkoutBtn" class="block w-full bg-gray-300 text-gray-500 font-display font-bold uppercase tracking-wider py-3.5 text-center cursor-not-allowed transition-colors duration-300 pointer-events-none">
                        Checkout (0)
                    </a>
                    <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="block w-full bg-brand-black text-white font-display font-bold uppercase tracking-wider py-3.5 text-center hover:bg-brand-orange transition-colors duration-300">
                        Login untuk Checkout
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Toast -->
    <div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[70] bg-brand-black text-white px-6 py-3 rounded shadow-xl flex items-center gap-3 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
        <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span id="toastMsg" class="text-sm font-medium"></span>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const headers = { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' };
        const formatRupiah = (num) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);

        // ═══ SELECTION STATE (localStorage) ═══
        const STORAGE_KEY = 'matterhorn_cart_selected';
        function getSelectedIds() {
            try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || []; }
            catch { return []; }
        }
        function saveSelectedIds(ids) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
        }

        let cartData = [];
        let selectedIds = getSelectedIds();

        // ═══ LOAD STORE STATUS ═══
        async function loadStoreStatus() {
            try {
                const res = await fetch('/api/store-status', { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                const badge = document.getElementById('storeStatusBadge');
                if (data.data.is_open) {
                    badge.innerHTML = `<span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span><span class="text-xs font-medium text-green-600">${data.data.message}</span>`;
                } else {
                    badge.innerHTML = `<span class="w-2 h-2 rounded-full bg-red-500"></span><span class="text-xs font-medium text-red-600">${data.data.message}</span>`;
                }
            } catch(e) {}
        }
        loadStoreStatus();

        // ═══ LOAD CART ═══
        async function loadCart() {
            const loading = document.getElementById('cartLoading');
            const empty = document.getElementById('cartEmpty');
            const items = document.getElementById('cartItems');
            const selectBar = document.getElementById('selectAllBar');

            loading.classList.remove('hidden');
            empty.classList.add('hidden');
            items.classList.add('hidden');
            selectBar.classList.add('hidden');

            try {
                const res = await fetch('/api/cart', { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                loading.classList.add('hidden');

                cartData = data.data?.items || [];
                document.getElementById('cartCount').textContent = `${data.data?.total_items || 0} item`;

                if (cartData.length === 0) {
                    empty.classList.remove('hidden');
                    updateSummary();
                    return;
                }

                // Clean up selectedIds — remove ids that no longer exist in cart
                const validIds = cartData.map(i => i.id);
                selectedIds = selectedIds.filter(id => validIds.includes(id));
                saveSelectedIds(selectedIds);

                renderItems();
                selectBar.classList.remove('hidden');
                selectBar.classList.add('flex');
                items.classList.remove('hidden');
                updateSelectAll();
                updateSummary();
            } catch(e) {
                loading.classList.add('hidden');
                empty.classList.remove('hidden');
            }
        }

        function renderItems() {
            const container = document.getElementById('cartItems');
            container.innerHTML = cartData.map((item, idx) => {
                const isSelected = selectedIds.includes(item.id);
                const imgSrc = item.product?.image_url;
                return `
                <div class="cart-item bg-white border border-gray-200 p-4 sm:p-5 fade-in ${isSelected ? 'selected' : ''}" data-id="${item.id}" style="animation-delay: ${idx * 0.05}s">
                    <div class="flex gap-4">
                        <!-- Checkbox -->
                        <div class="flex items-start pt-1">
                            <input type="checkbox" class="item-checkbox form-checkbox h-5 w-5 text-brand-orange rounded border-gray-300 focus:ring-brand-orange cursor-pointer" data-id="${item.id}" ${isSelected ? 'checked' : ''}>
                        </div>

                        <!-- Image -->
                        <a href="/product/${item.product_id}" class="w-20 h-20 sm:w-24 sm:h-24 bg-gray-100 flex-shrink-0 overflow-hidden border border-gray-200 block">
                            ${imgSrc ? `<img src="${imgSrc}" alt="${item.product?.name}" class="w-full h-full object-cover hover:scale-105 transition duration-300">` : '<div class="w-full h-full flex items-center justify-center text-gray-300"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>'}
                        </a>

                        <!-- Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start gap-2">
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">${item.product?.category || ''}</p>
                                    <a href="/product/${item.product_id}" class="font-display font-bold text-sm uppercase text-brand-black leading-tight hover:text-brand-orange transition line-clamp-2">${item.product?.name || 'Produk'}</a>
                                </div>
                                <button onclick="removeItem('${item.id}')" class="text-gray-300 hover:text-brand-red transition p-1 flex-shrink-0" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>

                            <div class="flex items-end justify-between mt-3">
                                <div class="flex items-center border border-gray-300">
                                    <button onclick="updateQty('${item.id}', ${item.quantity - 1})" class="qty-btn px-3 py-1.5 text-sm font-bold">−</button>
                                    <span class="px-4 py-1.5 border-x border-gray-300 text-sm font-medium min-w-[3rem] text-center">${item.quantity}</span>
                                    <button onclick="updateQty('${item.id}', ${item.quantity + 1})" class="qty-btn px-3 py-1.5 text-sm font-bold">+</button>
                                </div>
                                <div class="text-right">
                                    <p class="text-brand-orange font-bold text-base">${formatRupiah(item.subtotal)}</p>
                                    <p class="text-xs text-gray-400">${formatRupiah(item.product?.price_24h || 0)}/hari × ${item.quantity}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            }).join('');

            // Bind checkbox events
            document.querySelectorAll('.item-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    const id = this.dataset.id;
                    if (this.checked) {
                        if (!selectedIds.includes(id)) selectedIds.push(id);
                    } else {
                        selectedIds = selectedIds.filter(x => x !== id);
                    }
                    saveSelectedIds(selectedIds);
                    updateUI();
                });
            });
        }

        // ═══ SELECT ALL ═══
        document.getElementById('selectAll').addEventListener('change', function() {
            if (this.checked) {
                selectedIds = cartData.map(i => i.id);
            } else {
                selectedIds = [];
            }
            saveSelectedIds(selectedIds);
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
            updateUI();
        });

        function updateSelectAll() {
            const selectAll = document.getElementById('selectAll');
            if (cartData.length > 0 && selectedIds.length === cartData.length) {
                selectAll.checked = true;
                selectAll.indeterminate = false;
            } else if (selectedIds.length > 0) {
                selectAll.checked = false;
                selectAll.indeterminate = true;
            } else {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            }
        }

        function updateUI() {
            // Update card visuals
            document.querySelectorAll('.cart-item').forEach(card => {
                const id = card.dataset.id;
                card.classList.toggle('selected', selectedIds.includes(id));
            });
            updateSelectAll();
            updateSummary();
            updateDeleteBtn();
        }

        function updateDeleteBtn() {
            const btn = document.getElementById('deleteSelected');
            if (selectedIds.length > 0) {
                btn.classList.remove('hidden');
                btn.classList.add('flex');
            } else {
                btn.classList.add('hidden');
                btn.classList.remove('flex');
            }
        }

        // ═══ DELETE SELECTED ═══
        document.getElementById('deleteSelected').addEventListener('click', async function() {
            if (!confirm(`Hapus ${selectedIds.length} item terpilih?`)) return;
            for (const id of selectedIds) {
                await fetch(`/api/cart/${id}`, { method: 'DELETE', headers });
            }
            selectedIds = [];
            saveSelectedIds(selectedIds);
            showToast('Item terpilih berhasil dihapus.');
            loadCart();
        });

        // ═══ UPDATE SUMMARY ═══
        function updateSummary() {
            const selected = cartData.filter(i => selectedIds.includes(i.id));
            const totalItems = selected.reduce((s, i) => s + i.quantity, 0);
            const totalPrice = selected.reduce((s, i) => s + i.subtotal, 0);

            document.getElementById('selectedCount').textContent = `${totalItems} barang`;
            document.getElementById('selectedTotal').textContent = formatRupiah(totalPrice);
            document.getElementById('grandTotal').textContent = formatRupiah(totalPrice);

            const checkoutBtn = document.getElementById('checkoutBtn');
            if (checkoutBtn) {
                if (selectedIds.length > 0) {
                    checkoutBtn.textContent = `Checkout (${selectedIds.length})`;
                    checkoutBtn.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed', 'pointer-events-none');
                    checkoutBtn.classList.add('bg-brand-black', 'text-white', 'hover:bg-brand-orange');
                } else {
                    checkoutBtn.textContent = 'Checkout (0)';
                    checkoutBtn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed', 'pointer-events-none');
                    checkoutBtn.classList.remove('bg-brand-black', 'text-white', 'hover:bg-brand-orange');
                }
            }
        }

        // ═══ CART ACTIONS ═══
        window.updateQty = async function(itemId, qty) {
            if (qty < 1) return removeItem(itemId);
            await fetch(`/api/cart/${itemId}`, { method: 'PUT', headers, body: JSON.stringify({ quantity: qty }) });
            loadCart();
        };

        window.removeItem = async function(itemId) {
            await fetch(`/api/cart/${itemId}`, { method: 'DELETE', headers });
            selectedIds = selectedIds.filter(x => x !== itemId);
            saveSelectedIds(selectedIds);
            showToast('Item dihapus dari keranjang.');
            loadCart();
        };

        // ═══ TOAST ═══
        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMsg').textContent = msg;
            toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
            setTimeout(() => toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none'), 3000);
        }

        // ═══ INIT ═══
        loadCart();
    });
    </script>

</body>
</html>
<?php /**PATH C:\MyWork\Jurusan\WEBSITE\code\laravel\tent\resources\views/cart.blade.php ENDPATH**/ ?>