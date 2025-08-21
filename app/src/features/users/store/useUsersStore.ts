import type { Paginated, User, UserCreateInput } from "@/entities/user";
import { defineStore } from "pinia";
import { createUser, deleteUser, getUser, listUsers, updateUser } from "../api/usersApi";

export const useUsersStore = defineStore('users', {
  state: () => ({
    items: [] as User[],
    page: 1,
    perPage: 10,
    total: 0,
    lastPage: 1,
    search: '',
    loading: false,
    error: null as string | null,
    current: null as User | null
  }),

  actions: {
    async load() {
      this.loading  = true;
      this.error    = null;

      try {
        const res: Paginated<User> = await listUsers({
          page: this.page,
          perPage: this.perPage,
          search: this.search,
        });

        this.items    = res.data;
        this.page     = res.meta.current_page;
        this.perPage  = res.meta.per_page;
        this.total    = res.meta.total;
        this.lastPage = res.meta.last_page;
      } catch(e: any) {
        this.error = e?.response?.data?.message ?? e?.message ?? 'Erro ao carregar usuários';
      } finally {
        this.loading = false;
      }
    },

    async fetchOne(id: number) {
      this.loading  = true;
      this.error    = null;

      try {
        this.current = await getUser(id);
      } catch(e: any) {
        this.error = e?.response?.data?.message ?? e?.message ?? 'Erro ao carregar usuário';
      } finally {
        this.loading = false;
      }
    },

    async create(payload: UserCreateInput) {
      this.loading  = true;
      this.error    = null;

      try {
        await createUser(payload);
        await this.load();
      } catch(e: any) {
        this.error = e?.response?.data?.message ?? e?.message ?? 'Erro ao criar usuário'
        throw e
      } finally {
        this.loading = false;
      }
    },

    async update(id: number, payload: UserCreateInput) {
      this.loading  = true;
      this.error    = null;

      try {
        await updateUser(id, payload);
        await this.load();
      } catch(e: any) {
        this.error = e?.response?.data?.message ?? e?.message ?? 'Erro ao atualizar usuário'
        throw e
      } finally {
        this.loading = false;
      }
    },

    async remove(id: number) {
      this.loading  = true;
      this.error    = null;

      try {
        await deleteUser(id);
        this.load();
      } catch(e: any) {
        this.error = e?.response?.data?.message ?? e?.message ?? 'Erro ao excluir usuário'
        throw e
      } finally {
        this.loading = false;
      }
    },
  }
});
