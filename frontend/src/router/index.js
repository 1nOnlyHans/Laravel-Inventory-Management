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
import AdminUserManagement from "@/views/Admin/AdminUserManagement.vue";
import AdminProductManagement from "@/views/Admin/AdminProductManagement.vue";
import AdminSupplierManagement from "@/views/Admin/AdminSupplierManagement.vue";
import AdminCategoriesManagement from "@/views/Admin/AdminCategoriesManagement.vue";
import AdminPurchaseManagement from "@/views/Admin/AdminPurchaseManagement.vue";
import AdminPurchaseHistory from "@/views/Admin/AdminPurchaseHistory.vue";
import AdminAddProduct from "@/views/Admin/AdminAddProduct.vue";
import AdminProductDetails from "@/views/Admin/AdminProductDetails.vue";
import AdminEditProductDetails from "@/views/Admin/AdminEditProductDetails.vue";

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
      {
        path: "users",
        name: "Admin User Management",
        component: AdminUserManagement,
        meta: {
          requiresAuth: true,
          role: "Admin",
        },
      },
      {
        path: "products",
        name: "Admin Products Management",
        component: AdminProductManagement,
        meta: {
          requiresAuth: true,
          role: "Admin",
        },
      },
      {
        path: "addProduct",
        name: "Admin Add Products",
        component: AdminAddProduct,
        meta: {
          requiresAuth: true,
          role: "Admin",
        },
      },
      {
        path: "product_details/:product_id",
        name: "Admin Product Details",
        component: AdminProductDetails,
        meta: {
          requiresAuth: true,
          role: "Admin",
        },
      },
      {
        path: "product_edit/:product_id",
        name: "Admin Edit Product",
        component: AdminEditProductDetails,
        meta: {
          requiresAuth: true,
          role: "Admin",
        },
      },
      {
        path: "suppliers",
        name: "Admin Suppliers Management",
        component: AdminSupplierManagement,
        meta: {
          requiresAuth: true,
          role: "Admin",
        },
      },
      {
        path: "categories",
        name: "Admin Categories Management",
        component: AdminCategoriesManagement,
        meta: {
          requiresAuth: true,
          role: "Admin",
        },
      },
      {
        path: "purchase",
        name: "Admin Purchase Management",
        component: AdminPurchaseManagement,
        meta: {
          requiresAuth: "true",
          role: "Admin",
        },
      },
      {
        path: "purchase_history",
        name: "Admin Purchase History",
        component: AdminPurchaseHistory,
        meta: {
          requiresAuth: "true",
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
