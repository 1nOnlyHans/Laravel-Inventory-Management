import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";

import GuestStaffLayout from "@/views/Layouts/GuestStaffLayout.vue";
import StaffLogin from "@/views/Auth/StaffLogin.vue";
import Forbidden from "@/views/Pages/Forbidden.vue";
import NoPage from "@/views/Pages/NoPage.vue";

// Admin Routes
import AdminDashboard from "@/views/Admin/AdminDashboard.vue";
import AdminLayout from "@/views/Layouts/AdminLayout.vue";
import AdminStaffManagement from "@/views/Admin/AdminStaffManagement.vue";
import AdminStaffDetails from "@/views/Admin/AdminStaffDetails.vue";
import AdminProductManagement from "@/views/Admin/AdminProductManagement.vue";
import AdminSupplierManagement from "@/views/Admin/AdminSupplierManagement.vue";
import AdminCategoriesManagement from "@/views/Admin/AdminCategoriesManagement.vue";
import AdminPurchaseManagement from "@/views/Admin/AdminPurchaseManagement.vue";
import AdminPurchaseHistory from "@/views/Admin/AdminPurchaseHistory.vue";
import AdminAddProduct from "@/views/Admin/AdminAddProduct.vue";
import AdminProductDetails from "@/views/Admin/AdminProductDetails.vue";
import AdminEditProductDetails from "@/views/Admin/AdminEditProductDetails.vue";
import AdminBrandsManagement from "@/views/Admin/AdminBrandsManagement.vue";
import AdminOnlinePayment from "@/views/Admin/AdminOnlinePayment.vue";
import PaymentLayout from "@/views/Layouts/PaymentLayout.vue";
import success from "@/views/Payment/Success.vue";
import failed from "@/views/Payment/Failed.vue";
import AdminStockMovementManagement from "@/views/Admin/AdminStockMovementManagement.vue";
import AdminAlerts from "@/views/Admin/AdminAlerts.vue";
import AdminLogs from "@/views/Admin/AdminLogs.vue";

// Staff Routes
import StaffLayout from "@/views/Layouts/StaffLayout.vue";
import StaffDashboard from "@/views/Staff/StaffDashboard.vue";
import StaffPOS from "@/views/Staff/StaffPOS.vue";
import StaffLogs from "@/views/Staff/StaffLogs.vue";
import StaffAlerts from "@/views/Staff/StaffAlerts.vue";
import StaffProductDetails from "@/views/Staff/StaffProductDetails.vue";
import StaffTransactions from "@/views/Staff/StaffTransactions.vue";

const routes = [
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
        path: "brands",
        name: "Admin Brands Management",
        component: AdminBrandsManagement,
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
      {
        path: "online_payment/:order_id",
        name: "Admin Online Payment",
        component: AdminOnlinePayment,
        meta: {
          requiresAuth: "true",
          role: "Admin",
        },
      },
      {
        path: "stock_movements",
        name: "Admin Stock Movements",
        component: AdminStockMovementManagement,
        meta: {
          requiresAuth: "true",
          role: "Admin",
        },
      },
      {
        path: "alerts",
        name: "Admin Alerts",
        component: AdminAlerts,
        meta: {
          requiresAuth: "true",
          role: "Admin",
        },
      },
      {
        path: "logs",
        name: "Admin Logs",
        component: AdminLogs,
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
    path: "/payment",
    component: PaymentLayout,
    children: [
      {
        path: "success",
        name: "Payment Success",
        component: success,
        meta: {
          requiresAuth: true,
        },
      },
      {
        path: "failed",
        name: "Payment Failed",
        component: failed,
        meta: {
          requiresAuth: true,
        },
      },
    ],
  },
  {
    path: "/staff",
    component: StaffLayout,
    children: [
      {
        path: "dashboard",
        name: "Staff Dashboard",
        component: StaffDashboard,
        meta: {
          requiresAuth: true,
          role: "Staff",
        },
      },
      {
        path: "pos",
        name: "Staff POS",
        component: StaffPOS,
        meta: {
          requiresAuth: true,
          role: "Staff",
        },
      },
      {
        path: "logs",
        name: "Staff Logs",
        component: StaffLogs,
        meta: {
          requiresAuth: true,
          role: "Staff",
        },
      },
      {
        path: "alerts",
        name: "Staff Alerts",
        component: StaffAlerts,
        meta: {
          requiresAuth: true,
          role: "Staff",
        },
      },
      {
        path: "product_details/:product_id",
        name: "Staff Product Details",
        component: StaffProductDetails,
        meta: {
          requiresAuth: true,
          role: "Staff",
        },
      },
      {
        path: "transactions",
        name: "Staff Transactions",
        component: StaffTransactions,
        meta: {
          requiresAuth: true,
          role: "Staff",
        },
      },
    ],
    meta: {
      requiresAuth: true,
      role: "Staff",
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
