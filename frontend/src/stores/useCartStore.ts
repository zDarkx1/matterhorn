// ─── Cart Store (Zustand) ────────────────────────────────────
// Syncs with backend API (no localStorage persistence — source of truth is backend).

import { create } from 'zustand';
import { cartService } from '@/services/cart.service';
import type { CartItem, AddToCartPayload } from '@/types';

interface CartState {
  items: CartItem[];
  totalItems: number;
  totalPrice: number;
  loading: boolean;

  fetchCart: () => Promise<void>;
  addItem: (payload: AddToCartPayload) => Promise<void>;
  updateItem: (itemId: string, quantity: number) => Promise<void>;
  removeItem: (itemId: string) => Promise<void>;
  clearCart: () => void;
}

export const useCartStore = create<CartState>()((set) => ({
  items: [],
  totalItems: 0,
  totalPrice: 0,
  loading: false,

  fetchCart: async () => {
    set({ loading: true });
    try {
      const data = await cartService.getCart();
      set({
        items: data.items,
        totalItems: data.total_items,
        totalPrice: data.total_price,
        loading: false,
      });
    } catch {
      set({ loading: false });
    }
  },

  addItem: async (payload) => {
    set({ loading: true });
    try {
      await cartService.addItem(payload);
      // Re-fetch cart for full data
      const data = await cartService.getCart();
      set({
        items: data.items,
        totalItems: data.total_items,
        totalPrice: data.total_price,
        loading: false,
      });
    } catch {
      set({ loading: false });
      throw new Error('Gagal menambahkan ke keranjang.');
    }
  },

  updateItem: async (itemId, quantity) => {
    try {
      const result = await cartService.updateItem(itemId, quantity);
      set({ totalItems: result.total_items, totalPrice: result.total_price });
      // Re-fetch full cart
      const data = await cartService.getCart();
      set({ items: data.items });
    } catch {
      throw new Error('Gagal memperbarui item.');
    }
  },

  removeItem: async (itemId) => {
    try {
      const result = await cartService.removeItem(itemId);
      set((state) => ({
        items: state.items.filter((i) => i.id !== itemId),
        totalItems: result.total_items,
        totalPrice: result.total_price,
      }));
    } catch {
      throw new Error('Gagal menghapus item.');
    }
  },

  clearCart: () => {
    set({ items: [], totalItems: 0, totalPrice: 0 });
  },
}));
