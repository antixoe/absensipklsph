<template>
  <div class="grid gap-6">
    <section class="rounded-[2rem] border border-white/10 bg-white/6 p-8 shadow-2xl shadow-black/20 backdrop-blur-xl">
      <div class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-300">Settings</div>
      <h1 class="mt-3 text-4xl font-black text-white">Account and preferences</h1>
      <p class="mt-3 max-w-2xl text-slate-300">
        This SPA keeps your session in a token and stores language preference locally for now.
      </p>
    </section>

    <section class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
      <div class="panel">
        <div class="panel-head">
          <h2>Profile</h2>
        </div>

        <div class="mt-4 grid gap-4">
          <div class="info-card">
            <div class="info-label">Name</div>
            <div class="info-value">{{ authStore.user?.name || '—' }}</div>
          </div>
          <div class="info-card">
            <div class="info-label">Email</div>
            <div class="info-value">{{ authStore.user?.email || '—' }}</div>
          </div>
          <div class="info-card">
            <div class="info-label">Role</div>
            <div class="info-value">{{ authStore.roleLabel }}</div>
          </div>

          <button class="secondary-btn" @click="logout">Logout</button>
        </div>
      </div>

      <div class="panel">
        <div class="panel-head">
          <h2>Language</h2>
        </div>

        <div class="mt-4 grid gap-4">
          <label class="grid gap-2">
            <span class="label">Interface language</span>
            <select v-model="locale" class="input">
              <option value="en">English</option>
              <option value="id">Bahasa Indonesia</option>
              <option value="zh">中文</option>
            </select>
          </label>

          <button class="primary-btn" @click="saveLanguage">Save preference</button>

          <div v-if="message" class="rounded-2xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
            {{ message }}
          </div>

          <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-slate-300">
            Preference is currently saved in your browser. The backend language sync can be added later if needed.
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const locale = ref(localStorage.getItem('appLocale') || 'en');
const message = ref('');

const saveLanguage = () => {
  localStorage.setItem('appLocale', locale.value);
  message.value = 'Language preference saved.';
};

const logout = async () => {
  await authStore.logout();
  await router.push('/login');
};

onMounted(() => {
  localStorage.setItem('appLocale', locale.value);
});
</script>

<style scoped>
.panel {
  border: 1px solid rgba(255, 255, 255, 0.1);
  background: rgba(15, 23, 42, 0.8);
  padding: 1.5rem;
  border-radius: 2rem;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2);
  backdrop-filter: blur(24px);
}

.panel-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.panel-head h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #fff;
}

.label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #e2e8f0;
}

.input {
  border: 1px solid rgba(255, 255, 255, 0.1);
  background: rgba(255, 255, 255, 0.05);
  color: #fff;
  outline: none;
  padding: 0.75rem 1rem;
  border-radius: 1rem;
  transition: border-color 0.2s ease;
}

.input:focus {
  border-color: rgba(251, 146, 60, 0.6);
}

.info-card {
  border: 1px solid rgba(255, 255, 255, 0.1);
  background: rgba(255, 255, 255, 0.05);
  padding: 1rem;
  border-radius: 1rem;
}

.info-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.24em;
  color: #94a3b8;
}

.info-value {
  margin-top: 0.5rem;
  font-size: 1.125rem;
  font-weight: 600;
  color: #fff;
}

.primary-btn,
.secondary-btn {
  border-radius: 1rem;
  padding: 0.75rem 1rem;
  font-weight: 600;
  transition: filter 0.2s ease, border-color 0.2s ease, background 0.2s ease;
}

.primary-btn {
  border: 0;
  color: #020617;
  background: linear-gradient(90deg, #fb923c, #ea580c);
}

.primary-btn:hover {
  filter: brightness(1.08);
}

.secondary-btn {
  border: 1px solid rgba(255, 255, 255, 0.1);
  background: rgba(255, 255, 255, 0.05);
  color: #fff;
}

.secondary-btn:hover {
  border-color: rgba(253, 186, 116, 0.4);
  background: rgba(255, 255, 255, 0.1);
}
</style>
