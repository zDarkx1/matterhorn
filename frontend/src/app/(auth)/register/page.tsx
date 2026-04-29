'use client';

import { useState } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { useAuthStore } from '@/stores/useAuthStore';
import { Eye, EyeOff } from 'lucide-react';
import Image from 'next/image';

export default function RegisterPage() {
  const router = useRouter();
  const { register, loading } = useAuthStore();
  const [form, setForm] = useState({ name: '', email: '', phone_number: '', password: '', password_confirmation: '' });
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState('');

  const updateField = (field: string, value: string) => setForm((prev) => ({ ...prev, [field]: value }));

  const handlePhoneChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    let value = e.target.value.replace(/\D/g, '');
    if (value.startsWith('0')) {
      value = value.substring(1);
    }
    if (value.length > 13) value = value.slice(0, 13);
    updateField('phone_number', value);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(form.email)) {
      setError('Format email tidak valid. Pastikan menggunakan @ dan domain.');
      return;
    }

    if (form.password !== form.password_confirmation) {
      setError('Password tidak sama.');
      return;
    }


    try {
      const submissionForm = {
        ...form,
        phone_number: form.phone_number ? `+62${form.phone_number}` : ''
      };
      await register(submissionForm);
      router.push('/');
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Registrasi gagal.');
    }
  };

  return (
    <div className="flex min-h-[calc(100vh-[60px])] w-full font-sans">
      {/* Left Column - Form */}
      <div className="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12">
        <div className="w-full max-w-md">
          <h1 className="text-2xl font-bold text-gray-900 mb-6">Masuk atau Daftar Akun</h1>

          <div className="bg-[#f0f5fa] rounded-lg p-4 mb-6 flex items-start gap-3">
            <div className="mt-1 flex-shrink-0">
               {/* Icon matching the 'EAC' box in the image */}
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" stroke="#e05424" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
              </svg>
            </div>
            <p className="text-sm text-gray-600 leading-relaxed">
              Buat akun Matterhorn baru untuk mulai menyewa perlengkapan outdoor.
            </p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            {error && (
              <div className="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
                {error}
              </div>
            )}

            <div>
              <label className="block text-sm font-semibold text-gray-900 mb-2">Nama Lengkap</label>
              <input type="text" value={form.name} onChange={(e) => updateField('name', e.target.value)} className="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm outline-none focus:border-black transition" placeholder="John Doe" required />
            </div>

            <div>
              <label className="block text-sm font-semibold text-gray-900 mb-2">Email</label>
              <input type="email" value={form.email} onChange={(e) => updateField('email', e.target.value)} className="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm outline-none focus:border-black transition" placeholder="email@example.com" required />
            </div>

            <div>
              <label className="block text-sm font-semibold text-gray-900 mb-2">No. Telepon</label>
              <div className="flex w-full border border-gray-300 rounded-lg overflow-hidden focus-within:border-black transition">
                <div className="bg-gray-50 border-r border-gray-300 px-4 py-3 text-sm text-gray-700 flex items-center justify-center font-medium">
                  +62
                </div>
                <input 
                  type="tel" 
                  value={form.phone_number} 
                  onChange={handlePhoneChange} 
                  className="w-full px-4 py-3 text-sm outline-none" 
                  placeholder="81234567890" 
                />
              </div>
            </div>

            <div>
              <label className="block text-sm font-semibold text-gray-900 mb-2">Password</label>
              <div className="relative">
                <input type={showPassword ? 'text' : 'password'} value={form.password} onChange={(e) => updateField('password', e.target.value)} className="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm outline-none focus:border-black transition pr-12" placeholder="Minimal 8 karakter" required minLength={8} />
                <button type="button" onClick={() => setShowPassword(!showPassword)} className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                  {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                </button>
              </div>
            </div>

            <div>
              <label className="block text-sm font-semibold text-gray-900 mb-2">Konfirmasi Password</label>
              <input type="password" value={form.password_confirmation} onChange={(e) => updateField('password_confirmation', e.target.value)} className="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm outline-none focus:border-black transition" placeholder="Ulangi password" required />
            </div>

            <button type="submit" disabled={loading} className="w-full bg-black text-white font-semibold py-3.5 rounded-lg hover:bg-gray-800 transition shadow-md mt-4">
              {loading ? 'Memproses...' : 'Daftar Akun'}
            </button>
            
          </form>

          <div className="flex items-center gap-4 my-8">
            <div className="h-px bg-gray-200 flex-1"></div>
            <span className="text-sm text-gray-400">or</span>
            <div className="h-px bg-gray-200 flex-1"></div>
          </div>

          <button
            type="button"
            className="w-full bg-white border border-gray-300 text-gray-700 font-semibold py-3 rounded-lg hover:bg-gray-50 transition flex items-center justify-center gap-3 mb-6 shadow-sm"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
              <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
              <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
              <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Continue with Google Account
          </button>

          <p className="text-xs text-gray-400 leading-relaxed mt-12 text-center">
            This site is protected by reCAPTCHA and the Google <Link href="#" className="font-medium text-[#e05424]">Privacy Policy</Link> and <Link href="#" className="font-medium text-[#e05424]">Terms of Service</Link> apply.
          </p>
          
          <p className="text-center text-sm text-gray-500 mt-6">
            Sudah punya akun?{' '}
            <Link href="/login" className="text-[#e05424] font-bold hover:underline">Masuk</Link>
          </p>
        </div>
      </div>

      {/* Right Column - Image */}
      <div className="hidden lg:block lg:w-1/2 relative bg-gray-100">
        <Image
          src="/images/hiking.png"
          alt="Hiking Background"
          fill
          style={{ objectFit: 'cover' }}
          priority
        />
      </div>
    </div>
  );
}
