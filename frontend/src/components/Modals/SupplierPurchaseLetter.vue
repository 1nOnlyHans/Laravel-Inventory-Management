<script setup>
import { defineProps } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog"
const props = defineProps({
    purchaseCred: Object
});

</script>
<template>
    <Dialog v-if="props.purchaseCred">
        <DialogContent class="sm:max-w-[1200px] grid-rows-[auto_minmax(0,1fr)_auto] p-0 max-h-[90dvh]">
            <DialogHeader class="p-6 pb-0 border-b">
                <DialogTitle class="text-xl font-semibold">Purchase Order Overview</DialogTitle>
                <DialogDescription>
                    A summary of your purchase request and supplier details.
                </DialogDescription>
            </DialogHeader>
            <div class="grid gap-4 py-4 overflow-y-auto px-6">
                <!-- Letter Title -->
                <h1 class="text-center font-bold text-3xl text-blue-700">
                    LapTopia PURCHASE ORDER REQUEST
                </h1>

                <!-- Company Details -->
                <div class="flex flex-col space-y-1 text-gray-700">
                    <h2 class="font-bold text-2xl">LapTopia</h2>
                    <p class="font-medium">Barangay Jlectronics Street</p>
                    <p class="font-medium">Bacoor, Cavite 4102</p>
                    <p class="font-medium">Date: {{ props.purchaseCred.order_date }}</p>
                </div>

                <!-- Supplier Details -->
                <div v-if="props.purchaseCred.supplier" class="flex flex-col space-y-1 mt-6 text-gray-700">
                    <h2 class="font-bold text-2xl">{{ props.purchaseCred.supplier_name }}</h2>
                    <p class="font-medium">{{ props.purchaseCred.address }}</p>
                </div>

                <!-- Letter Body -->
                <div class="flex flex-col space-y-4 mt-8 leading-relaxed text-gray-700">
                    <p class="italic">Dear {{ props.purchaseCred.supplier_name || 'Supplier' }},</p>

                    <p>
                        We at <span class="font-bold">LapTopia</span> are pleased to formally request
                        the supply of computer units and related accessories. Kindly review the
                        details of our order below:
                    </p>

                    <!-- Order Details Table -->
                    <table class="w-full border border-gray-300 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">Product Name</th>
                                <th class="border px-3 py-2 text-center">Quantity</th>
                                <th class="border px-3 py-2 text-center">Unit Price</th>
                                <th class="border px-3 py-2 text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in props.purchaseCred.items" :key="index">
                                <td class="border px-3 py-2">{{ item.product.product_name }}</td>
                                <td class="border px-3 py-2 text-center">{{ item.quantity }}</td>
                                <td class="border px-3 py-2 text-center">₱{{ item.unit_price }}</td>
                                <td class="border px-3 py-2 text-center">₱{{ (item.unit_price *
                                    item.quantity).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="font-semibold text-right mt-3">
                        Grand Total: ₱{{props.purchaseCred.items.reduce((sum, item) => {
                            return sum += parseFloat(item.total);
                        }, 0)}}
                    </p>

                    <p>
                        We kindly request delivery of the above-mentioned items on or before
                        <span class="font-semibold">{{ props.purchaseCred.expected_date }}</span>.
                        Please ensure all products are in good condition and meet our specifications.
                    </p>

                    <p>
                        Should you have any clarifications, feel free to reach us at
                        <span class="font-semibold">LapTopia@gmail.com</span> or call
                        <span class="font-semibold">09123456789</span>.
                    </p>

                    <p class="mt-6">
                        Thank you for your continued support and partnership. We look forward
                        to your confirmation and a successful transaction.
                    </p>

                    <p class="mt-10">
                        Sincerely,<br />
                        <span class="font-bold">LapTopia Procurement Team</span>
                    </p>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>