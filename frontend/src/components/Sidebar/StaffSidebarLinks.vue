<script setup>
import { ref } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";

import {
    Sidebar,
    SidebarContent,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from "@/components/ui/sidebar";

import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from "@/components/ui/collapsible";

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
    faTags,
    faArrowTrendUp,
    faChevronDown,
    faUsers,
} from "@fortawesome/free-solid-svg-icons";
import { faCreditCard } from "@fortawesome/free-regular-svg-icons";

// Sidebar menu items
const items = [
    {
        group: "System",
        menus: [{ title: "Dashboard", url: "/staff/dashboard", icon: faDashboard }],
    },
    {
        group: "Management",
        menus: [
            {
                title: "Sales",
                url: "/staff/pos",
                icon: faInbox,
                submenus: [
                    { title: "POS", url: "/staff/pos", icon: faCreditCard },
                ],
            },
            { title: "Customers", url: "/staff/customers", icon: faUsers },
        ],
    },
    {
        group: "Records",
        menus: [
            { title: "Transactions", url: "/staff/transactions", icon: faArrowsLeftRight },
            { title: "Audit Logs", url: "/staff/logs", icon: faClipboardList },
            { title: "Alerts", url: "/staff/alerts", icon: faBell },
            { title: "Reports", url: "/staff/reports", icon: faFileAlt },
        ],
    },
];

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();
const isLoading = ref(false);

const handleLogout = async () => {
    isLoading.value = true;
    try {
        const response = await auth.logout();
        if (response.status === 200) {
            router.push("/employee/login");
        }
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <Sidebar :background="'bg-brand text-white'" class="h-screen flex flex-col overflow-hidden">
        <!-- Header -->
        <SidebarHeader class="flex flex-col items-center text-center py-4 border-b border-gray-700">
            <h1 class="font-bold text-2xl text-white tracking-wide">LapTopia</h1>
            <p class="text-gray-400 text-sm">Point of Sale System</p>
        </SidebarHeader>

        <!-- Sidebar Content -->
        <SidebarContent class="flex-1 overflow-y-auto scrollbar-hide">
            <Collapsible v-for="section in items" :key="section.group" defaultOpen class="group/collapsible">
                <SidebarGroup>
                    <SidebarGroupLabel class="text-white flex justify-between items-center">
                        <span class="text-lg font-semibold">{{ section.group }}</span>
                        <CollapsibleTrigger>
                            <FontAwesomeIcon :icon="faChevronDown"
                                class="transition-transform group-data-[state=open]/collapsible:rotate-180" />
                        </CollapsibleTrigger>
                    </SidebarGroupLabel>

                    <CollapsibleContent>
                        <SidebarGroupContent class="mt-3 space-y-2">
                            <SidebarMenu>
                                <SidebarMenuItem v-for="item in section.menus" :key="item.title">
                                    <!-- If item has submenus -->
                                    <div v-if="item.submenus">
                                        <Collapsible>
                                            <CollapsibleTrigger
                                                class="flex items-center justify-between w-full px-3 py-2 rounded hover:bg-white hover:text-black">
                                                <div class="flex items-center gap-x-2">
                                                    <FontAwesomeIcon :icon="item.icon" />
                                                    <span class="text-lg">{{ item.title }}</span>
                                                </div>
                                                <FontAwesomeIcon :icon="faChevronDown" class="text-sm" />
                                            </CollapsibleTrigger>

                                            <CollapsibleContent>
                                                <div class="ml-6 mt-2 space-y-1">
                                                    <RouterLink v-for="sub in item.submenus" :key="sub.title"
                                                        :to="sub.url" :class="[
                                                            'block px-3 py-1.5 rounded text-sm',
                                                            route.path === sub.url
                                                                ? 'bg-white text-black font-medium'
                                                                : 'hover:bg-white hover:text-black'
                                                        ]">
                                                        <FontAwesomeIcon :icon="sub.icon" class="mr-2" />
                                                        {{ sub.title }}
                                                    </RouterLink>
                                                </div>
                                            </CollapsibleContent>
                                        </Collapsible>
                                    </div>

                                    <!-- Regular menu item -->
                                    <div v-else>
                                        <SidebarMenuButton asChild>
                                            <RouterLink :to="item.url" :class="[
                                                'flex items-center gap-x-2 rounded px-3 py-2',
                                                route.path === item.url
                                                    ? 'bg-white text-black font-medium'
                                                    : 'hover:bg-white hover:text-black'
                                            ]">
                                                <FontAwesomeIcon :icon="item.icon" />
                                                <span class="text-lg">{{ item.title }}</span>
                                            </RouterLink>
                                        </SidebarMenuButton>
                                    </div>
                                </SidebarMenuItem>
                            </SidebarMenu>
                        </SidebarGroupContent>
                    </CollapsibleContent>
                </SidebarGroup>
            </Collapsible>
        </SidebarContent>

        <!-- Footer -->
        <SidebarFooter class="border-t border-gray-700 p-3">
            <button type="button"
                class="flex items-center gap-x-3 w-full px-3 py-2 rounded hover:bg-white hover:text-black"
                @click="handleLogout" :disabled="isLoading">
                <template v-if="isLoading">
                    <span class="animate-pulse">Logging out...</span>
                </template>
                <template v-else>
                    <FontAwesomeIcon :icon="faRightFromBracket" />
                    Logout
                </template>
            </button>
        </SidebarFooter>
    </Sidebar>
</template>

<style scoped>
/* Hide scrollbars for a clean look */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    scrollbar-width: none;
    /* Firefox */
}
</style>
