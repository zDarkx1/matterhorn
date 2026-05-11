'use client';

import { useState, useEffect, useCallback } from 'react';
import { adminService, type ProductSize } from '@/services/admin.service';
import { formatRupiah } from '@/utils/format';
import type { Product } from '@/types/product.types';
import type { PaginationMeta } from '@/types/api.types';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import { Spinner } from '@/components/ui/spinner';
import {
  Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
} from '@/components/ui/table';
import {
  Dialog, DialogBody, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogClose,
} from '@/components/ui/dialog';
import {
  AlertDialog, AlertDialogContent,
  AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Plus, Search, Pencil, Trash2, PackagePlus, X, ImageIcon } from 'lucide-react';
import { toast } from 'sonner';

export default function AdminProductsPage() {
  const [products, setProducts] = useState<Product[]>([]);
  const [meta, setMeta] = useState<PaginationMeta | null>(null);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);

  // Form state
  const [formOpen, setFormOpen] = useState(false);
  const [editing, setEditing] = useState<Product | null>(null);
  const [submitting, setSubmitting] = useState(false);

  // Stock dialog
  const [stockOpen, setStockOpen] = useState(false);
  const [stockProduct, setStockProduct] = useState<Product | null>(null);
  const [sizes, setSizes] = useState<ProductSize[]>([]);
  const [loadingSizes, setLoadingSizes] = useState(false);
  const [newSizeName, setNewSizeName] = useState('');
  const [newSizeStock, setNewSizeStock] = useState(0);
  const [restockAmounts, setRestockAmounts] = useState<Record<number, number>>({});

  // Delete
  const [deleteTarget, setDeleteTarget] = useState<Product | null>(null);
  const [deleting, setDeleting] = useState(false);

  // Categories
  const [categories, setCategories] = useState<string[]>([]);
  const [showNewCategory, setShowNewCategory] = useState(false);
  const [selectedCategory, setSelectedCategory] = useState('');

  const fetchProducts = useCallback(async () => {
    setLoading(true);
    try {
      const res = await adminService.getProducts({ search, page, per_page: 10 });
      setProducts(res.data);
      setMeta(res.meta);
    } catch (e: unknown) {
      toast.error((e as Error).message);
    } finally {
      setLoading(false);
    }
  }, [search, page]);

  useEffect(() => { fetchProducts(); }, [fetchProducts]);

  useEffect(() => {
    adminService.getCategories().then(setCategories).catch(() => {});
  }, []);

  // ─── Product Form Submit ───
  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setSubmitting(true);
    try {
      const form = e.currentTarget;
      const fd = new FormData(form);

      // Parse sizes from dynamic rows — send as FormData array
      const sizeInputs = form.querySelectorAll<HTMLInputElement>('[data-size-name]');
      const stockInputs = form.querySelectorAll<HTMLInputElement>('[data-size-stock]');
      sizeInputs.forEach((el, i) => {
        if (el.value && stockInputs[i]) {
          fd.append(`sizes[${i}][size]`, el.value);
          fd.append(`sizes[${i}][stock]`, stockInputs[i].value || '0');
        }
      });

      if (editing) {
        await adminService.updateProduct(editing.id, fd);
        toast.success('Produk berhasil diperbarui.');
      } else {
        await adminService.createProduct(fd);
        toast.success('Produk berhasil ditambahkan.');
      }
      setFormOpen(false);
      setEditing(null);
      fetchProducts();
    } catch (e: unknown) {
      toast.error((e as Error).message);
    } finally {
      setSubmitting(false);
    }
  };

  // ─── Delete ───
  const handleDelete = async () => {
    if (!deleteTarget) return;
    setDeleting(true);
    try {
      await adminService.deleteProduct(deleteTarget.id);
      toast.success('Produk berhasil dihapus.');
      setDeleteTarget(null);
      fetchProducts();
    } catch (e: unknown) {
      toast.error((e as Error).message);
    } finally {
      setDeleting(false);
    }
  };

  // ─── Stock Management ───
  const openStockDialog = async (product: Product) => {
    setStockProduct(product);
    setStockOpen(true);
    setLoadingSizes(true);
    try {
      const res = await adminService.getProductSizes(product.id);
      setSizes(res.sizes);
    } catch (e: unknown) {
      toast.error((e as Error).message);
    } finally {
      setLoadingSizes(false);
    }
  };

  const handleAddSize = async () => {
    if (!stockProduct || !newSizeName.trim()) return;
    try {
      const res = await adminService.addProductSize(stockProduct.id, newSizeName.trim(), newSizeStock);
      setSizes((prev) => [...prev, res]);
      setNewSizeName('');
      setNewSizeStock(0);
      toast.success('Size berhasil ditambahkan.');
      fetchProducts();
    } catch (e: unknown) {
      toast.error((e as Error).message);
    }
  };

  const handleRestock = async (sizeId: number) => {
    if (!stockProduct) return;
    const qty = restockAmounts[sizeId] || 0;
    if (qty <= 0) return;
    try {
      const res = await adminService.restockSize(stockProduct.id, sizeId, qty);
      setSizes((prev) => prev.map((s) => (s.id === sizeId ? res : s)));
      setRestockAmounts((prev) => ({ ...prev, [sizeId]: 0 }));
      toast.success('Stok berhasil ditambahkan.');
      fetchProducts();
    } catch (e: unknown) {
      toast.error((e as Error).message);
    }
  };

  const handleDeleteSize = async (sizeId: number) => {
    if (!stockProduct) return;
    try {
      await adminService.deleteSize(stockProduct.id, sizeId);
      setSizes((prev) => prev.filter((s) => s.id !== sizeId));
      toast.success('Size berhasil dihapus.');
      fetchProducts();
    } catch (e: unknown) {
      toast.error((e as Error).message);
    }
  };

  // ─── Dynamic size rows for product form ───
  const [formSizes, setFormSizes] = useState<{ size: string; stock: number }[]>([]);

  const openForm = (product?: Product) => {
    if (product) {
      setEditing(product);
      setFormSizes(product.sizes?.map((s) => ({ size: s.size, stock: s.stock })) || []);
      setSelectedCategory(product.category || '');
      setShowNewCategory(false);
    } else {
      setEditing(null);
      setFormSizes([]);
      setSelectedCategory('');
      setShowNewCategory(false);
    }
    setFormOpen(true);
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="font-display text-2xl font-bold uppercase tracking-tight">Kelola Produk</h1>
          <p className="text-sm text-muted-foreground mt-1">
            {meta ? `${meta.total} produk tersedia` : 'Memuat...'}
          </p>
        </div>
        <Button onClick={() => openForm()} className="bg-brand-orange hover:bg-brand-orange/90 text-white">
          <Plus className="h-4 w-4 mr-2" /> Tambah Produk
        </Button>
      </div>

      {/* Search */}
      <div className="relative max-w-sm">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" aria-hidden="true" />
        <Input
          placeholder="Cari produk..."
          value={search}
          onChange={(e) => { setSearch(e.target.value); setPage(1); }}
          className="pl-10"
          aria-label="Cari produk"
        />
      </div>

      {/* Table */}
      <Card>
        <CardContent className="p-0 overflow-x-auto">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead className="w-16">Foto</TableHead>
                <TableHead>Nama</TableHead>
                <TableHead>Kategori</TableHead>
                <TableHead>Harga/24h</TableHead>
                <TableHead>Stok</TableHead>
                <TableHead>Sizes</TableHead>
                <TableHead className="text-right">Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {loading ? (
                [...Array(5)].map((_, i) => (
                  <TableRow key={i}>
                    {[...Array(7)].map((_, j) => (
                      <TableCell key={j}><Skeleton className="h-4 w-full" /></TableCell>
                    ))}
                  </TableRow>
                ))
              ) : products.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={7} className="text-center py-12 text-muted-foreground">
                    Tidak ada produk ditemukan.
                  </TableCell>
                </TableRow>
              ) : (
                products.map((product) => (
                  <TableRow key={product.id}>
                    <TableCell>
                      {product.image_url ? (
                        <img
                          src={product.image_url}
                          alt={product.name}
                          className="h-10 w-10 rounded-md object-cover"
                        />
                      ) : (
                        <div className="h-10 w-10 rounded-md bg-muted flex items-center justify-center">
                          <ImageIcon className="h-4 w-4 text-muted-foreground" />
                        </div>
                      )}
                    </TableCell>
                    <TableCell className="font-medium">{product.name}</TableCell>
                    <TableCell>
                      <Badge variant="secondary" className="text-xs">{product.category}</Badge>
                    </TableCell>
                    <TableCell>{formatRupiah(product.price_24h)}</TableCell>
                    <TableCell>
                      <span className={product.stock_available <= 2 ? 'text-red-600 font-semibold' : ''}>
                        {product.stock_available}/{product.stock_total}
                      </span>
                    </TableCell>
                    <TableCell>
                      {product.sizes && product.sizes.length > 0 ? (
                        <div className="flex flex-wrap gap-1">
                          {product.sizes.map((s) => (
                            <Badge key={s.id} variant="outline" className="text-[10px]">
                              {s.size}: {s.stock}
                            </Badge>
                          ))}
                        </div>
                      ) : (
                        <span className="text-muted-foreground text-xs">—</span>
                      )}
                    </TableCell>
                    <TableCell className="text-right">
                      <div className="flex items-center justify-end gap-1">
                        <Button variant="ghost" size="sm" onClick={() => openStockDialog(product)} title="Kelola Stok">
                          <PackagePlus className="h-4 w-4" />
                        </Button>
                        <Button variant="ghost" size="sm" onClick={() => openForm(product)} title="Edit">
                          <Pencil className="h-4 w-4" />
                        </Button>
                        <Button variant="ghost" size="sm" onClick={() => setDeleteTarget(product)} title="Hapus" className="text-destructive hover:text-destructive">
                          <Trash2 className="h-4 w-4" />
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                ))
              )}
            </TableBody>
          </Table>
        </CardContent>
      </Card>

      {/* Pagination */}
      {meta && meta.last_page > 1 && (
        <div className="flex items-center justify-center gap-2">
          <Button variant="outline" size="sm" disabled={page <= 1} onClick={() => setPage((p) => p - 1)}>
            Sebelumnya
          </Button>
          <span className="text-sm text-muted-foreground">
            Hal {meta.current_page} dari {meta.last_page}
          </span>
          <Button variant="outline" size="sm" disabled={page >= meta.last_page} onClick={() => setPage((p) => p + 1)}>
            Selanjutnya
          </Button>
        </div>
      )}

      {/* ─── Product Form Dialog ─── */}
      <Dialog open={formOpen} onOpenChange={({ open }) => { if (!open) { setFormOpen(false); setEditing(null); } }}>
        <DialogContent size="lg">
          <DialogHeader title={editing ? 'Edit Produk' : 'Tambah Produk Baru'} />
          <DialogBody>
            <form id="product-form" onSubmit={handleSubmit} className="space-y-5">
              <div>
                <label className="block text-sm font-medium mb-1.5">Nama Produk</label>
                <Input name="name" defaultValue={editing?.name || ''} required />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium mb-1.5">Kategori</label>
                  {showNewCategory ? (
                    <div className="flex gap-2">
                      <Input name="category" placeholder="Nama kategori baru" required className="flex-1" />
                      <Button type="button" variant="ghost" size="sm" onClick={() => setShowNewCategory(false)}>
                        <X className="h-3 w-3" />
                      </Button>
                    </div>
                  ) : (
                    <div className="flex gap-2">
                      <select
                        name="category"
                        value={selectedCategory}
                        onChange={(e) => {
                          if (e.target.value === '__new__') {
                            setShowNewCategory(true);
                            setSelectedCategory('');
                          } else {
                            setSelectedCategory(e.target.value);
                          }
                        }}
                        className="w-full h-10 rounded-lg border border-input bg-background px-3 text-sm shadow-xs/5 outline-none focus-visible:border-primary focus-visible:ring-[3px] focus-visible:ring-ring/32 transition-[color,box-shadow]"
                        required
                      >
                        <option value="">Pilih kategori...</option>
                        {categories.map((cat) => (
                          <option key={cat} value={cat}>{cat}</option>
                        ))}
                        <option value="__new__">+ Tambah Kategori Baru</option>
                      </select>
                    </div>
                  )}
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1.5">Gender</label>
                  <select name="gender" defaultValue={editing?.gender || 'unisex'} className="w-full h-10 rounded-lg border border-input bg-background px-3 text-sm shadow-xs/5 outline-none focus-visible:border-primary focus-visible:ring-[3px] focus-visible:ring-ring/32 transition-[color,box-shadow]">
                    <option value="unisex">Unisex</option>
                    <option value="pria">Pria</option>
                    <option value="wanita">Wanita</option>
                    <option value="anak">Anak</option>
                  </select>
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium mb-1.5">Deskripsi</label>
                <textarea name="description" defaultValue={editing?.description || ''} rows={3} className="w-full rounded-lg border border-input bg-background px-3 py-2.5 text-sm shadow-xs/5 outline-none focus-visible:border-primary focus-visible:ring-[3px] focus-visible:ring-ring/32 transition-[color,box-shadow]" />
              </div>
              <div className="grid grid-cols-3 gap-4">
                <div>
                  <label className="block text-sm font-medium mb-1.5">Harga/24h</label>
                  <Input name="price_24h" type="number" min="0" defaultValue={editing?.price_24h || ''} required />
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1.5">Stok Total</label>
                  <Input name="stock_total" type="number" min="0" defaultValue={editing?.stock_total || ''} required />
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1.5">Stok Tersedia</label>
                  <Input name="stock_available" type="number" min="0" defaultValue={editing?.stock_available || ''} required />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium mb-1.5">Gambar Produk</label>
                <Input name="image" type="file" accept="image/*" />
                {editing?.image_url && (
                  <div className="mt-2">
                    <img src={editing.image_url} alt="Preview" className="h-20 w-20 rounded-md object-cover border" />
                  </div>
                )}
              </div>

              {/* Dynamic Sizes */}
              <div>
                <div className="flex items-center justify-between mb-2">
                  <label className="text-sm font-medium">Ukuran (Sizes)</label>
                  <Button type="button" variant="outline" size="sm" onClick={() => setFormSizes((p) => [...p, { size: '', stock: 0 }])}>
                    <Plus className="h-3 w-3 mr-1" /> Tambah Size
                  </Button>
                </div>
                {formSizes.length > 0 && (
                  <div className="space-y-2">
                    {formSizes.map((s, i) => (
                      <div key={i} className="flex gap-2 items-center">
                        <Input
                          data-size-name=""
                          placeholder="Contoh: M, L, XL"
                          value={s.size}
                          onChange={(e) => {
                            const copy = [...formSizes];
                            copy[i] = { ...copy[i], size: e.target.value };
                            setFormSizes(copy);
                          }}
                          className="flex-1"
                        />
                        <Input
                          data-size-stock=""
                          type="number"
                          min="0"
                          placeholder="Stok"
                          value={s.stock}
                          onChange={(e) => {
                            const copy = [...formSizes];
                            copy[i] = { ...copy[i], stock: parseInt(e.target.value) || 0 };
                            setFormSizes(copy);
                          }}
                          className="w-24"
                        />
                        <Button type="button" variant="ghost" size="sm" onClick={() => setFormSizes((p) => p.filter((_, j) => j !== i))}>
                          <X className="h-3 w-3" />
                        </Button>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            </form>
          </DialogBody>
          <DialogFooter>
            <DialogClose asChild>
              <Button type="button" variant="outline">Batal</Button>
            </DialogClose>
            <Button type="submit" form="product-form" disabled={submitting} className="bg-brand-orange hover:bg-brand-orange/90 text-white">
              {submitting && <Spinner className="mr-2 h-4 w-4" />}
              {editing ? 'Simpan Perubahan' : 'Tambah Produk'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* ─── Stock Management Dialog ─── */}
      <Dialog open={stockOpen} onOpenChange={({ open }) => { if (!open) setStockOpen(false); }}>
        <DialogContent size="md">
          <DialogHeader title={`Kelola Stok — ${stockProduct?.name || ''}`} />
          <DialogBody>
            {loadingSizes ? (
              <div className="space-y-3">{[...Array(3)].map((_, i) => <Skeleton key={i} className="h-10 w-full" />)}</div>
            ) : (
              <div className="space-y-4">
                {/* Existing sizes */}
                {sizes.length > 0 ? (
                  <div className="space-y-2">
                    {sizes.map((s) => (
                      <div key={s.id} className="flex items-center gap-2 p-3 border rounded-lg">
                        <Badge variant="outline" className="font-mono">{s.size}</Badge>
                        <span className="text-sm flex-1">Stok: <strong>{s.stock}</strong></span>
                        <Input
                          type="number"
                          min="1"
                          placeholder="+Qty"
                          value={restockAmounts[s.id] || ''}
                          onChange={(e) => setRestockAmounts((prev) => ({ ...prev, [s.id]: parseInt(e.target.value) || 0 }))}
                          className="w-20 h-8 text-sm"
                        />
                        <Button size="sm" variant="outline" onClick={() => handleRestock(s.id)} disabled={!restockAmounts[s.id]}>
                          Restock
                        </Button>
                        <Button size="sm" variant="ghost" onClick={() => handleDeleteSize(s.id)} className="text-destructive hover:text-destructive">
                          <Trash2 className="h-3 w-3" />
                        </Button>
                      </div>
                    ))}
                  </div>
                ) : (
                  <p className="text-sm text-muted-foreground text-center py-4">Belum ada size.</p>
                )}

                {/* Add new size */}
                <div className="border-t pt-4">
                  <label className="block text-sm font-medium mb-2">Tambah Size Baru</label>
                  <div className="flex gap-2">
                    <Input
                      placeholder="Nama size (M, L, XL...)"
                      value={newSizeName}
                      onChange={(e) => setNewSizeName(e.target.value)}
                      className="flex-1"
                    />
                    <Input
                      type="number"
                      min="0"
                      placeholder="Stok"
                      value={newSizeStock || ''}
                      onChange={(e) => setNewSizeStock(parseInt(e.target.value) || 0)}
                      className="w-24"
                    />
                    <Button onClick={handleAddSize} disabled={!newSizeName.trim()} className="bg-brand-orange hover:bg-brand-orange/90 text-white">
                      <Plus className="h-4 w-4" />
                    </Button>
                  </div>
                </div>
              </div>
            )}
          </DialogBody>
        </DialogContent>
      </Dialog>

      {/* ─── Delete Confirmation ─── */}
      <AlertDialog open={!!deleteTarget} onOpenChange={({ open }) => { if (!open) setDeleteTarget(null); }}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Hapus Produk?</AlertDialogTitle>
            <AlertDialogDescription>
              Produk <strong>{deleteTarget?.name}</strong> akan dihapus permanen. Produk yang sedang disewa tidak bisa dihapus.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <Button variant="outline" onClick={() => setDeleteTarget(null)}>Batal</Button>
            <Button variant="destructive" onClick={handleDelete} disabled={deleting}>
              {deleting ? 'Menghapus...' : 'Hapus'}
            </Button>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  );
}
