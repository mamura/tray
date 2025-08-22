import { api } from '@/shared/api/apiClient'
import type { Seller } from '@/entities/seller'
import type { Paginated } from '@/shared/types/http'
import { z } from 'zod'

const SellerDto = z.object({ id: z.number(), name: z.string(), email: z.string().email() })
const SellersPageDto = z.object({ data: z.array(SellerDto) })

export async function listSellers(params?: { page?: number; perPage?: number }) {
  const res = await api.get('/sellers', { params: { page: params?.page, per_page: params?.perPage ?? 10 } })
  const parsed = SellersPageDto.parse(res.data)
  return { data: parsed.data as unknown as Paginated<Seller>['data'] }
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
