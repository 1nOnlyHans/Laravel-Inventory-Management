<template>
    <auth-layout>
        <div class="p-6 max-w-2xl mx-auto bg-white rounded-xl shadow-md">
            <h1 class="text-2xl font-bold text-blue-600">Dashboard</h1>
            <div v-if="auth.user">
                <p class="mb-3"><span class="font-semibold">Name:</span> {{ auth.user.name }}</p>
                <p class="mb-3"><span class="font-semibold">Email:</span> {{ auth.user.email }}</p>
                <p class="mb-3"><span class="font-semibold">Token:</span> {{ auth.token }}</p>
            </div>
        </div>
    </auth-layout>
</template>

<script setup>
    import { useAuthStore } from "@/stores/auth";
    const auth = useAuthStore()
</script>
