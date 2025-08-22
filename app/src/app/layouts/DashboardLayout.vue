<template>
  <div>
    <AdminSidebar />

    <div class="relative md:ml-64 bg-slate-100 min-h-screen">
      <AdminNavbar />
      <HeaderStats />

      <div class="px-4 md:px-10 mx-auto w-full -mt-24">
        <router-view />
        <FooterAdmin />
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
  import AdminSidebar from '@/components/Sidebar/AdminSidebar.vue'
  import AdminNavbar from '@/components/Navbars/AdminNavbar.vue'
  import HeaderStats from '@/components/Headers/HeaderStats.vue'
  import FooterAdmin from '@/components/Footers/FooterAdmin.vue';
</script>
