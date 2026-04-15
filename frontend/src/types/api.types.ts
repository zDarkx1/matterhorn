// ─── API Response Types ──────────────────────────────────────
// Standard response format dari Laravel backend

export interface ApiResponse<T> {
  status: 'success' | 'error';
  data: T;
  message?: string;
  errors?: Record<string, string[]>;
}

export interface PaginatedResponse<T> {
  status: 'success';
  data: T[];
  meta: PaginationMeta;
}

export interface PaginationMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}
