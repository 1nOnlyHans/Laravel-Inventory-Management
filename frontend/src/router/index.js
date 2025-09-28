import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";

import Home from "@/views/Home.vue";
import Login from "@/views/Login.vue";
import Register from "@/views/Register.vue";
import Dashboard from "@/views/Pages/Dashboard.vue";
import GuestStaffLayout from "@/views/Layouts/GuestStaffLayout.vue";
import StaffLogin from "@/views/Auth/StaffLogin.vue";
import AdminDashboard from "@/views/Admin/AdminDashboard.vue";
import AdminLayout from "@/views/Layouts/AdminLayout.vue";
import Forbidden from "@/views/Pages/Forbidden.vue";
import NoPage from "@/views/Pages/NoPage.vue";
import AdminStaffManagement from "@/views/Admin/AdminStaffManagement.vue";
import AdminStaffDetails from "@/views/Admin/AdminStaffDetails.vue";

const routes = [
  // //Guest
  // { path: '/', component: Home, name: 'home' },
  // { path: '/login', component: Login, name: 'login' },
  // { path: '/register', component: Register, name: 'register' },
  // //Auth
  // { path: '/dashboard', component: Dashboard, name: 'dashboard', meta: { requiresAuth: true } },
  {
    path: "/employee",
    component: GuestStaffLayout,
    children: [
      { path: "login", name: "Employee Login", component: StaffLogin },
    ],
  },
  {
    path: "/admin",
    component: AdminLayout,
    children: [
      {
        path: "dashboard",
        name: "Admin Dashboard",
        component: AdminDashboard,
        meta: { requiresAuth: true, role: "Admin" },
      },
      {
        path: "employees",
        name: "Admin Employee Management",
        component: AdminStaffManagement,
        meta: {
          requiresAuth: true,
          role: "Admin",
        },
      },
      {
        path: "employee_details/:employee_id",
        name: "Admin Employee Details",
        component: AdminStaffDetails,
        meta: {
          requiresAuth: true,
          role: "Admin",
        },
      },
    ],
    meta: {
      requiresAuth: true,
      role: "Admin",
    },
  },
  {
    path: "/403",
    name: "Forbidden Page",
    component: Forbidden,
  },
  {
    path: "/:catchAll(.*)",
    name: "No Page",
    component: NoPage,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes: routes,
});

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();
  //If not authenticated
  if (!auth.isAuthenticated) {
    await auth.fetchUser();
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next("/login");
  }

  //Invalid role
  if (to.meta.role && auth.user.role !== to.meta.role) {
    next("/403");
  }

  next();
});

export default router;
