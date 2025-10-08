<script setup>
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import Label from "../ui/label/Label.vue";
import Input from "../ui/input/Input.vue";
import Button from "../ui/button/Button.vue";
import { defineProps, onUnmounted, ref, watch, defineEmits, reactive } from "vue";
import { onlinePayment } from "@/composables/usePO";

const openPaymongoModal = ref(false);
const openCOD = ref(false);
const { paymentCred, createOnlinePayment } = onlinePayment();

const COD = () => {
    openCOD.value = !openCOD.value
    openPaymongoModal.value = false;
}
const toggleModal = () => {
    openPaymongoModal.value = !openPaymongoModal.value;
    openCOD.value = false;
};

const emit = defineEmits(['cod']);

const handleEmit = async (purchase_id, payment_method, amount_paid, total_amount, order_id) => {
    emit('cod', purchase_id, payment_method, amount_paid, total_amount, order_id);
}

const props = defineProps({
    order_id: String,
    supplier_name: String,
    amount: Number,
    items: Array
});

watch(() => [props.order_id, props.supplier_name, props.amount, props.items], () => {
    paymentCred.order_id = props.order_id
    paymentCred.name = 'LapTopia'
    paymentCred.email = 'LapTopia@gmail.com'
    paymentCred.contact = '09123456789'
    paymentCred.amount = props.amount
    paymentCred.items = props.items
}, {
    immediate: true
})

</script>

<template>
    <Dialog>
        <DialogContent v-if="!openPaymongoModal && !openCOD" class="sm:max-w-[420px] rounded-2xl shadow-xl">
            <DialogHeader class="border-b pb-3">
                <DialogTitle class="text-lg font-semibold text-gray-800">
                    Select Payment Method
                </DialogTitle>
                <DialogDescription class="text-sm text-gray-500">
                    Choose how you would like to pay for this purchase.
                </DialogDescription>
            </DialogHeader>

            <!-- STEP 1: Payment Method Selection -->
            <div class="p-6 space-y-4">
                <div class="flex flex-col sm:flex-row gap-3">
                    <Button
                        class="w-full sm:w-1/2 py-3 bg-accents text-white rounded-md text-center font-medium hover:bg-accents-hover transition-all duration-150"
                        @click="COD">
                        Cash on Delivery
                    </Button>

                    <Button @click="toggleModal"
                        class="w-full sm:w-1/2 py-3 bg-green-600 text-white rounded-md text-center font-medium hover:bg-green-700 transition-all duration-150">
                        Online Payment
                    </Button>
                </div>
            </div>
        </DialogContent>

        <DialogContent v-else-if="openPaymongoModal && !openCOD"
            class="sm:max-w-[420px] rounded-2xl shadow-xl p-0 overflow-hidden">
            <DialogHeader class="border-b px-6 py-4 bg-gray-50">
                <DialogTitle class="text-lg font-semibold text-gray-800">
                    Order Details
                </DialogTitle>
                <DialogDescription class="text-sm text-gray-500">
                    Review your order details before proceeding to PayMongo.
                </DialogDescription>
            </DialogHeader>

            <div class="p-6 space-y-4">
                <!-- Supplier -->
                <div class="space-y-1">
                    <Label class="text-sm font-medium text-gray-700">Supplier</Label>
                    <Input type="text" readonly v-model="props.supplier_name" placeholder="Supplier Name"
                        class="bg-gray-50 border-gray-200" />
                </div>

                <!-- Items -->
                <div class="space-y-1">
                    <Label class="text-sm font-medium text-gray-700">Items</Label>
                    <ul class="bg-gray-50 border border-gray-200 rounded-md p-3 max-h-32 overflow-y-auto">
                        <li v-for="(item, index) in props.items" :key="index"
                            class="text-gray-600 text-sm flex justify-between py-0.5">
                            <span>{{ item.product.product_name }} × {{ item.quantity }}</span>
                            <span>₱{{ item.total }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Total -->
                <div class="space-y-1">
                    <Label class="text-sm font-medium text-gray-700">Total Amount</Label>
                    <Input type="text" readonly v-model="props.amount" placeholder="₱0.00"
                        class="bg-gray-50 border-gray-200 font-semibold text-green-700" />
                </div>

                <DialogFooter class="flex justify-between gap-3 pt-4">
                    <Button variant="outline" class="w-1/2 py-2 border-gray-300 hover:bg-gray-100 transition"
                        @click="toggleModal">
                        Back
                    </Button>
                    <Button class="w-1/2 bg-green-600 text-white py-2 hover:bg-green-700 transition"
                        @click="createOnlinePayment(paymentCred)">
                        Proceed to PayMongo
                    </Button>
                </DialogFooter>
            </div>
        </DialogContent>

        <DialogContent v-else-if="openCOD && !openPaymongoModal"
            class="sm:max-w-[420px] rounded-2xl shadow-lg p-0 overflow-hidden border border-gray-200 bg-white">
            <!-- Header -->
            <DialogHeader class="border-b bg-gray-50 px-6 py-4">
                <DialogTitle class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    Create Payment Record
                </DialogTitle>
                <DialogDescription class="text-sm text-gray-500">
                    Review your order details before finalizing payment.
                </DialogDescription>
            </DialogHeader>

            <!-- Body -->
            <div class="p-6 space-y-5">
                <!-- Supplier -->
                <div class="space-y-1">
                    <Label class="text-sm font-medium text-gray-700">Supplier</Label>
                    <Input type="text" readonly v-model="props.supplier_name" placeholder="Supplier Name"
                        class="bg-gray-50 border-gray-200 text-gray-800 cursor-not-allowed" />
                </div>

                <!-- Items List -->
                <div class="space-y-1">
                    <Label class="text-sm font-medium text-gray-700">Items</Label>
                    <ul
                        class="bg-gray-50 border border-gray-200 rounded-lg p-3 max-h-40 overflow-y-auto divide-y divide-gray-100">
                        <li v-for="(item, index) in props.items" :key="index"
                            class="flex justify-between py-2 text-sm text-gray-600">
                            <span class="font-medium text-gray-700">
                                {{ item.product.product_name }} × {{ item.quantity }}
                            </span>
                            <span class="font-semibold text-green-700">
                                ₱{{ item.total }}
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="space-y-1">
                    <Label class="text-sm font-medium text-gray-700">Total Amount to Pay:</Label>
                    <Input type="text" readonly v-model="paymentCred.amount" placeholder="₱0.00"
                        class="bg-green-50 border-green-200 font-semibold text-green-800" />
                </div>

                <!-- Total Amount -->
                <div class="space-y-1">
                    <Label class="text-sm font-medium text-gray-700">Amount</Label>
                    <Input type="text" placeholder="₱0.00" v-model="paymentCred.amount_pay" required />
                </div>

                <!-- Footer Buttons -->
                <DialogFooter class="flex justify-between gap-3 pt-4">
                    <Button variant="outline" class="w-1/2 py-2 border-gray-300 hover:bg-gray-100 transition-all"
                        @click="COD">
                        Back
                    </Button>
                    <Button class="w-1/2 bg-green-600 text-white py-2 hover:bg-green-700 transition-all"
                        @click="handleEmit(paymentCred.order_id, 'Cash', paymentCred.amount_pay, paymentCred.amount, paymentCred.order_id)">
                        Confirm Payment
                    </Button>
                </DialogFooter>
            </div>
        </DialogContent>

    </Dialog>
</template>
