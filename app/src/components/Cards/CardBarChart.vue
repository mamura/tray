<template>
  <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
    <div class="rounded-t mb-0 px-4 py-3 bg-transparent">
      <div class="flex flex-wrap items-center">
        <div class="relative w-full max-w-full flex-grow flex-1">
          <h6 class="uppercase text-slate-400 mb-1 text-xs font-semibold">Performance</h6>
          <h2 class="text-slate-700 text-xl font-semibold">Total orders</h2>
        </div>
      </div>
    </div>
    <div class="p-4 flex-auto">
      <div class="relative h-[350px]">
        <ApexChart
          type="bar"
          height="100%"
          :options="chartOptions"
          :series="series"
        />
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
  import ApexChart from 'vue3-apexcharts'
  import { ref } from 'vue';

  const series = ref([
    {
      name: `${new Date().getFullYear()}`,
      data: [30, 78, 56, 34, 100, 45, 13],
    },
    {
      name: `${new Date().getFullYear() - 1}`,
      data: [27, 68, 86, 74, 10, 4, 87],
    },
  ]);

  const chartOptions = ref({
    chart: {
      type: 'bar',
      toolbar: { show: false },
    },
    colors: ['#ed64a6', '#4c51bf'],
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '40%',
        endingShape: 'rounded',
      },
    },
    dataLabels: {
      enabled: false,
    },
    xaxis: {
      categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
      labels: {
        style: {
          colors: '#94a3b8', // slate-400
        },
      },
    },
    yaxis: {
      labels: {
        style: {
          colors: '#94a3b8',
        },
      },
    },
    legend: {
      position: 'bottom',
      labels: {
        colors: '#64748b', // slate-500
      },
    },
    tooltip: {
      theme: 'light',
    },
  });
  </script>
