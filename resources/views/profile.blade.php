<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil | Matterhorn Adventure Rental</title>

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
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
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
                    <span class="text-white font-display font-bold text-2xl">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                </div>
                <div>
                    <h1 class="font-display font-bold text-2xl uppercase">{{ Auth::user()->name }}</h1>
                    <p class="text-gray-500 text-sm">{{ Auth::user()->email }}</p>
                    <span class="inline-block mt-2 bg-brand-orange text-white text-xs font-bold px-2 py-1 uppercase tracking-widest">{{ ucfirst(Auth::user()->role ?? 'Customer') }}</span>
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
                    <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Email</span>
                    <span class="text-sm font-medium">{{ Auth::user()->email }}</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Nomor Telepon</span>
                    <span class="text-sm font-medium">{{ Auth::user()->phone_number ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Alamat</span>
                    <span class="text-sm font-medium">{{ Auth::user()->address ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm text-gray-500">Bergabung Sejak</span>
                    <span class="text-sm font-medium">{{ Auth::user()->created_at->format('d F Y') }}</span>
                </div>
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

    <script>
        document.getElementById('passwordForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const btn = document.getElementById('pwSubmitBtn');
            const msg = document.getElementById('pwMsg');
            btn.disabled = true;
            btn.textContent = 'Menyimpan...';

            try {
                const res = await fetch('/api/profile/password', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
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
                    msg.innerHTML = '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' + errors;
                }
            } catch (err) {
                msg.classList.remove('hidden');
                msg.classList.add('bg-red-50', 'text-red-600', 'border', 'border-red-200');
                msg.innerHTML = 'Terjadi kesalahan jaringan.';
            }

            btn.disabled = false;
            btn.textContent = 'Simpan Password';
        });
    </script>

</body>
</html>
