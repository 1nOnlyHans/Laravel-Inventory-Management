<template>
    <guest-layout>
        <div class="container mx-auto">
            <div class="mb-5 text-center">
                <h1 class="text-blue-500 text-2xl font-bold text-bold">Login Page</h1>
            </div>
            <form @submit.prevent="handleLogin">
                <div class="mb-3">
                    <label class="block text-blue-500">Email</label>
                    <input type="text" v-model="userData.email" class="border-2 border-gray-300 rounded p-2 w-full">
                </div>
                <div class="mb-3">
                    <label class="block text-blue-500">Password</label>
                    <input type="password" v-model="userData.password"
                        class="border-2 border-gray-300 rounded p-2 w-full">
                </div>
                <div class="text-center mt-10">
                    <Button type="submit">Login</Button>
                </div>
            </form>
        </div>
    </guest-layout>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";
import { useAuthStore } from "@/stores/auth";
import Button from "@/components/ui/button/Button.vue";
const userData = ref({
    email: '',
    password: '',
});
const router = useRouter();
const auth = useAuthStore();

const handleLogin = async () => {
    try {
        const response = await auth.login(userData.value.email, userData.value.password);
        if (response.status === 200) {
            router.push("/dashboard");
        }
        Swal.fire(response.data.message);
    } catch (error) {
        Swal.fire(error.response.data.message);
    }
}
</script>