// ─── User Types ──────────────────────────────────────────────

export type UserRole = 'customer' | 'admin';

export interface User {
  id: number;
  name: string;
  email: string;
  role: UserRole;
  phone_number: string | null;
  address: string | null;
  avatar: string | null;
  google_id: string | null;
  created_at: string;
}

export interface UserAddress {
  id: number;
  user_id: number;
  label: string;
  recipient_name: string;
  phone: string;
  full_address: string;
  district: string;
  city: string;
  province: string;
  postal_code: string;
  notes: string | null;
  is_default: boolean;
  created_at: string;
}

export interface LoginPayload {
  email: string;
  password: string;
}

export interface RegisterPayload {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  phone_number?: string;
}

export interface AuthResponse {
  user: User;
  token: string;
}
