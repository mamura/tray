import type { UserRole } from "@/entities/user";

export type UserCreateInput = {
  name: string;
  email: string;
  password: string;
  role?: UserRole;
}

export type UserUpdateInput = Partial<Omit<UserCreateInput, 'password'>> & { password?: string }
