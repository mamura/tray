import DashboardLayout from "@app/layouts/DashboardLayout.vue";
import DashboardPage from "@/pages/DashboardPage.vue";
import AdminLayout from "@app/layouts/AdminLayout.vue";
import type { RouteRecordRaw } from "vue-router";

const UsersList   = () => import('@/pages/users/List.vue')
const SellersList = () => import('@/pages/sellers/ListView.vue')
const SalesList   = () => import('@/pages/sales/ListView.vue')

export const routes: RouteRecordRaw[] = [
  {
    path: '/login',
    component: () => import('@pages/LoginPage.vue'),
    meta: { layout: 'auth' }
  },

  {
    path: '/',
    component: DashboardLayout,
    meta: { requiresAuth: true },
    children: [
      { path: '', name: 'home', component: DashboardPage },
    ],
  },
  {
    path: '/',
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
      { path: 'sellers', component: SellersList },
      { path: 'users', component: UsersList },
      { path: 'sales', component: SalesList },
      //{ path: 'reports/admin', component: ReportsAdminPage },
      //{ path: 'reports/sellers', component: ReportsSellersPage },
    ]
  },

  { path: '/:pathMatch(.*)*', redirect: '/' }
];
