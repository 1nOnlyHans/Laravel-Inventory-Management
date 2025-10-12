<script setup>
import { Input } from "@/components/ui/input"
import { Button } from "@/components/ui/button"

import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faUser } from "@fortawesome/free-regular-svg-icons";
import { faL, faLock } from "@fortawesome/free-solid-svg-icons";
import { RegularSwal } from "@/components/Swals/useSwals";
import Swal from "sweetalert2";

const userData = ref({
    username: '',
    password: '',
});
const router = useRouter();
const auth = useAuthStore();
const isLoading = ref(false);
const handleLogin = async () => {
    isLoading.value = true
    try {
        const response = await auth.login(userData.value.username, userData.value.password);
        if (response.status === 200) {
            router.push(`/${auth.user.role.toLowerCase()}/dashboard`);
        }
        RegularSwal(response.data.message);
    } catch (error) {
        Swal.fire(error.response.data.message);
    } finally {
        isLoading.value = false;
    }
}
</script>
<template>
    <section class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
            <!-- Logo -->
            <div class="flex flex-col items-center mb-8">
                <img src="@/assets/images/logo 2.png" class="w-28 h-auto mb-3" />
                <h1 class="text-xl font-semibold text-gray-800">System Account Login</h1>
                <p class="text-sm text-gray-500">Enter your credentials to access the system</p>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleLogin" class="space-y-5">
                <!-- Username -->
                <div class="relative">
                    <FontAwesomeIcon :icon="faUser"
                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                    <Input type="text" id="username" name="username" v-model="userData.username" placeholder="Your ID"
                        class="w-full pl-10 pr-3 py-3 rounded-lg bg-gray-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                </div>

                <!-- Password -->
                <div class="relative">
                    <FontAwesomeIcon :icon="faLock"
                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                    <Input type="password" id="password" name="password" v-model="userData.password"
                        placeholder="Password"
                        class="w-full pl-10 pr-3 py-3 rounded-lg bg-gray-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                </div>

                <!-- Button -->
                <div>
                    <Button
                        class="w-full text-white py-3 rounded-lg flex justify-center items-center font-medium transition">
                        <span v-if="isLoading">
                            <VueSpinner size="20" />
                        </span>
                        <span v-else>Login</span>
                    </Button>
                </div>
            </form>
        </div>
    </section>
</template>
