<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="bg-orange-500 shadow-md">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-2xl font-bold text-white">Aplikasi Absensi & Agenda PKL</h1>
          </div>
          <div class="flex items-center space-x-4">
            <div v-if="isAuthenticated" class="flex items-center space-x-4">
              <span class="text-white">{{ userFullName }}</span>
              <button @click="logout" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Logout
              </button>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto">
      <RouterView />
    </div>

    <!-- Modal Container -->
    <VueFinalModal class="flex justify-center items-center" />
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { RouterView, useRouter } from 'vue-router';
import { VueFinalModal } from 'vue-final-modal';
import { useAuthStore } from './stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const isAuthenticated = computed(() => authStore.isAuthenticated);
const userFullName = computed(() => authStore.user?.name || 'User');

const logout = async () => {
  await authStore.logout();
  await router.push('/login');
};

onMounted(() => {
  // Check if user is authenticated on mount
  authStore.initializeAuth();
});
</script>

<style scoped>
/* Global styles for the app */
</style>
