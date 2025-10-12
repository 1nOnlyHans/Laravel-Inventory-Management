<script setup>
import AdminDashboardCard from '@/components/Cards/AdminDashboardCard.vue';
import { faArrowTrendUp, faBox, faBoxesStacked, faBoxOpen, faCartShopping, faMoneyBill, faTriangleExclamation } from '@fortawesome/free-solid-svg-icons';
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
        <div class="container mx-auto px-6 py-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-semibold text-gray-800">Dashboard Overview</h1>
            </div>

            <!-- Dashboard Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- 1. Today's Sales -->
                <admin-dashboard-card :title="'Today\'s Sales'" :icon="faMoneyBill" :value="datas.today_sales"
                    :subtitle="'Total Amount'" :is-money="true" />

                <!-- 2. Total Sales -->
                <admin-dashboard-card :title="'Total Sales'" :icon="faMoneyBill" :value="datas.total_sales"
                    :subtitle="'Overall Revenue'" :is-money="true" />

                <!-- 3. Total Stocks -->
                <admin-dashboard-card :title="'Total Stocks'" :icon="faBoxOpen" :value="datas.total_stocks"
                    :subtitle="'Units in Stock'" />

                <!-- 4. Inventory Value -->
                <admin-dashboard-card :title="'Inventory Value'" :icon="faArrowTrendUp" :value="datas.inventory_value"
                    :subtitle="'Total Inventory Value'" :is-money="true" />

                <!-- 5. Low Stocks -->
                <admin-dashboard-card :title="'Low Stocks'" :icon="faTriangleExclamation" :value="datas.low_stock"
                    :subtitle="'Low Stock Items'" />

                <!-- 6. Out of Stocks -->
                <admin-dashboard-card :title="'Out of Stocks'" :icon="faTriangleExclamation" :value="datas.no_stock"
                    :subtitle="'No Stock Items'" />

                <!-- 7. Pending Purchase Orders -->
                <admin-dashboard-card :title="'Pending Purchase Orders'" :icon="faCartShopping" :value="datas.orders"
                    :subtitle="'Awaiting Approval'" />

                <!-- 8. Received Items Today -->
                <admin-dashboard-card :title="'Items Received Today'" :icon="faBoxesStacked"
                    :value="datas.received_stocks" :subtitle="'Incoming Stocks'" />
            </div>

            <!-- Sales/Inventory Chart -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Sales & Inventory Overview</h2>
                <Bar :chart-data="chartData" :chart-options="chartOptions" />
            </div>

            <!-- Recent Stock Movements -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-800 text-lg mb-4">Recent Stock Movements</h2>

                <!-- Scrollable List -->
                <div
                    class="max-h-64 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                    <div v-for="(item, index) in datas.movements" :key="item.id"
                        class="py-3 flex justify-between items-start border-b border-gray-100 last:border-none">
                        <!-- Left -->
                        <div>
                            <h3 class="font-medium text-gray-800">
                                {{ item.product.product_name }}
                            </h3>
                            <p class="text-sm text-gray-500">{{ item.reason }}</p>
                        </div>

                        <!-- Right -->
                        <div class="text-right">
                            <h3 :class="{
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
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ new Date(item.created_at).toLocaleDateString('en-US') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- View All -->
                <div class="pt-4 flex justify-center">
                    <RouterLink :to="{ name: 'Admin Stock Movements' }" class="text-brand font-medium hover:underline">
                        View All
                    </RouterLink>
                </div>
            </div>
        </div>
    </section>

</template>