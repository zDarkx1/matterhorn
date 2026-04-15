<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Profil | Matterhorn Adventure Rental</title>

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
        .addr-card { transition: all 0.2s ease; }
        .addr-card:hover { border-color: #D64500; }
        .addr-card.is-default { border-left: 4px solid #D64500; }
        #addressModal { transition: opacity 0.3s ease; }
        #addressModal.show { opacity: 1; pointer-events: auto; }
        #addressModal .modal-body { transition: transform 0.3s ease, opacity 0.3s ease; }
        #addressModal.show .modal-body { transform: translateY(0); opacity: 1; }
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
                    <a href="/" class="text-sm font-medium text-gray-600 hover:text-brand-orange transition">← Kembali</a>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="text-sm font-display font-medium uppercase border border-gray-300 px-4 py-2 hover:border-brand-orange hover:text-brand-orange transition">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <!-- Profile Header -->
        <div class="bg-white border border-gray-200 p-8 mb-6">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-brand-black rounded-full flex items-center justify-center flex-shrink-0" aria-hidden="true">
                    <span class="text-white font-display font-bold text-2xl"><?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?></span>
                </div>
                <div>
                    <h1 class="font-display font-bold text-2xl uppercase"><?php echo e(Auth::user()->name); ?></h1>
                    <p class="text-gray-500 text-sm"><?php echo e(Auth::user()->email); ?></p>
                    <span class="inline-block mt-2 bg-brand-orange text-white text-xs font-bold px-2 py-1 uppercase tracking-widest"><?php echo e(ucfirst(Auth::user()->role ?? 'Customer')); ?></span>
                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="bg-white border border-gray-200 p-8 mb-6">
            <h2 class="font-display font-bold text-lg uppercase mb-6 flex items-center gap-3">
                <span class="w-1 h-5 bg-brand-orange block"></span>
                Detail Profil
            </h2>

            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Nama Lengkap</span>
                    <span class="text-sm font-medium"><?php echo e(Auth::user()->name); ?></span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Email</span>
                    <span class="text-sm font-medium"><?php echo e(Auth::user()->email); ?></span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Nomor Telepon</span>
                    <span class="text-sm font-medium"><?php echo e(Auth::user()->phone_number ?? '-'); ?></span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm text-gray-500">Bergabung Sejak</span>
                    <span class="text-sm font-medium"><?php echo e(Auth::user()->created_at->format('d F Y')); ?></span>
                </div>
            </div>
        </div>

        <!-- ═══════════ ALAMAT SAYA ═══════════ -->
        <div class="bg-white border border-gray-200 p-8 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-display font-bold text-lg uppercase flex items-center gap-3">
                    <span class="w-1 h-5 bg-brand-orange block"></span>
                    Alamat Domisili
                </h2>
                <p class="text-xs text-gray-400 -mt-4 mb-2">Digunakan sebagai identitas penyewa demi keamanan peralatan.</p>
                <button id="btnAddAddress" class="flex items-center gap-2 text-sm font-bold text-brand-orange hover:text-orange-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Alamat
                </button>
            </div>

            <!-- Address List -->
            <div id="addressList" class="space-y-3">
                <div id="addressLoading" class="flex items-center justify-center py-8">
                    <svg class="w-6 h-6 animate-spin text-brand-orange" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>
                <div id="addressEmpty" class="hidden text-center py-8">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <p class="text-gray-500 text-sm">Belum ada alamat tersimpan.</p>
                    <p class="text-gray-400 text-xs mt-1">Tambahkan alamat domisili sebagai identitas penyewa.</p>
                </div>
                <div id="addressCards" class="space-y-3 hidden"></div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="bg-white border border-gray-200 p-8">
            <h2 class="font-display font-bold text-lg uppercase mb-6 flex items-center gap-3">
                <span class="w-1 h-5 bg-brand-orange block"></span>
                Ubah Password
            </h2>

            <!-- Success/Error Messages -->
            <div id="pwMsg" class="hidden mb-4 px-4 py-3 rounded-sm text-sm flex items-center gap-3"></div>

            <form id="passwordForm" class="space-y-4">
                <div>
                    <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Password Lama</label>
                    <input type="password" id="current_password" name="current_password" class="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition" placeholder="Masukkan password saat ini" required>
                </div>
                <div>
                    <label for="new_password" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" class="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition" placeholder="Minimal 8 karakter" required>
                </div>
                <div>
                    <label for="new_password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Konfirmasi Password Baru</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition" placeholder="Ulangi password baru" required>
                </div>
                <button type="submit" id="pwSubmitBtn" class="bg-brand-black text-white font-display font-bold uppercase tracking-wider px-8 py-3 hover:bg-brand-orange transition-colors duration-300">
                    Simpan Password
                </button>
            </form>
        </div>

    </main>

    <!-- ═══════════ ADDRESS MODAL ═══════════ -->
    <div id="addressModal" class="fixed inset-0 z-[80] flex items-start justify-center pt-10 pb-10 bg-black/50 opacity-0 pointer-events-none overflow-y-auto">
        <div class="modal-body bg-white w-full max-w-lg mx-4 shadow-2xl transform -translate-y-4 opacity-0 my-auto">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 id="modalTitle" class="font-display font-bold text-lg uppercase flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Buat Alamat Baru</span>
                </h3>
                <button id="closeModal" class="text-gray-400 hover:text-brand-black transition p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form id="addressForm" class="p-6 space-y-6">
                <input type="hidden" id="addrId" value="">

                <!-- Informasi Penerima -->
                <div>
                    <h4 class="font-bold text-sm uppercase tracking-wider mb-4 text-brand-black">Informasi Penyewa</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Nama Lengkap Penerima</label>
                            <input type="text" id="addrName" class="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Nomor Handphone</label>
                            <div class="flex">
                                <span class="bg-gray-100 border border-r-0 border-gray-300 px-3 py-3 text-sm text-gray-500 flex-shrink-0">+62</span>
                                <input type="tel" id="addrPhone" class="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition" placeholder="8123456789" required>
                            </div>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 mt-3 cursor-pointer group">
                        <input type="checkbox" id="useProfileData" class="form-checkbox h-4 w-4 text-brand-orange rounded border-gray-300 focus:ring-brand-orange">
                        <span class="text-xs text-gray-500 group-hover:text-brand-orange transition">Gunakan informasi yang sama dengan data pada halaman profil</span>
                    </label>
                </div>

                <!-- Detail Alamat -->
                <div>
                    <h4 class="font-bold text-sm uppercase tracking-wider mb-4 text-brand-black">Detail Alamat</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Label Alamat</label>
                            <input type="text" id="addrLabel" class="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition" placeholder="Label Alamat" required>
                            <p class="text-xs text-blue-400 mt-1">Contoh: Alamat Rumah, Alamat Kantor</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Provinsi</label>
                            <select id="addrProvince" class="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition bg-white appearance-none cursor-pointer" required>
                                <option value="">Provinsi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Kota / Kabupaten</label>
                            <select id="addrCity" class="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition bg-white appearance-none cursor-pointer" required>
                                <option value="">Kota / Kabupaten</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Kecamatan</label>
                            <select id="addrDistrict" class="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition bg-white appearance-none cursor-pointer" required>
                                <option value="">Kecamatan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Kode Pos</label>
                            <select id="addrPostalCode" class="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition bg-white appearance-none cursor-pointer" required>
                                <option value="">Kode Pos</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Alamat Lengkap</label>
                            <input type="text" id="addrFull" class="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition" placeholder="Alamat lengkap detail" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Catatan Tambahan</label>
                            <textarea id="addrNotes" rows="3" class="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition resize-none" placeholder="Catatan tambahan (opsional)"></textarea>
                            <p class="text-xs text-gray-400 mt-1">Patokan rumah, informasi tambahan, dll.</p>
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div>
                    <h4 class="font-bold text-sm uppercase tracking-wider mb-2 text-brand-black">Titik Alamat</h4>
                    <div id="mapContainer" class="relative">
                        <div id="addressMap" class="w-full h-52 bg-gray-200 border border-gray-300 rounded-sm z-0"></div>
                        <div id="mapOverlay" class="absolute inset-0 bg-gray-800/60 flex items-center justify-center rounded-sm cursor-pointer z-10">
                            <p class="text-white text-sm text-center px-4">Silakan isi alamat lengkap terlebih dahulu</p>
                        </div>
                    </div>
                    <button type="button" id="btnPinpoint" class="mt-2 w-full border border-brand-orange text-brand-orange font-bold text-sm py-2.5 flex items-center justify-center gap-2 hover:bg-brand-orange hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tentukan Pinpoint Lokasi
                    </button>
                    <input type="hidden" id="addrLat" value="">
                    <input type="hidden" id="addrLng" value="">
                </div>

                <!-- Set Default -->
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" id="addrDefault" class="form-checkbox h-4 w-4 text-brand-orange rounded border-gray-300 focus:ring-brand-orange">
                    <span class="text-sm text-gray-600 group-hover:text-brand-orange transition">Jadikan alamat utama</span>
                </label>

                <!-- Error msg -->
                <div id="addrMsg" class="hidden px-4 py-3 rounded-sm text-sm"></div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button type="button" id="cancelAddr" class="flex-1 border border-gray-300 text-gray-600 font-display font-bold uppercase tracking-wider py-3 hover:border-brand-black hover:text-brand-black transition">Batal</button>
                    <button type="submit" id="saveAddr" class="flex-1 bg-brand-black text-white font-display font-bold uppercase tracking-wider py-3 hover:bg-brand-orange transition-colors duration-300">Simpan Alamat</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[90] bg-brand-black text-white px-6 py-3 rounded shadow-xl flex items-center gap-3 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
        <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span id="toastMsg" class="text-sm font-medium"></span>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const headers = { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' };

        // ═══ REGION DATA (Static — Jawa Barat focused) ═══
        const regionData = {
            'Jawa Barat': {
                'Kota Bandung': {
                    districts: ['Andir','Antapani','Arcamanik','Astanaanyar','Babakan Ciparay','Bandung Kidul','Bandung Kulon','Bandung Wetan','Batununggal','Bojongloa Kaler','Bojongloa Kidul','Buahbatu','Cibeunying Kaler','Cibeunying Kidul','Cibiru','Cicendo','Cidadap','Cinambo','Coblong','Gedebage','Kiaracondong','Lengkong','Mandalajati','Panyileukan','Rancasari','Regol','Sukajadi','Sukasari','Sumur Bandung','Ujungberung'],
                    postalCodes: ['40111','40112','40113','40114','40115','40116','40117','40121','40122','40123','40124','40125','40131','40132','40133','40134','40135','40141','40142','40143','40151','40152','40153','40154','40161','40162','40163','40171']
                },
                'Kota Cimahi': {
                    districts: ['Cimahi Selatan','Cimahi Tengah','Cimahi Utara'],
                    postalCodes: ['40511','40512','40513','40521','40522','40523','40524','40525','40531']
                },
                'Kabupaten Bandung': {
                    districts: ['Baleendah','Banjaran','Bojongsoang','Cangkuang','Cicalengka','Cikancung','Cilengkrang','Cimaung','Cimenyan','Ciparay','Ciwidey','Dayeuhkolot','Ibun','Katapang','Kertasari','Kutawaringin','Majalaya','Margaasih','Margahayu','Nagreg','Pacet','Pameungpeuk','Pangalengan','Pasirjambu','Rancabali','Rancaekek','Solokanjeruk','Soreang'],
                    postalCodes: ['40375','40376','40383','40384','40385','40386','40387','40391','40392','40393','40394','40395','40396','40397','40398']
                },
                'Kabupaten Bandung Barat': {
                    districts: ['Batujajar','Cihampelas','Cipatat','Cipeundeuy','Cikalongwetan','Cipongkor','Gununghalu','Lembang','Ngamprah','Padalarang','Parongpong','Rongga','Saguling','Sindangkerta'],
                    postalCodes: ['40551','40552','40553','40554','40555','40556','40557','40558','40559']
                },
                'Kota Bekasi': {
                    districts: ['Bantar Gebang','Bekasi Barat','Bekasi Selatan','Bekasi Timur','Bekasi Utara','Jatiasih','Jatisampurna','Medan Satria','Mustika Jaya','Pondok Gede','Pondok Melati','Rawalumbu'],
                    postalCodes: ['17111','17112','17113','17114','17121','17131','17141','17142','17143','17144','17145','17146','17147','17148']
                },
                'Kota Bogor': {
                    districts: ['Bogor Barat','Bogor Selatan','Bogor Tengah','Bogor Timur','Bogor Utara','Tanah Sareal'],
                    postalCodes: ['16111','16112','16113','16114','16115','16116','16117','16118','16119','16121','16122','16123','16124','16125','16131','16132','16141','16142','16143','16144','16151','16152','16153','16154','16155','16161']
                },
                'Kota Depok': {
                    districts: ['Beji','Bojongsari','Cilodong','Cimanggis','Cinere','Cipayung','Limo','Pancoran Mas','Sawangan','Sukmajaya','Tapos'],
                    postalCodes: ['16411','16412','16413','16414','16415','16416','16417','16418','16419','16421','16422','16431','16432','16433','16434','16435','16436','16437','16438','16439']
                },
            },
            'DKI Jakarta': {
                'Jakarta Selatan': {
                    districts: ['Cilandak','Jagakarsa','Kebayoran Baru','Kebayoran Lama','Mampang Prapatan','Pancoran','Pasar Minggu','Pesanggrahan','Setiabudi','Tebet'],
                    postalCodes: ['12110','12120','12130','12140','12150','12160','12170','12180','12190','12210','12220','12230','12240','12310','12320','12330','12410','12420','12430','12440','12450','12510','12520','12530','12560','12610','12620','12630','12640','12710','12720','12730','12740','12750','12810','12820','12830','12840','12850','12860','12870','12910','12920','12930','12940','12950','12960','12970','12980']
                },
                'Jakarta Pusat': {
                    districts: ['Cempaka Putih','Gambir','Johar Baru','Kemayoran','Menteng','Sawah Besar','Senen','Tanah Abang'],
                    postalCodes: ['10110','10120','10130','10140','10150','10160','10210','10220','10230','10240','10250','10310','10320','10330','10340','10350','10410','10420','10430','10440','10450','10510','10520','10530','10540','10550','10560','10570','10610','10620','10630','10640','10650','10710','10720','10730','10740','10750']
                },
                'Jakarta Barat': {
                    districts: ['Cengkareng','Grogol Petamburan','Kalideres','Kebon Jeruk','Kembangan','Palmerah','Taman Sari','Tambora'],
                    postalCodes: ['11110','11120','11130','11140','11150','11160','11210','11220','11230','11240','11250','11310','11320','11330','11410','11420','11430','11440','11450','11460','11470','11510','11520','11530','11540','11550','11610','11620','11630','11640','11650','11710','11720','11730','11740','11750','11810','11820','11830']
                },
                'Jakarta Timur': {
                    districts: ['Cakung','Cipayung','Ciracas','Duren Sawit','Jatinegara','Kramat Jati','Makasar','Matraman','Pasar Rebo','Pulo Gadung'],
                    postalCodes: ['13110','13120','13130','13140','13150','13160','13210','13220','13230','13240','13250','13310','13320','13330','13340','13350','13410','13420','13430','13440','13450','13460','13470','13510','13520','13530','13540','13550','13560','13570','13610','13620','13630','13640','13710','13720','13730','13740','13750','13760']
                },
                'Jakarta Utara': {
                    districts: ['Cilincing','Kelapa Gading','Koja','Pademangan','Penjaringan','Tanjung Priok'],
                    postalCodes: ['14110','14120','14130','14140','14150','14210','14220','14230','14240','14250','14260','14270','14310','14320','14330','14340','14350','14410','14420','14430','14440','14450','14460']
                },
            },
            'Jawa Tengah': {
                'Kota Semarang': {
                    districts: ['Banyumanik','Candisari','Gajah Mungkur','Genuk','Gunungpati','Mijen','Ngaliyan','Pedurungan','Semarang Barat','Semarang Selatan','Semarang Tengah','Semarang Timur','Semarang Utara','Tembalang','Tugu'],
                    postalCodes: ['50111','50121','50131','50132','50133','50134','50135','50136','50141','50142','50143','50144','50145','50151','50152','50153','50154','50161','50162','50163','50164','50165','50166','50167','50171','50172','50173','50174','50175','50181','50182','50183','50184','50185','50191','50192','50193','50194','50195','50196','50197','50198','50199']
                },
            },
            'Jawa Timur': {
                'Kota Surabaya': {
                    districts: ['Asemrowo','Benowo','Bubutan','Bulak','Dukuh Pakis','Gayungan','Genteng','Gubeng','Gunung Anyar','Jambangan','Karang Pilang','Kenjeran','Krembangan','Lakarsantri','Mulyorejo','Pabean Cantikan','Pakal','Rungkut','Sambikerep','Sawahan','Semampir','Simokerto','Sukolilo','Sukomanunggal','Tambaksari','Tandes','Tegalsari','Tenggilis Mejoyo','Wiyung','Wonocolo','Wonokromo'],
                    postalCodes: ['60111','60112','60113','60114','60115','60116','60117','60118','60119','60121','60122','60123','60131','60132','60133','60134','60141','60142','60143','60144','60151','60152','60153','60211','60212','60213','60214','60215','60216','60221','60222','60223','60224','60225','60226','60231','60232','60233','60234','60235','60236','60237','60238','60239','60241','60243','60244','60245','60246','60247','60248','60249','60251','60252','60253','60254','60255','60256']
                },
            },
            'Banten': {
                'Kota Tangerang': {
                    districts: ['Batuceper','Benda','Cibodas','Ciledug','Cipondoh','Jatiuwung','Karang Tengah','Karawaci','Larangan','Neglasari','Periuk','Pinang','Tangerang'],
                    postalCodes: ['15111','15112','15113','15114','15115','15116','15117','15118','15121','15122','15123','15124','15125','15126','15127','15128','15131','15132','15133','15134','15135','15136','15137','15138','15139','15141','15142','15143','15144','15145','15146','15147','15148']
                },
                'Kota Tangerang Selatan': {
                    districts: ['Ciputat','Ciputat Timur','Pamulang','Pondok Aren','Serpong','Serpong Utara','Setu'],
                    postalCodes: ['15310','15311','15312','15313','15314','15315','15316','15317','15318','15319','15320','15321','15322','15323','15324','15325','15326','15327','15328','15329','15330','15331','15332','15333','15334','15335','15336','15337','15338','15339','15340','15341','15342','15343','15344','15345','15346','15347']
                },
            },
        };

        // ═══ POPULATE DROPDOWNS ═══
        const provSelect = document.getElementById('addrProvince');
        const citySelect = document.getElementById('addrCity');
        const distSelect = document.getElementById('addrDistrict');
        const postalSelect = document.getElementById('addrPostalCode');

        Object.keys(regionData).forEach(prov => {
            const opt = document.createElement('option');
            opt.value = prov; opt.textContent = prov;
            provSelect.appendChild(opt);
        });

        provSelect.addEventListener('change', () => {
            citySelect.innerHTML = '<option value="">Kota / Kabupaten</option>';
            distSelect.innerHTML = '<option value="">Kecamatan</option>';
            postalSelect.innerHTML = '<option value="">Kode Pos</option>';
            if (regionData[provSelect.value]) {
                Object.keys(regionData[provSelect.value]).forEach(city => {
                    const opt = document.createElement('option');
                    opt.value = city; opt.textContent = city;
                    citySelect.appendChild(opt);
                });
            }
        });

        citySelect.addEventListener('change', () => {
            distSelect.innerHTML = '<option value="">Kecamatan</option>';
            postalSelect.innerHTML = '<option value="">Kode Pos</option>';
            const cityData = regionData[provSelect.value]?.[citySelect.value];
            if (cityData) {
                cityData.districts.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d; opt.textContent = d;
                    distSelect.appendChild(opt);
                });
                cityData.postalCodes.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p; opt.textContent = p;
                    postalSelect.appendChild(opt);
                });
            }
        });

        // ═══ USE PROFILE DATA CHECKBOX ═══
        document.getElementById('useProfileData').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('addrName').value = '<?php echo e(Auth::user()->name); ?>';
                document.getElementById('addrPhone').value = '<?php echo e(Auth::user()->phone_number ?? ""); ?>'.replace(/^\+?62/, '');
            }
        });

        // ═══ LEAFLET MAP ═══
        let map, marker;
        const mapEl = document.getElementById('addressMap');
        const mapOverlay = document.getElementById('mapOverlay');

        function initMap(lat = -6.9175, lng = 107.6191) {
            if (map) { map.remove(); }
            map = L.map('addressMap').setView([lat, lng], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', function() {
                const pos = marker.getLatLng();
                document.getElementById('addrLat').value = pos.lat.toFixed(7);
                document.getElementById('addrLng').value = pos.lng.toFixed(7);
            });
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                document.getElementById('addrLat').value = e.latlng.lat.toFixed(7);
                document.getElementById('addrLng').value = e.latlng.lng.toFixed(7);
            });
            document.getElementById('addrLat').value = lat.toFixed(7);
            document.getElementById('addrLng').value = lng.toFixed(7);
        }

        document.getElementById('btnPinpoint').addEventListener('click', function() {
            mapOverlay.classList.add('hidden');
            setTimeout(() => {
                initMap();
                map.invalidateSize();
            }, 100);
        });

        // ═══ MODAL LOGIC ═══
        const modal = document.getElementById('addressModal');
        const form = document.getElementById('addressForm');

        function openModal(addr = null) {
            form.reset();
            document.getElementById('addrId').value = '';
            document.getElementById('addrLat').value = '';
            document.getElementById('addrLng').value = '';
            mapOverlay.classList.remove('hidden');
            document.getElementById('addrMsg').classList.add('hidden');

            if (addr) {
                document.getElementById('modalTitle').querySelector('span').textContent = 'Edit Alamat';
                document.getElementById('addrId').value = addr.id;
                document.getElementById('addrName').value = addr.recipient_name;
                document.getElementById('addrPhone').value = addr.phone.replace(/^\+?62/, '');
                document.getElementById('addrLabel').value = addr.label;

                // Set province and trigger cascading
                provSelect.value = addr.province;
                provSelect.dispatchEvent(new Event('change'));
                setTimeout(() => {
                    citySelect.value = addr.city;
                    citySelect.dispatchEvent(new Event('change'));
                    setTimeout(() => {
                        distSelect.value = addr.district;
                        postalSelect.value = addr.postal_code;
                    }, 50);
                }, 50);

                document.getElementById('addrFull').value = addr.full_address;
                document.getElementById('addrNotes').value = addr.notes || '';
                document.getElementById('addrDefault').checked = addr.is_default;

                if (addr.latitude && addr.longitude) {
                    mapOverlay.classList.add('hidden');
                    setTimeout(() => {
                        initMap(parseFloat(addr.latitude), parseFloat(addr.longitude));
                        map.invalidateSize();
                    }, 200);
                }
            } else {
                document.getElementById('modalTitle').querySelector('span').textContent = 'Buat Alamat Baru';
            }

            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            // Invalidate map after modal animation
            setTimeout(() => { if (map) map.invalidateSize(); }, 400);
        }

        function closeModal() {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }

        document.getElementById('btnAddAddress').addEventListener('click', () => openModal());
        document.getElementById('closeModal').addEventListener('click', closeModal);
        document.getElementById('cancelAddr').addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

        // ═══ SAVE ADDRESS ═══
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const msgEl = document.getElementById('addrMsg');
            const saveBtn = document.getElementById('saveAddr');
            saveBtn.disabled = true;
            saveBtn.textContent = 'Menyimpan...';

            const addrId = document.getElementById('addrId').value;
            const phone = document.getElementById('addrPhone').value;

            const payload = {
                label: document.getElementById('addrLabel').value,
                recipient_name: document.getElementById('addrName').value,
                phone: '+62' + phone.replace(/^0+/, ''),
                province: provSelect.value,
                city: citySelect.value,
                district: distSelect.value,
                postal_code: postalSelect.value,
                full_address: document.getElementById('addrFull').value,
                notes: document.getElementById('addrNotes').value || null,
                latitude: document.getElementById('addrLat').value || null,
                longitude: document.getElementById('addrLng').value || null,
                is_default: document.getElementById('addrDefault').checked,
            };

            try {
                const url = addrId ? `/api/addresses/${addrId}` : '/api/addresses';
                const method = addrId ? 'PUT' : 'POST';
                const res = await fetch(url, { method, headers, body: JSON.stringify(payload) });
                const data = await res.json();

                if (res.ok) {
                    closeModal();
                    showToast(data.message);
                    loadAddresses();
                } else {
                    msgEl.classList.remove('hidden');
                    msgEl.className = 'px-4 py-3 rounded-sm text-sm bg-red-50 text-red-600 border border-red-200';
                    const errors = data.errors ? Object.values(data.errors).flat().join('<br>') : data.message;
                    msgEl.innerHTML = errors;
                }
            } catch (err) {
                msgEl.classList.remove('hidden');
                msgEl.className = 'px-4 py-3 rounded-sm text-sm bg-red-50 text-red-600 border border-red-200';
                msgEl.textContent = 'Terjadi kesalahan jaringan.';
            }

            saveBtn.disabled = false;
            saveBtn.textContent = 'Simpan Alamat';
        });

        // ═══ LOAD ADDRESSES ═══
        async function loadAddresses() {
            const loading = document.getElementById('addressLoading');
            const empty = document.getElementById('addressEmpty');
            const cards = document.getElementById('addressCards');
            loading.classList.remove('hidden'); empty.classList.add('hidden'); cards.classList.add('hidden');

            try {
                const res = await fetch('/api/addresses', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
                const data = await res.json();
                loading.classList.add('hidden');

                if (!data.data || data.data.length === 0) {
                    empty.classList.remove('hidden');
                    return;
                }

                cards.innerHTML = data.data.map(addr => `
                    <div class="addr-card border border-gray-200 p-5 relative ${addr.is_default ? 'is-default' : ''}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-display font-bold text-sm uppercase">${addr.label}</span>
                                    ${addr.is_default ? '<span class="bg-brand-orange text-white text-[10px] font-bold px-2 py-0.5 uppercase tracking-widest">Utama</span>' : ''}
                                </div>
                                <p class="font-medium text-sm">${addr.recipient_name}</p>
                                <p class="text-xs text-gray-500 mt-0.5">${addr.phone}</p>
                                <p class="text-sm text-gray-600 mt-2 leading-relaxed">${addr.full_address}, ${addr.district}, ${addr.city}, ${addr.province} ${addr.postal_code}</p>
                                ${addr.notes ? `<p class="text-xs text-gray-400 mt-1 italic">📝 ${addr.notes}</p>` : ''}
                            </div>
                            <div class="flex flex-col gap-1 flex-shrink-0">
                                <button onclick="editAddress(${addr.id})" class="text-xs text-brand-orange hover:text-orange-700 font-bold transition">Edit</button>
                                ${!addr.is_default ? `<button onclick="setDefault(${addr.id})" class="text-xs text-gray-500 hover:text-brand-orange font-medium transition">Set Utama</button>` : ''}
                                <button onclick="deleteAddress(${addr.id})" class="text-xs text-gray-400 hover:text-brand-red font-medium transition">Hapus</button>
                            </div>
                        </div>
                    </div>
                `).join('');
                cards.classList.remove('hidden');

                // Store addresses for edit use
                window._addresses = data.data;
            } catch (err) { loading.classList.add('hidden'); }
        }

        window.editAddress = function(id) {
            const addr = window._addresses?.find(a => a.id === id);
            if (addr) openModal(addr);
        };

        window.setDefault = async function(id) {
            await fetch(`/api/addresses/${id}/default`, { method: 'PUT', headers });
            showToast('Alamat utama berhasil diubah.');
            loadAddresses();
        };

        window.deleteAddress = async function(id) {
            if (!confirm('Hapus alamat ini?')) return;
            await fetch(`/api/addresses/${id}`, { method: 'DELETE', headers });
            showToast('Alamat berhasil dihapus.');
            loadAddresses();
        };

        loadAddresses();

        // ═══ CHANGE PASSWORD ═══
        document.getElementById('passwordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('pwSubmitBtn');
            const msg = document.getElementById('pwMsg');
            btn.disabled = true;
            btn.textContent = 'Menyimpan...';

            try {
                const res = await fetch('/api/profile/password', {
                    method: 'PUT',
                    headers,
                    body: JSON.stringify({
                        current_password: document.getElementById('current_password').value,
                        new_password: document.getElementById('new_password').value,
                        new_password_confirmation: document.getElementById('new_password_confirmation').value,
                    }),
                });
                const data = await res.json();
                msg.classList.remove('hidden', 'bg-green-50', 'text-green-700', 'bg-red-50', 'text-red-600', 'border-green-200', 'border-red-200');
                if (res.ok) {
                    msg.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
                    msg.innerHTML = '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' + data.message;
                    this.reset();
                } else {
                    const errors = data.errors ? Object.values(data.errors).flat().join('<br>') : data.message;
                    msg.classList.add('bg-red-50', 'text-red-600', 'border', 'border-red-200');
                    msg.innerHTML = errors;
                }
            } catch (err) {
                msg.classList.remove('hidden');
                msg.classList.add('bg-red-50', 'text-red-600', 'border', 'border-red-200');
                msg.innerHTML = 'Terjadi kesalahan jaringan.';
            }
            btn.disabled = false;
            btn.textContent = 'Simpan Password';
        });

        // ═══ TOAST ═══
        window.showToast = function(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMsg').textContent = msg;
            toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
            setTimeout(() => toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none'), 3000);
        };
    });
    </script>

</body>
</html>
<?php /**PATH C:\MyWork\Jurusan\WEBSITE\code\laravel\tent\resources\views/profile.blade.php ENDPATH**/ ?>