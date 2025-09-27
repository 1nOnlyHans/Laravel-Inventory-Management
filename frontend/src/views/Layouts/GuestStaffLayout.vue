<script setup>
import GuestStaffNavbar from '@/components/Navbar/GuestStaffNavbar.vue';
import { RouterView } from 'vue-router';
</script>
<template>
    <GuestStaffNavbar />
    <section>
        <div class="container-xl mx-auto">
            <RouterView />
        </div>
    </section>
</template>