<script setup>
import { Button } from "@/components/ui/button"
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { defineProps, defineEmits, watch, ref } from "vue"

const emit = defineEmits(['stockIn']);

const handleStockIn = async (product_id, reference_no, purchase_id) => {
    emit('stockIn', product_id, reference_no, purchase_id);
}

const props = defineProps({
    purchaseData: Object
});


</script>
<template>
    <Dialog>
        <DialogContent class="sm:max-w-[800px] rounded-2xl shadow-lg">
            <DialogHeader>
                <DialogTitle class="text-xl font-semibold text-gray-800">
                    Received Order Items
                </DialogTitle>
                <DialogDescription class="text-gray-600">
                    Below is a summary of the items you’ve received from the supplier. Please review the details
                    carefully before confirming to stock them into your inventory.
                </DialogDescription>
            </DialogHeader>

            <div class="grid gap-6 py-4">
                <!-- Order Information -->
                <div class="border rounded-lg p-4 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">Supplier Information</h3>
                    <p><span class="font-semibold">Supplier Name:</span> {{ props.purchaseData.supplier.supplier_name }}
                    </p>
                    <p><span class="font-semibold">Supplier Email:</span> {{ props.purchaseData.supplier.email }}</p>
                    <p><span class="font-semibold">Supplier Contact Person:</span> {{
                        props.purchaseData.supplier.contact_person }}</p>
                    <p><span class="font-semibold">Supplier Address:</span> {{ props.purchaseData.supplier.address }}
                    </p>
                </div>

                <!-- Items Table -->
                <div class="border rounded-lg p-4 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Items Received</h3>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">Item Name</th>
                                <th class="py-2 text-center">Quantity</th>
                                <th class="py-2 text-right">Unit Price</th>
                                <th class="py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in props.purchaseData.items" class="border-b hover:bg-gray-100">
                                <td class="py-2">{{ item.product.product_name }}</td>
                                <td class="py-2">{{ item.quantity }}</td>
                                <td class="py-2 text-center">{{ item.unit_price }}</td>
                                <td class="py-2 text-right">{{ item.total }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="font-semibold">
                                <td colspan="4" class="py-3 text-right">Total Amount:</td>
                                <td class="py-3 text-right">₱{{props.purchaseData.items.reduce((sum, item) => {
                                    return sum += parseFloat(item.total);
                                }, 0) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <DialogFooter class="flex justify-end space-x-3">
                <Button type="submit" class="bg-green-600 hover:bg-green-700 text-white"
                    @click="handleStockIn(props.purchaseData.items, props.purchaseData.reference_no, props.purchaseData.encrypted_id)">
                    Stock In
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

</template>