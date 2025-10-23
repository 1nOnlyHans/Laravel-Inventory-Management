<script setup>
/* -------------------------
   Imports
------------------------- */
import { ref, onMounted, computed, h } from "vue";
import {
    useVueTable,
    FlexRender,
    getCoreRowModel,
    createColumnHelper,
    getFilteredRowModel,
} from '@tanstack/vue-table';
import { getTransactions } from "@/composables/usePOS";
import { Button } from "@/components/ui/button";
import Input from "@/components/ui/input/Input.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faPrint } from "@fortawesome/free-solid-svg-icons";
import POSreceipt from "@/components/receipt/POSreceipt.vue";
/* -------------------------
   Data Fetch
------------------------- */
const { transaction, isLoading, fetchTransactions } = getTransactions();
const transactionCred = ref(null);
const openReceipt = ref(false);
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
    {
        id: "Actions",
        header: "Actions",
        cell: ({ row }) => {
            return h(
                Button, {
                variant: 'secondary',
                onClick: () => {
                    transactionCred.value = row.original
                    openReceipt.value = true;
                }
            },
                () => h(
                    FontAwesomeIcon, {
                    icon: faPrint
                }
                )
            )
        }
    }
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
    <section v-else class="mx-auto p-6">
        <div class="border">
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
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full text-sm text-gray-600">
                    <thead class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wide">
                        <tr>
                            <th v-for="header in table.getFlatHeaders()" :key="header.id"
                                class="px-4 py-3 font-medium tracking-wide text-center">
                                <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                            </th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody v-if="transaction.length > 0">
                        <tr v-for="row in table.getRowModel().rows" :key="row.id"
                            class="hover:bg-gray-50 transition-all duration-150">
                            <td v-for="cell in row.getVisibleCells()" :key="cell.id"
                                class="px-4 py-3 text-center border-t border-gray-200">
                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </td>
                        </tr>
                    </tbody>

                    <!-- Empty State -->
                    <tbody v-else>
                        <tr>
                            <td colspan="100%" class="text-center py-8 text-gray-500">
                                No purchase records found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <POSreceipt v-model:open="openReceipt" :transaction="transactionCred" />
</template>

<style scoped>
.container-xl {
    max-width: 1280px;
}
</style>
