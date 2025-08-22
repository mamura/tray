export type SaleCreateInput = {
  seller_id: number;
  amount: number;
  sold_at: string;
}

export type SaleUpdateInput = Partial<Pick<SaleCreateInput, 'amount' | 'sold_at'>> & {
  seller_id?: number;
}
