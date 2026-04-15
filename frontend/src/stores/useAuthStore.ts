// ─── Auth Store (Zustand + Persist) ──────────────────────────

import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import { authService } from '@/services/auth.service';
import type { User, LoginPayload, RegisterPayload } from '@/types';

interface AuthState {
  user: User | null;
  token: string | null;
  isAdmin: boolean;
  loading: boolean;

  login: (payload: LoginPayload) => Promise<void>;
  register: (payload: RegisterPayload) => Promise<void>;
  logout: () => Promise<void>;
  fetchMe: () => Promise<void>;
  setLoading: (loading: boolean) => void;
  hasAddress: () => boolean;
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      user: null,
      token: null,
      isAdmin: false,
      loading: false,

      login: async (payload) => {
        set({ loading: true });
        try {
          const { user, token } = await authService.login(payload);
          set({ user, token, isAdmin: user.role === 'admin', loading: false });
        } catch (error) {
          set({ loading: false });
          throw error;
        }
      },

      register: async (payload) => {
        set({ loading: true });
        try {
          const { user, token } = await authService.register(payload);
          set({ user, token, isAdmin: false, loading: false });
        } catch (error) {
          set({ loading: false });
          throw error;
        }
      },

      logout: async () => {
        try {
          await authService.logout();
        } catch {
          // Proceed even if API call fails
        }
        set({ user: null, token: null, isAdmin: false });
      },

      fetchMe: async () => {
        try {
          const user = await authService.me();
          set({ user, isAdmin: user.role === 'admin' });
        } catch {
          set({ user: null, token: null, isAdmin: false });
        }
      },

      setLoading: (loading) => set({ loading }),

      hasAddress: () => {
        // Will be checked against addresses endpoint in profile
        return !!get().user;
      },
    }),
    {
      name: 'matterhorn-auth', // localStorage key
      partialize: (state) => ({
        user: state.user,
        token: state.token,
        isAdmin: state.isAdmin,
      }),
    }
  )
);
