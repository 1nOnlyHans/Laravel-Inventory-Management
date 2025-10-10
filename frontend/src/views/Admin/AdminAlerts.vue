<script setup>
import { getAlerts } from '@/composables/useStockAlert';
import { onMounted } from 'vue';

const { alerts, isLoading, fetchAlerts } = getAlerts();

onMounted(() => {
    fetchAlerts();
})

</script>
<template>
    <section>
        <div class="container-xl mx-auto p-4">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-5">
                    <h1 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-bell text-yellow-500"></i>
                        Stock Alerts
                    </h1>
                    <span class="text-sm text-gray-500">
                        {{ alerts.length }} active alert<span v-if="alerts.length !== 1">s</span>
                    </span>
                </div>

                <!-- Alerts List -->
                <div v-if="alerts && alerts.length > 0" class="divide-y divide-gray-100">
                    <div v-for="(item, index) in alerts" :key="item.id"
                        class="flex justify-between items-start py-4 hover:bg-gray-50 transition-colors duration-200 rounded-lg px-2">
                        <!-- Left Side -->
                        <div class="flex flex-col">
                            <h2 class="font-medium text-gray-800 leading-snug">
                                {{ item.alert_message }}
                            </h2>
                            <p class="text-sm text-gray-500 mt-1">
                                <span class="font-medium">{{ item.product.product_name }}</span> —
                                Only <span class="text-red-500 font-semibold">{{ item.product.product_quantity }}</span>
                                remaining
                            </p>
                        </div>

                        <!-- Right Side -->
                        <div class="text-right">
                            <span :class="{
                                'bg-green-100 text-green-700': item.status === 'Resolved',
                                'bg-red-100 text-red-600': item.status === 'Pending',
                            }" class="px-2 py-1 text-xs font-semibold rounded-full">
                                {{ item.status }}
                            </span>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ new Date(item.created_at).toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric',
                                }) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-10 text-gray-500">
                    <i class="fa-solid fa-circle-check text-3xl text-green-500 mb-2"></i>
                    <p>No active stock alerts.</p>
                </div>
            </div>
        </div>

    </section>
</template>