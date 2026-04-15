// ─── Format Utilities ────────────────────────────────────────

/**
 * Format angka ke Rupiah: 50000 → "Rp 50.000"
 */
export function formatRupiah(amount: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
}

/**
 * Format tanggal ISO ke format Indonesia: "14 Apr 2026"
 */
export function formatDate(dateString: string): string {
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  });
}

/**
 * Format tanggal lengkap: "Senin, 14 April 2026"
 */
export function formatDateFull(dateString: string): string {
  return new Date(dateString).toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
}

/**
 * Hitung selisih hari antara 2 tanggal
 */
export function calculateDays(start: string, end: string): number {
  const startDate = new Date(start);
  const endDate = new Date(end);
  const diffTime = Math.abs(endDate.getTime() - startDate.getTime());
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 karena hari mulai = hari pertama
}
