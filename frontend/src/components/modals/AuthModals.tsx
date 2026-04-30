'use client';

import { useState, useId } from 'react';
import { useRouter } from 'next/navigation';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useAuthStore } from '@/stores/useAuthStore';
import { toast } from 'sonner';

interface SessionExpiredModalProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
}

export function SessionExpiredModal({ open, onOpenChange }: SessionExpiredModalProps) {
  const [showSignIn, setShowSignIn] = useState(false);

  if (showSignIn) {
    return <SignInModal open={true} onOpenChange={(v) => { setShowSignIn(v); onOpenChange(v); }} />;
  }

  return (
    <Dialog open={open} onOpenChange={({ open: o }) => onOpenChange(o)}>
      <DialogContent showCloseButton={false}>
        <DialogHeader>
          <DialogTitle>Sesi Berakhir</DialogTitle>
          <DialogDescription>
            Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan masuk kembali untuk melanjutkan.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button className="w-full" onClick={() => setShowSignIn(true)}>
            Masuk Kembali
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

interface SignInModalProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
}

export function SignInModal({ open, onOpenChange }: SignInModalProps) {
  const id = useId();
  const router = useRouter();
  const { login, loading } = useAuthStore();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    try {
      await login({ email, password });
      toast.success('Berhasil masuk kembali.');
      onOpenChange(false);
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Login gagal.');
    }
  };

  return (
    <Dialog open={open} onOpenChange={({ open: o }) => onOpenChange(o)}>
      <DialogContent className="sm:max-w-sm">
        <div className="flex flex-col items-center gap-2">
          <div className="flex size-11 shrink-0 items-center justify-center rounded-full border" aria-hidden="true">
            <span className="font-display font-bold text-lg text-brand-black">M</span>
          </div>
          <DialogHeader>
            <DialogTitle className="sm:text-center">Selamat Datang Kembali</DialogTitle>
            <DialogDescription className="sm:text-center">
              Masukkan kredensial Anda untuk melanjutkan.
            </DialogDescription>
          </DialogHeader>
        </div>

        <form onSubmit={handleSubmit} className="space-y-5">
          {error && (
            <div role="alert" aria-live="assertive" className="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg animate-[error-pulse_0.5s_ease-in-out]">
              {error}
            </div>
          )}
          <div className="space-y-4">
            <div className="*:not-first:mt-2">
              <Label htmlFor={`${id}-email`}>Email</Label>
              <Input
                id={`${id}-email`}
                placeholder="email@example.com"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
              />
            </div>
            <div className="*:not-first:mt-2">
              <Label htmlFor={`${id}-password`}>Password</Label>
              <Input
                id={`${id}-password`}
                placeholder="Masukkan password"
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
              />
            </div>
          </div>
          <Button type="submit" className="w-full" disabled={loading}>
            {loading ? 'Memproses...' : 'Masuk'}
          </Button>
        </form>
      </DialogContent>
    </Dialog>
  );
}
