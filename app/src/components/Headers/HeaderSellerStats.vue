<script setup lang="ts">
import { ref } from 'vue'
import ApexChart from 'vue3-apexcharts'

const series = ref([
  {
    name: 'Chance OKeefe',
    data: [65, 78, 66, 44, 56, 67, 75],
  },
  {
    name: 'Okertz Mann',
    data: [40, 68, 86, 74, 56, 60, 87],
  },
  {
    name: 'Paolo Rowe',
    data: [85, 18, 36, 12, 27, 44, 89],
  },
  {
    name: 'Olin Goyette',
    data: [40, 68, 86, 74, 56, 60, 87],
  },
])

const chartOptions = ref({
  chart: {
    toolbar: { show: false },
    foreColor: '#3954a4',
  },
  stroke: {
    curve: 'smooth',
    width: 1,
  },
  xaxis: {
    categories: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Dom'],
  },
  legend: {
    position: 'bottom',
    labels: {
      colors: '##3954a4',
    },
  },
  colors: ['#c2a202', '#cccccc', '#89b079', '#b07990'],
})
</script>

<template>
  <!-- Header -->
  <div class="relative z-0 bg-primary md:pt-32 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full">
      <div>
        <!-- Card stats -->
        <div class=" flex flex-col min-w-0 break-words mb-6 xl:mb-0 ">
          <div class="w-full px-4">
            <div class="flex flex-wrap">
              <div class="bg-white rounded w-full text-primary p-6 shadow-lg">

                  <ApexChart
                    height="150"
                    type="line"
                    :options="chartOptions"
                    :series="series"
                  />

              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>


