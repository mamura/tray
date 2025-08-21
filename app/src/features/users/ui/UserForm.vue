<template>
  <form class="space-y-3" @submit.prevent="onSubmit">
    <div class="grid gap-2">
      <label class="text-sm">Nome</label>
      <input v-model.trim="form.name" required class="border rounded p-2 w-full" />
    </div>

    <div class="grid gap-2">
      <label class="text-sm">Email</label>
      <input v-model.trim="form.email" type="email" required class="border rounded p-2 w-full" />
    </div>

    <div class="grid gap-2">
      <label class="text-sm">Papel</label>
      <select v-model="form.role" class="border rounded p-2 w-full">
        <option :value="undefined">—</option>
        <option value="admin">Admin</option>
        <option value="manager">Manager</option>
        <option value="viewer">Viewer</option>
      </select>
    </div>

    <div class="grid gap-2" v-if="mode === 'create'">
      <label class="text-sm">Senha</label>
      <input v-model="form.password" type="password" required minlength="6" class="border rounded p-2 w-full" />
    </div>
    <div class="grid gap-2" v-else>
      <label class="text-sm">Senha (opcional)</label>
      <input v-model="form.password" type="password" minlength="6" class="border rounded p-2 w-full" />
      <small class="opacity-70">Deixe em branco para manter a senha atual.</small>
    </div>

    <div class="flex gap-2">
      <button :disabled="submitting" class="border rounded px-4 py-2">
        {{ submitting ? 'Salvando…' : 'Salvar' }}
      </button>
      <button type="button" class="border rounded px-4 py-2" @click="$emit('cancel')">Cancelar</button>
    </div>

    <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>
  </form>
</template>

<script setup lang="ts">
import { reactive, ref, watchEffect } from 'vue'
import type { User, UserCreateInput, UserUpdateInput } from '@entities/user'

const props = defineProps<{
  mode: 'create' | 'edit'
  value?: User | null
}>()

const emit = defineEmits<{
  submit: [payload: UserCreateInput | UserUpdateInput]
  cancel: []
}>()

const error = ref<string | null>(null)
const submitting = ref(false)

const form = reactive<UserCreateInput & UserUpdateInput>({
  name: '',
  email: '',
  role: undefined,
  password: ''
})

watchEffect(() => {
  if (props.mode === 'edit' && props.value) {
    form.name = props.value.name
    form.email = props.value.email
    form.role = props.value.role
    form.password = ''
  } else {
    form.name = ''
    form.email = ''
    form.role = undefined
    form.password = ''
  }
})

async function onSubmit() {
  error.value = null
  submitting.value = true
  try {
    const payload: any = {
      name: form.name,
      email: form.email,
      role: form.role
    }
    if (props.mode === 'create' || form.password) payload.password = form.password
    emit('submit', payload)
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? e?.message ?? 'Erro ao salvar'
  } finally {
    submitting.value = false
  }
}
</script>
