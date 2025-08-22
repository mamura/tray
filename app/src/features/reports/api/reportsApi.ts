import { api } from '@/shared/api/apiClient'
import { z } from 'zod'

const AdminDailySummaryDto = z.object({
  date: z.string(),
  totalCount: z.number(),
  totalAmount: z.number(),
  totalCommission: z.number(),
})

const SellerDailySummaryDto = z.object({
  sellerId: z.number(),
  sellerName: z.string(),
  sellerEmail: z.string().email(),
  date: z.string(),
  count: z.number(),
  totalAmount: z.number(),
  totalCommission: z.number(),
})

export type AdminDailySummary = z.infer<typeof AdminDailySummaryDto>
export type SellerDailySummary = z.infer<typeof SellerDailySummaryDto>

export async function adminDaily(date: string) {
  const res = await api.get('/reports/daily/admin', { params: { date } })
  return AdminDailySummaryDto.parse(res.data)
}

export async function sellersDaily(date: string) {
  const res = await api.get('/reports/daily/sellers', { params: { date } })
  return z.array(SellerDailySummaryDto).parse(res.data)
}

export async function sellerDaily(sellerId: number, date: string) {
  const res = await api.get(`/reports/daily/sellers/${sellerId}`, { params: { date } })
  return SellerDailySummaryDto.parse(res.data)
}
