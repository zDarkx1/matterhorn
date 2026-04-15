// ─── Store Service ───────────────────────────────────────────

import { apiFetch } from '@/lib/api';

export interface StoreStatus {
  is_open: boolean;
  current_time: string;
  open_time: string;
  close_time: string;
  message: string;
}

export const storeService = {
  async getStatus(): Promise<StoreStatus> {
    const res = await apiFetch<StoreStatus>('/store-status');
    return res.data;
  },
};
