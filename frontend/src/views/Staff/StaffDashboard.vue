<script setup>
import AdminDashboardCard from '@/components/Cards/AdminDashboardCard.vue';
import { POSdashboard } from '@/composables/useDashboard';
import { faArrowsLeftRight, faBoxesStacked, faMoneyBill } from '@fortawesome/free-solid-svg-icons';
import { onMounted, computed } from 'vue';
import Bar from '@/components/Charts/Bar.vue';
const { datas, fetchDashboardDatas } = POSdashboard();

const chartData = computed(() => {
    const category = (datas.value?.top_selling ?? []).map((item) => item.product_name);
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
        labels: category,
        datasets: [
            {
                label: 'Stocks',
                backgroundColor: Object.keys(category).map((_, i) => colors[i % colors.length]),
                borderColor: '#1f2937',
                borderWidth: 1,
                data: (datas.value?.top_selling ?? []).map((item) => item.total_sold ?? 0),
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
            text: 'Top Selling Products'
        }
    }
}

onMounted(async () => {
    await fetchDashboardDatas();
});

</script>
<template>
    <section>
        <div class="container-xl mx-auto p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <AdminDashboardCard title="Today's Sales" :subtitle="'Total Amount'" :icon="faMoneyBill"
                    :value="datas.today_sales" :is-money="true" />
                <AdminDashboardCard :title="'Total Sales'" :subtitle="'Total Amount'" :icon="faMoneyBill"
                    :value="datas.total_sales" :is-money="true" />
                <AdminDashboardCard :title="'Total Items Sold'" :subtitle="'Total Units'" :icon="faBoxesStacked"
                    :value="datas.total_items_sold" />
                <AdminDashboardCard :title="'Total Transactions Today'" :subtitle="'Total'" :icon="faArrowsLeftRight"
                    :value="datas.total_transactions_today" />
            </div>
        </div>
        <Bar :chart-data="chartData" :chart-options="chartOptions" />
    </section>
</template>