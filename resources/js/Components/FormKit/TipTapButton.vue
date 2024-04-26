<template>
  <button
    class="relative inline-flex items-center px-3 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-100 focus:z-10"
    :class="{ 'bg-gray-300': isActive }"
    type="button"
  >
    <slot>
      <iconify-icon v-if="icon" :icon="icon"></iconify-icon>
    </slot>
  </button>
</template>

<script setup>
import { defineProps } from "vue";
defineProps({
  isActive: Boolean,
  icon: {
    type: String,
    default: "",
  },
});
</script>
