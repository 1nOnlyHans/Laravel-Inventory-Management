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
    payment_record: Object
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
                    <title>Order Receipt</title>
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
                <DialogTitle class="text-2xl font-bold text-gray-800">Order Receipt</DialogTitle>
                <DialogDescription class="text-sm text-gray-500">
                    Official payment record issued by LapTopia Inventory Management System.
                </DialogDescription>
            </DialogHeader>

            <section id="receipt">
                <!-- Receipt Body -->
                <div class="px-6 py-4 text-sm text-gray-700 space-y-3">
                    <div class="flex justify-between">
                        <span class="font-medium">Reference No:</span>
                        <span class="font-semibold text-gray-900">{{ payment_record.reference_no }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="font-medium">Payment Method:</span>
                        <span class="font-semibold text-gray-900">{{ payment_record.payment_method }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="font-medium">Total Amount:</span>
                        <span class="font-semibold text-gray-900">
                            ₱{{ Number(payment_record.total_amount).toLocaleString() }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="font-medium">Amount Paid:</span>
                        <span class="font-semibold text-green-700">
                            ₱{{ Number(payment_record.amount_paid).toLocaleString() }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="font-medium">Date Issued:</span>
                        <span class="text-gray-900">
                            {{ new Date(payment_record.created_at).toLocaleString('en-PH', {
                                dateStyle: 'medium',
                                timeStyle: 'short'
                            }) }}
                        </span>
                    </div>
                </div>

                <!-- Footer Message -->
                <div class="border-t px-6 py-4 text-center">
                    <p class="text-sm text-gray-600 italic">
                        Payment successfully sent to supplier. Thank you for your continued partnership!
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