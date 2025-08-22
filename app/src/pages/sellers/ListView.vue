<script setup lang="ts">
import type { Seller } from '@/entities/seller';
import { createSeller, listSellers, resendCommission, updateSeller } from '@/features/sellers/api/sellersApi';
import { useNotify } from '@/shared/ui/useNotify';
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

  const error   = ref<string | null>(null);
  const success = ref<string | null>(null);
  const notify  = useNotify();
  const route   = useRoute();
  const router  = useRouter();

  // table state
  const sellers = ref<Seller[]>([]);
  const loading = ref(false);

  // pagination
  const page    = ref<number>(Number(route.query.page ?? 1) || 1);
  const perPage = ref<number>(Number(route.query.per_page ?? 20) || 20);

  type Meta = { current_page: number; per_page: number; total: number; last_page?: number }
  const meta = ref<Meta | null>(null)

  const total           = computed(() => meta.value?.total ?? 0)
  const currentPage     = computed(() => meta.value?.current_page ?? page.value)
  const currentPerPage  = computed(() => meta.value?.per_page ?? perPage.value)
  const lastPage        = computed(() => meta.value?.last_page ?? Math.max(1, Math.ceil(total.value / currentPerPage.value)))

  const startRow = computed(() => (currentPage.value - 1) * currentPerPage.value + 1)
  const endRow   = computed(() => Math.min(currentPage.value * currentPerPage.value, total.value))


  // Novo modal state
  const isCreateOpen  = ref(false)
  const newName       = ref('')
  const newEmail      = ref('')
  const savingCreate  = ref(false)

  // Edit modal state
  const isEditOpen  = ref(false);
  const editing     = ref<{ id: number; name: string; email: string } | null>(null);
  const savingEdit  = ref(false);

  // Resend popover/modal state
  const isResendOpen  = ref(false)
  const resendSeller  = ref<Seller | null>(null)
  const resendDate    = ref<string>(new Date().toISOString().slice(0, 10))
  const sendingResend = ref(false)

// helpers de p/ evitar `any`
  type ValidationBag = Record<string, string[]>

  function hasValidation(e: unknown): e is { validation: ValidationBag } {
    return typeof e === 'object' && e !== null && 'validation' in e
  }

  function firstValidationError(bag: ValidationBag | undefined): string | null {
    if (!bag) return null
    const first = Object.values(bag)[0]
    return first?.[0] ?? null
  }

  async function fetchSellers() {
    loading.value = true;
    try {
      const res = await listSellers({
        page: page.value,
        perPage: perPage.value
      });
      sellers.value = res.data;
      meta.value    = res.meta ?? {
        current_page: page.value,
        per_page: perPage.value,
        total: res.data.length
      };
    } catch(e: unknown) {
      notify.error(e instanceof Error ? e.message : 'Falha ao carregar vendedores.');
    } finally {
      loading.value = false;
    }
  }

  function pushQuery(nextPage = page.value, nextPer = perPage.value) {
    router.replace({
      query: {
        ...route.query,
        page: String(nextPage),
        per_page: String(nextPer),
      },
    })
  };

  function goFirst() { if (currentPage.value > 1) { page.value = 1; pushQuery(1) } }
  function goPrev()  { if (currentPage.value > 1) { page.value = currentPage.value - 1; pushQuery(page.value) } }
  function goNext()  { if (currentPage.value < lastPage.value) { page.value = currentPage.value + 1; pushQuery(page.value) } }
  function goLast()  { if (currentPage.value < lastPage.value) { page.value = lastPage.value; pushQuery(page.value) } }


  function openCreate() {
    isCreateOpen.value  = true;
    newName.value       = '';
    newEmail.value      = '';
    error.value         = null;
    success.value       = null;
  }

  async function saveCreate() {
    savingCreate.value  = true
    error.value         = null
    success.value       = null
    try {
      await createSeller(
        {
          name: newName.value.trim(),
          email: newEmail.value.trim()
        }
      );

      notify.success('Vendedor criado com sucesso.');
      closeCreate();
      await fetchSellers();
    } catch (e: unknown) {
      if (hasValidation(e)) {
        notify.error(firstValidationError(e.validation) ?? 'Erro de validação.');
      } else {
        notify.error(e instanceof Error ? e.message : 'Falha ao criar vendedor.');
      }
    } finally {
      savingCreate.value = false
    }
  }

  function closeCreate() {
    isCreateOpen.value = false;
  }



  function openEdit(seller: Seller) {
    editing.value     = { id: seller.id, name: seller.name, email: seller.email };
    isEditOpen.value  = true;
    success.value     = null;
    error.value       = null;
  }

  function closeEdit() {
    isEditOpen.value  = false;
    editing.value     = null;
  }

  async function saveEdit() {
    if (!editing.value) {
      return
    }

    savingEdit.value  = true;
    error.value       = null;
    success.value     = null;

    try {
      await updateSeller(editing.value.id, {
        name: editing.value.name,
        email: editing.value.email,
      });

      notify.success('Vendedor atualizado com sucesso.');
      await fetchSellers();
      closeEdit();
    } catch (e: unknown) {
      if (hasValidation(e)){
        notify.error(firstValidationError(e.validation) ?? 'Erro de validação.');
      } else {
        notify.error(e instanceof Error ? e.message : 'Falha ao atualizar.');
      }
    } finally {
      savingEdit.value = false;
    }
  }

  function openResend(seller: Seller) {
    resendSeller.value = seller
    resendDate.value = new Date().toISOString().slice(0, 10)
    isResendOpen.value = true
    success.value = null
    error.value = null
  }

  function closeResend() {
    isResendOpen.value = false
    resendSeller.value = null
  }

  async function doResend() {
    if (!resendSeller.value) {
       return;
    }

    sendingResend.value = true;
    error.value         = null;
    success.value       = null;
    try {
      await resendCommission(resendSeller.value.id, resendDate.value);
      notify.success(`Reenvio solicitado para ${resendSeller.value.email} (data ${resendDate.value}).`);
      closeResend()
    } catch (e: unknown) {
      if (hasValidation(e)){
        notify.error(firstValidationError(e.validation) ?? 'Erro de validação.');
      } else {
        notify.error(e instanceof Error ? e.message : 'Falha ao reenviar e-mail.');
      }
    } finally {
      sendingResend.value = false
    }
  }

  watch(() => route.query, (q) => {
    // se a URL mudar (ex.: back/forward), sincroniza estado e refetch
    const qp = Number(q.page ?? page.value) || 1
    const qpp = Number(q.per_page ?? perPage.value) || currentPerPage.value
    let changed = false
    if (qp !== page.value) { page.value = qp; changed = true }
    if (qpp !== perPage.value) { perPage.value = qpp; changed = true }
    if (changed) fetchSellers()
  });

  watch([page, perPage], ([p, pp], [op, opp]) => {
    if (p !== op || pp !== opp) {
      pushQuery(p, pp)
      fetchSellers()
    }
  });

  onMounted(fetchSellers)
</script>

<template>
  <div class="flex flex-wrap mt-4">
    <div class="w-full mb-12 px-4">
      <div
        class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white"
      >
        <div class="rounded-t mb-0 px-4 py-3 border-0">
          <div class="flex flex-wrap items-center">
            <div class="relative w-full px-4 max-w-full flex justify-between">
              <div class="flex gap-2">
                <h3
                  class="font-semibold text-lg text-slate-700">
                  Sellers
                </h3>

                <div>
                  <select
                    class="border-0 rounded px-2 py-1 text-sm"
                    :value="perPage"
                    @change="perPage = Number(($event.target as HTMLSelectElement).value)"
                  >
                    <option :value="10">10</option>
                    <option :value="20">20</option>
                    <option :value="50">50</option>
                  </select>

                  por página
                </div>
              </div>


              <button
                class="cursor-pointer disabled:cursor-default bg-primary text-white active:bg-emerald-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150"
                type="button"
                @click="openCreate"
              >
                <i class="fas fa-user-plus"></i> Novo
              </button>
            </div>
          </div>
        </div>

        <div class="block w-full overflow-x-auto">
          <table class="items-center w-full bg-transparent border-collapse">
            <thead>
              <tr>
                <th
                  class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-slate-50 text-slate-500 border-slate-100"
                >
                  ID
                </th>
                <th
                  class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-slate-50 text-slate-500 border-slate-100"
                >
                  Nome
                </th>
                <th
                  class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-slate-50 text-slate-500 border-slate-100"
                >
                  E-mail
                </th>
                <th
                  class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-slate-50 text-slate-500 border-slate-100"
                >
                  Ações
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="seller in sellers" :key="seller.id">
                <th
                  class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left flex items-center"
                >
                {{ seller.id }}
                </th>
                <td
                  class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                >
                  {{ seller.name }}
                </td>
                <td
                  class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                >
                 {{ seller.email }}
                </td>
                <td
                  class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                >
                  <div class="flex gap-2">
                    <button
                      class="cursor-pointer disabled:cursor-default bg-cyan-700 hover:bg-sky-900 text-white text-xs px-3 py-1 rounded"
                      @click="openEdit(seller)"
                    >
                      <i class="fas fa-pen-to-square"></i> Editar
                    </button>

                    <button
                      class="cursor-pointer disabled:cursor-default bg-slate-600 hover:bg-slate-700 text-white text-xs px-3 py-1 rounded"
                      @click="openResend(seller)"
                    >
                      <i class="fas fa-envelope-open-text"></i> Relatório
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-if="loading">Carregando...</div>
        </div>

        <div class="flex items-center justify-between px-6 py-4 border-t bg-white">
          <div class="text-xs text-primary">
            <template v-if="total > 0">
              Mostrando <b>{{ startRow }}</b>–<b>{{ endRow }}</b> de <b>{{ total }}</b>
            </template>
            <template v-else>
              Nenhum registro
            </template>
          </div>
          <div class="flex items-center gap-1 text-white">
            <button class="cursor-pointer disabled:cursor-default px-3 py-1 border rounded bg-primary text-sm disabled:opacity-50" :disabled="currentPage <= 1" @click="goFirst"><i class="fas fa-angles-left"></i></button>
            <button class="cursor-pointer disabled:cursor-default px-3 py-1 border rounded bg-primary text-sm disabled:opacity-50" :disabled="currentPage <= 1" @click="goPrev"><i class="fas fa-angle-left"></i></button>
            <span class="px-2 text-sm text-primary">Página {{ currentPage }} / {{ lastPage }}</span>
            <button class="cursor-pointer disabled:cursor-default px-3 py-1 border rounded bg-primary text-sm disabled:opacity-50" :disabled="currentPage >= lastPage" @click="goNext"><i class="fas fa-angle-right"></i></button>
            <button class="cursor-pointer disabled:cursor-default px-3 py-1 border rounded bg-primary text-sm disabled:opacity-50" :disabled="currentPage >= lastPage" @click="goLast"><i class="fas fa-angles-right"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Novo -->
  <div
    v-if="isCreateOpen"
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    @click.self="closeCreate"
  >
    <div class="relative flex flex-col break-words w-full bg-slate-100 rounded shadow-lg w-full max-w-md p-6">
      <div class="rounded-t bg-white mb-0 px-6 py-6">
        <div class="text-center flex justify-between">
          <h6 class="text-slate-700 text-xl font-bold">Novo vendedor</h6>
        </div>
      </div>

      <div class="flex-auto px-4 lg:px-10 py-10 pt-0">
        <h6 class="text-slate-400 text-sm mt-3 mb-6 font-bold uppercase">
          Informações do vendedor
        </h6>

        <div class="flex flex-col">
          <div class="w-full px-4">
            <div class="relative w-full mb-3">
              <label
                class="block uppercase text-slate-600 text-xs font-bold mb-2"
                htmlFor="grid-password"
              >
                Nome
              </label>

              <input
                type="text"
                class="border-0 px-3 py-3 placeholder-slate-300 text-slate-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                v-model.trim="newName"
              />
            </div>
          </div>

          <div class="w-full px-4">
            <div class="relative w-full mb-3">
              <label
                class="block uppercase text-slate-600 text-xs font-bold mb-2"
                htmlFor="grid-password"
              >
                E-mail
              </label>
              <input
                type="email"
                class="border-0 px-3 py-3 placeholder-slate-200 text-slate-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                v-model.trim="newEmail"
                placeholder="email@exemplo.com"
              />
            </div>
          </div>
        </div>
      </div>

      <div class="mt-6 flex justify-end gap-2">
        <button class="cursor-pointer disabled:cursor-default px-3 py-2 rounded border" @click="closeCreate">Cancelar</button>

        <button
          class="cursor-pointer disabled:cursor-default px-3 py-2 rounded bg-slate-600 text-white"
          :disabled="savingCreate || !newName || !newEmail"
          @click="saveCreate"
        >
          {{ savingCreate ? 'Salvando…' : 'Salvar' }}
        </button>
      </div>
    </div>
  </div>

  <!-- Modal Edit -->
  <div
    v-if="isEditOpen && editing"
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    @click.self="closeEdit"
  >
    <div class="relative flex flex-col break-words w-full bg-slate-100 rounded shadow-lg w-full max-w-md p-6">
      <div class="rounded-t bg-white mb-0 px-6 py-6">
        <div class="text-center flex justify-between">
          <h6 class="text-slate-700 text-xl font-bold">Editar vendedor #{{ editing.id }}</h6>
        </div>
      </div>

      <div class="flex-auto px-4 lg:px-10 py-10 pt-0">
        <h6 class="text-slate-400 text-sm mt-3 mb-6 font-bold uppercase">
          Informações do vendedor
        </h6>

        <div class="flex flex-col">
          <div class="w-full px-4">
            <div class="relative w-full mb-3">
              <label
                class="block uppercase text-slate-600 text-xs font-bold mb-2"
                htmlFor="grid-password"
              >
                Nome
              </label>

              <input
                type="text"
                class="border-0 px-3 py-3 placeholder-slate-300 text-slate-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                v-model="editing.name"
              />
            </div>
          </div>

          <div class="w-full px-4">
            <div class="relative w-full mb-3">
              <label
                class="block uppercase text-slate-600 text-xs font-bold mb-2"
                htmlFor="grid-password"
              >
                E-mail
              </label>
              <input
                type="email"
                class="border-0 px-3 py-3 placeholder-slate-200 text-slate-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                v-model="editing.email"
                placeholder="email@exemplo.com"
              />
            </div>
          </div>
        </div>
      </div>

      <div class="mt-6 flex justify-end gap-2">
        <button class="cursor-pointer disabled:cursor-default px-3 py-2 rounded border" @click="closeEdit">Cancelar</button>

        <button
          class="cursor-pointer disabled:cursor-default px-3 py-2 rounded bg-slate-600 text-white"
          :disabled="savingEdit"
          @click="saveEdit"
        >
          {{ savingEdit ? 'Salvando…' : 'Salvar' }}
        </button>
      </div>
    </div>
  </div>

  <!-- Resend Modal -->
   <div
    v-if="isResendOpen && resendSeller"
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    @click.self="closeResend"
  >
    <div class="bg-white rounded shadow-lg w-full max-w-md p-6">
      <h4 class="text-lg font-semibold mb-4">
        Reenviar e-mail para <span class="font-bold">{{ resendSeller.name }}</span>
      </h4>

      <label class="block">
        <span class="text-sm">Data do relatório</span>
        <input
          v-model="resendDate"
          type="date"
          class="mt-1 block w-full border rounded px-3 py-2"
        />
      </label>

      <div class="mt-6 flex justify-end gap-2">
        <button class="cursor-pointer disabled:cursor-default px-3 py-2 rounded border" @click="closeResend">Cancelar</button>

        <button
          class="cursor-pointer disabled:cursor-default px-3 py-2 rounded bg-slate-600 text-white"
          :disabled="sendingResend"
          @click="doResend"
        >
          {{ sendingResend ? 'Enviando…' : 'Reenviar' }}
        </button>
      </div>
    </div>
  </div>
</template>
