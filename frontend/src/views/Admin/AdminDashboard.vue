<script setup>
import AdminDashboardCard from '@/components/Cards/AdminDashboardCard.vue';
import { faArrowTrendUp, faBox, faBoxesStacked, faBoxOpen, faCartShopping, faTriangleExclamation } from '@fortawesome/free-solid-svg-icons';
import { getDashboardDatas } from '@/composables/useDashboard';
import { computed, onMounted } from 'vue';
import Bar from '@/components/Charts/Bar.vue';
const { datas, fetchDashboardDatas } = getDashboardDatas();

const chartData = computed(() => {
    const category = datas.value.total_stocks_per_category || {}
    const colors = [
        '#3b82f6', // blue
        '#10b981', // green
        '#f59e0b', // amber
        '#ef4444', // red
        '#8b5cf6', // purple
        '#14b8a6', // teal
        '#f97316', // orange
    ]

    return {
        labels: Object.keys(category),
        datasets: [
            {
                label: 'Stocks',
                backgroundColor: Object.keys(category).map((_, i) => colors[i % colors.length]),
                borderColor: '#1f2937',
                borderWidth: 1,
                data: Object.values(category),
            },
        ],
    }
})


const chartOptions = {
    responsive: true,
    plugins: {
        legend: {
            display: true,
            position: 'bottom'
        },
        title: {
            display: true,
            text: 'Total Inventory Stocks Per Category'
        }
    }
}

onMounted(async () => {
    await fetchDashboardDatas();
})

</script>
<template>
    <section>
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-bold pb-3">Dashboard</h1>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                <admin-dashboard-card :title="'Total Stocks'" :icon="faBoxOpen" :value="datas.total_stocks"
                    :subtitle="'Units in Stock'" />
                <admin-dashboard-card :title="'Inventory Value'" :icon="faArrowTrendUp" :value="datas.inventory_value"
                    :subtitle="'Total Value'" />
                <admin-dashboard-card :title="'Received items for Today'" :icon="faBoxesStacked"
                    :value="datas.received_stocks" :subtitle="'Total Value'" />
                <admin-dashboard-card :title="'Pending Purchase Request Order'" :icon="faCartShopping"
                    :value="datas.orders" :subtitle="'Purchase Request Orders'" />
                <admin-dashboard-card :title="'Low Stocks'" :icon="faTriangleExclamation" :value="datas.low_stock"
                    :subtitle="'Low Stock Items'" />
                <admin-dashboard-card :title="'Out of Stocks'" :icon="faTriangleExclamation" :value="datas.no_stock"
                    :subtitle="'No Stock Items'" />
            </div>
            <Bar :chart-data="chartData" :chart-options="chartOptions" />
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                <h1 class="font-semibold text-gray-800 text-lg mb-3">
                    Recent Stock Movements
                </h1>

                <!-- Scrollable List -->
                <div
                    class="max-h-64 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                    <div v-for="(item, index) in datas.movements" :key="item.id"
                        class="py-3 flex justify-between items-start border-b border-gray-100 last:border-none">
                        <!-- Left side -->
                        <div>
                            <h2 class="font-medium text-gray-800">
                                {{ item.product.product_name }}
                            </h2>
                            <p class="text-sm text-gray-500">{{ item.reason }}</p>
                        </div>

                        <!-- Right side -->
                        <div class="text-right">
                            <h2 :class="{
                                'text-green-500': item.movement_type === 'Stock In',
                                'text-red-500': item.movement_type === 'Stock Out',
                                'text-yellow-500':
                                    item.movement_type !== 'Stock In' &&
                                    item.movement_type !== 'Stock Out',
                            }" class="font-semibold text-sm">
                                {{
                                    item.movement_type === 'Stock In'
                                        ? '+'
                                        : item.movement_type === 'Stock Out'
                                            ? '-'
                                            : ''
                                }}
                                {{ item.quantity }} units
                            </h2>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ new Date(item.created_at).toLocaleDateString('en-US') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- View All Button -->
                <div class="pt-3 flex justify-center">
                    <RouterLink :to="{ name: 'Admin Stock Movements' }" class="text-brand underline">View All
                    </RouterLink>
                </div>
            </div>
        </div>
    </section>
</template>