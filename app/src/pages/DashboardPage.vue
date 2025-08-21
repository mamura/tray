<template>
  <div>
    <div class="flex flex-wrap">
      <div class="w-full xl:w-8/12 mb-12 xl:mb-0 px-4">
        <CardLineChart />
      </div>
      <div class="w-full xl:w-4/12 px-4">
        <CardBarChart />
      </div>
    </div>

    <div class="flex flex-wrap mt-4">
      <div class="w-full xl:w-8/12 mb-12 xl:mb-0 px-4">
        <CardPageVisits />
      </div>
      <div class="w-full xl:w-4/12 px-4">
        <CardSocialTraffic />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import CardLineChart from '@/components/Cards/CardLineChart.vue'
  import CardBarChart from '@/components/Cards/CardBarChart.vue'
  import CardPageVisits from '@/components/Cards/CardPageVisits.vue'
  import CardSocialTraffic from '@/components/Cards/CardSocialTraffic.vue'
</script>
