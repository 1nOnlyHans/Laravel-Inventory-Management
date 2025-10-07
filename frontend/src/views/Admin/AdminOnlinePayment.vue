<script setup>
import { getPurchaseData } from '@/composables/usePO';
import { onlinePayment } from '@/composables/usePO';
import { onMounted } from 'vue';
import { useRoute } from 'vue-router';

const { purchaseData, fetchPurchaseData } = getPurchaseData();
const { paymentCred, createOnlinePayment } = onlinePayment();
const route = useRoute();

onMounted(() => {
    fetchPurchaseData(route.params.order_id);
});
</script>

<template>
    <section class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="w-[90%] md:w-[80%] lg:w-[70%] border border-blue-500 grid md:grid-cols-2 shadow-md bg-white">
            <!-- LEFT SIDE -->
            <div class="p-8">
                <h1 class="text-3xl font-bold mb-6 text-center md:text-left">LapTopia</h1>

                <h2 class="text-2xl font-semibold mb-2">Payment</h2>
                <p class="text-sm text-gray-500 mb-6">
                    Choose payment method, All transactions are encrypted.
                </p>

                <div class="space-y-4">
                    <!-- Credit/Debit -->
                    <div
                        class="flex items-center justify-between border rounded-md px-4 py-3 hover:border-blue-400 cursor-pointer">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="radio" name="payment_method" class="w-5 h-5 accent-blue-600" />
                            <span class="font-medium text-gray-700">Credit Card/Debit Card</span>
                        </label>
                    </div>

                    <!-- Gcash -->
                    <div
                        class="flex items-center justify-between border rounded-md px-4 py-3 hover:border-blue-400 cursor-pointer">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="radio" name="payment_method" class="w-5 h-5 accent-blue-600" />
                            <span class="font-medium text-gray-700">Gcash via Paymongo</span>
                        </label>
                    </div>

                    <div
                        class="flex items-center justify-between border rounded-md px-4 py-3 hover:border-blue-400 cursor-pointer">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="radio" name="payment_method" class="w-5 h-5 accent-blue-600" />
                            <span class="font-medium text-gray-700">Paymaya via Paymongo</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="p-8 border-l border-gray-200" v-if="purchaseData">
                <h2 class="text-xl font-semibold text-green-600 mb-4">
                    PAYMENT AMOUNT: <span class="text-green-700 font-bold">₱200,000</span>
                </h2>

                <div class="mb-6 text-gray-500 text-sm">
                    <p>Payment for: {{ purchaseData.supplier.supplier_name }}</p>
                    <p class="font-medium text-gray-700">John Pork - #johnpork22</p>
                </div>

                <form @submit.prevent="createOnlinePayment" class="space-y-4">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Email:</label>
                        <input type="email" v-model="paymentCred.email" placeholder="Enter your email"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-green-200" />
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Number:</label>
                        <input type="text" v-model="paymentCred.number" placeholder="Enter your number"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-green-200" />
                    </div>

                    <button type="submit"
                        class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-md shadow transition">
                        CONFIRM
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>
