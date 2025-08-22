import { api } from '@/shared/api/apiClient'
import type { Seller } from '@/entities/seller'
import type { Paginated } from '@/shared/types/http'
import { z } from 'zod'

const SellerDto = z.object({
  id: z.number(),
  name: z.string(),
  email: z.string().email(),
  created_at: z.string().optional(),
  updated_at: z.string().optional(),
});

const SellersPageDto = z.object({
  data: z.array(SellerDto),
  meta: z.object({
    current_page: z.number(),
    per_page: z.number(),
    total: z.number(),
    last_page: z.number().optional(),
  }).optional(),
});

const toSeller = (d: z.infer<typeof SellerDto>): Seller => ({
  id: d.id,
  name: d.name,
  email: d.email,
  created_at: d.created_at,
  updated_at: d.updated_at,
})

export async function listSellers(params?: { page?: number; perPage?: number }) {
  const { page = 1, perPage = 20, search = '' } = params;
  const res     = await api.get('/sellers', { params: { page, per_page: perPage, search } });
  const parsed  = SellersPageDto.parse(res.data);

  return {
    data: parsed.data.map(toSeller), meta: parsed.meta
  }
}

export async function createSeller(payload: Pick<Seller, 'name' | 'email'>) {
  const res = await api.post('/sellers', payload)
  return SellerDto.parse(res.data) as unknown as Seller
}

export async function updateSeller(id: number, payload: Pick<Seller, 'name' | 'email'>) {
  const res = await api.put(`/sellers/${id}`, payload)
  return SellerDto.parse(res.data) as unknown as Seller
}

export async function deleteSeller(id: number) {
  await api.delete(`/sellers/${id}`)
}

export async function resendCommission(id: number, date: string) {
  await api.post(`/sellers/${id}/commission/resend`, { date })
}
