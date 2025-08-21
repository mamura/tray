<template>
  <div class="relative">
    <!-- Botão do usuário -->
    <button
      class="text-gray-500 block cursor-pointer focus:outline-none"
      ref="btnDropdownRef"
      @click="toggleDropdown"
    >
      <div class="flex items-center">
        <span
          class="w-12 h-12 text-sm text-white bg-gray-200 inline-flex items-center justify-center rounded-full"
        >
          <img
            alt="User"
            class="w-full rounded-full align-middle border-none shadow-lg"
            :src="image"
          />
        </span>
      </div>
    </button>

    <!-- Dropdown -->
    <div
      ref="popoverDropdownRef"
      class="absolute mt-2 right-0 bg-white text-base z-50 py-2 list-none text-left rounded shadow-lg min-w-[12rem]"
      :class="{ hidden: !dropdownPopoverShow, block: dropdownPopoverShow }"
    >
      <a
        href="#"
        class="block px-4 py-2 text-sm font-normal text-gray-700 hover:bg-gray-100"
      >
        Action
      </a>
      <a
        href="#"
        class="block px-4 py-2 text-sm font-normal text-gray-700 hover:bg-gray-100"
      >
        Another action
      </a>
      <a
        href="#"
        class="block px-4 py-2 text-sm font-normal text-gray-700 hover:bg-gray-100"
      >
        Something else here
      </a>
      <div class="border-t my-2 border-gray-100" />
      <a
        href="#"
        class="block px-4 py-2 text-sm font-normal text-gray-700 hover:bg-gray-100"
      >
        Separated link
      </a>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { createPopper } from '@popperjs/core'
import image from '@/assets/images/user-woman.jpeg'

const btnDropdownRef      = ref<HTMLElement | null>(null)
const popoverDropdownRef  = ref<HTMLElement | null>(null)
const dropdownPopoverShow = ref(false)

function toggleDropdown(event: MouseEvent) {
  event.preventDefault()
  dropdownPopoverShow.value = !dropdownPopoverShow.value

  if (dropdownPopoverShow.value && btnDropdownRef.value && popoverDropdownRef.value) {
    createPopper(btnDropdownRef.value, popoverDropdownRef.value, {
      placement: 'bottom-start',
    })
  }
}
</script>

