<template>
  <section class="max-w-lg">
    <h1 class="text-2xl font-semibold mb-4">Editar usuário</h1>
    <div v-if="loading">Carregando…</div>
    <div v-else-if="error" class="text-red-600">{{ error }}</div>
    <div v-else-if="!current">Usuário não encontrado.</div>
    <div v-else>
      <UserForm mode="edit" :value="current" @submit="handleSubmit" @cancel="goBack" />
    </div>
  </section>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import UserForm from '@features/users/ui/UserForm.vue'

import type { UserUpdateInput } from '@entities/user'

const route = useRoute()
const router = useRouter()
const store = useUsersStore()
const { current, loading, error } = storeToRefs(store)

onMounted(() => {
  const id = Number(route.params.id)
  store.fetchOne(id)
})

async function handleSubmit(payload: UserUpdateInput) {
  const id = Number(route.params.id)
  await store.update(id, payload)
  router.replace('/users')
}
function goBack() {
  router.back()
}
</script>
