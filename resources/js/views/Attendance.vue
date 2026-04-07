<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-8 flex justify-between items-center">
        <h1 class="text-4xl font-bold text-gray-900">Attendance</h1>
        <button
          v-if="authStore.user?.role?.name === 'student'"
          @click="openCheckIn"
          class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600"
        >
          Check In
        </button>
      </div>

      <!-- Check In/Out Modal -->
      <div v-if="showCheckInModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
          <h2 class="text-2xl font-bold mb-4">{{ checkInMode === 'in' ? 'Check In' : 'Check Out' }}</h2>
          <div class="space-y-4">
            <button
              @click="captureLocation()"
              class="w-full bg-orange-500 text-white py-2 rounded hover:bg-orange-600"
            >
              📍 {{ locationCaptures === 0 ? 'Get Location' : '✓ Location Captured' }}
            </button>
            <input
              type="file"
              accept="image/*"
              @change="capturePhoto"
              class="w-full"
            />
            <div class="flex gap-4">
              <button
                @click="showCheckInModal = false"
                class="flex-1 bg-gray-300 text-gray-900 py-2 rounded hover:bg-gray-400"
              >
                Cancel
              </button>
              <button
                @click="submitCheckIn"
                :disabled="!locationCaptures"
                class="flex-1 bg-orange-500 text-white py-2 rounded hover:bg-orange-600 disabled:opacity-50"
              >
                Submit
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Attendance Records -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b">
          <input
            v-model="searchMonth"
            type="month"
            class="px-4 py-2 border border-gray-300 rounded-lg"
          />
        </div>
        <table class="w-full">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Date</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Check In</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Check Out</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Notes</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="attendances.length === 0" class="border-t">
              <td colspan="5" class="px-6 py-4 text-center text-gray-500">No attendance records found</td>
            </tr>
            <tr v-for="attendance in attendances" :key="attendance.id" class="border-t hover:bg-gray-50">
              <td class="px-6 py-4 text-sm">{{ formatDate(attendance.attendance_date) }}</td>
              <td class="px-6 py-4 text-sm">{{ attendance.check_in_time || '—' }}</td>
              <td class="px-6 py-4 text-sm">{{ attendance.check_out_time || '—' }}</td>
              <td class="px-6 py-4 text-sm">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-semibold',
                    attendance.status === 'present' && 'bg-green-100 text-green-700',
                    attendance.status === 'late' && 'bg-yellow-100 text-yellow-700',
                    attendance.status === 'absent' && 'bg-red-100 text-red-700',
                    attendance.status === 'sick' && 'bg-blue-100 text-blue-700',
                    attendance.status === 'permission' && 'bg-purple-100 text-purple-700'
                  ]"
                >
                  {{ attendance.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ attendance.notes || '—' }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Report Section -->
      <div v-if="authStore.user?.role?.name === 'student'" class="mt-8 bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-4">Attendance Report</h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
          <div class="bg-blue-50 p-4 rounded">
            <div class="text-xs text-gray-600">Total Days</div>
            <div class="text-2xl font-bold text-blue-600">{{ attendanceReport.total_days }}</div>
          </div>
          <div class="bg-green-50 p-4 rounded">
            <div class="text-xs text-gray-600">Present</div>
            <div class="text-2xl font-bold text-green-600">{{ attendanceReport.present }}</div>
          </div>
          <div class="bg-yellow-50 p-4 rounded">
            <div class="text-xs text-gray-600">Late</div>
            <div class="text-2xl font-bold text-yellow-600">{{ attendanceReport.late }}</div>
          </div>
          <div class="bg-red-50 p-4 rounded">
            <div class="text-xs text-gray-600">Absent</div>
            <div class="text-2xl font-bold text-red-600">{{ attendanceReport.absent }}</div>
          </div>
          <div class="bg-purple-50 p-4 rounded">
            <div class="text-xs text-gray-600">Rate</div>
            <div class="text-2xl font-bold text-purple-600">{{ attendanceReport.attendance_rate }}%</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useAttendanceStore } from '../stores/attendance';

const authStore = useAuthStore();
const attendanceStore = useAttendanceStore();

const attendances = ref([]);
const showCheckInModal = ref(false);
const checkInMode = ref('in');
const searchMonth = ref(new Date().toISOString().slice(0, 7));
const locationCaptures = ref(0);
const currentLocation = ref(null);
const currentPhoto = ref(null);
const attendanceReport = ref({
  total_days: 0,
  present: 0,
  late: 0,
  absent: 0,
  attendance_rate: 0
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const openCheckIn = () => {
  checkInMode.value = 'in';
  locationCaptures.value = 0;
  currentPhoto.value = null;
  showCheckInModal.value = true;
};

const captureLocation = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition((position) => {
      currentLocation.value = {
        latitude: position.coords.latitude,
        longitude: position.coords.longitude
      };
      locationCaptures.value = 1;
    });
  }
};

const capturePhoto = (event) => {
  currentPhoto.value = event.target.files[0];
};

const submitCheckIn = async () => {
  try {
    const formData = new FormData();
    formData.append('latitude', currentLocation.value.latitude);
    formData.append('longitude', currentLocation.value.longitude);
    if (currentPhoto.value) {
      formData.append('photo', currentPhoto.value);
    }

    await attendanceStore.checkIn(
      currentLocation.value.latitude,
      currentLocation.value.longitude,
      currentPhoto.value
    );

    showCheckInModal.value = false;
    await loadAttendances();
    alert('Check-in successful!');
  } catch (error) {
    alert('Error during check-in: ' + error.message);
  }
};

const loadAttendances = async () => {
  try {
    const month = searchMonth.value.split('-')[1];
    const res = await attendanceStore.fetchAttendances({ month });
    attendances.value = res.data || [];

    // Load report
    if (authStore.user?.role?.name === 'student') {
      const reportRes = await attendanceStore.getReport(authStore.user.student.id, month);
      attendanceReport.value = reportRes.summary;
    }
  } catch (error) {
    console.error('Error loading attendance:', error);
  }
};

watch(searchMonth, () => {
  loadAttendances();
});

onMounted(() => {
  loadAttendances();
});
</script>
