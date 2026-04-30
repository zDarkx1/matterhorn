'use client';

import { useEffect, useState, Suspense } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
import { useAuthStore } from '@/stores/useAuthStore';

function GoogleCallbackContent() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const { setAuth } = useAuthStore();
  const [error, setError] = useState('');

  useEffect(() => {
    const token = searchParams.get('token');
    const userParam = searchParams.get('user');
    const errorParam = searchParams.get('error');

    if (errorParam) {
      setError(decodeURIComponent(errorParam));
      setTimeout(() => router.push('/login'), 3000);
      return;
    }

    if (token && userParam) {
      try {
        const user = JSON.parse(decodeURIComponent(userParam));
        setAuth(user, token);
        router.push('/');
      } catch {
        setError('Gagal memproses data login Google.');
        setTimeout(() => router.push('/login'), 3000);
      }
    } else {
      setError('Token atau data user tidak ditemukan.');
      setTimeout(() => router.push('/login'), 3000);
    }
  }, [searchParams, setAuth, router]);

  if (error) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-white">
        <div className="text-center max-w-md mx-auto px-4">
          <div className="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg className="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            </svg>
          </div>
          <h1 className="text-xl font-bold text-gray-900 mb-2">Login Gagal</h1>
          <p className="text-sm text-gray-500 mb-4" role="alert">{error}</p>
          <p className="text-xs text-gray-400">Mengalihkan ke halaman login...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-white">
      <div className="text-center">
        <div className="w-12 h-12 border-4 border-gray-200 border-t-brand-orange rounded-full animate-spin mx-auto mb-4" role="status">
          <span className="sr-only">Memproses login Google...</span>
        </div>
        <p className="text-sm text-gray-500">Memproses login Google...</p>
      </div>
    </div>
  );
}

export default function GoogleCallbackPage() {
  return (
    <Suspense fallback={
      <div className="min-h-screen flex items-center justify-center bg-white">
        <div className="w-12 h-12 border-4 border-gray-200 border-t-brand-orange rounded-full animate-spin" role="status">
          <span className="sr-only">Memuat...</span>
        </div>
      </div>
    }>
      <GoogleCallbackContent />
    </Suspense>
  );
}
