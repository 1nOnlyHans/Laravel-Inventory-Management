<template>
    <guest-layout>
        <div class="container mx-auto">
            <div class="mb-5 text-center">
                <h1 class="text-blue-500 text-2xl font-bold text-bold">Registration Page</h1>
            </div>
            <form @submit.prevent="handleRegister">
                <div class="mb-3">
                    <label class="block text-blue-500">Name</label>
                    <input type="text" v-model="userData.name" class="border-2 border-gray-300 rounded p-2 w-full">
                </div>
                <div class="mb-3">
                    <label class="block text-blue-500">Email</label>
                    <input type="text" v-model="userData.email" class="border-2 border-gray-300 rounded p-2 w-full">
                </div>
                <div class="mb-3">
                    <label class="block text-blue-500">Password</label>
                    <input type="password" v-model="userData.password" class="border-2 border-gray-300 rounded p-2 w-full">
                </div>
                <div class="mb-3">
                    <label class="block text-blue-500">Confirm Password</label>
                    <input type="password" v-model="userData.password_confirmation" class="border-2 border-gray-300 rounded p-2 w-full">
                </div>
                <div class="text-center mt-10">
                    <button type="submit" class="bg-blue-500 text-white py-2 rounded px-30 px-">Register</button>
                </div>
            </form>
        </div>
    </guest-layout>
</template>

<script setup>
    import { ref } from 'vue';
    import axios from '@/axios';
    import Swal from 'sweetalert2';
    import { useRouter } from 'vue-router';

    const router = useRouter();
    const userData = ref({
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
    });
    
    const handleRegister = async () => {
        try {
            const response = await axios.post('/api/register', userData.value);
            Swal.fire(response.data.message);
        } catch(error) {
            Swal.fire(error.response.data.message);
        } finally {
            userData.value = {
                name: '',
                email: '',
                password: '',
                password_confirmation: ''
            };            
        }
    }
</script>
