import { api } from '@/shared/api/apiClient'
import type { Sale } from '@/entities/sale'
import type { Paginated } from '@/shared/types/http'
import { z } from 'zod'

const SaleDto = z.object({
  id: z.number(),
  seller_id: z.number(),
  amount: z.number(),
  sold_at: z.string(),
  commission: z.number().optional(),
})
const SalesPageDto = z.object({ data: z.array(SaleDto) })

export async function listSales(params?: {
  seller_id?: number; date_from?: string; date_to?: string; page?: number; per_page?: number
}) {
  const res = await api.get('/sales', { params })
  const parsed = SalesPageDto.parse(res.data)
  return { data: parsed.data as unknown as Paginated<Sale>['data'] }
}

export async function createSale(payload: Pick<Sale, 'seller_id' | 'amount' | 'sold_at'>) {
  const res = await api.post('/sales', payload)
  return SaleDto.parse(res.data) as unknown as Sale
}

export async function updateSale(id: number, payload: Pick<Sale, 'amount' | 'sold_at'>) {
  const res = await api.put(`/sales/${id}`, payload)
  return SaleDto.parse(res.data) as unknown as Sale
}

export async function deleteSale(id: number) {
  await api.delete(`/sales/${id}`)
}
