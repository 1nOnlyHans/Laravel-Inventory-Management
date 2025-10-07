<script setup>
import { getPurchases } from '@/composables/usePO';
import { computed, onMounted, ref, h, reactive } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faGear, faPen, faEye, faTrash, faPrint, } from '@fortawesome/free-solid-svg-icons';
import { Button } from "@/components/ui/button";
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import PaymentMethod from '@/components/Modals/PaymentMethod.vue';
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
import { icon } from '@fortawesome/fontawesome-svg-core';
import { faCashApp, faPaypal } from '@fortawesome/free-brands-svg-icons';

const { purchases, isLoading, fetchPurchases } = getPurchases();
const openPayment = ref(false);
const orderCred = reactive({
    order_id: '',
    supplier_name: '',
    amount: 0,
    items: null
});
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
    columnHelper.accessor(row => row.items.reduce((sum, item) => {
        const total = sum += parseFloat(item.total)
        return parseFloat(total)
    }, 0), {
        id: "Total Amount",
        header: "Total Amount",
        cell: info => info.getValue()
    }),
    columnHelper.accessor('status', {
        id: "Status",
        header: "Status",
        cell: info => info.getValue()
    }),
    {
        id: "Actions",
        header: "Actions",
        cell: ({ row }) => {
            return h(
                "div", {
                class: 'flex justify-center items-center space-x-6'
            },
                [
                    h(
                        Button, {
                        variant: 'none',
                        class: "text-accents hover:text-accents-hovered"
                    }
                        , () => h(
                            FontAwesomeIcon, {
                            icon: faPrint
                        }
                        )
                    ),
                    row.original.status === 'Approved' ?
                        h(
                            Button, {
                            variant: 'none',
                            class: "bg-green-500 text-white hover:bg-green-900",
                            onClick: () => {
                                openPayment.value = true
                                orderCred.order_id = row.original.encrypted_id
                                orderCred.supplier_name = row.original.supplier.supplier_name
                                orderCred.amount = row.original.items.reduce((sum, item) => {
                                    return sum += parseFloat(item.total);
                                }, 0);
                                orderCred.items = row.original.items
                            }
                        }
                            , () => h(
                                "p", [
                                "Pay Now"
                            ]
                            )
                        ) : '',
                ]
            )
        }
    }
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
    <!-- Loading State -->
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center p-6 space-y-4">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <p class="text-gray-500 text-base font-medium">
            Loading the latest purchase records...
        </p>
    </section>

    <!-- Main Table Section -->
    <section v-else class="min-h-screen bg-gray-50 py-10 px-6">
        <div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-sm p-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Purchase History</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Review all purchase orders and their current statuses.
                    </p>
                </div>

                <!-- Search Bar -->
                <div class="flex items-center space-x-3 mt-4 md:mt-0">
                    <Label class="text-gray-700 font-medium">Search:</Label>
                    <Input type="text" placeholder="Search by supplier or status..." v-model="globalFilter"
                        class="w-64 border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg" />
                </div>
            </div>

            <!-- Table Wrapper -->
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full text-sm text-gray-600">
                    <thead class="bg-blue-600 text-white">
                        <tr>
                            <th v-for="header in purchaseTable.getFlatHeaders()" :key="header.id"
                                class="px-4 py-3 font-medium tracking-wide text-center">
                                <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                            </th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody v-if="purchases.length > 0">
                        <tr v-for="row in purchaseTable.getRowModel().rows" :key="row.id"
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

    <PaymentMethod v-model:open="openPayment" :order_id="orderCred.order_id" :supplier_name="orderCred.supplier_name"
        :amount="orderCred.amount" :items="orderCred.items" />
</template>
