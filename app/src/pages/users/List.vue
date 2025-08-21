<template>
  <section>
    <header class="mb-4 flex items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Usuários</h1>
      <div class="flex gap-2">
        <input
          v-model.trim="search"
          @keyup.enter="applySearch"
          placeholder="Buscar por nome/email…"
          class="border rounded p-2"
        />
        <button class="border rounded px-3 py-2" @click="applySearch">Buscar</button>
        <RouterLink to="/users/new" class="border rounded px-3 py-2">Novo</RouterLink>
      </div>
    </header>

    <div v-if="loading">Carregando…</div>
    <div v-else-if="error" class="text-red-600">{{ error }}</div>
    <div v-else>
      <div class="overflow-x-auto border rounded">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left p-2">ID</th>
              <th class="text-left p-2">Nome</th>
              <th class="text-left p-2">Email</th>
              <th class="text-left p-2">Papel</th>
              <th class="text-left p-2">Criado em</th>
              <th class="text-right p-2">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in items" :key="u.id" class="border-t">
              <td class="p-2">{{ u.id }}</td>
              <td class="p-2">{{ u.name }}</td>
              <td class="p-2">{{ u.email }}</td>
              <td class="p-2">{{ u.role ?? '—' }}</td>
              <td class="p-2">{{ u.created_at?.slice(0,10) ?? '—' }}</td>
              <td class="p-2 text-right">
                <RouterLink :to="`/users/${u.id}/edit`" class="underline mr-3">Editar</RouterLink>
                <button class="text-red-600 underline" @click="confirmDelete(u.id)">Excluir</button>
              </td>
            </tr>
            <tr v-if="!items.length">
              <td colspan="6" class="p-4 text-center opacity-70">Nenhum usuário</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-3 flex items-center gap-3">
        <button class="border rounded px-3 py-1" :disabled="page<=1" @click="goPage(page-1)">Anterior</button>
        <span>Página {{ page }} de {{ lastPage }}</span>
        <button class="border rounded px-3 py-1" :disabled="page>=lastPage" @click="goPage(page+1)">Próxima</button>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { useUsersStore } from '@features/users/store/useUsersStore'

const store = useUsersStore()
const { items, loading, error, page, lastPage } = storeToRefs(store)
const search = $ref(store.search)

function applySearch() {
  store.search = search
  store.page = 1
  store.load()
}
function goPage(p: number) {
  store.page = p
  store.load()
}
async function confirmDelete(id: number) {
  if (confirm('Excluir usuário?')) {
    await store.remove(id)
  }
}

onMounted(() => store.load())
</script>
