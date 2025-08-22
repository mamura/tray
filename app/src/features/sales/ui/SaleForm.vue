<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import type { SaleCreateInput, SaleUpdateInput, Sale } from '@/entities/sale'
import type { Seller } from '@/entities/seller'
import { listSellers } from '@/features/sellers/api/sellersApi'

type Mode = 'create' | 'edit'

const props = withDefaults(defineProps<{
  mode: Mode
  initial?: Partial<Sale>   // para edição
}>(), { mode: 'create', initial: undefined })

const emit = defineEmits<{
  submit: [payload: SaleCreateInput | SaleUpdateInput]
  cancel: []
}>()

const sellers = ref<Seller[]>([])
const sellerId = ref<number | null>(null)
const amount = ref<number | null>(null)
const soldAt = ref<string>(new Date().toISOString().slice(0,10))
const loadingSellers = ref(false)

onMounted(async () => {
  loadingSellers.value = true
  try {
    sellers.value = (await listSellers({ perPage: 100 })).data
  } finally {
    loadingSellers.value = false
  }
})

watch(() => props.initial, (val) => {
  if (!val) return
  sellerId.value = val.seller_id ?? sellerId.value
  amount.value = val.amount ?? amount.value
  soldAt.value = val.sold_at ?? soldAt.value
}, { immediate: true })

function onSubmit() {
  if (props.mode === 'create') {
    if (sellerId.value == null || amount.value == null || !soldAt.value) return
    emit('submit', { seller_id: sellerId.value, amount: amount.value, sold_at: soldAt.value })
  } else {
    const payload: SaleUpdateInput = {}
    if (sellerId.value != null) payload.seller_id = sellerId.value
    if (amount.value != null) payload.amount = amount.value
    if (soldAt.value) payload.sold_at = soldAt.value
    emit('submit', payload)
  }
}
</script>

<template>
  <div class="space-y-3">
    <label class="block">
      <span class="text-sm">Vendedor</span>
      <select
        v-model.number="sellerId"
        class="mt-1 block w-full border rounded px-3 py-2"
        :disabled="loadingSellers || mode === 'edit' && !initial?.seller_id"
      >
        <option :value="undefined" disabled>Selecione</option>
        <option v-for="s in sellers" :key="s.id" :value="s.id">
          #{{ s.id }} — {{ s.name }}
        </option>
      </select>
    </label>

    <label class="block">
      <span class="text-sm">Valor (R$)</span>
      <input
        v-model.number="amount"
        type="number" step="0.01" min="0"
        class="mt-1 block w-full border rounded px-3 py-2"
        placeholder="0.00"
      />
    </label>

    <label class="block">
      <span class="text-sm">Data da venda</span>
      <input
        v-model="soldAt"
        type="date"
        class="mt-1 block w-full border rounded px-3 py-2"
      />
    </label>

    <div class="mt-4 flex justify-end gap-2">
      <button class="px-3 py-2 rounded border" @click="$emit('cancel')">Cancelar</button>
      <button class="px-3 py-2 rounded bg-emerald-600 text-white" @click="onSubmit">
        {{ mode === 'create' ? 'Salvar' : 'Atualizar' }}
      </button>
    </div>
  </div>
</template>
