<template>
  <div class="flex justify-center items-center min-h-screen px-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
      <!-- Logo -->
      <div class="text-center mb-6">
        <img src="@/assets/images/logo.png" alt="Logo" class="mx-auto w-24 mb-2" />
        <h1 class="text-gray-600 text-lg font-semibold">Acesso ao Sistema</h1>
      </div>

      <!-- Formulário -->
      <form @submit.prevent="login" class="space-y-4">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Digite seu email"
          />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Digite sua senha"
          />
        </div>

        <div class="flex items-center">
          <input id="remember" type="checkbox" class="w-4 h-4 text-blue-600 rounded" />
          <label for="remember" class="ml-2 block text-sm text-gray-600">Lembre-me</label>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-blue-600 text-white py-2 rounded-md font-semibold hover:bg-blue-700 transition"
        >
          {{ loading ? 'Entrando…' : 'Entrar' }}
        </button>

        <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>
      </form>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/features/auth/store/useAuthStore';

const email     = ref('');
const password  = ref('');
const error     = ref<string | null>(null);
const loading   = ref(false);
const auth      = useAuthStore();
const route     = useRoute();
const router    = useRouter();

const redirectTarget = computed(() => {
  const q = route.query.redirect;

  return typeof q === 'string' && q !== '/login' ? q : '/';
});

watch(() => auth.isAuthenticated, (ok) => {
  if (ok) {
    router.replace(redirectTarget.value);
  }
});

async function login() {
  error.value   = null;
  loading.value = true;

  try {
    auth.login(email.value, password.value);
    router.replace( (route.query.redirect as string) || '/' )

  } catch (e: unknown) {
    error.value = e?.response?.data?.message || 'Falha no login';
  }
}
</script>
