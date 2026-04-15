// ─── Cart Service ────────────────────────────────────────────

import { apiFetch } from '@/lib/api';
import type { CartData, AddToCartPayload } from '@/types';

export const cartService = {
  async getCart(): Promise<CartData> {
    const res = await apiFetch<CartData>('/cart');
    return res.data;
  },

  async addItem(payload: AddToCartPayload): Promise<{ total_items: number }> {
    const res = await apiFetch<{ total_items: number }>('/cart', {
      method: 'POST',
      body: JSON.stringify(payload),
    });
    return res.data;
  },

  async updateItem(itemId: string, quantity: number): Promise<{ total_items: number; total_price: number }> {
    const res = await apiFetch<{ total_items: number; total_price: number }>(`/cart/${itemId}`, {
      method: 'PUT',
      body: JSON.stringify({ quantity }),
    });
    return res.data;
  },

  async removeItem(itemId: string): Promise<{ total_items: number; total_price: number }> {
    const res = await apiFetch<{ total_items: number; total_price: number }>(`/cart/${itemId}`, {
      method: 'DELETE',
    });
    return res.data;
  },

  async clearCart(): Promise<void> {
    await apiFetch('/cart', { method: 'DELETE' });
  },
};
