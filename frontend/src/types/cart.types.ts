// ─── Cart Types ──────────────────────────────────────────────

import type { Product } from './product.types';

export interface CartItem {
  id: string;
  product_id: number;
  quantity: number;
  size?: string;
  product?: Product;
}

export interface CartData {
  items: CartItem[];
  total_items: number;
  total_price: number;
}

export interface AddToCartPayload {
  product_id: number;
  quantity: number;
  size?: string;
}
