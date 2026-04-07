import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { createVfm } from 'vue-final-modal';
import 'vue-final-modal/style.css';
import App from './App.vue';
import router from './router';
import { useAuthStore } from './stores/auth';

const app = createApp(App);

const pinia = createPinia();
app.use(pinia);
app.use(router);
app.use(createVfm());

// Auto-login if no token exists
const authStore = useAuthStore();
if (!authStore.token) {
  authStore.autoLogin().then(() => {
    app.mount('#app');
  }).catch(() => {
    app.mount('#app');
  });
} else {
  authStore.initializeAuth();
  app.mount('#app');
}
