<script setup>
import { defineProps } from 'vue';
import Button from '../ui/button/Button.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faPrint, faReceipt } from '@fortawesome/free-solid-svg-icons';
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
    transaction: Object
});

const printReceipt = (elementId) => {
    const elementToPrint = document.getElementById(elementId);

    if (elementToPrint) {
        const printWindow = window.open('', '_blank', 'width=800,height=600');
        if (!printWindow) {
            console.error('Failed to open print window.');
            return;
        }

        // Copy styles and content
        printWindow.document.write(`
            <html>
                <head>
                    <title>Receipt</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        h1, h2, h3, h4, h5, h6 { margin: 0; }
                        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                        .text-center { text-align: center; }
                        .border-t { border-top: 1px solid #ccc; }
                    </style>
                </head>
                <body>
                    ${elementToPrint.innerHTML}
                </body>
            </html>
        `);

        printWindow.document.close();

        // Wait for content to load before printing
        printWindow.onload = () => {
            printWindow.focus();
            printWindow.print();
            printWindow.onafterprint = () => {
                printWindow.close();
            };
        };
    } else {
        console.error(`Element with ID '${elementId}' not found.`);
    }
};
</script>
<template>
    <Dialog>
        <DialogContent class="sm:max-w-[500px] p-0 overflow-hidden rounded-2xl shadow-xl">
            <!-- Dialog Header -->
            <DialogHeader class="bg-gray-50 border-b px-6 py-4 text-center">
                <DialogTitle class="text-2xl font-bold text-gray-800">Transaction Receipt</DialogTitle>
                <DialogDescription class="text-sm text-gray-500">
                    Official receipt.
                </DialogDescription>
            </DialogHeader>

            <section id="receipt">
                <!-- Receipt Body -->
                <div class="px-6 py-4 text-sm text-gray-700 space-y-4">
                    <!-- Customer Details -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-2">
                            Customer Information
                        </h3>
                        <div class="space-y-1">
                            <div class="flex justify-between">
                                <span class="font-medium">Name:</span>
                                <span class="text-gray-900">{{ transaction.customer?.name || 'N/A' }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="font-medium">Email:</span>
                                <span class="text-gray-900">{{ transaction.customer?.email || 'N/A' }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="font-medium">Phone:</span>
                                <span class="text-gray-900">{{ transaction.customer?.phone || 'N/A' }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="font-medium">Address:</span>
                                <span class="text-gray-900 text-right w-1/2 break-words">
                                    {{ transaction.customer?.address || 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-300" />
                    <!-- Transaction Details -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-2">
                            Transaction Details
                        </h3>

                        <div class="flex justify-between">
                            <span class="font-medium">Reference No:</span>
                            <span class="font-semibold text-gray-900">{{ transaction.reference_no }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="font-medium">Payment Method:</span>
                            <span class="font-semibold text-gray-900">{{ transaction.payment_method }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="font-medium">Total Amount:</span>
                            <span class="font-semibold text-gray-900">
                                ₱{{ Number(transaction.total_amount).toLocaleString() }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="font-medium">Amount Paid:</span>
                            <span class="font-semibold text-green-700">
                                ₱{{ Number(transaction.amount_paid).toLocaleString() }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="font-medium">Change:</span>
                            <span class="font-semibold text-blue-700">
                                ₱{{ (transaction.amount_paid - transaction.total_amount).toLocaleString() }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="font-medium">Date Issued:</span>
                            <span class="text-gray-900">
                                {{ new Date(transaction.created_at).toLocaleString('en-PH', {
                                    dateStyle: 'medium',
                                    timeStyle: 'short'
                                }) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer Message -->
                <div class="border-t px-6 py-4 text-center">
                    <p class="text-sm text-gray-700 italic font-medium">
                        Thank you for your purchase, {{ transaction.customer?.name?.split(' ')[0] || 'Valued Customer'
                        }}!
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        This is a system-generated receipt. No signature required.
                    </p>
                </div>
            </section>

            <!-- Dialog Footer -->
            <DialogFooter class="border-t bg-gray-50 px-6 py-3 flex justify-end">
                <Button class="bg-accents hover:bg-accents-hover" @click="printReceipt('receipt')">
                    <FontAwesomeIcon :icon="faPrint" class="mr-2" /> Print Receipt
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>