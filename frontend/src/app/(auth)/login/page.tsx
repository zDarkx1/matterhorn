'use client';

import { useState, useRef, useEffect, Suspense } from 'react';
import Link from 'next/link';
import { useRouter, useSearchParams } from 'next/navigation';
import { useAuthStore } from '@/stores/useAuthStore';
import { Eye, EyeOff } from 'lucide-react';
import Image from 'next/image';

const BACKEND_URL = process.env.NEXT_PUBLIC_BACKEND_URL || 'http://localhost:8000';

function LoginContent() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const { login, loading } = useAuthStore();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState('');
  const errorRef = useRef<HTMLDivElement>(null);

  // Pick up Google OAuth error from URL
  useEffect(() => {
    const urlError = searchParams.get('error');
    if (urlError) {
      setError(decodeURIComponent(urlError));
      // Clean the URL so the error doesn't persist on refresh
      window.history.replaceState({}, '', '/login');
    }
  }, [searchParams]);

  useEffect(() => {
    if (error && errorRef.current) {
      errorRef.current.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }, [error]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    try {
      await login({ email, password });
      router.push('/');
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Login gagal.');
    }
  };

  const handleGoogleLogin = () => {
    window.location.href = `${BACKEND_URL}/auth/google/redirect`;
  };

  return (
    <div className="flex min-h-[calc(100vh-60px)] w-full font-sans">
      {/* Left Column - Form */}
      <div className="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12">
        <div className="w-full max-w-md">
          <h1 className="text-2xl font-bold text-gray-900 mb-6">Masuk atau Daftar Akun</h1>

          <div className="bg-[#f0f5fa] rounded-lg p-4 mb-6 flex items-start gap-3">
            <div className="mt-1 flex-shrink-0" aria-hidden="true">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" stroke="#e05424" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
              </svg>
            </div>
            <p className="text-sm text-gray-600 leading-relaxed">
              Apakah kamu sudah memiliki akun Matterhorn? Silakan masuk dengan menggunakan email terdaftar
            </p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4" noValidate>
            {error && (
              <div ref={errorRef} role="alert" aria-live="assertive" className="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg animate-[error-pulse_0.5s_ease-in-out]">
                {error}
              </div>
            )}

            <div>
              <label htmlFor="login-email" className="block text-sm font-semibold text-gray-900 mb-2">Email</label>
              <input
                id="login-email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm transition focus-visible:ring-2 focus-visible:ring-brand-orange focus-visible:border-transparent"
                placeholder="Masukkan email"
                required
                autoComplete="email"
              />
            </div>

            <div>
              <label htmlFor="login-password" className="block text-sm font-semibold text-gray-900 mb-2">Password</label>
              <div className="relative">
                <input
                  id="login-password"
                  type={showPassword ? 'text' : 'password'}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm transition pr-12 focus-visible:ring-2 focus-visible:ring-brand-orange focus-visible:border-transparent"
                  placeholder="Masukkan password"
                  required
                  autoComplete="current-password"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 rounded p-1"
                  aria-label={showPassword ? 'Sembunyikan password' : 'Tampilkan password'}
                >
                  {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                </button>
              </div>
            </div>

            <button
              type="submit"
              disabled={loading}
              className="w-full bg-black text-white font-semibold py-3.5 rounded-lg hover:bg-gray-800 transition shadow-md mt-4 disabled:opacity-50"
            >
              {loading ? 'Memproses...' : 'Lanjut'}
            </button>
            
            <div className="text-center mt-3">
              <Link href="#" className="text-sm font-semibold text-black underline decoration-black underline-offset-4 decoration-2">Lupa kata sandi</Link>
            </div>
          </form>

          <div className="flex items-center gap-4 my-8" aria-hidden="true">
            <div className="h-px bg-gray-200 flex-1"></div>
            <span className="text-sm text-gray-400">or</span>
            <div className="h-px bg-gray-200 flex-1"></div>
          </div>

          <button
            type="button"
            onClick={handleGoogleLogin}
            className="w-full bg-white border border-gray-300 text-gray-700 font-semibold py-3 rounded-lg hover:bg-gray-50 transition flex items-center justify-center gap-3 mb-6 shadow-sm"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
              <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
              <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
              <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Lanjutkan dengan Google
          </button>

          <p className="text-xs text-gray-400 leading-relaxed mt-12 text-center">
            This site is protected by reCAPTCHA and the Google <Link href="#" className="font-medium text-[#e05424]">Privacy Policy</Link> and <Link href="#" className="font-medium text-[#e05424]">Terms of Service</Link> apply.
          </p>
          
          <p className="text-center text-sm text-gray-500 mt-6">
            Belum punya akun?{' '}
            <Link href="/register" className="text-[#e05424] font-bold hover:underline">Daftar Sekarang</Link>
          </p>
        </div>
      </div>

      {/* Right Column - Image */}
      <div className="hidden lg:block lg:w-1/2 relative bg-gray-100">
        <Image
          src="/images/hiking.png"
          alt=""
          fill
          style={{ objectFit: 'cover' }}
          priority
        />
      </div>
    </div>
  );
}

export default function LoginPage() {
  return (
    <Suspense fallback={<div className="min-h-screen flex items-center justify-center">Memuat...</div>}>
      <LoginContent />
    </Suspense>
  );
}
