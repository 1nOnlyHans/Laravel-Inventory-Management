<script setup>
/* -------------------------
   Imports
------------------------- */
import { ref, onMounted, computed } from "vue";
import { useVueTable, createColumnHelper, getCoreRowModel, getFilteredRowModel } from "@tanstack/vue-table";
import { getTransactions } from "@/composables/usePOS";
import { Button } from "@/components/ui/button";
import Input from "@/components/ui/input/Input.vue";

/* -------------------------
   Data Fetch
------------------------- */
const { transaction, isLoading, fetchTransactions } = getTransactions();

/* -------------------------
   Table Setup
------------------------- */
const columnHelper = createColumnHelper();

const columns = [
    columnHelper.accessor("reference_no", {
        header: "Reference No",
        cell: (info) => info.getValue(),
    }),
    columnHelper.accessor((row) => row.customer?.name || "Unknown", {
        id: "customer_name",
        header: "Customer Name",
        cell: (info) => info.getValue(),
    }),
    columnHelper.accessor("payment_method", {
        header: "Payment Method",
        cell: (info) => info.getValue(),
    }),
    columnHelper.accessor("amount_paid", {
        header: "Amount Paid",
        cell: (info) =>
            `₱${parseFloat(info.getValue() || 0).toLocaleString()}`,
    }),
    columnHelper.accessor("total_amount", {
        header: "Total Amount",
        cell: (info) =>
            `₱${parseFloat(info.getValue() || 0).toLocaleString()}`,
    }),
    columnHelper.accessor(row => new Date(row.created_at).toLocaleDateString('en-us', { year: "numeric", month: "short", day: "numeric" }), {
        id: "Order Date",
        header: "Order Date",
        cell: ({ row }) => {
            const formmattedDate = new Date(row.original.created_at).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
            return formmattedDate
        }
    }),
];

/* -------------------------
   Reactive State
------------------------- */
const globalFilter = ref("");
const table = useVueTable({
    data: computed(() => transaction.value),
    columns,
    state: {
        get globalFilter() {
            return globalFilter.value
        }
    },
    onGlobalFilterChange: value => {
        globalFilter.value = value
    },
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel()
});

/* -------------------------
   Lifecycle
------------------------- */
onMounted(() => {
    fetchTransactions();
});
</script>

<template>
    <!-- Loading State -->
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center p-6 space-y-3">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <p class="text-gray-600 text-base">Loading sale transactions...</p>
    </section>

    <!-- Main Content -->
    <section v-else class="container-xl mx-auto p-6">
        <div class="bg-white shadow-lg rounded-lg border">
            <!-- Header -->
            <div class="text-xs uppercase tracking-wide px-6 py-4 flex justify-between items-center rounded-t-lg">
                <h1 class="font-bold text-2xl tracking-wide text-gray-800">
                    Sale Transactions
                </h1>
                <span class="text-sm opacity-80">Generated: {{ new Date().toLocaleDateString() }}</span>
            </div>

            <!-- Filter and Controls -->
            <div class="flex justify-between items-center p-6 border-b bg-gray-50">
                <Input type="text" placeholder="Search by reference or customer..." v-model="globalFilter"
                    class="w-1/3" />
                <Button @click="() => {
                    globalFilter = ''
                    fetchTransactions();
                }">Refresh</Button>
            </div>

            <!-- Transactions Table -->
            <div class="p-6 overflow-x-auto">
                <table class="w-full border border-gray-300 rounded-lg overflow-hidden text-sm">
                    <thead class="bg-gray-100 text-gray-700 uppercase tracking-wide">
                        <tr>
                            <th v-for="header in table.getHeaderGroups()[0].headers" :key="header.id"
                                class="border px-4 py-3 text-left font-semibold">
                                {{ header.column.columnDef.header }}
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="row in table.getRowModel().rows" :key="row.id"
                            class="hover:bg-gray-50 transition-colors">
                            <td v-for="cell in row.getVisibleCells()" :key="cell.id"
                                class="border px-4 py-2 text-gray-800">
                                {{ cell.getValue() }}
                            </td>
                        </tr>

                        <!-- Empty State -->
                        <tr v-if="table.getRowModel().rows.length === 0">
                            <td colspan="6" class="text-center py-4 text-gray-500 border">
                                No transactions found.
                            </td>
                        </tr>
                    </tbody>

                    <tfoot class="bg-gray-50 text-sm">
                        <tr>
                            <th colspan="3" class="text-right px-4 py-2 font-medium">
                                Total Transactions:
                            </th>
                            <td colspan="3" class="px-4 py-2 text-center font-semibold">
                                {{ transaction.length }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
</template>

<style scoped>
.container-xl {
    max-width: 1280px;
}
</style>
