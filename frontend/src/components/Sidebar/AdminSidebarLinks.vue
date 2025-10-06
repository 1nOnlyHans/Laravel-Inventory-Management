<script setup>
import { useAuthStore } from "@/stores/auth";
import { useRouter, useRoute } from "vue-router";
import {
  Sidebar,
  SidebarContent,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from "@/components/ui/sidebar"
import { ChevronDown } from "lucide-vue-next";
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from '@/components/ui/collapsible'
import SidebarHeader from "../ui/sidebar/SidebarHeader.vue";
import SidebarFooter from "../ui/sidebar/SidebarFooter.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
  faDashboard,
  faInbox,
  faUser,
  faUserCircle,
  faArrowsLeftRight,
  faClipboardList,
  faBell,
  faBoxesStacked,
  faBuilding,
  faFileAlt,
  faRightFromBracket,
  faCartShopping,
  faTags
} from "@fortawesome/free-solid-svg-icons";
import { ref } from "vue";
import { icon } from "@fortawesome/fontawesome-svg-core";
// Menu items.
const items = [
  {
    group: "System",
    menus: [
      { title: "Dashboard", url: "/admin/dashboard", icon: faDashboard },
    ],
  },
  {
    group: "Management",
    menus: [
      { title: "Products", url: "/admin/products", icon: faInbox },
      { title: "Suppliers", url: "/admin/suppliers", icon: faBuilding },
      { title: "Categories", url: "/admin/categories", icon: faBoxesStacked },
      { title: "Brands", url: "/admin/brands", icon: faTags },
      { title: "Purchase", url: "/admin/purchase", icon: faCartShopping },
      { title: "Staff", url: "/admin/employees", icon: faUser },
      { title: "Users", url: "/admin/users", icon: faUserCircle },
    ],
  },
  {
    group: "Records",
    menus: [
      { title: "Transactions", url: "/admin/transactions", icon: faArrowsLeftRight },
      { title: "Purchase History", url: "/admin/purchase_history", icon: faCartShopping },
      { title: "Audit Logs", url: "/admin/logs", icon: faClipboardList },
      { title: "Alerts", url: "/admin/alerts", icon: faBell },
      { title: "Reports", url: "/admin/reports", icon: faFileAlt },
    ],
  },
];


const auth = useAuthStore();
const router = useRouter();
const route = useRoute();
const isLoading = ref(false);
const handleLogout = async () => {
  isLoading.value = true
  try {
    const response = await auth.logout();
    if (response.status === 200) {
      router.push('/employee/login');
    }
    console.log(response);
  } catch (error) {
    console.log(error);
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <Sidebar :background="'bg-brand text-white'">
    <SidebarHeader class="flex flex-col items-center text-center py-4 border-b border-gray-700">
      <h1 class="font-bold text-2xl text-white tracking-wide">LapTopia</h1>
      <p class="text-gray-400 text-sm">Inventory Management System</p>
    </SidebarHeader>
    <SidebarContent>
      <Collapsible v-for="section in items" :key="section.group" defaultOpen class="group/collapsible">
        <SidebarGroup>
          <SidebarGroupLabel class="text-white" asChild>
            <div class="flex justify-between items-center">
              <h1 class="text-lg">{{ section.group }}</h1>
              <CollapsibleTrigger>
                <ChevronDown class="ml-auto transition-transform group-data-[state=open]/collapsible:rotate-180" />
              </CollapsibleTrigger>
            </div>
          </SidebarGroupLabel>
          <CollapsibleContent>
            <SidebarGroupContent class="mt-3">
              <SidebarMenu>
                <SidebarMenuItem v-for="item in section.menus" :key="item.title" class="mb-3">
                  <SidebarMenuButton asChild>
                    <RouterLink :to="item.url" :class="route.path === item.url ? 'bg-white text-black' : ''">
                      <FontAwesomeIcon :icon="item.icon" />
                      <span class="text-lg">{{ item.title }}</span>
                    </RouterLink>
                  </SidebarMenuButton>
                </SidebarMenuItem>
              </SidebarMenu>
            </SidebarGroupContent>
          </CollapsibleContent>
        </SidebarGroup>
      </Collapsible>
    </SidebarContent>

    <SidebarFooter>
      <button type="button"
        class="flex justify-start items-center gap-x-3 px-3 py-2 rounded hover:bg-white hover:text-black cursor-pointer"
        @click="handleLogout">
        <span v-if="isLoading">
          <VueSpinner />
        </span>
        <span v-else>
          <FontAwesomeIcon :icon="faRightFromBracket" /> Logout
        </span>
      </button>
    </SidebarFooter>
  </Sidebar>
</template>