<script setup>
import { getPurchases } from '@/composables/usePO';
import { manageStock } from '@/composables/useStock';
import { managePO } from '@/composables/usePO';
import { onlinePayment } from '@/composables/usePO';
import { computed, onMounted, ref, h, reactive } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faGear, faPen, faEye, faTrash, faPrint, } from '@fortawesome/free-solid-svg-icons';
import { Button } from "@/components/ui/button";
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import PaymentMethod from '@/components/Modals/PaymentMethod.vue';
import ReceiveItemsModal from '@/components/Modals/ReceiveItemsModal.vue';
import SupplierPurchaseLetter from '@/components/Modals/SupplierPurchaseLetter.vue';
import SupplierReceipt from '@/components/receipt/SupplierReceipt.vue';
// TanStack Table
import {
    useVueTable,
    FlexRender,
    getCoreRowModel,
    createColumnHelper,
    getFilteredRowModel,
} from '@tanstack/vue-table';
import { RegularSwal } from '@/components/Swals/useSwals';

const { purchases, isLoading, fetchPurchases } = getPurchases();
const { createPaymentRecord, updatePaymentStatus } = onlinePayment();
const { markAsDelivered } = managePO();
const { stockIn } = manageStock();
const openPayment = ref(false);
const receivedItemsModal = ref(false);
const openLetter = ref(false);
const openReceipt = ref(false);
const payment_record = ref(null);
const orderCred = reactive({
    order_id: '',
    supplier_name: '',
    amount: 0,
    items: null
});
const purchaseData = ref(null);

const handleStockIn = async (product_id, purchase_id) => {
    const delivered = await markAsDelivered(purchase_id);
    const In = await stockIn(product_id);

    if ((delivered && delivered.status === 200) && (In && In.status === 200)) {
        receivedItemsModal.value = false
        await fetchPurchases();
        RegularSwal(In.data);
    }
}
const handleCOD = async (purchase_id, payment_method, amount_paid, total_amount, order_id) => {
    if (amount_paid < total_amount) {
        alert("Enter Valid Amount");
        return;
    }
    const success = await createPaymentRecord(purchase_id, payment_method, amount_paid, total_amount, order_id);
    if (success && success.status === 200) {
        openPayment.value = false;
        await updatePaymentStatus(purchase_id);
        await fetchPurchases();
        RegularSwal(success.data);
    }
}

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
            const record = row.original;

            const isApproved = record.status === "Approved";
            const isPaid = record.payment_status === "Paid";
            const isDelivered = record.status === "Delivered";

            return h(
                "div",
                { class: "flex justify-center items-center space-x-3" },
                [
                    // 👁 View button (always visible)
                    h(
                        Button,
                        {
                            variant: "none",
                            class: "text-accents hover:text-accents-hovered",
                            onClick: () => {
                                purchaseData.value = record;
                                openLetter.value = true;
                            },
                        },
                        () => h(FontAwesomeIcon, { icon: faEye })
                    ),

                    // 🟢 Case 1: Approved but Not Paid → Pay Now
                    isApproved && !isPaid &&
                    h(
                        Button,
                        {
                            variant: "none",
                            class: "bg-green-500 text-white hover:bg-green-700",
                            onClick: () => {
                                openPayment.value = true;
                                orderCred.order_id = record.encrypted_id;
                                orderCred.supplier_name = record.supplier.supplier_name;
                                orderCred.amount = record.items.reduce(
                                    (sum, item) => sum + parseFloat(item.total),
                                    0
                                );
                                orderCred.items = record.items;
                            },
                        },
                        () => h("p", "Pay Now")
                    ),

                    // 🔵 Case 2: Paid but Not Delivered → Print + Mark as Delivered
                    isPaid && record.status !== "Delivered" && [
                        h(
                            Button,
                            {
                                variant: "none",
                                class: "bg-blue-500 text-white hover:bg-blue-700",
                                onClick: () => {
                                    payment_record.value = row.original.payment_record
                                    openReceipt.value = true
                                },
                            },
                            () => h("p", "Print Receipt")
                        ),
                        h(
                            Button,
                            {
                                variant: "none",
                                class: "bg-yellow-500 text-white hover:bg-yellow-700",
                                onClick: () => {
                                    receivedItemsModal.value = true;
                                    purchaseData.value = record;
                                },
                            },
                            () => h("p", "Mark as Delivered")
                        ),
                    ],

                    // 🧾 Case 3: Paid + Delivered → Print Receipt only
                    isPaid && isDelivered &&
                    h(
                        Button,
                        {
                            variant: "none",
                            class: "bg-blue-500 text-white hover:bg-blue-700",
                            onClick: () => {
                                payment_record.value = row.original.payment_record
                                openReceipt.value = true
                            },
                        },
                        () => h("p", "Print Receipt")
                    ),
                ]
            );
        },
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
        :amount="orderCred.amount" :items="orderCred.items" @cod="handleCOD" />
    <ReceiveItemsModal v-model:open="receivedItemsModal" :purchase-data="purchaseData" @stock-in="handleStockIn" />
    <SupplierPurchaseLetter v-model:open="openLetter" :purchase-cred="purchaseData" />
    <SupplierReceipt v-model:open="openReceipt" :payment_record="payment_record" />
</template>
