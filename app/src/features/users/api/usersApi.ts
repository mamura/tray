import type { Paginated, User, UserCreateInput, UserUpdateInput } from "@/entities/user";
import { api } from "@/shared/api/apiClient";

export async function listUsers(
  params: {
    page?: number;
    perPage?: number;
    search?: string
  } = {}
) {
  const { page = 1, perPage = 10, search = '' } = params;
  const { data } = await api.get<Paginated<User>>('/api/users', {
    params: {
      page,
      per_page:perPage,
      search
    }
  });

  return data;
}

export async function getUser(id: number) {
  const { data } = await api.get<User>(`/api/users/${id}`);
  return data;
}

export async function createUser(payload: UserCreateInput) {
  const { data } = await api.post<User>('/api/users', payload);
  return data;
}


export async function updateUser(id: number, payload: UserUpdateInput) {
  const { data } = await api.put<User>(`/api/usres/${id}`, payload);
  return data;
}

export async function deleteUser(id: number) {
  await api.delete(`/api/users/${id}`);
}
