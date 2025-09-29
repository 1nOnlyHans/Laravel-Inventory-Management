<script setup lang="ts">
import { SidebarProvider, SidebarTrigger } from "@/components/ui/sidebar";
import AdminSidebarLinks from "./AdminSidebarLinks.vue";
</script>

<template>
    <SidebarProvider>
        <AdminSidebarLinks />
        <SidebarTrigger class="md:hidden"/>
        <section class="container mx-auto p-3">
            <RouterView />
        </section>
        <main>
            <slot />

        </main>
    </SidebarProvider>
</template>