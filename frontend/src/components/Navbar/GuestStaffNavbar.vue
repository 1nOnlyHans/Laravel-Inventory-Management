<script setup>
import { RouterLink } from 'vue-router';
</script>
<template>
    <nav>
        <div class="container-xl mx-auto p-3 bg-brand">
            <div class="flex justify-start items-center">
                <RouterLink :to="{ name: 'Employee Login' }" class="text-white font-medium text-2xl">EzeePC</RouterLink>
            </div>
        </div>
    </nav>
</template>s