<script setup lang="ts">
import type { Seller } from '@/entities/seller';
import { listSellers, resendCommission, updateSeller } from '@/features/sellers/api/sellersApi';
import { onMounted, ref } from 'vue';

  const sellers = ref<Seller[]>([]);
  const loading = ref(false);
  const error   = ref<string | null>(null);
  const success = ref<string | null>(null);

  // Edit modal state
  const isEditOpen  = ref(false);
  const editing     = ref<{ id: number; name: string; email: string } | null>(null);
  const savingEdit  = ref(false);

  // Resend popover/modal state
const isResendOpen  = ref(false)
const resendSeller  = ref<Seller | null>(null)
const resendDate    = ref<string>(new Date().toISOString().slice(0, 10))
const sendingResend = ref(false)

  async function fetchSellers() {
    loading.value = true;
    try {
      sellers.value = (await listSellers({ perPage: 20 })).data
    } finally {
      loading.value = false;
    }
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

      success.value = 'Vendedor atualizado com sucesso.'
      await fetchSellers();
      closeEdit();
    } catch (e: any) {
      if (e.validation) {
        const first = Object.values(e.validation)[0]?.[0];
        error.value = first || 'Erro de validação.'
      } else {
        error.value = e?.message ?? 'Falha ao salvar.'
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

    sendingResend.value = true
    error.value         = null
    success.value       = null
    try {

      await resendCommission(resendSeller.value.id, resendDate.value)
      success.value = `Reenvio solicitado para ${resendSeller.value.email} (data ${resendDate.value}).`
      closeResend()
    } catch (e: any) {
      if (e.validation) {
        const first = Object.values(e.validation)[0]?.[0]
        error.value = first || 'Erro de validação.'
      } else {
        error.value = e?.message ?? 'Falha ao reenviar e-mail.'
      }
    } finally {
      sendingResend.value = false
    }
  }

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
            <div class="relative w-full px-4 max-w-full flex-grow flex-1">
              <h3
                class="font-semibold text-lg text-blueGray-700">
                Sellers
              </h3>
            </div>
          </div>
        </div>

        <div class="block w-full overflow-x-auto">
          <table class="items-center w-full bg-transparent border-collapse">
            <thead>
              <tr>
                <th
                  class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-blueGray-50 text-blueGray-500 border-blueGray-100"
                >
                  ID
                </th>
                <th
                  class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-blueGray-50 text-blueGray-500 border-blueGray-100"
                >
                  Nome
                </th>
                <th
                  class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-blueGray-50 text-blueGray-500 border-blueGray-100"
                >
                  E-mail
                </th>
                <th
                  class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-blueGray-50 text-blueGray-500 border-blueGray-100"
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
                      class="bg-amber-500 hover:bg-amber-600 text-white text-xs px-3 py-1 rounded"
                      @click="openEdit(seller)"
                    >
                      Editar
                    </button>

                    <button
                      class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-3 py-1 rounded"
                      @click="openResend(seller)"
                    >
                      Reenviar E-mail
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Edit -->
 <div
    v-if="isEditOpen && editing"
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    @click.self="closeEdit"
  >
    <div class="bg-white rounded shadow-lg w-full max-w-md p-6">
      <h4 class="text-lg font-semibold mb-4">Editar vendedor #{{ editing.id }}</h4>

      <div class="space-y-3">
        <label class="block">
          <span class="text-sm">Nome</span>
          <input
            type="text"
            v-model="editing.name"
            class="mt-1 block w-full border rounded px-3 py-2"
          />
        </label>

        <label class="block">
          <span class="text-sm">E-mail</span>
          <input
            v-model="editing.email"
            type="email"
            class="mt-1 block w-full border rounded px-3 py-2"
            placeholder="email@exemplo.com"
          />
        </label>
      </div>

      <div class="mt-6 flex justify-end gap-2">
        <button class="px-3 py-2 rounded border" @click="closeEdit">Cancelar</button>

        <button
          class="px-3 py-2 rounded bg-blue-600 text-white"
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
        <button class="px-3 py-2 rounded border" @click="closeResend">Cancelar</button>

        <button
          class="px-3 py-2 rounded bg-indigo-600 text-white"
          :disabled="sendingResend"
          @click="doResend"
        >
          {{ sendingResend ? 'Enviando…' : 'Reenviar' }}
        </button>
      </div>
    </div>
  </div>
</template>
