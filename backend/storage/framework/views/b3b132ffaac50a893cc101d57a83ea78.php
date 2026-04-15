<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Daftar Akun | Matterhorn Adventure Rental</title>

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
        .auth-image {
            background-image: url('https://images.unsplash.com/photo-1533130061792-64b345e4a833?auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
        }
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px white inset;
            transition: background-color 5000s ease-in-out 0s;
        }
        .input-focus:focus-within {
            border-color: #D64500;
        }
        .input-focus:focus-within label {
            color: #D64500;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-up {
            animation: slideUp 0.5s ease-out forwards;
        }
        .animate-slide-up-delay {
            animation: slideUp 0.5s ease-out 0.1s forwards;
            opacity: 0;
        }
        .animate-slide-up-delay-2 {
            animation: slideUp 0.5s ease-out 0.2s forwards;
            opacity: 0;
        }
        .strength-bar {
            transition: all 0.3s ease;
        }
    </style>
</head>

<body class="bg-white text-brand-black antialiased font-sans min-h-screen">

    <div class="min-h-screen flex flex-col lg:flex-row">

        <!-- LEFT SIDE: FORM -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 py-12 lg:px-16 xl:px-24 relative">

            <!-- Back to Home -->
            <a href="/" class="absolute top-6 left-6 lg:top-10 lg:left-10 flex items-center gap-2 text-sm text-gray-500 hover:text-brand-orange transition group">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>

            <div class="w-full max-w-md">

                <!-- Logo -->
                <div class="mb-8 animate-slide-up">
                    <a href="/" class="inline-block">
                        <div class="flex flex-col leading-none">
                            <span class="font-display font-bold text-3xl tracking-tighter text-black">MATTERHORN</span>
                            <span class="text-[0.6rem] tracking-[0.2em] text-gray-500 uppercase">Adventure Rental</span>
                        </div>
                    </a>
                </div>

                <!-- Heading -->
                <div class="mb-6 animate-slide-up-delay">
                    <h1 class="font-display font-bold text-3xl uppercase text-brand-black mb-2">Buat Akun Baru</h1>
                    <p class="text-gray-500 text-sm">Daftar untuk mulai menyewa peralatan outdoor premium.</p>
                </div>

                <!-- Error Box -->
                <?php if($errors->any()): ?>
                <div class="bg-red-50 border border-red-200 text-red-600 rounded-sm px-4 py-3 mb-6 flex items-start gap-3 text-sm">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p><?php echo e($error); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Register Form -->
                <form method="POST" action="<?php echo e(route('register')); ?>" class="space-y-4 animate-slide-up-delay-2" id="registerForm">
                    <?php echo csrf_field(); ?>

                    <!-- Nama Lengkap -->
                    <div class="input-focus border border-gray-300 rounded-sm px-4 py-3 transition-colors duration-200">
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1 transition-colors">Nama Lengkap</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="<?php echo e(old('name')); ?>"
                            placeholder="Masukkan nama lengkap"
                            class="w-full bg-transparent text-sm outline-none placeholder:text-gray-500"
                            required
                            autofocus
                        >
                    </div>

                    <!-- Email -->
                    <div class="input-focus border border-gray-300 rounded-sm px-4 py-3 transition-colors duration-200">
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1 transition-colors">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo e(old('email')); ?>"
                            placeholder="nama@email.com"
                            class="w-full bg-transparent text-sm outline-none placeholder:text-gray-500"
                            required
                        >
                    </div>

                    <!-- Nomor Handphone -->
                    <div class="input-focus border border-gray-300 rounded-sm px-4 py-3 transition-colors duration-200">
                        <label for="phone_number" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1 transition-colors">Nomor Handphone</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-400 flex-shrink-0 border-r border-gray-200 pr-2">+62</span>
                            <input
                                type="tel"
                                id="phone_number"
                                name="phone_number"
                                value="<?php echo e(old('phone_number')); ?>"
                                placeholder="812 3456 7890"
                                class="w-full bg-transparent text-sm outline-none placeholder:text-gray-500"
                            >
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="input-focus border border-gray-300 rounded-sm px-4 py-3 transition-colors duration-200">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1 transition-colors">Password</label>
                        <div class="flex items-center">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Minimal 8 karakter"
                                class="w-full bg-transparent text-sm outline-none placeholder:text-gray-500"
                                required
                            >
                            <button type="button" id="togglePassword" class="text-gray-400 hover:text-brand-orange transition ml-2 flex-shrink-0">
                                <svg id="eyeOff" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                </svg>
                                <svg id="eyeOn" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <!-- Password Strength Indicator -->
                        <div class="flex gap-1 mt-2" id="strengthBars">
                            <div class="h-1 flex-1 bg-gray-200 rounded-full strength-bar" id="bar1"></div>
                            <div class="h-1 flex-1 bg-gray-200 rounded-full strength-bar" id="bar2"></div>
                            <div class="h-1 flex-1 bg-gray-200 rounded-full strength-bar" id="bar3"></div>
                            <div class="h-1 flex-1 bg-gray-200 rounded-full strength-bar" id="bar4"></div>
                        </div>
                        <p class="text-xs mt-1 text-gray-500" id="strengthText"></p>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="input-focus border border-gray-300 rounded-sm px-4 py-3 transition-colors duration-200">
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1 transition-colors">Konfirmasi Password</label>
                        <div class="flex items-center">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Ulangi password"
                                class="w-full bg-transparent text-sm outline-none placeholder:text-gray-500"
                                required
                            >
                            <div id="matchIcon" class="ml-2 flex-shrink-0 hidden">
                                <svg id="matchOk" class="w-5 h-5 text-green-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <svg id="matchFail" class="w-5 h-5 text-red-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        id="submitBtn"
                        class="w-full bg-brand-black text-white font-display font-bold uppercase tracking-wider py-4 rounded-sm hover:bg-brand-orange transition-colors duration-300 flex items-center justify-center gap-2 relative overflow-hidden group mt-2"
                    >
                        <span id="btnText">Daftar Sekarang</span>
                        <svg id="btnSpinner" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <div class="absolute inset-0 bg-white/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-500 skew-x-12"></div>
                    </button>
                </form>

                <!-- Divider -->
                <div class="flex items-center gap-4 my-6">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-xs text-gray-400 uppercase tracking-widest">atau</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <!-- Social Login -->
                <div class="space-y-3">
                    <button type="button" class="w-full border border-gray-300 rounded-sm py-3 flex items-center justify-center gap-3 text-sm font-medium hover:border-gray-400 hover:bg-gray-50 transition group">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span class="text-gray-600 group-hover:text-brand-black transition">Daftar dengan Google</span>
                    </button>

                    <button type="button" class="w-full border border-gray-300 rounded-sm py-3 flex items-center justify-center gap-3 text-sm font-medium hover:border-gray-400 hover:bg-gray-50 transition group">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.32 2.32-1.55 4.3-3.74 4.25z"/>
                        </svg>
                        <span class="text-gray-600 group-hover:text-brand-black transition">Daftar dengan Apple</span>
                    </button>
                </div>

                <!-- Login Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">
                        Sudah punya akun?
                        <a href="<?php echo e(route('login')); ?>" class="font-bold text-brand-orange hover:text-orange-700 transition ml-1">Masuk di Sini</a>
                    </p>
                </div>

                <!-- Footer Note -->
                <p class="mt-4 text-xs text-gray-500 text-center leading-relaxed pb-4">
                    Dengan mendaftar, Anda menyetujui <a href="#" class="text-brand-orange hover:underline">Syarat & Ketentuan</a> dan <a href="#" class="text-brand-orange hover:underline">Kebijakan Privasi</a> Matterhorn.
                </p>
            </div>
        </div>

        <!-- RIGHT SIDE: IMAGE -->
        <div class="hidden lg:block lg:w-1/2 auth-image relative">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>

            <!-- Content over image -->
            <div class="absolute bottom-0 left-0 right-0 p-12 xl:p-16 text-white">
                <span class="bg-brand-orange text-white text-xs font-bold px-2 py-1 uppercase tracking-widest mb-4 inline-block">Join Us</span>
                <h2 class="font-display font-bold text-4xl xl:text-5xl uppercase leading-none mb-3">Bergabung<br>Dengan Kami</h2>
                <p class="text-gray-300 text-sm max-w-sm font-light leading-relaxed">Dapatkan akses ke ribuan peralatan outdoor premium dan promo eksklusif member.</p>

                <!-- Stats -->
                <div class="flex items-center gap-8 mt-8">
                    <div>
                        <span class="font-display font-bold text-2xl text-brand-orange">2.4K+</span>
                        <p class="text-xs text-gray-400 mt-1">Member Aktif</p>
                    </div>
                    <div class="w-px h-10 bg-white/20"></div>
                    <div>
                        <span class="font-display font-bold text-2xl text-white">500+</span>
                        <p class="text-xs text-gray-400 mt-1">Peralatan</p>
                    </div>
                    <div class="w-px h-10 bg-white/20"></div>
                    <div>
                        <span class="font-display font-bold text-2xl text-white">4.9</span>
                        <p class="text-xs text-gray-400 mt-1">Rating</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Hero -->
        <div class="lg:hidden auth-image h-48 relative -order-1">
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 to-black/70"></div>
            <div class="absolute bottom-4 left-6 text-white">
                <h2 class="font-display font-bold text-xl uppercase">Bergabung Sekarang</h2>
                <p class="text-gray-300 text-xs">Daftar dan mulai petualanganmu.</p>
            </div>
        </div>

    </div>

    <script>
        // Toggle password visibility
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeOff = document.getElementById('eyeOff');
        const eyeOn = document.getElementById('eyeOn');

        toggleBtn.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeOff.classList.toggle('hidden');
            eyeOn.classList.toggle('hidden');
        });

        // Password strength indicator
        const bar1 = document.getElementById('bar1');
        const bar2 = document.getElementById('bar2');
        const bar3 = document.getElementById('bar3');
        const bar4 = document.getElementById('bar4');
        const strengthText = document.getElementById('strengthText');

        passwordInput.addEventListener('input', () => {
            const val = passwordInput.value;
            let score = 0;

            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const bars = [bar1, bar2, bar3, bar4];
            const colors = ['#D32F2F', '#D64500', '#B45309', '#15803D'];
            const labels = ['Lemah', 'Cukup', 'Bagus', 'Kuat'];

            bars.forEach((bar, i) => {
                if (i < score) {
                    bar.style.backgroundColor = colors[score - 1];
                } else {
                    bar.style.backgroundColor = '#e5e7eb';
                }
            });

            strengthText.textContent = val.length > 0 ? labels[score - 1] || '' : '';
            strengthText.style.color = val.length > 0 ? colors[score - 1] || '#9CA3AF' : '#9CA3AF';
        });

        // Password match indicator
        const confirmInput = document.getElementById('password_confirmation');
        const matchIcon = document.getElementById('matchIcon');
        const matchOk = document.getElementById('matchOk');
        const matchFail = document.getElementById('matchFail');

        confirmInput.addEventListener('input', () => {
            const val = confirmInput.value;
            if (val.length === 0) {
                matchIcon.classList.add('hidden');
                return;
            }

            matchIcon.classList.remove('hidden');
            if (val === passwordInput.value) {
                matchOk.classList.remove('hidden');
                matchFail.classList.add('hidden');
            } else {
                matchOk.classList.add('hidden');
                matchFail.classList.remove('hidden');
            }
        });

        // Loading state on form submit
        const form = document.getElementById('registerForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');

        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            btnText.textContent = 'Memproses...';
            btnSpinner.classList.remove('hidden');
        });
    </script>

</body>
</html>
<?php /**PATH C:\MyWork\Jurusan\WEBSITE\code\laravel\tent\resources\views/auth/register.blade.php ENDPATH**/ ?>