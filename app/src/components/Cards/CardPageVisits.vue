<template>
  <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
    <div class="rounded-t mb-0 px-4 py-3 border-0">
      <div class="flex flex-wrap items-center">
        <div class="w-full px-4 max-w-full flex-grow flex-1">
          <h3 class="font-semibold text-base text-gray-700">Paginas mais visitadas</h3>
        </div>
        <div class="w-full px-4 max-w-full flex-grow flex-1 text-right">
          <button
            class="bg-primary text-white text-xs font-bold uppercase px-3 py-1 rounded transition duration-150 ease-in-out"
            type="button"
          >
            Ver tudo
          </button>
        </div>
      </div>
    </div>

    <div class="block w-full overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr>
            <th class="px-6 py-3 text-xs font-semibold text-gray-500 bg-gray-50 border-b">Página</th>
            <th class="px-6 py-3 text-xs font-semibold text-gray-500 bg-gray-50 border-b">Visitantes</th>
            <th class="px-6 py-3 text-xs font-semibold text-gray-500 bg-gray-50 border-b">Usuários únicos</th>
            <th class="px-6 py-3 text-xs font-semibold text-gray-500 bg-gray-50 border-b">Taxa de retorno</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(row, index) in rows"
            :key="index"
            class="border-t text-sm text-gray-700"
          >
            <td class="px-6 py-4 whitespace-nowrap">{{ row.page }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ row.visitors }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ row.users }}</td>
            <td class="px-6 py-4 whitespace-nowrap flex items-center">
              <i :class="[row.icon, row.color, 'mr-2']"></i>
              {{ row.bounce }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
const rows = [
  { page: '/argon/', visitors: '4,569', users: '340', bounce: '46.53%', icon: 'fas fa-arrow-up', color: 'text-emerald-500' },
  { page: '/argon/index.html', visitors: '3,985', users: '319', bounce: '46.53%', icon: 'fas fa-arrow-down', color: 'text-orange-500' },
  { page: '/argon/charts.html', visitors: '3,513', users: '294', bounce: '36.49%', icon: 'fas fa-arrow-down', color: 'text-orange-500' },
  { page: '/argon/tables.html', visitors: '2,050', users: '147', bounce: '50.87%', icon: 'fas fa-arrow-up', color: 'text-emerald-500' },
  { page: '/argon/profile.html', visitors: '1,795', users: '190', bounce: '46.53%', icon: 'fas fa-arrow-down', color: 'text-red-500' },
];
</script>
