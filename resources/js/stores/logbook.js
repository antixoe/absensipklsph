import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useLogbookStore = defineStore('logbook', () => {
  const entries = ref([]);
  const loading = ref(false);
  const error = ref(null);

  const fetchEntries = async (filters = {}) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.get('/api/v1/logbook', { params: filters });
      entries.value = response.data.data;
      return response.data;
    } catch (err) {
      error.value = err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const getEntry = async (id) => {
    try {
      const response = await axios.get(`/api/v1/logbook/${id}`);
      return response.data;
    } catch (err) {
      error.value = err.message;
      throw err;
    }
  };

  const createEntry = async (data) => {
    try {
      const response = await axios.post('/api/v1/logbook', data);
      entries.value.unshift(response.data.data);
      return response.data;
    } catch (err) {
      error.value = err.message;
      throw err;
    }
  };

  const updateEntry = async (id, data) => {
    try {
      const response = await axios.put(`/api/v1/logbook/${id}`, data);
      const index = entries.value.findIndex(e => e.id === id);
      if (index >= 0) {
        entries.value[index] = response.data.data;
      }
      return response.data;
    } catch (err) {
      error.value = err.message;
      throw err;
    }
  };

  const submitEntry = async (id) => {
    try {
      const response = await axios.post(`/api/v1/logbook/${id}/submit`);
      const index = entries.value.findIndex(e => e.id === id);
      if (index >= 0) {
        entries.value[index] = response.data.data;
      }
      return response.data;
    } catch (err) {
      error.value = err.message;
      throw err;
    }
  };

  const approveEntry = async (id, feedback = null) => {
    try {
      const response = await axios.post(`/api/v1/logbook/${id}/approve`, { feedback });
      const index = entries.value.findIndex(e => e.id === id);
      if (index >= 0) {
        entries.value[index] = response.data.data;
      }
      return response.data;
    } catch (err) {
      error.value = err.message;
      throw err;
    }
  };

  const rejectEntry = async (id, feedback) => {
    try {
      const response = await axios.post(`/api/v1/logbook/${id}/reject`, { feedback });
      const index = entries.value.findIndex(e => e.id === id);
      if (index >= 0) {
        entries.value[index] = response.data.data;
      }
      return response.data;
    } catch (err) {
      error.value = err.message;
      throw err;
    }
  };

  const deleteEntry = async (id) => {
    try {
      await axios.delete(`/api/v1/logbook/${id}`);
      entries.value = entries.value.filter(e => e.id !== id);
    } catch (err) {
      error.value = err.message;
      throw err;
    }
  };

  return {
    entries,
    loading,
    error,
    fetchEntries,
    getEntry,
    createEntry,
    updateEntry,
    submitEntry,
    approveEntry,
    rejectEntry,
    deleteEntry
  };
});
