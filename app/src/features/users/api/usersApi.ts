import { z } from 'zod'
import type { User } from '@/entities/user'
import type { Paginated } from '@/shared/types/http'
import { api } from '@/shared/api/apiClient'

// DTO da API (snake_case)
const UserDto = z.object({
  id: z.number(),
  name: z.string(),
  email: z.string().email(),
  role: z.enum(['admin','manager','viewer']).optional(),
  created_at: z.string().optional(),
  updated_at: z.string().optional(),
})

// Página paginada (opcional incluir meta)
const UsersPageDto = z.object({
  data: z.array(UserDto),
  meta: z.object({
    current_page: z.number(),
    per_page: z.number(),
    total: z.number(),
    last_page: z.number().optional(),
  }).optional(),
})

// Mapper (se quiser camelCase no domínio)
const toUser = (d: z.infer<typeof UserDto>): User => ({
  id: d.id,
  name: d.name,
  email: d.email,
  role: d.role,
  created_at: d.created_at,
  updated_at: d.updated_at,
})

export async function listUsers(params: { page?: number; perPage?: number; search?: string } = {}) {
  const { page = 1, perPage = 10, search = '' } = params
  const res = await api.get('/api/users', { params: { page, per_page: perPage, search } })
  const parsed = UsersPageDto.parse(res.data)
  return {
    data: parsed.data.map(toUser),
    meta: parsed.meta,
  } satisfies Paginated<User>
}

export async function getUser(id: number) {
  const res = await api.get(`/api/users/${id}`)
  return toUser(UserDto.parse(res.data))
}

export async function createUser(payload: { name: string; email: string; password: string; role?: User['role'] }) {
  const res = await api.post('/api/users', payload)
  return toUser(UserDto.parse(res.data))
}

export async function updateUser(id: number, payload: Partial<{ name: string; email: string; password: string; role?: User['role'] }>) {
  const res = await api.put(`/api/users/${id}`, payload)
  return toUser(UserDto.parse(res.data))
}

export async function deleteUser(id: number) {
  await api.delete(`/api/users/${id}`)
}
