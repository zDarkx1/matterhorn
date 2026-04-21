// ─── Rental Types ────────────────────────────────────────────

import type { Product } from './product.types';
import type { User } from './user.types';

export type RentalStatus = 'booked' | 'active' | 'returned' | 'canceled' | 'overdue';

export interface Rental {
  id: number;
  user_id: number;
  admin_id: number | null;
  invoice_no: string;
  start_date: string;
  end_date: string;
  return_date: string | null;
  total_price: number;
  fine_amount: number;
  status: RentalStatus;
  guarantee_info: string | null;
  user?: User;
  admin?: User;
  items?: RentalItem[];
  payment?: Payment;
  created_at: string;
}

export interface RentalItem {
  id: number;
  rental_id: number;
  product_id: number;
  quantity: number;
  price_at_rental: number;
  product?: Product;
}

export interface Payment {
  id: number;
  rental_id: number;
  amount: number;
  payment_method: 'qris' | 'cash';
  status: 'pending' | 'verified';
  proof_url: string | null;
  qris_invoice: string | null;
  qris_url: string | null;
  expired_at: string | null;
  created_at: string;
}
