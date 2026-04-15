'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import { useAuthStore } from '@/stores/useAuthStore';
import { apiFetch } from '@/lib/api';
import { toast } from 'sonner';
import type { UserAddress } from '@/types';
import { User, Mail, Phone, MapPin, Plus, Trash2, Star, Pencil, X } from 'lucide-react';
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
  phone_number: '',
  address_line: '',
  city: '',
  province: '',
  postal_code: '',
  is_default: false,
};

export default function ProfilePage() {
  const router = useRouter();
  const { user, fetchMe } = useAuthStore();
  const [addresses, setAddresses] = useState<UserAddress[]>([]);
  const [editing, setEditing] = useState(false);
  const [form, setForm] = useState({ name: '', phone_number: '' });
  const [saving, setSaving] = useState(false);

  // Address modal states
  const [addrModalOpen, setAddrModalOpen] = useState(false);
  const [addrForm, setAddrForm] = useState(EMPTY_ADDR);
  const [editingAddrId, setEditingAddrId] = useState<number | null>(null);
  const [savingAddr, setSavingAddr] = useState(false);

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
    setAddrForm({ ...EMPTY_ADDR, recipient_name: user?.name || '' });
    setAddrModalOpen(true);
  };

  const openEditAddress = (addr: UserAddress) => {
    setEditingAddrId(addr.id);
    setAddrForm({
      label: addr.label,
      recipient_name: addr.recipient_name,
      phone_number: addr.phone_number,
      address_line: addr.address_line,
      city: addr.city,
      province: addr.province,
      postal_code: addr.postal_code,
      is_default: addr.is_default,
    });
    setAddrModalOpen(true);
  };

  const handleSaveAddress = async () => {
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
      toast.error(e instanceof Error ? e.message : 'Gagal menyimpan alamat.');
    } finally {
      setSavingAddr(false);
    }
  };

  const handleDeleteAddress = async (id: number) => {
    try {
      await apiFetch(`/addresses/${id}`, { method: 'DELETE' });
      toast.info('Alamat berhasil dihapus.');
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
        <h1 className="font-display font-bold text-3xl uppercase mb-8 flex items-center gap-3">
          <span className="w-1 h-8 bg-brand-orange block" />
          Profil Saya
        </h1>

        {/* ═══ User Info ═══ */}
        <div className="border border-gray-200 p-6 mb-6 rounded-lg">
          <div className="flex justify-between items-center mb-6">
            <h3 className="font-display font-bold uppercase text-sm">Informasi Akun</h3>
            <button onClick={() => setEditing(!editing)} className="text-sm text-brand-orange font-bold hover:underline">
              {editing ? 'Batal' : 'Edit'}
            </button>
          </div>

          {editing ? (
            <div className="space-y-4">
              <div>
                <Label className="text-xs font-bold uppercase text-gray-700 mb-1">Nama</Label>
                <Input
                  value={form.name}
                  onChange={(e) => setForm({ ...form, name: e.target.value })}
                />
              </div>
              <div>
                <Label className="text-xs font-bold uppercase text-gray-700 mb-1">No. Telepon</Label>
                <Input
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
                <User className="w-4 h-4 text-gray-400" /><span>{user.name}</span>
              </div>
              <div className="flex items-center gap-3 text-sm">
                <Mail className="w-4 h-4 text-gray-400" /><span>{user.email}</span>
              </div>
              <div className="flex items-center gap-3 text-sm">
                <Phone className="w-4 h-4 text-gray-400" /><span>{user.phone_number || '-'}</span>
              </div>
            </div>
          )}
        </div>

        {/* ═══ Addresses ═══ */}
        <div className="border border-gray-200 p-6 rounded-lg">
          <div className="flex justify-between items-center mb-6">
            <h3 className="font-display font-bold uppercase text-sm flex items-center gap-2">
              <MapPin className="w-4 h-4 text-brand-orange" /> Alamat Tersimpan
            </h3>
            <Button size="sm" variant="outline" onClick={openAddAddress} className="gap-1">
              <Plus className="w-4 h-4" /> Tambah Alamat
            </Button>
          </div>

          {addresses.length === 0 ? (
            <div className="text-center py-10 bg-gray-50 rounded-lg">
              <MapPin className="w-10 h-10 text-gray-300 mx-auto mb-3" />
              <p className="text-gray-500 text-sm mb-4">Belum ada alamat tersimpan.</p>
              <Button size="sm" onClick={openAddAddress} className="bg-brand-orange hover:bg-orange-700 text-white">
                Tambah Alamat Pertama
              </Button>
            </div>
          ) : (
            <div className="space-y-4">
              {addresses.map((addr) => (
                <div key={addr.id} className={`border p-4 rounded-lg ${addr.is_default ? 'border-brand-orange bg-orange-50/50' : 'border-gray-200'}`}>
                  <div className="flex justify-between items-start">
                    <div className="flex-1 min-w-0">
                      <div className="flex items-center gap-2 mb-1">
                        <span className="font-bold text-sm">{addr.label}</span>
                        {addr.is_default && (
                          <span className="bg-brand-orange text-white text-[10px] px-1.5 py-0.5 font-bold rounded flex items-center gap-1">
                            <Star className="w-3 h-3" /> Utama
                          </span>
                        )}
                      </div>
                      <p className="text-sm">{addr.recipient_name} &bull; {addr.phone_number}</p>
                      <p className="text-sm text-gray-500">{addr.address_line}, {addr.city}, {addr.province} {addr.postal_code}</p>
                    </div>
                    <div className="flex items-center gap-1 flex-shrink-0 ml-2">
                      {!addr.is_default && (
                        <Button size="sm" variant="ghost" onClick={() => handleSetDefault(addr.id)} title="Jadikan Utama" className="text-xs text-gray-500 hover:text-brand-orange">
                          <Star className="w-4 h-4" />
                        </Button>
                      )}
                      <Button size="sm" variant="ghost" onClick={() => openEditAddress(addr)} title="Edit" className="text-xs text-gray-500 hover:text-brand-orange">
                        <Pencil className="w-4 h-4" />
                      </Button>
                      <Button size="sm" variant="ghost" onClick={() => handleDeleteAddress(addr.id)} title="Hapus" className="text-xs text-gray-500 hover:text-red-600">
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

      {/* ═══ Address Modal ═══ */}
      <Dialog open={addrModalOpen} onOpenChange={setAddrModalOpen}>
        <DialogContent className="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>{editingAddrId ? 'Edit Alamat' : 'Tambah Alamat Baru'}</DialogTitle>
          </DialogHeader>
          <div className="-mx-4 px-4 max-h-[70vh] overflow-y-auto space-y-4 py-2">
            <div>
              <Label>Label (misal: Rumah, Kos, Kantor)</Label>
              <Input value={addrForm.label} onChange={(e) => setAddrForm({ ...addrForm, label: e.target.value })} placeholder="Rumah" />
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div>
                <Label>Nama Penerima</Label>
                <Input value={addrForm.recipient_name} onChange={(e) => setAddrForm({ ...addrForm, recipient_name: e.target.value })} placeholder="John Doe" />
              </div>
              <div>
                <Label>No. Telepon</Label>
                <Input type="tel" value={addrForm.phone_number} onChange={(e) => setAddrForm({ ...addrForm, phone_number: e.target.value })} placeholder="08123456789" />
              </div>
            </div>
            <div>
              <Label>Alamat Lengkap</Label>
              <Input value={addrForm.address_line} onChange={(e) => setAddrForm({ ...addrForm, address_line: e.target.value })} placeholder="Jl. Merdeka No. 123" />
            </div>
            <div className="grid grid-cols-3 gap-3">
              <div>
                <Label>Kota</Label>
                <Input value={addrForm.city} onChange={(e) => setAddrForm({ ...addrForm, city: e.target.value })} placeholder="Bandung" />
              </div>
              <div>
                <Label>Provinsi</Label>
                <Input value={addrForm.province} onChange={(e) => setAddrForm({ ...addrForm, province: e.target.value })} placeholder="Jawa Barat" />
              </div>
              <div>
                <Label>Kode Pos</Label>
                <Input value={addrForm.postal_code} onChange={(e) => setAddrForm({ ...addrForm, postal_code: e.target.value })} placeholder="40154" />
              </div>
            </div>
            <div className="flex items-center gap-2">
              <Checkbox
                checked={addrForm.is_default}
                onCheckedChange={(v) => setAddrForm({ ...addrForm, is_default: v === true })}
                id="addr-default"
              />
              <Label htmlFor="addr-default" className="font-normal text-sm cursor-pointer">Jadikan sebagai alamat utama</Label>
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
    </section>
  );
}
