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
import Textarea from "../ui/textarea/Textarea.vue"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select"
import { onMounted, ref, watch } from "vue"

const saleCred = ref({
    customer_name: '',
    email: '',
    phone: '',
    address: '',
    payment_method: '',
    amount_received: '',
    items: JSON.parse(localStorage.getItem('cart')) || [],
    notes: '',
});

const props = defineProps({
    items: Array
});

const emit = defineEmits(['sale']);

const handleEmit = async (saleCred) => {
    const totalPrice = props.items.reduce((sum, item) => {
        return sum += Number(item.total_price)
    }, 0);
    if (saleCred.amount_received < 0) {
        alert("Amount received cannot be negative.");
        saleCred.amount_received = 0;
        return;
    }

    if (saleCred.amount_received < totalPrice) {
        return alert('Invalid Amount');
    }

    await emit('sale', saleCred);
};


watch(() => props.items, (newItem) => {
    saleCred.value.items = newItem.map((item) => {
        return item;
    });
}, {
    deep: true,
    immediate: true
})
</script>
<template>
    <Dialog>
        <DialogContent class="sm:max-w-[600px] rounded-2xl">
            <DialogHeader>
                <DialogTitle class="text-xl font-semibold text-gray-800">
                    Checkout
                </DialogTitle>
                <DialogDescription class="text-gray-500 text-sm">

                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="handleEmit(saleCred)" id="payment-form">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-6">
                    <!-- Customer Name -->
                    <div class="flex flex-col gap-1">
                        <Label for="name" class="text-sm font-medium text-gray-700">
                            Customer Name
                        </Label>
                        <Input id="name" placeholder="Enter full name" v-model="saleCred.customer_name" required />
                    </div>

                    <!-- Email -->
                    <div class="flex flex-col gap-1">
                        <Label for="email" class="text-sm font-medium text-gray-700">
                            Email
                        </Label>
                        <Input id="email" type="email" placeholder="example@email.com" v-model="saleCred.email"
                            required />
                    </div>

                    <!-- Phone -->
                    <div class="flex flex-col gap-1">
                        <Label for="phone" class="text-sm font-medium text-gray-700">
                            Phone
                        </Label>
                        <Input id="phone" type="tel" placeholder="+63 900 000 0000" v-model="saleCred.phone" required
                            @input="saleCred.phone = saleCred.phone.replace(/[^0-9+]/g, '')" />
                    </div>

                    <div class="flex flex-col gap-1">
                        <Label for="phone" class="text-sm font-medium text-gray-700">
                            Address
                        </Label>
                        <Textarea v-model="saleCred.address" required />
                    </div>

                    <!-- Payment Method -->
                    <div class="flex flex-col gap-1">
                        <Label for="payment" class="text-sm font-medium text-gray-700">
                            Payment Method
                        </Label>
                        <Select v-model="saleCred.payment_method" required>
                            <SelectTrigger class="w-[full]">
                                <SelectValue placeholder="Select payment method" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Payments</SelectLabel>
                                    <SelectItem value="Cash">
                                        Cash
                                    </SelectItem>
                                    <SelectItem value="Gcash">
                                        Gcash
                                    </SelectItem>
                                    <SelectItem value="Paymaya">
                                        Paymaya
                                    </SelectItem>
                                    <SelectItem value="Card">
                                        Card
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Amount Received -->
                    <div class="flex flex-col gap-1">
                        <Label for="amount" class="text-sm font-medium text-gray-700">
                            Amount Received
                        </Label>
                        <Input id="amount" type="number" placeholder="₱0.00" pattern="^\d*\.?\d*$"
                            v-model="saleCred.amount_received" required min="0" />
                    </div>

                    <!-- Notes -->
                    <div class="flex flex-col gap-1 sm:col-span-2">
                        <Label for="notes" class="text-sm font-medium text-gray-700">
                            Notes
                        </Label>
                        <Input id="notes" placeholder="Additional details..." />
                    </div>
                </div>

                <div class="space-y-1">
                    <Label class="text-sm font-medium text-gray-700">Items</Label>
                    <ul
                        class="bg-gray-50 border border-gray-200 rounded-lg p-3 max-h-40 overflow-y-auto divide-y divide-gray-100">
                        <li v-for="(item, index) in props.items" :key="index"
                            class="flex justify-between py-2 text-sm text-gray-600">
                            <span class="font-medium text-gray-700">
                                {{ item.product_name }} × {{ item.quantity }}
                            </span>
                            <span class="font-semibold text-green-700">
                                ₱{{ item.total_price }}
                            </span>
                        </li>
                        <div class="flex justify-between items-center border-b">
                            <h1 class="font-medium text-gray-700">Total Price:</h1>
                            <span class="font-semibold text-green-700"> ₱{{props.items.reduce((sum, item) => {
                                return sum += Number(item.total_price)
                            }, 0)}}</span>
                        </div>
                    </ul>
                </div>
            </form>
            <!-- Form Section -->


            <DialogFooter>
                <Button type="submit" class="w-full sm:w-auto bg-brand text-white hover:bg-brand-dark"
                    form="payment-form">
                    Complete Transaction
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

</template>