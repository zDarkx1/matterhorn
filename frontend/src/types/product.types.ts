// ─── Product Types ───────────────────────────────────────────

export interface Product {
  id: number;
  name: string;
  category: string;
  gender: 'unisex' | 'pria' | 'wanita' | 'anak';
  description: string | null;
  image: string | null;
  image_url: string | null;
  price_24h: number;
  stock_total: number;
  stock_available: number;
  sizes?: ProductSize[];
  created_at: string;
}

export interface ProductSize {
  id: number;
  size: string;
  stock: number;
}

export interface ProductFilters {
  search?: string;
  category?: string;
  gender?: string;
  min_price?: number;
  max_price?: number;
  sort?: 'newest' | 'price_asc' | 'price_desc' | 'name_asc';
  per_page?: number;
  page?: number;
}
