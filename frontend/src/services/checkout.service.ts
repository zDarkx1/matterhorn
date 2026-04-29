// ─── Checkout & Rental Service ───────────────────────────────
import { apiFetch } from '@/lib/api';
import type { Rental } from '@/types';

export interface CheckoutPayload {
  payment_method: 'cash' | 'qris';
  start_date: string;
  end_date: string;
  item_ids?: string[];
}

export interface CheckoutResponse {
  id: number;
  invoice_no: string;
  status: string;
  total_price: number;
  payment?: {
    qris_url?: string | null;
    qris_invoice?: string | null;
    status: string;
  };
}

export interface PaymentCheckResponse {
  payment_status: 'pending' | 'verified';
  message: string;
}

export interface RentalListMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export const checkoutService = {
  /** Create a new rental from cart */
  async createOrder(payload: CheckoutPayload): Promise<Rental> {
    const res = await apiFetch<Rental>('/checkout', {
      method: 'POST',
      body: JSON.stringify(payload),
    });
    return res.data;
  },

  /** Get user's rental history */
  async getMyRentals(params?: { status?: string; page?: number; per_page?: number }): Promise<{
    data: Rental[];
    meta: RentalListMeta;
  }> {
    const searchParams = new URLSearchParams();
    if (params?.status) searchParams.set('status', params.status);
    if (params?.page) searchParams.set('page', String(params.page));
    if (params?.per_page) searchParams.set('per_page', String(params.per_page));

    const qs = searchParams.toString();
    const res = await apiFetch<Rental[]>(`/rentals${qs ? `?${qs}` : ''}`);
    return {
      data: res.data,
      meta: (res as unknown as { meta: RentalListMeta }).meta,
    };
  },

  /** Get single rental detail */
  async getRentalDetail(id: number): Promise<Rental> {
    const res = await apiFetch<Rental>(`/rentals/${id}`);
    return res.data;
  },

  /** Poll QRIS payment status */
  async checkPaymentStatus(rentalId: number): Promise<PaymentCheckResponse> {
    const res = await apiFetch<PaymentCheckResponse>(`/rentals/${rentalId}/check-payment`, {
      method: 'POST',
    });
    return res.data;
  },
};
