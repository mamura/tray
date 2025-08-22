import type { SellerId } from "./seller";

export type SaleId = number;

export interface Sale {
  id: SaleId;
  seller_id: SellerId;
  amount: number;
  sold_at: string
  commission?: number;
  created_at?: string;
  updated_at?: string;
}
