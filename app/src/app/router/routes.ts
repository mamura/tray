import type { RouteRecordRaw } from "vue-router";

export const routes: RouteRecordRaw[] = [
  {
    path: '/login',
    component: () => import('@pages/LoginPage.vue'),
    meta: { layout: 'auth' }
  },

  {
    path: '/',
    component: () => import('@app/layouts/AdminLayout.vue'),
    meta: {
      requiresAuth: true
    },
    children: [
      {
        path: '',
        name: 'home',
        component: () => import('@pages/DashboardPage.vue')
      }
    ]
  },

  { path: '/:pathMatch(.*)*', redirect: '/' }
];
