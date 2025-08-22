export type SellerId = number;

export interface Seller {
  id: SellerId;
  name: string;
  email: string;
  created_at?: string;
  updated_at?: string;
}
