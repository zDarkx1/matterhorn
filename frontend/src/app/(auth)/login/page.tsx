'use client';

import { useState } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { useAuthStore } from '@/stores/useAuthStore';
import { Eye, EyeOff, ArrowRight } from 'lucide-react';

export default function LoginPage() {
  const router = useRouter();
  const { login, loading } = useAuthStore();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState('');

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

  return (
    <div className="w-full max-w-md mx-4">
      <div className="bg-white border border-gray-200 shadow-sm">
        {/* Header */}
        <div className="bg-brand-black text-white p-8">
          <Link href="/" className="font-display font-bold text-3xl tracking-tighter uppercase block mb-2">MATTERHORN</Link>
          <p className="text-gray-400 text-sm">Masuk ke akun Anda untuk melanjutkan.</p>
        </div>

        {/* Form */}
        <form onSubmit={handleSubmit} className="p-8 space-y-5">
          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
              {error}
            </div>
          )}

          <div>
            <label className="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-2">Email</label>
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition"
              placeholder="email@example.com"
              required
            />
          </div>

          <div>
            <label className="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-2">Password</label>
            <div className="relative">
              <input
                type={showPassword ? 'text' : 'password'}
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                className="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition pr-12"
                placeholder="••••••••"
                required
              />
              <button
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
              >
                {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
              </button>
            </div>
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full bg-brand-orange text-white font-display font-bold uppercase tracking-wider py-3 hover:bg-orange-700 transition disabled:opacity-50 flex items-center justify-center gap-2"
          >
            {loading ? 'Memproses...' : (<>Masuk <ArrowRight className="w-4 h-4" /></>)}
          </button>

          <p className="text-center text-sm text-gray-500">
            Belum punya akun?{' '}
            <Link href="/register" className="text-brand-orange font-bold hover:underline">Daftar Sekarang</Link>
          </p>
        </form>
      </div>
    </div>
  );
}
