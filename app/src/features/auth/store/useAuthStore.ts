import { api } from "@/shared/api/apiClient";
import { defineStore } from "pinia";

export interface AuthUser {
  id: number;
  name: string;
  email: string;
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as AuthUser | null,
    booted: false,
  }),

  getters: {
    isAuthenticated: (s) => !!s.user
  },

  actions: {
    async bootstrap() {
      try {
        const { data } = await api.get<AuthUser>('/api/me');
        this.user = data;
      } catch {
        this.user = null;
      } finally {
        this.booted = true;
      }
    },

    async login(email: string, password: string) {
      await api.get('sanctum/csrf-cookie');
      await api.post('login', { email, password });
      await this.bootstrap();
    },

    async logout() {
      try {
        await api.post('/api/logout')
      } catch {}

      this.user = null;
    }
  },
});
