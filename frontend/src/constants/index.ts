// ─── Rental Status Constants ─────────────────────────────────

import type { RentalStatus } from '@/types';

interface StatusConfig {
  label: string;
  color: string;
  bgColor: string;
  textColor: string;
}

export const RENTAL_STATUS: Record<RentalStatus, StatusConfig> = {
  booked: {
    label: 'Dipesan',
    color: '#3B82F6',
    bgColor: 'bg-blue-100',
    textColor: 'text-blue-700',
  },
  active: {
    label: 'Aktif',
    color: '#10B981',
    bgColor: 'bg-green-100',
    textColor: 'text-green-700',
  },
  returned: {
    label: 'Dikembalikan',
    color: '#6B7280',
    bgColor: 'bg-gray-100',
    textColor: 'text-gray-700',
  },
  canceled: {
    label: 'Dibatalkan',
    color: '#EF4444',
    bgColor: 'bg-red-100',
    textColor: 'text-red-700',
  },
  overdue: {
    label: 'Terlambat',
    color: '#F59E0B',
    bgColor: 'bg-amber-100',
    textColor: 'text-amber-700',
  },
};

export const PAYMENT_METHODS = [
  { value: 'qris', label: 'QRIS' },
  { value: 'cash', label: 'Bayar di Toko (Cash)' },
] as const;

export const STORE_HOURS = {
  open: '09:00',
  close: '21:45',
  timezone: 'Asia/Jakarta',
} as const;
