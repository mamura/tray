<template>
  <div class="relative min-h-screen flex items-center justify-center">
    <div
      class="absolute top-0 left-0 w-full h-full bg-gray-900 bg-no-repeat bg-cover z-0"
      :style="`background-image: url('${registerBg}');`"
    ></div>

    <div class="absolute top-0 left-0 w-full h-full bg-black opacity-60 z-0"></div>

    <router-view class="relative z-10 w-full" />
  </div>
</template>

<script lang="ts" setup>
import registerBg from "@/assets/images/register_bg.png";
</script>
