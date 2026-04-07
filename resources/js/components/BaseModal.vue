<template>
  <div class="fixed inset-0 flex items-center justify-center">
    <div class="fixed inset-0 bg-black bg-opacity-50" @click="close"></div>
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full mx-4 relative z-50">
      <!-- Close Button -->
      <button
        @click="close"
        class="absolute top-4 right-4 text-gray-500 hover:text-gray-700"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>

      <!-- Modal Header -->
      <div v-if="title" class="border-b border-gray-200 px-6 py-4">
        <h2 class="text-xl font-bold text-gray-900">{{ title }}</h2>
      </div>

      <!-- Modal Content -->
      <div class="px-6 py-4">
        <slot />
      </div>

      <!-- Modal Footer -->
      <div v-if="$slots.footer" class="border-t border-gray-200 px-6 py-4 flex gap-4 justify-end">
        <slot name="footer" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { useModal } from 'vue-final-modal';

defineProps({
  title: {
    type: String,
    default: ''
  }
});

const { close } = useModal();
</script>
