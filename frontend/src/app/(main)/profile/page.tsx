'use client';

import { useEffect, useState, useRef } from 'react';
import { useRouter } from 'next/navigation';
import { useAuthStore } from '@/stores/useAuthStore';
import { apiFetch } from '@/lib/api';
import { toast } from 'sonner';
import type { UserAddress } from '@/types';
import { User, Mail, Phone, MapPin, Plus, Trash2, Star, Pencil } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from '@/components/ui/dialog';
import { Checkbox } from '@/components/ui/checkbox';

const EMPTY_ADDR = {
  label: '',
  recipient_name: '',
  phone: '',
  full_address: '',
  district: '',
  city: '',
  province: '',
  postal_code: '',
  notes: '',
  is_default: false,
};

export default function ProfilePage() {
  const router = useRouter();
  const { user, fetchMe } = useAuthStore();
  const [addresses, setAddresses] = useState<UserAddress[]>([]);
  const [editing, setEditing] = useState(false);
  const [form, setForm] = useState({ name: '', phone_number: '' });
  const [saving, setSaving] = useState(false);
  const [addrError, setAddrError] = useState('');
  const errorRef = useRef<HTMLDivElement>(null);

  // Address modal states
  const [addrModalOpen, setAddrModalOpen] = useState(false);
  const [addrForm, setAddrForm] = useState(EMPTY_ADDR);
  const [editingAddrId, setEditingAddrId] = useState<number | null>(null);
  const [savingAddr, setSavingAddr] = useState(false);

  // Delete confirmation
  const [deleteAddrId, setDeleteAddrId] = useState<number | null>(null);

  useEffect(() => {
    if (!user) { router.push('/login'); return; }
    setForm({ name: user.name, phone_number: user.phone_number || '' });
    loadAddresses();
  }, [user, router]);

  const loadAddresses = async () => {
    try {
      const res = await apiFetch<UserAddress[]>('/addresses');
      setAddresses(res.data || []);
    } catch { /* ignore */ }
  };

  // ── Profile Save ──────────────────────────────────────
  const handleSaveProfile = async () => {
    setSaving(true);
    try {
      await apiFetch('/profile', { method: 'PUT', body: JSON.stringify(form) });
      await fetchMe();
      setEditing(false);
      toast.success('Profil berhasil diperbarui.');
    } catch {
      toast.error('Gagal memperbarui profil.');
    } finally {
      setSaving(false);
    }
  };

  // ── Address CRUD ──────────────────────────────────────
  const openAddAddress = () => {
    setEditingAddrId(null);
    setAddrError('');
    setAddrForm({ ...EMPTY_ADDR, recipient_name: user?.name || '' });
    setAddrModalOpen(true);
  };

  const openEditAddress = (addr: UserAddress) => {
    setEditingAddrId(addr.id);
    setAddrError('');
    setAddrForm({
      label: addr.label,
      recipient_name: addr.recipient_name,
      phone: addr.phone,
      full_address: addr.full_address,
      district: addr.district || '',
      city: addr.city,
      province: addr.province,
      postal_code: addr.postal_code,
      notes: addr.notes || '',
      is_default: addr.is_default,
    });
    setAddrModalOpen(true);
  };

  const handleSaveAddress = async () => {
    setAddrError('');
    // Client-side validation
    if (!addrForm.label.trim() || !addrForm.recipient_name.trim() || !addrForm.phone.trim() || !addrForm.full_address.trim() || !addrForm.district.trim() || !addrForm.city.trim() || !addrForm.province.trim() || !addrForm.postal_code.trim()) {
      setAddrError('Semua field wajib diisi kecuali Catatan.');
      setTimeout(() => errorRef.current?.scrollIntoView({ behavior: 'smooth', block: 'center' }), 100);
      return;
    }

    setSavingAddr(true);
    try {
      if (editingAddrId) {
        await apiFetch(`/addresses/${editingAddrId}`, { method: 'PUT', body: JSON.stringify(addrForm) });
        toast.success('Alamat berhasil diperbarui.');
      } else {
        await apiFetch('/addresses', { method: 'POST', body: JSON.stringify(addrForm) });
        toast.success('Alamat berhasil ditambahkan.');
      }
      setAddrModalOpen(false);
      loadAddresses();
    } catch (e) {
      const msg = e instanceof Error ? e.message : 'Gagal menyimpan alamat.';
      setAddrError(msg);
      setTimeout(() => errorRef.current?.scrollIntoView({ behavior: 'smooth', block: 'center' }), 100);
    } finally {
      setSavingAddr(false);
    }
  };

  const handleDeleteAddress = async (id: number) => {
    try {
      await apiFetch(`/addresses/${id}`, { method: 'DELETE' });
      toast.info('Alamat berhasil dihapus.');
      setDeleteAddrId(null);
      loadAddresses();
    } catch {
      toast.error('Gagal menghapus alamat.');
    }
  };

  const handleSetDefault = async (id: number) => {
    try {
      await apiFetch(`/addresses/${id}/default`, { method: 'PUT' });
      toast.success('Alamat utama diperbarui.');
      loadAddresses();
    } catch {
      toast.error('Gagal mengatur alamat utama.');
    }
  };

  if (!user) return null;

  return (
    <section className="py-8 bg-white min-h-screen">
      <div className="max-w-3xl mx-auto px-4">
        <h1 className="font-display font-bold text-2xl sm:text-3xl uppercase mb-8 flex items-center gap-3">
          <span className="w-1 h-8 bg-brand-orange block" aria-hidden="true" />
          Profil Saya
        </h1>

        {/* ═══ User Info ═══ */}
        <div className="border border-gray-200 p-4 sm:p-6 mb-6 rounded-lg">
          <div className="flex justify-between items-center mb-6">
            <h2 className="font-display font-bold uppercase text-sm">Informasi Akun</h2>
            <button
              onClick={() => setEditing(!editing)}
              className="text-sm text-brand-orange font-bold hover:underline focus-visible:outline-2 focus-visible:outline-brand-orange focus-visible:outline-offset-2 rounded"
              aria-label={editing ? 'Batalkan edit profil' : 'Edit profil'}
            >
              {editing ? 'Batal' : 'Edit'}
            </button>
          </div>

          {editing ? (
            <div className="space-y-4">
              <div>
                <Label htmlFor="profile-name" className="text-xs font-bold uppercase text-gray-700 mb-1">Nama</Label>
                <Input
                  id="profile-name"
                  value={form.name}
                  onChange={(e) => setForm({ ...form, name: e.target.value })}
                />
              </div>
              <div>
                <Label htmlFor="profile-phone" className="text-xs font-bold uppercase text-gray-700 mb-1">No. Telepon</Label>
                <Input
                  id="profile-phone"
                  type="tel"
                  value={form.phone_number}
                  onChange={(e) => setForm({ ...form, phone_number: e.target.value })}
                />
              </div>
              <Button onClick={handleSaveProfile} disabled={saving} className="bg-brand-orange hover:bg-orange-700 text-white">
                {saving ? 'Menyimpan...' : 'Simpan'}
              </Button>
            </div>
          ) : (
            <div className="space-y-3">
              <div className="flex items-center gap-3 text-sm">
                <User className="w-4 h-4 text-gray-400" aria-hidden="true" /><span>{user.name}</span>
              </div>
              <div className="flex items-center gap-3 text-sm">
                <Mail className="w-4 h-4 text-gray-400" aria-hidden="true" /><span>{user.email}</span>
              </div>
              <div className="flex items-center gap-3 text-sm">
                <Phone className="w-4 h-4 text-gray-400" aria-hidden="true" /><span>{user.phone_number || '-'}</span>
              </div>
            </div>
          )}
        </div>

        {/* ═══ Addresses ═══ */}
        <div className="border border-gray-200 p-4 sm:p-6 rounded-lg">
          <div className="flex justify-between items-center mb-6">
            <h2 className="font-display font-bold uppercase text-sm flex items-center gap-2">
              <MapPin className="w-4 h-4 text-brand-orange" aria-hidden="true" /> Alamat Tersimpan
            </h2>
            <Button size="sm" variant="outline" onClick={openAddAddress} className="gap-1">
              <Plus className="w-4 h-4" aria-hidden="true" /> Tambah Alamat
            </Button>
          </div>

          {addresses.length === 0 ? (
            <div className="text-center py-10 bg-gray-50 rounded-lg">
              <MapPin className="w-10 h-10 text-gray-300 mx-auto mb-3" aria-hidden="true" />
              <p className="text-gray-500 text-sm mb-4">Belum ada alamat tersimpan.</p>
              <Button size="sm" onClick={openAddAddress} className="bg-brand-orange hover:bg-orange-700 text-white">
                Tambah Alamat Pertama
              </Button>
            </div>
          ) : (
            <div className="space-y-4">
              {addresses.map((addr) => (
                <div key={addr.id} className={`border p-4 rounded-lg ${addr.is_default ? 'border-brand-orange bg-orange-50/50' : 'border-gray-200'}`}>
                  <div className="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                    <div className="flex-1 min-w-0">
                      <div className="flex items-center gap-2 mb-1 flex-wrap">
                        <span className="font-bold text-sm">{addr.label}</span>
                        {addr.is_default && (
                          <span className="bg-brand-orange text-white text-[10px] px-1.5 py-0.5 font-bold rounded flex items-center gap-1">
                            <Star className="w-3 h-3" aria-hidden="true" /> Utama
                          </span>
                        )}
                      </div>
                      <p className="text-sm">{addr.recipient_name} &bull; {addr.phone}</p>
                      <p className="text-sm text-gray-500">{addr.full_address}, {addr.district}, {addr.city}, {addr.province} {addr.postal_code}</p>
                      {addr.notes && <p className="text-xs text-gray-400 mt-1">Catatan: {addr.notes}</p>}
                    </div>
                    <div className="flex items-center gap-1 flex-shrink-0">
                      {!addr.is_default && (
                        <Button size="sm" variant="ghost" onClick={() => handleSetDefault(addr.id)} aria-label={`Jadikan ${addr.label} sebagai alamat utama`} className="text-xs text-gray-500 hover:text-brand-orange">
                          <Star className="w-4 h-4" />
                        </Button>
                      )}
                      <Button size="sm" variant="ghost" onClick={() => openEditAddress(addr)} aria-label={`Edit alamat ${addr.label}`} className="text-xs text-gray-500 hover:text-brand-orange">
                        <Pencil className="w-4 h-4" />
                      </Button>
                      <Button size="sm" variant="ghost" onClick={() => setDeleteAddrId(addr.id)} aria-label={`Hapus alamat ${addr.label}`} className="text-xs text-gray-500 hover:text-red-600">
                        <Trash2 className="w-4 h-4" />
                      </Button>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>

      <Dialog open={addrModalOpen} onOpenChange={({ open }) => setAddrModalOpen(open)}>
        <DialogContent className="sm:max-w-lg">
          <DialogHeader>
            <DialogTitle>{editingAddrId ? 'Edit Alamat' : 'Tambah Alamat Baru'}</DialogTitle>
          </DialogHeader>
          <div className="px-6 pb-2 max-h-[65vh] overflow-y-auto">
            <div className="space-y-5">
              {addrError && (
                <div ref={errorRef} role="alert" aria-live="assertive" className="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg animate-[error-pulse_0.5s_ease-in-out]">
                  {addrError}
                </div>
              )}
              <div>
                <Label htmlFor="addr-label">Label (misal: Rumah, Kos, Kantor) <span className="text-red-500">*</span></Label>
                <Input id="addr-label" value={addrForm.label} onChange={(e) => setAddrForm({ ...addrForm, label: e.target.value })} placeholder="Rumah" required />
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="addr-recipient">Nama Penerima <span className="text-red-500">*</span></Label>
                  <Input id="addr-recipient" value={addrForm.recipient_name} onChange={(e) => setAddrForm({ ...addrForm, recipient_name: e.target.value })} placeholder="John Doe" required />
                </div>
                <div>
                  <Label htmlFor="addr-phone">No. Telepon <span className="text-red-500">*</span></Label>
                  <Input id="addr-phone" type="tel" value={addrForm.phone} onChange={(e) => setAddrForm({ ...addrForm, phone: e.target.value })} placeholder="08123456789" required />
                </div>
              </div>
              <div>
                <Label htmlFor="addr-full">Alamat Lengkap <span className="text-red-500">*</span></Label>
                <Input id="addr-full" value={addrForm.full_address} onChange={(e) => setAddrForm({ ...addrForm, full_address: e.target.value })} placeholder="Jl. Merdeka No. 123, RT 01/RW 02" required />
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="addr-district">Kecamatan <span className="text-red-500">*</span></Label>
                  <Input id="addr-district" value={addrForm.district} onChange={(e) => setAddrForm({ ...addrForm, district: e.target.value })} placeholder="Coblong" required />
                </div>
                <div>
                  <Label htmlFor="addr-city">Kota <span className="text-red-500">*</span></Label>
                  <Input id="addr-city" value={addrForm.city} onChange={(e) => setAddrForm({ ...addrForm, city: e.target.value })} placeholder="Bandung" required />
                </div>
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="addr-province">Provinsi <span className="text-red-500">*</span></Label>
                  <Input id="addr-province" value={addrForm.province} onChange={(e) => setAddrForm({ ...addrForm, province: e.target.value })} placeholder="Jawa Barat" required />
                </div>
                <div>
                  <Label htmlFor="addr-postal">Kode Pos <span className="text-red-500">*</span></Label>
                  <Input id="addr-postal" value={addrForm.postal_code} onChange={(e) => setAddrForm({ ...addrForm, postal_code: e.target.value })} placeholder="40154" required />
                </div>
              </div>
              <div>
                <Label htmlFor="addr-notes">Catatan (opsional)</Label>
                <Input id="addr-notes" value={addrForm.notes} onChange={(e) => setAddrForm({ ...addrForm, notes: e.target.value })} placeholder="Dekat pasar, gang ke-2 sebelah kiri" />
              </div>
              <div className="flex items-center gap-2 pt-1">
                <Checkbox
                  checked={addrForm.is_default}
                  onCheckedChange={(v) => setAddrForm({ ...addrForm, is_default: v === true })}
                  id="addr-default"
                />
                <Label htmlFor="addr-default" className="font-normal text-sm cursor-pointer mb-0">Jadikan sebagai alamat utama</Label>
              </div>
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setAddrModalOpen(false)}>Batal</Button>
            <Button onClick={handleSaveAddress} disabled={savingAddr} className="bg-brand-orange hover:bg-orange-700 text-white">
              {savingAddr ? 'Menyimpan...' : 'Simpan'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* ═══ Delete Confirmation Dialog ═══ */}
      <Dialog open={!!deleteAddrId} onOpenChange={({ open }) => { if (!open) setDeleteAddrId(null); }}>
        <DialogContent className="sm:max-w-sm">
          <DialogHeader>
            <DialogTitle>Hapus Alamat?</DialogTitle>
          </DialogHeader>
          <p className="text-sm text-gray-600">Alamat ini akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
          <DialogFooter>
            <Button variant="outline" onClick={() => setDeleteAddrId(null)}>Batal</Button>
            <Button variant="destructive" onClick={() => deleteAddrId && handleDeleteAddress(deleteAddrId)}>
              Hapus
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </section>
  );
}
