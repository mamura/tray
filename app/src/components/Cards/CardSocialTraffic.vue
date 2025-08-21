<template>
  <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
    <div class="rounded-t mb-0 px-4 py-3 border-0">
      <div class="flex flex-wrap items-center">
        <div class="w-full px-4 max-w-full flex-grow flex-1">
          <h3 class="font-semibold text-base text-gray-700">Social traffic</h3>
        </div>
        <div class="w-full px-4 max-w-full flex-grow flex-1 text-right">
          <button
            class="bg-primary text-white text-xs font-bold uppercase px-3 py-1 rounded transition duration-150"
            type="button"
          >
            See all
          </button>
        </div>
      </div>
    </div>

    <div class="block w-full overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr>
            <th class="px-6 py-3 text-xs font-semibold text-gray-500 bg-gray-50 border-b">Referral</th>
            <th class="px-6 py-3 text-xs font-semibold text-gray-500 bg-gray-50 border-b">Visitors</th>
            <th class="px-6 py-3 text-xs font-semibold text-gray-500 bg-gray-50 border-b min-w-[140px]"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, index) in rows" :key="index" class="border-t text-sm text-gray-700">
            <td class="px-6 py-4 whitespace-nowrap">{{ row.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ row.visitors }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <span class="mr-2">{{ row.percent }}%</span>
                <div class="relative w-full">
                  <div class="overflow-hidden h-2 flex rounded" :class="row.bgTrack">
                    <div
                      class="shadow-none flex flex-col justify-center text-white text-center whitespace-nowrap"
                      :class="row.bgBar"
                      :style="{ width: row.percent + '%' }"
                    ></div>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
const rows = [
  {
    name: 'Facebook',
    visitors: '1,480',
    percent: 60,
    bgTrack: 'bg-red-200',
    bgBar: 'bg-red-500',
  },
  {
    name: 'Facebook',
    visitors: '5,480',
    percent: 70,
    bgTrack: 'bg-emerald-200',
    bgBar: 'bg-emerald-500',
  },
  {
    name: 'Google',
    visitors: '4,807',
    percent: 80,
    bgTrack: 'bg-purple-200',
    bgBar: 'bg-purple-500',
  },
  {
    name: 'Instagram',
    visitors: '3,678',
    percent: 75,
    bgTrack: 'bg-sky-200',
    bgBar: 'bg-sky-500',
  },
  {
    name: 'Twitter',
    visitors: '2,645',
    percent: 30,
    bgTrack: 'bg-orange-200',
    bgBar: 'bg-orange-500',
  },
];
</script>
