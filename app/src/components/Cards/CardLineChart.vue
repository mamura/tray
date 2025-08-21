<template>
  <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-[#2d3748] text-white">
    <div class="rounded-t mb-0 px-4 py-3 bg-transparent">
      <div class="flex flex-wrap items-center">
        <div class="relative w-full max-w-full flex-grow flex-1">
          <h6 class="uppercase text-blueGray-100 mb-1 text-xs font-semibold">
            Overview
          </h6>
          <h2 class="text-white text-xl font-semibold">Sales value</h2>
        </div>
      </div>
    </div>
    <div class="p-4 flex-auto">
      <ApexChart
        height="350"
        type="line"
        :options="chartOptions"
        :series="series"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import ApexChart from 'vue3-apexcharts'

const series = ref([
  {
    name: new Date().getFullYear().toString(),
    data: [65, 78, 66, 44, 56, 67, 75],
  },
  {
    name: (new Date().getFullYear() - 1).toString(),
    data: [40, 68, 86, 74, 56, 60, 87],
  },
])

const chartOptions = ref({
  chart: {
    toolbar: { show: false },
    foreColor: '#ffffff',
  },
  stroke: {
    curve: 'smooth',
  },
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
  },
  legend: {
    position: 'bottom',
    labels: {
      colors: '#ffffff',
    },
  },
  colors: ['#4c51bf', '#ffffff'],
})
</script>

<style scoped>
/* Estilo opcional para fundo personalizado */
</style>
