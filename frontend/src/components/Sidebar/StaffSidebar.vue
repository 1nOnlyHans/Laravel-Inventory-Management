<script setup>
import { SidebarProvider, SidebarTrigger } from "@/components/ui/sidebar";
import { useAuthStore } from "@/stores/auth";
import StaffSidebarLinks from "./StaffSidebarLinks.vue";

const auth = useAuthStore();
</script>
<template>
    <SidebarProvider>
        <StaffSidebarLinks />
        <main class="flex-1 overflow-y-auto">
            <div className="">
                <nav class="w-ful bg-brand shadow-md">
                    <div class="container-xl mx-auto p-3">
                        <div class="flex justify-between items-center">
                            <SidebarTrigger class="text-white" />
                            <h1 class="text-white font-medium">{{ auth.user.username }}</h1>
                        </div>
                    </div>
                </nav>
            </div>
            <slot />
            <RouterView />
        </main>
    </SidebarProvider>
</template>