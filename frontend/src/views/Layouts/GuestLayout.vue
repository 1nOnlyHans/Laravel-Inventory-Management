<script setup>
    import { ref } from 'vue';
    const isOpen = ref(false);
    const toggleMenu = () => {
        isOpen.value = !isOpen.value;
    };
</script>
<template>
    <nav class="bg-white shadow-md w-full mb-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <a href="#" class="text-2xl font-bold text-blue-500">NCST</a>
                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-6 items-center">
                    <router-link to="/" class="text-gray-700 hover:text-blue-500">Home</router-link>
                    <router-link to="/login" class="text-gray-700 hover:text-blue-500">Login</router-link>
                    <router-link to="/register" class="text-gray-700 hover:text-blue-500">Register</router-link>
                </div>

                <!-- Burger Button (mobile) -->
                <button @click="toggleMenu" class="md:hidden text-gray-700 focus:outline-none">
                    <!-- Burger -->
                    <svg v-if="!isOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <!-- Close -->
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Dropdown -->
        <div v-show="isOpen" class="md:hidden bg-white shadow-md">
            <div class="flex flex-col space-y-2 py-4 px-6">
                <a href="#" class="text-gray-700 hover:text-blue-500">Home</a>
                <a href="#" class="text-gray-700 hover:text-blue-500">About</a>
                <a href="#" class="text-gray-700 hover:text-blue-500">Services</a>
                <a href="#" class="text-gray-700 hover:text-blue-500">Contact</a>
            </div>
        </div>
    </nav>
    <slot></slot>
</template>