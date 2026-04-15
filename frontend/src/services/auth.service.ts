// ─── Auth Service ────────────────────────────────────────────
// Handles register, login, logout, and me API calls.

import { apiFetch } from '@/lib/api';
import type { AuthResponse, LoginPayload, RegisterPayload, User } from '@/types';

export const authService = {
  async register(payload: RegisterPayload): Promise<AuthResponse> {
    const res = await apiFetch<AuthResponse>('/auth/register', {
      method: 'POST',
      body: JSON.stringify(payload),
    });
    return res.data;
  },

  async login(payload: LoginPayload): Promise<AuthResponse> {
    const res = await apiFetch<AuthResponse>('/auth/login', {
      method: 'POST',
      body: JSON.stringify(payload),
    });
    return res.data;
  },

  async logout(): Promise<void> {
    await apiFetch('/auth/logout', { method: 'POST' });
  },

  async me(): Promise<User> {
    const res = await apiFetch<{ user: User }>('/auth/me');
    return res.data.user;
  },
};
