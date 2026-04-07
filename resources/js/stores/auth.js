import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const token = ref(localStorage.getItem('authToken'));

  const isAuthenticated = computed(() => !!token.value && !!user.value);

  const initializeAuth = async () => {
    if (token.value) {
      try {
        const response = await axios.get('/api/v1/auth/me', {
          headers: { Authorization: `Bearer ${token.value}` }
        });
        user.value = response.data.user;
      } catch (error) {
        logout();
      }
    }
  };

  const login = async (email, password) => {
    try {
      const response = await axios.post('/api/v1/auth/login', { email, password });
      token.value = response.data.token;
      user.value = response.data.user;
      localStorage.setItem('authToken', token.value);
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
      return true;
    } catch (error) {
      console.error('Login failed:', error);
      throw error;
    }
  };

  const logout = () => {
    token.value = null;
    user.value = null;
    localStorage.removeItem('authToken');
    delete axios.defaults.headers.common['Authorization'];
  };

  const registerStudent = async (data) => {
    try {
      const response = await axios.post('/api/v1/auth/register-student', data);
      token.value = response.data.token;
      user.value = response.data.user;
      localStorage.setItem('authToken', token.value);
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
      return true;
    } catch (error) {
      console.error('Registration failed:', error);
      throw error;
    }
  };

  const registerInstructor = async (data) => {
    try {
      const response = await axios.post('/api/v1/auth/register-instructor', data);
      token.value = response.data.token;
      user.value = response.data.user;
      localStorage.setItem('authToken', token.value);
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
      return true;
    } catch (error) {
      console.error('Registration failed:', error);
      throw error;
    }
  };

  const autoLogin = async () => {
    try {
      const response = await axios.post('/api/v1/auth/login', {
        email: 'student@example.com',
        password: 'password123'
      });
      token.value = response.data.token;
      user.value = response.data.user;
      localStorage.setItem('authToken', token.value);
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
      return true;
    } catch (error) {
      console.error('Auto-login failed:', error);
      return false;
    }
  };

  return {
    user,
    token,
    isAuthenticated,
    initializeAuth,
    login,
    logout,
    autoLogin,
    registerStudent,
    registerInstructor
  };
});
