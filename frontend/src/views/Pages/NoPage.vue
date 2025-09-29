<script setup>
import { RouterLink } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
</script>

<template>
    <main class="min-h-screen bg-brand flex items-center justify-center px-6 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            <!-- Left Side (Error Code & Message) -->
            <div class="text-center lg:text-left">
                <p class="text-6xl font-bold text-accents">404</p>
                <h1 class="mt-4 text-4xl sm:text-6xl font-semibold text-white">
                    Page Not Found
                </h1>
                <p class="mt-6 text-lg text-gray-400">
                    Sorry, we couldn’t find the page you’re looking for.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-4 sm:gap-6 justify-center lg:justify-start">
                    <RouterLink class="bg-accents text-white px-3 py-2 rounded hover:bg-accents-hover" to="/">
                        Go Back Home
                    </RouterLink>
                </div>
            </div>
        </div>
    </main>
</template>
