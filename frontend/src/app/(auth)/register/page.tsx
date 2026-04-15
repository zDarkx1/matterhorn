'use client';

import { useState } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { useAuthStore } from '@/stores/useAuthStore';
import { Eye, EyeOff, ArrowRight } from 'lucide-react';

export default function RegisterPage() {
  const router = useRouter();
  const { register, loading } = useAuthStore();
  const [form, setForm] = useState({ name: '', email: '', phone_number: '', password: '', password_confirmation: '' });
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState('');

  const updateField = (field: string, value: string) => setForm((prev) => ({ ...prev, [field]: value }));

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    if (form.password !== form.password_confirmation) {
      setError('Password tidak sama.');
      return;
    }

    try {
      await register(form);
      router.push('/');
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Registrasi gagal.');
    }
  };

  return (
    <div className="w-full max-w-md mx-4">
      <div className="bg-white border border-gray-200 shadow-sm">
        <div className="bg-brand-black text-white p-8">
          <Link href="/" className="font-display font-bold text-3xl tracking-tighter uppercase block mb-2">MATTERHORN</Link>
          <p className="text-gray-400 text-sm">Buat akun baru untuk mulai menyewa.</p>
        </div>

        <form onSubmit={handleSubmit} className="p-8 space-y-4">
          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">{error}</div>
          )}

          <div>
            <label className="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-2">Nama Lengkap</label>
            <input type="text" value={form.name} onChange={(e) => updateField('name', e.target.value)} className="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition" placeholder="John Doe" required />
          </div>

          <div>
            <label className="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-2">Email</label>
            <input type="email" value={form.email} onChange={(e) => updateField('email', e.target.value)} className="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition" placeholder="email@example.com" required />
          </div>

          <div>
            <label className="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-2">No. Telepon</label>
            <input type="tel" value={form.phone_number} onChange={(e) => updateField('phone_number', e.target.value)} className="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition" placeholder="08xxxxxxxxxx" />
          </div>

          <div>
            <label className="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-2">Password</label>
            <div className="relative">
              <input type={showPassword ? 'text' : 'password'} value={form.password} onChange={(e) => updateField('password', e.target.value)} className="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition pr-12" placeholder="Minimal 8 karakter" required minLength={8} />
              <button type="button" onClick={() => setShowPassword(!showPassword)} className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
              </button>
            </div>
          </div>

          <div>
            <label className="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-2">Konfirmasi Password</label>
            <input type="password" value={form.password_confirmation} onChange={(e) => updateField('password_confirmation', e.target.value)} className="w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-orange transition" placeholder="Ulangi password" required />
          </div>

          <button type="submit" disabled={loading} className="w-full bg-brand-orange text-white font-display font-bold uppercase tracking-wider py-3 hover:bg-orange-700 transition disabled:opacity-50 flex items-center justify-center gap-2">
            {loading ? 'Memproses...' : (<>Daftar <ArrowRight className="w-4 h-4" /></>)}
          </button>

          <p className="text-center text-sm text-gray-500">
            Sudah punya akun?{' '}
            <Link href="/login" className="text-brand-orange font-bold hover:underline">Masuk</Link>
          </p>
        </form>
      </div>
    </div>
  );
}
