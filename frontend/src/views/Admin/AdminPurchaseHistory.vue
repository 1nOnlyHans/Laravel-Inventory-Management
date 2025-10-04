<script setup>
import { getPurchases } from '@/composables/usePO';
import { computed, onMounted, ref, h, reactive } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faGear, faPen, faEye, faTrash, } from '@fortawesome/free-solid-svg-icons';
import { Button } from "@/components/ui/button";
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';

// TanStack Table
import {
    useVueTable,
    FlexRender,
    getCoreRowModel,
    createColumnHelper,
    getFilteredRowModel,
} from '@tanstack/vue-table';

import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";

const { purchases, isLoading, fetchPurchases } = getPurchases();

onMounted(() => {
    fetchPurchases();
})

const columnHelper = createColumnHelper();

const columns = [
    columnHelper.accessor(row => row.supplier.supplier_name, {
        id: "Supplier",
        header: "Supplier",
        cell: ({ row }) => {
            return row.original.supplier.supplier_name
        }
    }),
    columnHelper.accessor(row => row.items.length, {
        id: "Number of Items",
        header: "Number of Items",
        cell: info => info.getValue()
    }),
    columnHelper.accessor(row => {
        const totalQuantity = row.items.reduce((sum, item) => {
            return sum + parseInt(item.quantity);
        }, 0);
        return totalQuantity;
    }, {
        id: "Total Items Quantity",
        header: "Total Items Quantity",
        cell: ({ row }) => {
            const totalQuantity = row.original.items.reduce((sum, item) => {
                return sum + parseInt(item.quantity);
            }, 0);
            return totalQuantity;
        }
    }),
    columnHelper.accessor(row => new Date(row.order_date).toLocaleDateString('en-us', { year: "numeric", month: "short", day: "numeric" }), {
        id: "Order Date",
        header: "Order Date",
        cell: ({ row }) => {
            const formmattedDate = new Date(row.original.order_date).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
            return formmattedDate
        }
    }),
    columnHelper.accessor(row => new Date(row.expected_date).toLocaleDateString('en-us', { year: "numeric", month: "short", day: "numeric" }), {
        id: "Expected Arrival",
        header: "Expected Arrival",
        cell: ({ row }) => {
            const formmattedDate = new Date(row.original.expected_date).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
            return formmattedDate
        }
    }),
    columnHelper.accessor('status', {
        id: "Status",
        header: "Status",
        cell: info => info.getValue()
    })
];

const globalFilter = ref("");
const purchaseTable = useVueTable({
    data: computed(() => purchases.value),
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

</script>
<template>
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center p-6 space-y-3">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <p class="text-gray-600 text-base">
            Loading the latest data, please wait...
        </p>
    </section>

    <section v-else>
        <div class="container-xl mx-auto p-3">
            <h1>Purchase History</h1>
            <!-- Table -->
            <div class="flex flex-row space-x-3 w-1/2 mb-6">
                <Label>Search:</Label>
                <Input type="text" placeholder="Search here..." v-model="globalFilter" />
            </div>
            <div class="overflow-x-auto rounded-xl shadow-sm bg-white">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-white uppercase bg-accents hover:bg-accents-hover">
                        <tr class="text-center">
                            <th v-for="header in purchaseTable.getFlatHeaders()" :key="header.id"
                                class="border p-3 font-semibold tracking-wide">
                                <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                            </th>
                        </tr>
                    </thead>
                    <tbody v-if="purchases.length > 0">
                        <tr v-for="row in purchaseTable.getRowModel().rows" :key="row.id"
                            class="text-center transition-colors duration-150 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td v-for="cell in row.getVisibleCells()" :key="cell.id" class="border px-3 py-2">
                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</template>