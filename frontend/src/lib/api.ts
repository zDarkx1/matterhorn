// ─── API Fetch Wrapper ───────────────────────────────────────
// Central fetch utility with Sanctum token auto-attach.

import type { ApiResponse } from '@/types';

const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';

function getToken(): string | null {
  if (typeof window === 'undefined') return null;
  try {
    const stored = localStorage.getItem('matterhorn-auth');
    if (!stored) return null;
    const parsed = JSON.parse(stored);
    return parsed?.state?.token ?? null;
  } catch {
    return null;
  }
}

// Auth endpoints that return 401 for invalid credentials (NOT session expiry)
const AUTH_ENDPOINTS = ['/auth/login', '/auth/register'];

export async function apiFetch<T>(
  endpoint: string,
  options: RequestInit = {}
): Promise<ApiResponse<T>> {
  const token = getToken();
  const isAuthEndpoint = AUTH_ENDPOINTS.some((e) => endpoint.startsWith(e));

  const headers: Record<string, string> = {
    'Content-Type': 'application/json',
    Accept: 'application/json',
    ...((options.headers as Record<string, string>) || {}),
  };

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  const response = await fetch(`${API_URL}${endpoint}`, {
    ...options,
    headers,
  });

  // Handle 401:
  // - Auth endpoints (login/register): 401 = wrong credentials → pass through to error handler
  // - Other endpoints with token: 401 = session expired → dispatch event
  if (response.status === 401 && !isAuthEndpoint) {
    if (typeof window !== 'undefined' && token) {
      localStorage.removeItem('matterhorn-auth');
      window.dispatchEvent(new Event('session-expired'));
    }
    throw new Error('Sesi telah berakhir, silakan login kembali.');
  }

  const data = await response.json();

  if (!response.ok || data.status === 'error') {
    const errorMessage = data.message || data.error || 'Terjadi kesalahan.';
    throw new Error(errorMessage);
  }

  return data as ApiResponse<T>;
}

export async function apiUpload<T>(
  endpoint: string,
  formData: FormData
): Promise<ApiResponse<T>> {
  const token = getToken();
  const headers: Record<string, string> = { Accept: 'application/json' };
  if (token) headers['Authorization'] = `Bearer ${token}`;

  const response = await fetch(`${API_URL}${endpoint}`, {
    method: 'POST',
    headers,
    body: formData,
  });

  if (response.status === 401) {
    if (typeof window !== 'undefined' && token) {
      localStorage.removeItem('matterhorn-auth');
      window.dispatchEvent(new Event('session-expired'));
    }
    throw new Error('Sesi telah berakhir.');
  }

  const data = await response.json();
  if (!response.ok || data.status === 'error') {
    throw new Error(data.message || 'Upload gagal.');
  }
  return data as ApiResponse<T>;
}

export { API_URL };
