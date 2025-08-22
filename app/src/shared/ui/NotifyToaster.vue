<script setup lang="ts">
import { useNotifyStore } from '@/stores/notify'
import { storeToRefs } from 'pinia'
import { ref } from 'vue';

const notify    = useNotifyStore();
const { items } = storeToRefs(notify);

function typeAlert(type: string) {
  if (type === 'success') return 'Sucesso!'
  if (type === 'error')   return 'Erro!'
  return 'bg-slate-700'
}

function typeBg(type: string) {
  if (type === 'success') return 'bg-emerald-700'
  if (type === 'error')   return 'bg-red-900'
  return 'bg-slate-700'
}

const showToast = ref(false);
setTimeout(() => (showToast.value = true), 1000);
</script>

<template>
  <div class="fixed top-0 right-0 z-[9999] p-4 space-y-3 w-full sm:w-96">
    <TransitionGroup
      name="toast"
      tag="div"
      enter-active-class="transition duration-500 ease-out"
      enter-from-class="translate-y-[-100%] opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-400 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-[-100%] opacity-0"
    >
      <div
        v-for="n in items"
        :key="n.id"
        class="text-white px-6 py-4 w-full shadow-lg fixed top-0 left-0 z-50 mb-4 bg-emerald-700"
        :class="typeBg(n.type)"
      >
        <span class="text-xl inline-block mr-5 align-middle">
          <i class="fas fa-bell"></i>
        </span>
        <span class="inline-block align-middle mr-8">
          <b class="capitalize">{{ typeAlert(n.type) }}</b> {{ n.message }}
        </span>
        <button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none" v-on:click="notify.remove(n.id)">
          <span>×</span>
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>
