<script setup>
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";
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
import SidebarHeader from "../ui/sidebar/SidebarHeader.vue";
import SidebarFooter from "../ui/sidebar/SidebarFooter.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faDashboard, faInbox, faUser, faUserCircle, faArrowsLeftRight, faClipboardList, faRightFromBracket } from "@fortawesome/free-solid-svg-icons";
import { ref } from "vue";
// Menu items.
const items = [
  {
    title: "Dashboard",
    url: "dashboard",
    icon: faDashboard,
  },
  {
    title: "Stocks",
    url: "stocks",
    icon: faInbox,
  },
  {
    title: "Staff",
    url: "staffs",
    icon: faUser,
  },
  {
    title: "Users",
    url: "users",
    icon: faUserCircle,
  },
  {
    title: "Transactions",
    url: "transactions",
    icon: faArrowsLeftRight,
  },
  {
    title: "Audit Logs",
    url: "logs",
    icon: faClipboardList,
  },
];

const auth = useAuthStore();
const router = useRouter();
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
    <SidebarHeader>
      <h1 class="font-bold text-2xl">EzeePC</h1>
    </SidebarHeader>
    <SidebarContent>
      <SidebarGroup>
        <SidebarGroupLabel class="text-white">
          <h1 class="text-lg">Management</h1>
        </SidebarGroupLabel>
        <SidebarGroupContent class="mt-3">
          <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title" class="mb-3">
              <SidebarMenuButton asChild>
                <RouterLink :to="item.url">
                  <FontAwesomeIcon :icon="item.icon" />
                  <span class="text-lg">{{ item.title }}</span>
                </RouterLink>
              </SidebarMenuButton>
            </SidebarMenuItem>
          </SidebarMenu>
        </SidebarGroupContent>
      </SidebarGroup>
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