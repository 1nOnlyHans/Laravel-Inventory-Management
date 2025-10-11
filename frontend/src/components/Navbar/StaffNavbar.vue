<script setup>
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();
</script>

<template>
    <nav class="fixed top-0 left-0 w-full z-10  bg-brand shadow-md">
        <div class="container-xl mx-auto p-3">
            <div class="flex justify-between items-center">
                <slot />
                <h1 class="text-white font-medium">{{ auth.user.username }}</h1>
            </div>
        </div>
    </nav>

    <!-- Spacer to prevent content from hiding behind navbar -->
    <div class="h-16"></div>
</template>
