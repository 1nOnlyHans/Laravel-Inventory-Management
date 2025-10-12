<script setup>
import Reports from '@/components/Cards/Reports.vue';
import { generateReports } from '@/composables/useReports';
import { faArrowDown, faArrowTrendUp, faBoxesStacked, faExclamation, faHistory, faMoneyBill } from '@fortawesome/free-solid-svg-icons';

const { generateInventoryReports, generateMovementReport, generatePurchaseHistoryReport, generateSalesReport, generateLowStockReport, generateOutOfstockReport } = generateReports();

const handleInventory = async () => {
    await generateInventoryReports()
}

const handleMovement = async () => {
    await generateMovementReport()
}

const handlePurchase = async () => {
    await generatePurchaseHistoryReport()
}

const handleSales = async () => {
    await generateSalesReport()
}

const handleLowStock = async () => {
    await generateLowStockReport()
}

const handleOutOfStock = async () => {
    await generateOutOfstockReport()
}
</script>
<template>
    <section class="bg-gray-50 py-12">
        <div class="container-xl mx-auto px-6">
            <!-- Header -->
            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold text-gray-900">Reports & Analytics</h1>
                <p class="mt-2 text-lg text-gray-500 font-light">
                    Generate Reports
                </p>
            </div>

            <!-- Report Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 ">
                <Reports :title="'Inventory Valuation'" :description="'Total Inventory Items Value'"
                    :icon="faBoxesStacked" @generate="handleInventory"
                    class="hover:shadow-lg transition duration-300 rounded-xl bg-white p-5" />

                <Reports :title="'Purchase History'" :description="'Summary of Purchases'" :icon="faHistory"
                    @generate="handlePurchase"
                    class="hover:shadow-lg transition duration-300 rounded-xl bg-white p-5" />

                <Reports :title="'Stock Movements'" :description="'Summary of Stock Movements'" :icon="faArrowTrendUp"
                    @generate="handleMovement"
                    class="hover:shadow-lg transition duration-300 rounded-xl bg-white p-5" />

                <Reports :title="'Sales Report'" :description="'Summary of Sales'" :icon="faMoneyBill"
                    @generate="handleSales" class="hover:shadow-lg transition duration-300 rounded-xl bg-white p-5" />

                <Reports :title="'Low Stocks'" :description="'Low Stock Items'" :icon="faArrowDown"
                    @generate="handleLowStock"
                    class="hover:shadow-lg transition duration-300 rounded-xl bg-white p-5" />

                <Reports :title="'Out of Stocks'" :description="'Out of Stock Items'" :icon="faExclamation"
                    @generate="handleOutOfStock"
                    class="hover:shadow-lg transition duration-300 rounded-xl bg-white p-5" />
            </div>
        </div>
    </section>

</template>