<template>
  <!-- Navbar -->
  <nav class="absolute top-0 left-0 w-full z-10 bg-transparent flex items-center p-4">
    <div class="w-full mx-auto flex items-center justify-between md:flex-nowrap flex-wrap md:px-10 px-4">
      <!-- Brand -->
      <a class="text-white text-sm uppercase hidden lg:inline-block font-semibold" href="javascript:void(0)">

      </a>

      <!-- User -->
      <ul class="flex-col md:flex-row list-none items-center hidden md:flex">
        <UserDropdown />
      </ul>
    </div>
  </nav>
</template>

<script lang="ts" setup>
  import UserDropdown from "@/components/Dropdowns/UserDropdown.vue";
</script>
