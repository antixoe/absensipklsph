<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Welcome Section -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Welcome, {{ userFullName }}!</h1>
        <p class="text-gray-500 mt-2">{{ roleLabel }}</p>
      </div>

      <!-- Quick Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="text-gray-500 text-sm font-semibold">Today's Attendance</div>
          <div class="text-3xl font-bold text-orange-600 mt-2">{{ todayAttendance }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <div class="text-gray-500 text-sm font-semibold">Pending Logbook</div>
          <div class="text-3xl font-bold text-orange-500 mt-2">{{ pendingLogbook }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <div class="text-gray-500 text-sm font-semibold">Approved Logbook</div>
          <div class="text-3xl font-bold text-orange-700 mt-2">{{ approvedLogbook }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <div class="text-gray-500 text-sm font-semibold">Active Activities</div>
          <div class="text-3xl font-bold text-orange-400 mt-2">{{ activeActivities }}</div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <router-link
            to="/attendance"
            class="block bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-lg text-center font-semibold transition"
          >
            📋 Attendance
          </router-link>
          <router-link
            to="/logbook"
            class="block bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-lg text-center font-semibold transition"
          >
            📝 Logbook
          </router-link>
          <router-link
            to="/activities"
            class="block bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-lg text-center font-semibold transition"
          >
            ⚙️ Activities
          </router-link>
          <router-link
            to="/documents"
            class="block bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-lg text-center font-semibold transition"
          >
            📄 Documents
          </router-link>
        </div>
      </div>

      <!-- Additional Features -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Recent Logbook Entries -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-xl font-bold text-gray-900 mb-4">Recent Logbook Entries</h3>
          <div class="space-y-3">
            <p v-if="recentEntries.length === 0" class="text-gray-500">No entries yet</p>
            <div
              v-for="entry in recentEntries"
              :key="entry.id"
              class="border-l-4 border-blue-500 pl-4 py-2"
            >
              <h4 class="font-semibold text-gray-900">{{ entry.title }}</h4>
              <p class="text-sm text-gray-500">{{ formatDate(entry.entry_date) }}</p>
              <span
                :class="[
                  'inline-block mt-1 px-2 py-1 rounded text-xs font-semibold',
                  entry.status === 'approved' && 'bg-green-100 text-green-700',
                  entry.status === 'draft' && 'bg-gray-100 text-gray-700',
                  entry.status === 'submitted' && 'bg-blue-100 text-blue-700'
                ]"
              >
                {{ entry.status }}
              </span>
            </div>
          </div>
        </div>

        <!-- Upcoming Activities -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-xl font-bold text-gray-900 mb-4">Upcoming Activities</h3>
          <div class="space-y-3">
            <p v-if="upcomingActivities.length === 0" class="text-gray-500">No activities</p>
            <div
              v-for="activity in upcomingActivities"
              :key="activity.id"
              class="border-l-4 border-purple-500 pl-4 py-2"
            >
              <h4 class="font-semibold text-gray-900">{{ activity.activity_name }}</h4>
              <p class="text-sm text-gray-500">{{ formatDate(activity.activity_date) }}</p>
              <p class="text-sm text-gray-600">{{ activity.category }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useAttendanceStore } from '../stores/attendance';
import { useLogbookStore } from '../stores/logbook';

const authStore = useAuthStore();
const attendanceStore = useAttendanceStore();
const logbookStore = useLogbookStore();

const todayAttendance = ref('—');
const pendingLogbook = ref(0);
const approvedLogbook = ref(0);
const activeActivities = ref(0);
const recentEntries = ref([]);
const upcomingActivities = ref([]);

const userFullName = computed(() => authStore.user?.name || 'User');
const roleLabel = computed(() => {
  const role = authStore.user?.role?.name;
  switch (role) {
    case 'student':
      return 'Student Portal';
    case 'instructor':
      return 'Instructor Portal';
    case 'admin':
      return 'Admin Portal';
    default:
      return '';
  }
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

onMounted(async () => {
  // Load dashboard data
  try {
    // Load logbook entries
    const logbookRes = await logbookStore.fetchEntries({ per_page: 5 });
    recentEntries.value = logbookRes.data || [];
    pendingLogbook.value = recentEntries.value.filter(e => e.status === 'submitted').length;
    approvedLogbook.value = recentEntries.value.filter(e => e.status === 'approved').length;

    // Load attendance (if student)
    if (authStore.user?.role?.name === 'student') {
      const attendanceRes = await attendanceStore.fetchAttendances({ per_page: 1 });
      const today = new Date().toISOString().split('T')[0];
      const todayRecord = attendanceRes.data?.find(a => a.attendance_date === today);
      if (todayRecord && todayRecord.check_in_time) {
        todayAttendance.value = '✓ Checked In';
      } else {
        todayAttendance.value = 'Not checked in';
      }
    }
  } catch (error) {
    console.error('Error loading dashboard data:', error);
  }
});
</script>
