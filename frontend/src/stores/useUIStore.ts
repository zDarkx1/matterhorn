// ─── UI Store (Zustand) ──────────────────────────────────────
// Ephemeral UI state — no persistence needed.

import { create } from 'zustand';

interface Toast {
  id: string;
  message: string;
  type: 'success' | 'error' | 'info';
}

interface UIState {
  isMobileMenuOpen: boolean;
  isSearchOpen: boolean;
  isCartDrawerOpen: boolean;
  toasts: Toast[];

  toggleMobileMenu: () => void;
  setMobileMenu: (open: boolean) => void;
  toggleSearch: () => void;
  setSearchOpen: (open: boolean) => void;
  setCartDrawer: (open: boolean) => void;
  addToast: (message: string, type?: 'success' | 'error' | 'info') => void;
  removeToast: (id: string) => void;
}

export const useUIStore = create<UIState>()((set) => ({
  isMobileMenuOpen: false,
  isSearchOpen: false,
  isCartDrawerOpen: false,
  toasts: [],

  toggleMobileMenu: () => set((s) => ({ isMobileMenuOpen: !s.isMobileMenuOpen })),
  setMobileMenu: (open) => set({ isMobileMenuOpen: open }),
  toggleSearch: () => set((s) => ({ isSearchOpen: !s.isSearchOpen })),
  setSearchOpen: (open) => set({ isSearchOpen: open }),
  setCartDrawer: (open) => set({ isCartDrawerOpen: open }),

  addToast: (message, type = 'success') => {
    const id = Date.now().toString();
    set((s) => ({ toasts: [...s.toasts, { id, message, type }] }));
    // Auto-remove after 4 seconds
    setTimeout(() => {
      set((s) => ({ toasts: s.toasts.filter((t) => t.id !== id) }));
    }, 4000);
  },

  removeToast: (id) => {
    set((s) => ({ toasts: s.toasts.filter((t) => t.id !== id) }));
  },
}));
