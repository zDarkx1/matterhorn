// ─── Admin Service ───────────────────────────────────────────
// API layer for all admin panel endpoints.

import { apiFetch, apiUpload } from '@/lib/api';
import type { Product } from '@/types/product.types';
import type { Rental, Payment } from '@/types/rental.types';
import type { User } from '@/types/user.types';
import type { PaginationMeta } from '@/types/api.types';

// ─── Dashboard ───────────────────────────────────────────────

export interface DashboardData {
  total_users: number;
  users_change: number;
  active_rentals: number;
  rentals_change: number;
  total_products: number;
  products_change: number;
  total_revenue: number;
  revenue_change: number;
  status_breakdown: Record<string, number>;
  monthly_revenue: { month: string; total: number }[];
  recent_rentals: Rental[];
}

// ─── Product Size ────────────────────────────────────────────

export interface ProductSize {
  id: number;
  size: string;
  stock: number;
}

// ─── Paginated Response ──────────────────────────────────────

interface Paginated<T> {
  data: T[];
  meta: PaginationMeta;
}

// ─── Service ─────────────────────────────────────────────────

export const adminService = {

  // ── Dashboard ──────────────────────────────────────────────

  async getDashboard(): Promise<DashboardData> {
    const res = await apiFetch<DashboardData>('/admin/dashboard');
    return res.data;
  },

  // ── Products ───────────────────────────────────────────────

  async getProducts(params?: {
    search?: string;
    category?: string;
    page?: number;
    per_page?: number;
  }): Promise<Paginated<Product>> {
    const sp = new URLSearchParams();
    if (params?.search) sp.set('search', params.search);
    if (params?.category) sp.set('category', params.category);
    if (params?.page) sp.set('page', String(params.page));
    if (params?.per_page) sp.set('per_page', String(params.per_page));
    const qs = sp.toString();
    const res = await apiFetch<Product[]>(`/admin/products${qs ? `?${qs}` : ''}`);
    return {
      data: res.data,
      meta: (res as unknown as { meta: PaginationMeta }).meta,
    };
  },

  async getCategories(): Promise<string[]> {
    const res = await apiFetch<string[]>('/products/categories');
    return res.data;
  },

  async getProduct(id: number): Promise<Product> {
    const res = await apiFetch<Product>(`/admin/products/${id}`);
    return res.data;
  },

  async createProduct(formData: FormData): Promise<Product> {
    const res = await apiUpload<Product>('/admin/products', formData);
    return res.data;
  },

  async updateProduct(id: number, formData: FormData): Promise<Product> {
    formData.append('_method', 'PUT');
    const res = await apiUpload<Product>(`/admin/products/${id}`, formData);
    return res.data;
  },

  async deleteProduct(id: number): Promise<void> {
    await apiFetch(`/admin/products/${id}`, { method: 'DELETE' });
  },

  // ── Stock / Sizes ──────────────────────────────────────────

  async getProductSizes(productId: number): Promise<{ product_id: number; product_name: string; sizes: ProductSize[] }> {
    const res = await apiFetch<{ product_id: number; product_name: string; sizes: ProductSize[] }>(
      `/admin/products/${productId}/sizes`
    );
    return res.data;
  },

  async addProductSize(productId: number, size: string, stock: number): Promise<ProductSize> {
    const res = await apiFetch<ProductSize>(`/admin/products/${productId}/sizes`, {
      method: 'POST',
      body: JSON.stringify({ size, stock }),
    });
    return res.data;
  },

  async restockSize(productId: number, sizeId: number, quantity: number): Promise<ProductSize> {
    const res = await apiFetch<ProductSize>(`/admin/products/${productId}/sizes/${sizeId}/restock`, {
      method: 'PUT',
      body: JSON.stringify({ quantity }),
    });
    return res.data;
  },

  async updateSize(productId: number, sizeId: number, data: { size?: string; stock?: number }): Promise<ProductSize> {
    const res = await apiFetch<ProductSize>(`/admin/products/${productId}/sizes/${sizeId}`, {
      method: 'PUT',
      body: JSON.stringify(data),
    });
    return res.data;
  },

  async deleteSize(productId: number, sizeId: number): Promise<void> {
    await apiFetch(`/admin/products/${productId}/sizes/${sizeId}`, { method: 'DELETE' });
  },

  // ── Rentals ────────────────────────────────────────────────

  async getRentals(params?: {
    status?: string;
    search?: string;
    sort?: string;
    page?: number;
    per_page?: number;
  }): Promise<Paginated<Rental>> {
    const sp = new URLSearchParams();
    if (params?.status) sp.set('status', params.status);
    if (params?.search) sp.set('search', params.search);
    if (params?.sort) sp.set('sort', params.sort);
    if (params?.page) sp.set('page', String(params.page));
    if (params?.per_page) sp.set('per_page', String(params.per_page));
    const qs = sp.toString();
    const res = await apiFetch<Rental[]>(`/admin/rentals${qs ? `?${qs}` : ''}`);
    return {
      data: res.data,
      meta: (res as unknown as { meta: PaginationMeta }).meta,
    };
  },

  async getRental(id: number): Promise<Rental> {
    const res = await apiFetch<Rental>(`/admin/rentals/${id}`);
    return res.data;
  },

  async updateRentalStatus(id: number, status: string): Promise<Rental> {
    const res = await apiFetch<Rental>(`/admin/rentals/${id}/status`, {
      method: 'PUT',
      body: JSON.stringify({ status }),
    });
    return res.data;
  },

  // ── Payments ───────────────────────────────────────────────

  async getPayments(params?: {
    status?: string;
    search?: string;
    page?: number;
    per_page?: number;
  }): Promise<Paginated<Payment>> {
    const sp = new URLSearchParams();
    if (params?.status) sp.set('status', params.status);
    if (params?.search) sp.set('search', params.search);
    if (params?.page) sp.set('page', String(params.page));
    if (params?.per_page) sp.set('per_page', String(params.per_page));
    const qs = sp.toString();
    const res = await apiFetch<Payment[]>(`/admin/payments${qs ? `?${qs}` : ''}`);
    return {
      data: res.data,
      meta: (res as unknown as { meta: PaginationMeta }).meta,
    };
  },

  async verifyPayment(id: number): Promise<Payment> {
    const res = await apiFetch<Payment>(`/admin/payments/${id}/verify`, {
      method: 'PUT',
    });
    return res.data;
  },

  // ── Users ──────────────────────────────────────────────────

  async getUsers(params?: {
    search?: string;
    role?: string;
    page?: number;
    per_page?: number;
  }): Promise<Paginated<User>> {
    const sp = new URLSearchParams();
    if (params?.search) sp.set('search', params.search);
    if (params?.role) sp.set('role', params.role);
    if (params?.page) sp.set('page', String(params.page));
    if (params?.per_page) sp.set('per_page', String(params.per_page));
    const qs = sp.toString();
    const res = await apiFetch<User[]>(`/admin/users${qs ? `?${qs}` : ''}`);
    return {
      data: res.data,
      meta: (res as unknown as { meta: PaginationMeta }).meta,
    };
  },

  async getUser(id: number): Promise<{ user: User; rentals: Rental[]; stats: { total_rentals: number; total_spent: number; active_rentals: number } }> {
    const res = await apiFetch<{ user: User; rentals: Rental[]; stats: { total_rentals: number; total_spent: number; active_rentals: number } }>(
      `/admin/users/${id}`
    );
    return res.data;
  },

  async createUser(data: { name: string; email: string; password: string; role?: string; phone_number?: string; address?: string }): Promise<User> {
    const res = await apiFetch<User>('/admin/users', {
      method: 'POST',
      body: JSON.stringify(data),
    });
    return res.data;
  },

  async updateUser(id: number, data: Partial<{ name: string; email: string; password: string; role: string; phone_number: string; address: string }>): Promise<User> {
    const res = await apiFetch<User>(`/admin/users/${id}`, {
      method: 'PUT',
      body: JSON.stringify(data),
    });
    return res.data;
  },

  async deleteUser(id: number): Promise<void> {
    await apiFetch(`/admin/users/${id}`, { method: 'DELETE' });
  },
};
