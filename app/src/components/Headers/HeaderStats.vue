<template>
  <!-- Header -->
  <div class="relative z-0 bg-primary md:pt-32 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full">
      <div>
        <!-- Card stats -->
        <div class="flex flex-wrap">
          <div class="w-full lg:w-6/12 xl:w-3/12 px-4" v-for="card in cards" :key="card.statTitle">
            <CardStats
              :statSubtitle="card.statSubtitle"
              :statTitle="card.statTitle"
              :statArrow="card.statArrow"
              :statPercent="card.statPercent"
              :statPercentColor="card.statPercentColor"
              :statDescription="card.statDescription"
              :statIconName="card.statIconName"
              :statIconColor="card.statIconColor"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import CardStats from '@/components/Cards/CardStats.vue'

const cards = [
  {
    statSubtitle: "TRAFFIC",
    statTitle: "350,897",
    statArrow: "up",
    statPercent: "3.48",
    statPercentColor: "text-emerald-500",
    statDescription: "Since last month",
    statIconName: "far fa-chart-bar",
    statIconColor: "bg-red-500"
  },
  {
    statSubtitle: "NEW USERS",
    statTitle: "2,356",
    statArrow: "down",
    statPercent: "3.48",
    statPercentColor: "text-red-500",
    statDescription: "Since last week",
    statIconName: "fas fa-chart-pie",
    statIconColor: "bg-orange-500"
  },
  {
    statSubtitle: "SALES",
    statTitle: "924",
    statArrow: "down",
    statPercent: "1.10",
    statPercentColor: "text-orange-500",
    statDescription: "Since yesterday",
    statIconName: "fas fa-users",
    statIconColor: "bg-pink-500"
  },
  {
    statSubtitle: "PERFORMANCE",
    statTitle: "49,65%",
    statArrow: "up",
    statPercent: "12",
    statPercentColor: "text-emerald-500",
    statDescription: "Since last month",
    statIconName: "fas fa-percent",
    statIconColor: "bg-emerald-500"
  }
]
</script>
