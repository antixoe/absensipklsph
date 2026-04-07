import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useAttendanceStore = defineStore('attendance', () => {
  const attendances = ref([]);
  const loading = ref(false);
  const error = ref(null);

  const fetchAttendances = async (filters = {}) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.get('/api/v1/attendance', { params: filters });
      attendances.value = response.data.data;
      return response.data;
    } catch (err) {
      error.value = err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const checkIn = async (latitude, longitude, photo = null) => {
    try {
      const formData = new FormData();
      formData.append('latitude', latitude);
      formData.append('longitude', longitude);
      if (photo) formData.append('photo', photo);

      const response = await axios.post('/api/v1/attendance/check-in', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      return response.data;
    } catch (err) {
      error.value = err.message;
      throw err;
    }
  };

  const checkOut = async (latitude, longitude, photo = null) => {
    try {
      const formData = new FormData();
      formData.append('latitude', latitude);
      formData.append('longitude', longitude);
      if (photo) formData.append('photo', photo);

      const response = await axios.post('/api/v1/attendance/check-out', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      return response.data;
    } catch (err) {
      error.value = err.message;
      throw err;
    }
  };

  const getReport = async (studentId, month = null) => {
    try {
      const params = month ? { month } : {};
      const response = await axios.get(`/api/v1/attendance/report/${studentId}`, { params });
      return response.data;
    } catch (err) {
      error.value = err.message;
      throw err;
    }
  };

  return {
    attendances,
    loading,
    error,
    fetchAttendances,
    checkIn,
    checkOut,
    getReport
  };
});
