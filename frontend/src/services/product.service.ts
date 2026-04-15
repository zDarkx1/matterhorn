// ─── Product Service ─────────────────────────────────────────

import { apiFetch } from '@/lib/api';
import type { Product, ProductFilters, PaginatedResponse } from '@/types';

export const productService = {
  async getProducts(filters: ProductFilters = {}): Promise<PaginatedResponse<Product>> {
    const params = new URLSearchParams();
    if (filters.search) params.set('search', filters.search);
    if (filters.category) params.set('category', filters.category);
    if (filters.gender) params.set('gender', filters.gender);
    if (filters.min_price) params.set('min_price', String(filters.min_price));
    if (filters.max_price) params.set('max_price', String(filters.max_price));
    if (filters.sort) params.set('sort', filters.sort);
    if (filters.per_page) params.set('per_page', String(filters.per_page));
    if (filters.page) params.set('page', String(filters.page));

    const queryString = params.toString();
    const endpoint = `/products${queryString ? `?${queryString}` : ''}`;

    const res = await fetch(
      `${process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api'}${endpoint}`,
      { headers: { Accept: 'application/json' } }
    );
    return res.json();
  },

  async getProduct(id: number): Promise<Product> {
    const res = await apiFetch<Product>(`/products/${id}`);
    return res.data;
  },

  async getCategories(): Promise<string[]> {
    const res = await apiFetch<string[]>('/products/categories');
    return res.data;
  },
};
