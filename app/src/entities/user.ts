export type UserId = number;
export type UserRole = 'admin' | 'manager' | 'viewer'

export interface User {
  id: UserId,
  name: string,
  email: string,
  role?: 'admin' | 'manager' | 'viewer',
  created_at?: string,
  updated_at?: string
}
