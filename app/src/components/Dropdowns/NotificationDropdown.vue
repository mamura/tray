<template>
  <div>
    <a
      class="text-blueGray-500 block py-1 px-3"
      ref="btnDropdownRef"
      v-on:click="toggleDropdown($event)"
    >
      <i class="fas fa-bell"></i>
    </a>
    <div
      ref="popoverDropdownRef"
      class="bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48"
      v-bind:class="{
        hidden: !dropdownPopoverShow,
        block: dropdownPopoverShow,
      }"
    >
      <a
        href="javascript:void(0);"
        class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700"
      >
        Action
      </a>
      <a
        href="javascript:void(0);"
        class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700"
      >
        Another action
      </a>
      <a
        href="javascript:void(0);"
        class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700"
      >
        Something else here
      </a>
      <div class="h-0 my-2 border border-solid border-blueGray-100" />
      <a
        href="javascript:void(0);"
        class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700"
      >
        Seprated link
      </a>
    </div>
  </div>
</template>
<script lang="ts">
import { createPopper } from "@popperjs/core";
import { ref } from "vue";

export default {
  setup() {
    const dropdownPopoverShow = ref(false);
    const btnDropdownRef      = ref<HTMLElement | null>(null);
    const popoverDropdownRef  = ref<HTMLElement | null>(null);

    const toggleDropdown = (event: MouseEvent) => {
      event.preventDefault();
      dropdownPopoverShow.value = !dropdownPopoverShow.value;

      if (
        dropdownPopoverShow.value &&
        btnDropdownRef.value &&
        popoverDropdownRef.value
      ) {
        createPopper(
          btnDropdownRef.value as Element,
          popoverDropdownRef.value as HTMLElement,
          {
            placement: "bottom-start",
          }
        );
      }
    };

    return {
      dropdownPopoverShow,
      btnDropdownRef,
      popoverDropdownRef,
      toggleDropdown,
    };
  }
};
</script>
