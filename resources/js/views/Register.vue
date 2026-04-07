<template>
  <div class="min-h-screen bg-gradient-to-r from-orange-400 to-orange-600 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md">
      <h2 class="text-3xl font-bold text-gray-900 mb-2">Register</h2>
      <p class="text-gray-600 mb-6">Choose your role and create an account</p>

      <!-- Role Selection -->
      <div class="flex gap-4 mb-6">
        <button
          @click="role = 'student'"
          :class="[
            'flex-1 py-2 px-4 rounded-lg font-semibold transition',
            role === 'student'
              ? 'bg-orange-500 text-white'
              : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
          ]"
        >
          Student
        </button>
        <button
          @click="role = 'instructor'"
          :class="[
            'flex-1 py-2 px-4 rounded-lg font-semibold transition',
            role === 'instructor'
              ? 'bg-orange-500 text-white'
              : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
          ]"
        >
          Instructor
        </button>
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <!-- Name Field -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
          <input
            v-model="form.name"
            type="text"
            id="name"
            required
            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
          />
        </div>

        <!-- Email Field -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input
            v-model="form.email"
            type="email"
            id="email"
            required
            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
          />
        </div>

        <!-- Password Field -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <input
            v-model="form.password"
            type="password"
            id="password"
            required
            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
          />
        </div>

        <!-- Password Confirm Field -->
        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            id="password_confirmation"
            required
            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
          />
        </div>

        <!-- Student Specific Fields -->
        <template v-if="role === 'student'">
          <div>
            <label for="nim" class="block text-sm font-medium text-gray-700">Student ID (NIM)</label>
            <input
              v-model="form.nim"
              type="text"
              id="nim"
              required
              class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg"
            />
          </div>
          <div>
            <label for="school" class="block text-sm font-medium text-gray-700">School</label>
            <input
              v-model="form.school"
              type="text"
              id="school"
              required
              class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg"
            />
          </div>
          <div>
            <label for="major" class="block text-sm font-medium text-gray-700">Major</label>
            <input
              v-model="form.major"
              type="text"
              id="major"
              required
              class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg"
            />
          </div>
        </template>

        <!-- Instructor Specific Fields -->
        <template v-if="role === 'instructor'">
          <div>
            <label for="nip" class="block text-sm font-medium text-gray-700">Employee ID (NIP)</label>
            <input
              v-model="form.nip"
              type="text"
              id="nip"
              class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg"
            />
          </div>
          <div>
            <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
            <input
              v-model="form.department"
              type="text"
              id="department"
              class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg"
            />
          </div>
          <div>
            <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
            <input
              v-model="form.position"
              type="text"
              id="position"
              class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg"
            />
          </div>
        </template>

        <!-- Error Message -->
        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          {{ error }}
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-orange-500 text-white font-semibold py-2 rounded-lg hover:bg-orange-600 disabled:opacity-50"
        >
          {{ loading ? 'Registering...' : 'Register' }}
        </button>
      </form>

      <!-- Login Link -->
      <p class="mt-4 text-center text-gray-600">
        Already have an account?
        <router-link to="/login" class="text-orange-500 hover:underline">Login here</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const role = ref('student');
const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  nim: '',
  school: '',
  major: '',
  nip: '',
  department: '',
  position: '',
  phone: ''
});

const loading = ref(false);
const error = ref(null);

const handleRegister = async () => {
  loading.value = true;
  error.value = null;

  try {
    if (role.value === 'student') {
      await authStore.registerStudent(form.value);
    } else {
      await authStore.registerInstructor(form.value);
    }
    await router.push('/');
  } catch (err) {
    error.value = err.response?.data?.message || 'Registration failed. Please try again.';
  } finally {
    loading.value = false;
  }
};
</script>
