import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

// Import views
import Login from '../views/Login.vue';
import Register from '../views/Register.vue';
import Dashboard from '../views/Dashboard.vue';
import Attendance from '../views/Attendance.vue';
import Logbook from '../views/Logbook.vue';
import LogbookDetail from '../views/LogbookDetail.vue';
import Activities from '../views/Activities.vue';
import Documents from '../views/Documents.vue';
import Reports from '../views/Reports.vue';
import NotFound from '../views/NotFound.vue';

const routes = [
  {
    path: '/',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    component: Login,
    meta: { requiresAuth: false }
  },
  {
    path: '/register',
    component: Register,
    meta: { requiresAuth: false }
  },
  {
    path: '/attendance',
    component: Attendance,
    meta: { requiresAuth: true }
  },
  {
    path: '/logbook',
    component: Logbook,
    meta: { requiresAuth: true }
  },
  {
    path: '/logbook/:id',
    component: LogbookDetail,
    meta: { requiresAuth: true }
  },
  {
    path: '/activities',
    component: Activities,
    meta: { requiresAuth: true }
  },
  {
    path: '/documents',
    component: Documents,
    meta: { requiresAuth: true }
  },
  {
    path: '/reports',
    component: Reports,
    meta: { requiresAuth: true }
  },
  {
    path: '/:pathMatch(.*)*',
    component: NotFound
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Navigation guard for authentication
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  const isAuthenticated = authStore.isAuthenticated;

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login');
  } else if ((to.path === '/login' || to.path === '/register') && isAuthenticated) {
    next('/');
  } else {
    next();
  }
});

export default router;
