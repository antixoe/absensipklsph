<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Logbook</h1>
        <button
          @click="openNewEntry"
          class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600"
        >
          + New Entry
        </button>
      </div>

      <!-- Entries List -->
      <div class="space-y-4">
        <div v-if="entries.length === 0" class="bg-white rounded-lg shadow p-6 text-center text-gray-500">
          No logbook entries yet
        </div>
        <div
          v-for="entry in entries"
          :key="entry.id"
          class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition cursor-pointer"
          @click="viewEntry(entry.id)"
        >
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-xl font-bold text-gray-900">{{ entry.title }}</h3>
              <p class="text-gray-600 mt-2">{{ entry.description }}</p>
              <p class="text-sm text-gray-500 mt-2">{{ formatDate(entry.entry_date) }} • {{ entry.hours_worked }} hours</p>
            </div>
            <span
              :class="[
                'px-3 py-1 rounded-full text-sm font-semibold',
                entry.status === 'approved' && 'bg-green-100 text-green-700',
                entry.status === 'draft' && 'bg-gray-100 text-gray-700',
                entry.status === 'submitted' && 'bg-blue-100 text-blue-700',
                entry.status === 'rejected' && 'bg-red-100 text-red-700'
              ]"
            >
              {{ entry.status }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useLogbookStore } from '../stores/logbook';

const router = useRouter();
const logbookStore = useLogbookStore();

const entries = ref([]);

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const openNewEntry = () => {
  router.push('/logbook/new');
};

const viewEntry = (id) => {
  router.push(`/logbook/${id}`);
};

onMounted(async () => {
  try {
    const res = await logbookStore.fetchEntries();
    entries.value = res.data || [];
  } catch (error) {
    console.error('Error loading logbook entries:', error);
  }
});
</script>
