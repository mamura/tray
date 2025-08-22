<script setup lang="ts">
import { onMounted, ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import type { Sale } from '@/entities/sale'
import { listSales, createSale, updateSale, deleteSale, getSale } from '@/features/sales/api/salesApi'
import { listSellers } from '@/features/sellers/api/sellersApi'
import type { Seller } from '@/entities/seller'
import { useNotify } from '@/shared/ui/useNotify'
import SaleForm from '@/features/sales/ui/SaleForm.vue'

const notify = useNotify()
const route = useRoute()
const router = useRouter()

// tabela
const rows = ref<Sale[]>([])
const loading = ref(false)

// filtros (URL-sync)
const sellerId = ref<number | undefined>(route.query.seller_id ? Number(route.query.seller_id) : undefined)
const dateFrom = ref<string | undefined>(typeof route.query.date_from === 'string' ? route.query.date_from : undefined)
const dateTo   = ref<string | undefined>(typeof route.query.date_to === 'string' ? route.query.date_to : undefined)

// paginação
const page = ref<number>(Number(route.query.page ?? 1) || 1)
const perPage = ref<number>(Number(route.query.per_page ?? 20) || 20)
type Meta = { current_page: number; per_page: number; total: number; last_page?: number }
const meta = ref<Meta | null>(null)

const total = computed(() => meta.value?.total ?? 0)
const currentPage = computed(() => meta.value?.current_page ?? page.value)
const currentPerPage = computed(() => meta.value?.per_page ?? perPage.value)
const lastPage = computed(() => meta.value?.last_page ?? Math.max(1, Math.ceil(total.value / currentPerPage.value)))
const startRow = computed(() => (currentPage.value - 1) * currentPerPage.value + 1)
const endRow   = computed(() => Math.min(currentPage.value * currentPerPage.value, total.value))

// sellers para filtro
const sellerOptions = ref<Seller[]>([])
async function loadSellerOptions() {
  try {
    sellerOptions.value = (await listSellers({ perPage: 100 })).data
  } catch { /* silencioso */ }
}

function pushQuery() {
  router.replace({
    query: {
      ...route.query,
      page: String(page.value),
      per_page: String(perPage.value),
      ...(sellerId.value ? { seller_id: String(sellerId.value) } : { seller_id: undefined }),
      ...(dateFrom.value ? { date_from: dateFrom.value } : { date_from: undefined }),
      ...(dateTo.value ? { date_to: dateTo.value } : { date_to: undefined }),
    },
  })
}

async function fetchRows() {
  loading.value = true
  try {
    const res = await listSales({
      page: page.value,
      perPage: perPage.value,
      sellerId: sellerId.value,
      dateFrom: dateFrom.value,
      dateTo: dateTo.value,
    })
    rows.value = res.data
    meta.value = res.meta ?? { current_page: page.value, per_page: perPage.value, total: res.data.length }
  } catch (e: unknown) {
    notify.error(e instanceof Error ? e.message : 'Falha ao carregar vendas.')
  } finally {
    loading.value = false
  }
}

function resetFilters() {
  sellerId.value = undefined
  dateFrom.value = undefined
  dateTo.value = undefined
  page.value = 1
  pushQuery()
  fetchRows()
}

watch(() => route.query, (q) => {
  const qp = Number(q.page ?? 1) || 1
  const qpp = Number(q.per_page ?? 20) || 20
  const sid = q.seller_id ? Number(q.seller_id) : undefined
  const df  = typeof q.date_from === 'string' ? q.date_from : undefined
  const dt  = typeof q.date_to === 'string' ? q.date_to : undefined

  let changed = false
  if (qp !== page.value)      { page.value = qp; changed = true }
  if (qpp !== perPage.value)  { perPage.value = qpp; changed = true }
  if (sid !== sellerId.value) { sellerId.value = sid; changed = true }
  if (df !== dateFrom.value)  { dateFrom.value = df; changed = true }
  if (dt !== dateTo.value)    { dateTo.value = dt; changed = true }

  if (changed) fetchRows()
})

watch([page, perPage], () => { pushQuery(); fetchRows() })

// criar/editar
const showCreate = ref(false)
const showEdit = ref(false)
const editingId = ref<number | null>(null)
const editingData = ref<Partial<Sale> | null>(null)

function openCreate() {
  showCreate.value = true
}
function closeCreate() {
  showCreate.value = false
}

async function openEdit(row: Sale) {
  editingId.value = row.id
  // opcional: carregar versão fresh da API
  try {
    editingData.value = await getSale(row.id)
  } catch {
    editingData.value = row
  }
  showEdit.value = true
}
function closeEdit() {
  showEdit.value = false
  editingId.value = null
  editingData.value = null
}

async function handleCreate(payload: { seller_id: number; amount: number; sold_at: string }) {
  try {
    await createSale(payload)
    notify.success('Venda criada com sucesso.')
    closeCreate()
    fetchRows()
  } catch (e: any) {
    const msg = e?.validation ? Object.values(e.validation)[0]?.[0] : e?.message
    notify.error(msg || 'Erro ao criar venda.')
  }
}

async function handleEdit(payload: any) {
  if (!editingId.value) return
  try {
    await updateSale(editingId.value, payload)
    notify.success('Venda atualizada.')
    closeEdit()
    fetchRows()
  } catch (e: any) {
    const msg = e?.validation ? Object.values(e.validation)[0]?.[0] : e?.message
    notify.error(msg || 'Erro ao atualizar venda.')
  }
}

// (opcional) deletar uma venda, se sua API suportar
async function confirmDelete(row: Sale) {
  const ok = window.confirm(`Excluir venda #${row.id}?`)
  if (!ok) return
  try {
    await deleteSale(row.id)
    notify.success('Venda excluída.')
    fetchRows()
  } catch (e: any) {
    notify.error(e?.message || 'Erro ao excluir venda.')
  }
}

onMounted(() => {
  loadSellerOptions()
  fetchRows()
})
</script>

<template>
  <div class="flex flex-wrap mt-4">
    <div class="w-full mb-12 px-4">
      <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white">

        <!-- Cabeçalho + Filtros -->
        <div class="rounded-t px-4 py-3 border-b">
          <div class="flex flex-wrap items-end gap-3">
            <div class="flex-1">
              <h3 class="font-semibold text-lg text-blueGray-700">Vendas</h3>
            </div>
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-3 py-2 rounded" @click="openCreate">
              + Nova Venda
            </button>
          </div>

          <div class="mt-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
            <label class="block">
              <span class="text-xs text-blueGray-500">Vendedor</span>
              <select
                class="mt-1 block w-full border rounded px-3 py-2"
                :value="sellerId"
                @change="sellerId = Number(($event.target as HTMLSelectElement).value) || undefined; page = 1; pushQuery(); fetchRows()"
              >
                <option :value="undefined">Todos</option>
                <option v-for="s in sellerOptions" :key="s.id" :value="s.id">#{{ s.id }} — {{ s.name }}</option>
              </select>
            </label>

            <label class="block">
              <span class="text-xs text-blueGray-500">De</span>
              <input type="date" class="mt-1 block w-full border rounded px-3 py-2"
                :value="dateFrom"
                @change="dateFrom = ($event.target as HTMLInputElement).value || undefined; page = 1; pushQuery(); fetchRows()"
              />
            </label>

            <label class="block">
              <span class="text-xs text-blueGray-500">Até</span>
              <input type="date" class="mt-1 block w-full border rounded px-3 py-2"
                :value="dateTo"
                @change="dateTo = ($event.target as HTMLInputElement).value || undefined; page = 1; pushQuery(); fetchRows()"
              />
            </label>

            <div class="flex items-end gap-2">
              <button class="px-3 py-2 border rounded text-sm" @click="resetFilters">Limpar</button>
              <button class="px-3 py-2 border rounded text-sm" :disabled="loading" @click="fetchRows">
                {{ loading ? 'Atualizando…' : 'Atualizar' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Tabela -->
        <div class="block w-full overflow-x-auto">
          <table class="items-center w-full bg-transparent border-collapse">
            <thead>
              <tr>
                <th class="px-6 py-3 text-xs uppercase whitespace-nowrap text-left bg-blueGray-50">ID</th>
                <th class="px-6 py-3 text-xs uppercase whitespace-nowrap text-left bg-blueGray-50">Vendedor</th>
                <th class="px-6 py-3 text-xs uppercase whitespace-nowrap text-left bg-blueGray-50">Data</th>
                <th class="px-6 py-3 text-xs uppercase whitespace-nowrap text-left bg-blueGray-50">Valor</th>
                <th class="px-6 py-3 text-xs uppercase whitespace-nowrap text-left bg-blueGray-50">Comissão</th>
                <th class="px-6 py-3 text-xs uppercase whitespace-nowrap text-left bg-blueGray-50">Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="r in rows" :key="r.id">
                <td class="px-6 py-3 text-xs whitespace-nowrap">{{ r.id }}</td>
                <td class="px-6 py-3 text-xs whitespace-nowrap">
                  <span v-if="r.seller">#{{ r.seller.id }} — {{ r.seller.name }}</span>
                  <span v-else>#{{ r.seller_id }}</span>
                </td>
                <td class="px-6 py-3 text-xs whitespace-nowrap">{{ r.sold_at }}</td>
                <td class="px-6 py-3 text-xs whitespace-nowrap">R$ {{ r.amount.toFixed(2) }}</td>
                <td class="px-6 py-3 text-xs whitespace-nowrap">R$ {{ r.commission.toFixed(2) }}</td>
                <td class="px-6 py-3 text-xs whitespace-nowrap">
                  <div class="flex gap-2">
                    <button class="bg-amber-500 hover:bg-amber-600 text-white text-xs px-3 py-1 rounded" @click="openEdit(r)">Editar</button>
                    <!-- Se a API suportar DELETE -->
                    <button class="bg-rose-600 hover:bg-rose-700 text-white text-xs px-3 py-1 rounded" @click="confirmDelete(r)">Excluir</button>
                  </div>
                </td>
              </tr>
              <tr v-if="!loading && rows.length === 0">
                <td colspan="6" class="p-6 text-sm text-blueGray-500">Nenhuma venda encontrada.</td>
              </tr>
            </tbody>
          </table>
          <div v-if="loading" class="p-6 text-sm text-blueGray-500">Carregando…</div>
        </div>

        <!-- Paginação -->
        <div class="flex items-center justify-between px-6 py-4 border-t bg-white">
          <div class="text-xs text-blueGray-500">
            <template v-if="total > 0">
              Mostrando <b>{{ startRow }}</b>–<b>{{ endRow }}</b> de <b>{{ total }}</b>
            </template>
            <template v-else>
              Nenhum registro
            </template>
          </div>
          <div class="flex items-center gap-2">
            <label class="text-xs text-blueGray-500">Por página</label>
            <select class="border rounded px-2 py-1 text-sm" :value="perPage"
              @change="perPage = Number(($event.target as HTMLSelectElement).value); page = 1; pushQuery(); fetchRows()">
              <option :value="10">10</option>
              <option :value="20">20</option>
              <option :value="50">50</option>
            </select>

            <button class="px-3 py-1 border rounded text-sm disabled:opacity-50" :disabled="currentPage <= 1" @click="page = 1; pushQuery(); fetchRows()">«</button>
            <button class="px-3 py-1 border rounded text-sm disabled:opacity-50" :disabled="currentPage <= 1" @click="page = currentPage - 1; pushQuery(); fetchRows()">‹</button>
            <span class="px-2 text-sm">Página {{ currentPage }} / {{ lastPage }}</span>
            <button class="px-3 py-1 border rounded text-sm disabled:opacity-50" :disabled="currentPage >= lastPage" @click="page = currentPage + 1; pushQuery(); fetchRows()">›</button>
            <button class="px-3 py-1 border rounded text-sm disabled:opacity-50" :disabled="currentPage >= lastPage" @click="page = lastPage; pushQuery(); fetchRows()">»</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Nova venda -->
  <div v-if="showCreate" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" @click.self="closeCreate">
    <div class="bg-white rounded shadow-lg w-full max-w-md p-6">
      <h4 class="text-lg font-semibold mb-4">Nova venda</h4>
      <SaleForm mode="create" @submit="handleCreate" @cancel="closeCreate" />
    </div>
  </div>

  <!-- Modal: Editar venda -->
  <div v-if="showEdit && editingData" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" @click.self="closeEdit">
    <div class="bg-white rounded shadow-lg w-full max-w-md p-6">
      <h4 class="text-lg font-semibold mb-4">Editar venda #{{ editingId }}</h4>
      <SaleForm mode="edit" :initial="editingData" @submit="handleEdit" @cancel="closeEdit" />
    </div>
  </div>
</template>
