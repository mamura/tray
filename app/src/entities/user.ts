export interface User {
  id: number,
  name: string,
  email: string,
  role?: 'admin' | 'manager' | 'viewer',
  create_at?: string,
  updated_at?: string
}

export interface Paginated<T> {
  data: T[]
  meta: {
    current_page: number,
    per_page: number,
    total: number,
    last_page: number,
  }
}

export type UserCreateInput = {
  name: string,
  email: string,
  password: string,
  role?: User['role']
}

export type UserUpdateInput = Partial<Omit<UserCreateInput, 'password'>> & {
  password?: string
}
