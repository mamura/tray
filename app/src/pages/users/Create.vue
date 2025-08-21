<template>
  <section class="max-w-lg">
    <h1 class="text-2xl font-semibold mb-4">Novo usuário</h1>
    <UserForm mode="create" @submit="handleSubmit" @cancel="goBack" />
  </section>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import UserForm from '@features/users/ui/UserForm.vue'
import { useUsersStore } from '@features/users/store/useUsersStore'
import type { UserCreateInput } from '@entities/user'

const router = useRouter()
const store = useUsersStore()

async function handleSubmit(payload: UserCreateInput) {
  await store.create(payload)
  router.replace('/users')
}
function goBack() {
  router.back()
}
</script>
