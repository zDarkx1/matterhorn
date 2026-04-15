<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout | Matterhorn Adventure Rental</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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
        @keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        .fade-in { animation: fadeIn 0.3s ease forwards; }
        .checkout-section { transition: all 0.2s ease; }
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
                    <a href="{{ route('cart') }}" class="text-sm font-medium text-gray-600 hover:text-brand-orange transition">← Kembali ke Keranjang</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-display font-bold text-3xl uppercase mb-8 flex items-center gap-3">
            <span class="w-1 h-8 bg-brand-orange block"></span>
            Checkout
        </h1>

        <div class="flex flex-col lg:flex-row gap-8">

            <!-- ═══ LEFT COLUMN ═══ -->
            <div class="flex-1 space-y-6">

                <!-- Alamat Domisili Section -->
                <div class="checkout-section bg-white border border-gray-200 p-6 sm:p-8 fade-in">
                    <h2 class="font-display font-bold text-lg uppercase mb-6 flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Alamat Domisili Penyewa
                    </h2>
                    <p class="text-xs text-gray-400 -mt-4 mb-5">Alamat digunakan sebagai data identitas penyewa demi keamanan peralatan.</p>

                    <!-- Address Loading -->
                    <div id="addrLoading" class="flex items-center justify-center py-8">
                        <svg class="w-6 h-6 animate-spin text-brand-orange" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </div>

                    <!-- No Address Warning -->
                    <div id="addrWarning" class="hidden">
                        <div class="bg-red-50 border border-red-200 p-5 rounded-sm">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                                <div>
                                    <p class="font-bold text-sm text-red-700 mb-1">Alamat domisili belum diatur</p>
                                    <p class="text-xs text-red-600 mb-3">Anda harus mengatur alamat domisili sebagai identitas penyewa sebelum melanjutkan checkout.</p>
                                    <a href="{{ route('profile') }}" class="inline-flex items-center gap-2 bg-brand-black text-white font-display font-bold uppercase tracking-wider text-xs px-5 py-2.5 hover:bg-brand-orange transition-colors duration-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Atur Alamat di Profil
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Display -->
                    <div id="addrDisplay" class="hidden">
                        <div class="border border-gray-200 p-5 relative" id="addressCard">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span id="addrLabelDisplay" class="font-display font-bold text-sm uppercase"></span>
                                        <span class="bg-brand-orange text-white text-[10px] font-bold px-2 py-0.5 uppercase tracking-widest">Utama</span>
                                    </div>
                                    <p id="addrNameDisplay" class="font-medium text-sm"></p>
                                    <p id="addrPhoneDisplay" class="text-xs text-gray-500 mt-0.5"></p>
                                    <p id="addrFullDisplay" class="text-sm text-gray-600 mt-2 leading-relaxed"></p>
                                    <p id="addrNotesDisplay" class="text-xs text-gray-400 mt-1 italic hidden"></p>
                                </div>
                                <a href="{{ route('profile') }}" class="text-xs text-brand-orange hover:text-orange-700 font-bold transition flex-shrink-0 border border-brand-orange px-3 py-1.5 hover:bg-brand-orange hover:text-white">Ubah</a>
                            </div>
                        </div>

                        <!-- Mini Map Display -->
                        <div id="checkoutMapContainer" class="hidden mt-3">
                            <div id="checkoutMap" class="w-full h-40 bg-gray-200 border border-gray-300 rounded-sm"></div>
                        </div>
                    </div>
                </div>

                <!-- Pengambilan di Toko Section -->
                <div class="checkout-section bg-white border border-gray-200 p-6 sm:p-8 fade-in" style="animation-delay: 0.1s">
                    <h2 class="font-display font-bold text-lg uppercase mb-6 flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Pengambilan di Toko
                    </h2>

                    <div class="border border-gray-200 p-5">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-brand-black rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-display font-bold text-lg">M</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-display font-bold uppercase">Matterhorn.co</h4>
                                <p class="text-sm text-gray-600 mt-1">Jl. Cihampelas No.22</p>
                                <p class="text-sm text-gray-500">Bandung, Jawa Barat</p>

                                <div class="flex items-center gap-4 mt-3">
                                    <div class="flex items-center gap-1.5" id="storeStatusCheckout">
                                        <span class="w-2 h-2 rounded-full bg-gray-400 animate-pulse"></span>
                                        <span class="text-xs font-medium text-gray-500">Memuat...</span>
                                    </div>
                                    <a href="tel:08112184109" class="text-xs text-brand-orange hover:text-orange-700 font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        0811-2184-109
                                    </a>
                                </div>

                                <!-- Store Map -->
                                <div class="mt-4 border border-gray-200 rounded-sm overflow-hidden">
                                    <div id="storeMap" class="w-full h-40 bg-gray-200"></div>
                                </div>
                                <p class="text-xs text-gray-400 mt-2">
                                    <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Barang hanya dapat diambil di lokasi toko saat jam operasional.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ RIGHT COLUMN ═══ -->
            <div class="w-full lg:w-[400px] lg:flex-shrink-0 space-y-4">

                <!-- Ringkasan Belanja -->
                <div class="bg-white border border-gray-200 p-6 sticky top-24 fade-in" style="animation-delay: 0.15s">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="font-display font-bold text-lg uppercase">Ringkasan Belanja</h3>
                        <a href="{{ route('cart') }}" class="text-xs text-gray-500 hover:text-brand-orange border border-gray-300 px-3 py-1 font-medium transition">Ubah</a>
                    </div>

                    <!-- Items Summary -->
                    <div id="checkoutItems" class="space-y-3 mb-6 max-h-64 overflow-y-auto">
                        <div class="flex items-center justify-center py-4">
                            <svg class="w-5 h-5 animate-spin text-brand-orange" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </div>
                    </div>

                    <div id="checkoutSummary" class="hidden">
                        <p id="summaryItemCount" class="text-xs text-gray-500 mb-4"></p>

                        <div class="space-y-3 border-t border-gray-200 pt-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500" id="totalHargaLabel">Total Harga (0 barang)</span>
                                <span id="totalHarga" class="text-sm font-medium">Rp 0</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Total Biaya</span>
                                <span id="totalBiaya" class="text-sm font-medium">Rp 0</span>
                            </div>
                            <div class="border-t border-gray-200 pt-3 flex items-center justify-between">
                                <span class="font-display font-bold uppercase">Total Tagihan</span>
                                <span id="totalTagihan" class="font-display font-bold text-xl text-brand-orange">Rp 0</span>
                            </div>
                        </div>

                        <!-- Pay Button -->
                        <button id="payBtn" class="mt-6 w-full bg-gray-300 text-gray-500 font-display font-bold uppercase tracking-wider py-3.5 cursor-not-allowed transition-colors duration-300" disabled>
                            Lanjutkan ke Pembayaran
                        </button>

                        <p id="payBtnHint" class="text-xs text-center text-gray-400 mt-2 hidden"></p>
                    </div>
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
        const headers = { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken };
        const formatRupiah = (num) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);

        let hasAddress = false;
        let storeIsOpen = false;
        let checkoutItems = [];

        // ═══ SELECTED ITEMS FROM LOCALSTORAGE ═══
        const STORAGE_KEY = 'matterhorn_cart_selected';
        function getSelectedIds() {
            try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || []; }
            catch { return []; }
        }
        const selectedIds = getSelectedIds();

        // ═══ LOAD ADDRESS ═══
        async function loadAddress() {
            const loading = document.getElementById('addrLoading');
            const warning = document.getElementById('addrWarning');
            const display = document.getElementById('addrDisplay');

            try {
                const res = await fetch('/api/addresses', { headers });
                const data = await res.json();
                loading.classList.add('hidden');

                if (!data.data || data.data.length === 0) {
                    warning.classList.remove('hidden');
                    hasAddress = false;
                    updatePayBtn();
                    return;
                }

                hasAddress = true;
                // Find default or first address
                const addr = data.data.find(a => a.is_default) || data.data[0];

                document.getElementById('addrLabelDisplay').textContent = addr.label;
                document.getElementById('addrNameDisplay').textContent = addr.recipient_name;
                document.getElementById('addrPhoneDisplay').textContent = addr.phone;
                document.getElementById('addrFullDisplay').textContent = `${addr.full_address}, ${addr.district}, ${addr.city}, ${addr.province} ${addr.postal_code}`;

                if (addr.notes) {
                    const notesEl = document.getElementById('addrNotesDisplay');
                    notesEl.textContent = '📝 ' + addr.notes;
                    notesEl.classList.remove('hidden');
                }

                display.classList.remove('hidden');

                // Show map if coordinates exist
                if (addr.latitude && addr.longitude) {
                    const mapContainer = document.getElementById('checkoutMapContainer');
                    mapContainer.classList.remove('hidden');
                    setTimeout(() => {
                        const map = L.map('checkoutMap', { zoomControl: false, dragging: false, scrollWheelZoom: false }).setView([addr.latitude, addr.longitude], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
                        L.marker([addr.latitude, addr.longitude]).addTo(map);
                    }, 100);
                }

                updatePayBtn();
            } catch(e) {
                loading.classList.add('hidden');
                warning.classList.remove('hidden');
            }
        }

        // ═══ LOAD STORE STATUS ═══
        async function loadStoreStatus() {
            try {
                const res = await fetch('/api/store-status', { headers: { 'Accept': 'application/json' } });
                const data = await res.json();

                storeIsOpen = data.data.is_open;

                const badges = [document.getElementById('storeStatusCheckout')];
                badges.forEach(badge => {
                    if (data.data.is_open) {
                        badge.innerHTML = `<span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span><span class="text-xs font-medium text-green-600">${data.data.message}</span>`;
                    } else {
                        badge.innerHTML = `<span class="w-2 h-2 rounded-full bg-red-500"></span><span class="text-xs font-medium text-red-600">${data.data.message}</span>`;
                    }
                });

                updatePayBtn();
            } catch(e) {}
        }

        // ═══ LOAD CART ITEMS ═══
        async function loadCartItems() {
            const container = document.getElementById('checkoutItems');
            const summarySection = document.getElementById('checkoutSummary');

            try {
                const res = await fetch('/api/cart', { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                const allItems = data.data?.items || [];

                // Filter to only selected items
                checkoutItems = selectedIds.length > 0
                    ? allItems.filter(i => selectedIds.includes(i.id))
                    : allItems;

                if (checkoutItems.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-6">
                            <p class="text-gray-500 text-sm mb-2">Tidak ada item untuk checkout.</p>
                            <a href="${'{{ route("cart") }}'}" class="text-brand-orange text-sm font-bold hover:text-orange-700">Kembali ke Keranjang</a>
                        </div>`;
                    return;
                }

                container.innerHTML = checkoutItems.map(item => `
                    <div class="flex gap-3 py-3 border-b border-gray-100 last:border-0">
                        <div class="w-14 h-14 bg-gray-100 flex-shrink-0 overflow-hidden border border-gray-200">
                            ${item.product?.image_url ? `<img src="${item.product.image_url}" alt="${item.product?.name}" class="w-full h-full object-cover">` : '<div class="w-full h-full flex items-center justify-center text-gray-300"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>'}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-display font-bold uppercase text-brand-black truncate">${item.product?.name || 'Produk'}</p>
                            <p class="text-xs text-gray-400 mt-0.5">${item.quantity}x @ ${formatRupiah(item.product?.price_24h || 0)}/hari</p>
                        </div>
                        <p class="text-sm font-bold text-brand-black flex-shrink-0">${formatRupiah(item.subtotal)}</p>
                    </div>
                `).join('');

                // Summary
                const totalQty = checkoutItems.reduce((s, i) => s + i.quantity, 0);
                const totalPrice = checkoutItems.reduce((s, i) => s + i.subtotal, 0);

                document.getElementById('summaryItemCount').textContent = `${checkoutItems.length} Item dalam keranjang`;
                document.getElementById('totalHargaLabel').textContent = `Total Harga (${totalQty} barang)`;
                document.getElementById('totalHarga').textContent = formatRupiah(totalPrice);
                document.getElementById('totalBiaya').textContent = formatRupiah(totalPrice);
                document.getElementById('totalTagihan').textContent = formatRupiah(totalPrice);

                summarySection.classList.remove('hidden');
                updatePayBtn();
            } catch(e) {
                container.innerHTML = '<p class="text-center text-red-500 text-sm py-4">Gagal memuat item.</p>';
            }
        }

        // ═══ UPDATE PAY BUTTON ═══
        function updatePayBtn() {
            const btn = document.getElementById('payBtn');
            const hint = document.getElementById('payBtnHint');

            if (!hasAddress) {
                btn.disabled = true;
                btn.className = 'mt-6 w-full bg-gray-300 text-gray-500 font-display font-bold uppercase tracking-wider py-3.5 cursor-not-allowed transition-colors duration-300';
                hint.textContent = 'Atur alamat domisili terlebih dahulu untuk melanjutkan.';
                hint.classList.remove('hidden');
                return;
            }

            if (!storeIsOpen) {
                btn.disabled = true;
                btn.className = 'mt-6 w-full bg-gray-300 text-gray-500 font-display font-bold uppercase tracking-wider py-3.5 cursor-not-allowed transition-colors duration-300';
                hint.textContent = 'Toko sedang tutup. Silakan checkout saat jam operasional (09:00 – 21:45 WIB).';
                hint.classList.remove('hidden');
                return;
            }

            if (checkoutItems.length === 0) {
                btn.disabled = true;
                btn.className = 'mt-6 w-full bg-gray-300 text-gray-500 font-display font-bold uppercase tracking-wider py-3.5 cursor-not-allowed transition-colors duration-300';
                hint.classList.add('hidden');
                return;
            }

            btn.disabled = false;
            btn.className = 'mt-6 w-full bg-brand-black text-white font-display font-bold uppercase tracking-wider py-3.5 hover:bg-brand-orange transition-colors duration-300 cursor-pointer';
            hint.classList.add('hidden');
        }

        // ═══ PAY BUTTON CLICK ═══
        document.getElementById('payBtn').addEventListener('click', function() {
            if (this.disabled) return;
            showToast('Fitur pembayaran segera hadir! 🚀');
        });

        // ═══ STORE MAP ═══
        function initStoreMap() {
            const storeLat = -6.8884;
            const storeLng = 107.6044;
            const map = L.map('storeMap', { zoomControl: false, scrollWheelZoom: false }).setView([storeLat, storeLng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            const storeIcon = L.divIcon({
                className: 'custom-marker',
                html: '<div style="background:#D64500; width:28px; height:28px; border-radius:50%; border:3px solid white; box-shadow:0 2px 8px rgba(0,0,0,0.3); display:flex; align-items:center; justify-content:center;"><span style="color:white; font-weight:bold; font-size:11px; font-family:Oswald,sans-serif;">M</span></div>',
                iconSize: [28, 28],
                iconAnchor: [14, 14],
            });

            L.marker([storeLat, storeLng], { icon: storeIcon }).addTo(map)
                .bindPopup('<b class="font-display">Matterhorn.co</b><br>Jl. Cihampelas No.22');
        }

        // ═══ TOAST ═══
        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMsg').textContent = msg;
            toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
            setTimeout(() => toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none'), 3000);
        }

        // ═══ INIT ═══
        loadAddress();
        loadStoreStatus();
        loadCartItems();
        setTimeout(initStoreMap, 200);
    });
    </script>

</body>
</html>
