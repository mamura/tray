import { api } from '@/shared/api/apiClient'
import type { Sale } from '@/entities/sale'
import type { Paginated } from '@/shared/types/http'
import { z } from 'zod'
import type { SaleCreateInput, SaleUpdateInput } from '../model/inputs';

const SaleDto = z.object({
  id: z.number(),
  seller_id: z.number(),
  amount: z.number(),
  sold_at: z.string(),
  commission: z.number().optional(),
  created_at: z.string().optional(),
  updated_at: z.string().optional(),
  seller: z.object({
    id: z.number(),
    name: z.string(),
    email: z.string().email(),
  }).optional(),
});

type SaleDto = z.infer<typeof SaleDto>;

const SalesPageDto = z.object({
  data: z.array(SaleDto),
  meta: z.object({
    current_page: z.number(),
    per_page: z.number(),
    total: z.number(),
    last_page: z.number().optional(),
  }).optional(),
});

const toSale = (d: SaleDto): Sale => ({
  id: d.id,
  seller_id: d.seller_id,
  amount: d.amount,
  commission: d.commission,
  sold_at: d.sold_at,
  created_at: d.created_at,
  updated_at: d.updated_at,
  seller: d.seller ? { id: d.seller.id, name: d.seller.name, email: d.seller.email } : undefined,
});


export async function listSales(params: {
  page?: number
  perPage?: number
  sellerId?: number
  dateFrom?: string
  dateTo?: string
} = {}): Promise<Paginated<Sale>> {
  const { page = 1, perPage = 20, sellerId, dateFrom, dateTo } = params
  const res = await api.get('/sales', {
    params: {
      page,
      per_page: perPage,
      seller_id: sellerId,
      date_from: dateFrom,
      date_to: dateTo,
    },
  })
  const parsed = SalesPageDto.parse(res.data);
  return {
    data: parsed.data.map(toSale),
    meta: parsed.meta,
  }
}

export async function getSale(id: number): Promise<Sale> {
  const res = await api.get(`/sales/${id}`)
  return toSale(SaleDto.parse(res.data))
}

export async function createSale(payload: SaleCreateInput): Promise<Sale> {
  const res = await api.post('/sales', payload)
  return toSale(SaleDto.parse(res.data))
}

export async function updateSale(id: number, payload: SaleUpdateInput): Promise<Sale> {
  const res = await api.put(`/sales/${id}`, payload)
  return toSale(SaleDto.parse(res.data))
}

// Habilite se sua API tiver DELETE /sales/:id
export async function deleteSale(id: number): Promise<void> {
  await api.delete(`/sales/${id}`)
}
