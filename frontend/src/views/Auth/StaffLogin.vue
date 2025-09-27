<script setup>
import { Input } from "@/components/ui/input"
import { Button } from "@/components/ui/button"

import { ref } from "vue";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";
import { useAuthStore } from "@/stores/auth";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faUser } from "@fortawesome/free-regular-svg-icons";
import { faL, faLock } from "@fortawesome/free-solid-svg-icons";
import { VueSpinnerRings } from "vue3-spinners";
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
            if (auth.user.role === 'Admin') {
                router.push('/admin/dashboard');
            }
        }
        Swal.fire(response.data.message);
    } catch (error) {
        Swal.fire(error.response.data.message);
    } finally {
        isLoading.value = false;
    }
}
</script>
<template>
    <section class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="container mx-auto p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Login Card -->
                <div class="bg-white rounded-2xl shadow-lg p-8 flex flex-col justify-center space-y-8">
                    <h1 class="text-center font-bold text-3xl">EMPLOYEE LOGIN</h1>
                    <form @submit.prevent="handleLogin">
                        <!-- Username -->
                        <div class="flex flex-col space-y-3 mb-3">
                            <label for="username" class="font-medium text-2xl">
                                <FontAwesomeIcon :icon="faUser" />
                            </label>
                            <Input type="text" placeholder="Enter Employee ID" id="username" name="username"
                                class="py-6" v-model="userData.username" />
                        </div>

                        <!-- Password -->
                        <div class="flex flex-col space-y-3 mb-3">
                            <label for="password" class="font-medium text-2xl">
                                <FontAwesomeIcon :icon="faLock" />
                            </label>
                            <Input type="password" placeholder="Enter Password" id="password" name="password"
                                class="py-6" v-model="userData.password" />
                        </div>

                        <!-- Button -->
                        <div class="flex justify-center my-10">
                            <Button class="bg-button hover:bg-button-hovered text-white px-10 py-3 rounded-lg">
                                <span v-if="isLoading">
                                    <VueSpinner />
                                </span>
                                <span v-else>LOGIN</span>
                            </Button>

                        </div>
                    </form>
                </div>

                <!-- Info / Image Card -->
                <div
                    class="bg-white rounded-2xl shadow-lg p-8 flex flex-col items-center justify-center text-center space-y-6">
                    <!-- Placeholder Image -->
                    <div class="w-[350px] h-[350px]rounded-full flex items-center justify-center">
                        <img src="@/assets/images/logo.png" alt="Logo">
                    </div>

                    <p class="text-gray-700">
                        EzeePC — Tech it easy! <br />
                        No.1 PC seller worldwide offering premium services
                        and quality products.
                    </p>
                </div>

            </div>
        </div>
    </section>
</template>
