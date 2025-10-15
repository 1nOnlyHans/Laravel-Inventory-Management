<script setup>
/* -------------------------
   Imports
------------------------- */
import { onMounted, watch, ref, computed, reactive } from 'vue';

import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

import {
    NumberField,
    NumberFieldContent,
    NumberFieldDecrement,
    NumberFieldIncrement,
    NumberFieldInput,
} from "@/components/ui/number-field"

import { Button } from "@/components/ui/button";

import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";

import { ConfirmationSwal, RegularSwal } from '@/components/Swals/useSwals';
import { getSuppliers } from '@/composables/useSuppliers';
import { managePO } from '@/composables/usePO';
import { sendPO } from '@/composables/usePO';
import { getProducts } from '@/composables/useProducts';

/* -------------------------
   Composables
------------------------- */
const { suppliers, fetchSuppliers } = getSuppliers();
const { orderItems, itemCred, purchaseCred } = managePO();
const { products, fetchProductsBySupplier } = getProducts();
const { addPurchaseOrder, isLoading } = sendPO();

/* -------------------------
   Reactive State
------------------------- */
const openLetter = ref(false);
const letter = reactive({
    suppliers_name: '',
    address: '',
});

/* -------------------------
   Lifecycle
------------------------- */
onMounted(async () => {
    await fetchSuppliers();
});

/* -------------------------
   Computed
------------------------- */
const totalAmount = computed(() => {
    return orderItems.value.reduce((sum, item) => {
        const price = parseFloat(item.unit_price) || 0;
        const qty = parseInt(item.quantity) || 0;
        return sum + price * qty;
    }, 0);
});

/* -------------------------
   Methods
------------------------- */
const handleAddProduct = () => {
    const selectedProduct = products.value.find(
        (p) => p.encrypted_id === itemCred.product_id
    );

    orderItems.value.push({
        product_id: itemCred.product_id,
        product_name: selectedProduct ? selectedProduct.product_name : "Unknown Product",
        unit_price: itemCred.unit_price,
        quantity: itemCred.quantity,
        total: (itemCred.unit_price * itemCred.quantity).toFixed(2),
    });

    // Reset inputs
    itemCred.product_id = "";
    itemCred.unit_price = 0;
    itemCred.quantity = 0;
};

const handleRemoveProduct = (index) => {
    orderItems.value.splice(index, 1);
};

const toggleLetter = () => {
    openLetter.value = true;
};

const handleOrder = async (purchaseCred) => {
    const success = await addPurchaseOrder(purchaseCred);
    if (success && success.status === 200) {
        await fetchSuppliers();
        RegularSwal(success.data);

        openLetter.value = false;
        orderItems.value = [];
        purchaseCred.supplier_id = '';
        purchaseCred.order_date = null;
        purchaseCred.expected_date = null;
        purchaseCred.items = [];
    }
};

/* -------------------------
   Watchers
------------------------- */
watch(
    () => [purchaseCred.supplier_id],
    async () => {
        if (purchaseCred.supplier_id !== '') {
            await fetchProductsBySupplier(purchaseCred.supplier_id);

            const selectedSupplier = suppliers.value.filter((supplier) => {
                return supplier.encrypted_id === purchaseCred.supplier_id;
            });

            letter.supplier_name = selectedSupplier[0].supplier_name;
            letter.address = selectedSupplier[0].address;
        }
    },
    { immediate: true }
);
</script>

<template>
    <!-- Loading State -->
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center p-6 space-y-3">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <p class="text-gray-600 text-base">
            Loading the latest data, please wait...
        </p>
    </section>

    <!-- Main Content -->
    <section v-else class="container-xl mx-auto p-6">
        <div class="bg-white shadow-lg rounded-lg border">
            <!-- Header -->
            <div class=" text-xs uppercase tracking-wide px-6 py-4 flex justify-between items-center rounded-t-lg">
                <h1 class="font-bold text-2xl tracking-wide">Purchase Order</h1>
                <span class="text-sm opacity-80">Generated: {{ new Date().toLocaleDateString() }}</span>
            </div>

            <form class="space-y-8">
                <!-- Supplier and Order Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
                    <!-- Supplier -->
                    <div class="space-y-2">
                        <Label for="supplier_id">Supplier</Label>
                        <Select id="supplier_id" v-model="purchaseCred.supplier_id" required>
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select Supplier" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Suppliers</SelectLabel>
                                    <SelectItem v-for="supplier in suppliers" :key="supplier.encrypted_id"
                                        :value="supplier.encrypted_id">
                                        {{ supplier.supplier_name }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Order Date -->
                    <div class="space-y-2">
                        <Label for="order_date">Order Date</Label>
                        <input type="date" id="order_date"
                            class="border p-2 w-full rounded focus:ring-2 focus:ring-blue-500" required
                            v-model="purchaseCred.order_date" />
                    </div>

                    <!-- Expected Delivery -->
                    <div class="space-y-2">
                        <Label for="delivery_date">Expected Delivery</Label>
                        <input type="date" id="delivery_date"
                            class="border p-2 w-full rounded focus:ring-2 focus:ring-blue-500" required
                            v-model="purchaseCred.expected_date" />
                    </div>
                </div>

                <!-- Products Table -->
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="font-semibold text-lg">Products to Order</h2>
                        <Button type="button" :disabled="purchaseCred.supplier_id === ''" @click="handleAddProduct">
                            + Add Product
                        </Button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-300 rounded-lg overflow-hidden text-sm">
                            <thead class="bg-gray-100 text-gray-700 uppercase tracking-wide">
                                <tr>
                                    <th class="border px-4 py-3 text-left w-2/5">Product</th>
                                    <th class="border px-3 py-3 text-center w-1/6">Qty</th>
                                    <th class="border px-3 py-3 text-center w-1/6">Unit Price</th>
                                    <th class="border px-3 py-3 text-center w-1/6">Total</th>
                                    <th class="border px-3 py-3 text-center w-1/6">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(item, index) in orderItems" :key="index"
                                    class="hover:bg-gray-50 transition-colors">
                                    <!-- Product -->
                                    <td class="border px-3 py-2">
                                        <select
                                            class="border border-gray-300 p-2 w-full rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700"
                                            v-model="item.product_id" @change="
                                                item.product_name =
                                                products.find((p) => p.encrypted_id === item.product_id)?.product_name
                                                " required>
                                            <option disabled selected>Select Product</option>
                                            <option v-for="product in products" :key="product.id"
                                                :value="product.encrypted_id">
                                                {{ product.product_name }}
                                            </option>
                                        </select>
                                    </td>

                                    <!-- Quantity -->
                                    <td class="border px-3 py-2 text-center">
                                        <NumberField id="quantity" :default-value="0" :min="0" v-model="item.quantity">
                                            <Label for="quantity" class="hidden">Quantity</Label>
                                            <NumberFieldContent class="flex items-center justify-center gap-1">
                                                <NumberFieldDecrement />
                                                <NumberFieldInput class="text-center w-16 border-gray-300" />
                                                <NumberFieldIncrement />
                                            </NumberFieldContent>
                                        </NumberField>
                                    </td>

                                    <!-- Unit Price -->
                                    <td class="border px-3 py-2 text-center">
                                        <Input type="number"
                                            class="border border-gray-300 rounded-md text-center focus:ring-2 focus:ring-blue-500 w-24"
                                            step="0.01" required v-model="item.unit_price" />
                                    </td>

                                    <!-- Total -->
                                    <td class="border px-3 py-2 text-center font-semibold text-gray-800">
                                        ₱{{ (item.unit_price * item.quantity).toFixed(2) }}
                                    </td>

                                    <!-- Action -->
                                    <td class="border px-3 py-2 text-center">
                                        <button type="button" @click="handleRemoveProduct(index)"
                                            class="text-red-500 hover:text-red-700 font-medium transition">
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            </tbody>

                            <tfoot class="bg-gray-50 text-sm">
                                <tr>
                                    <th colspan="3" class="text-right px-4 py-2 font-medium">Total Items:</th>
                                    <td class="px-4 py-2 text-center">{{ orderItems.length }}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right px-4 py-2 font-medium">Total Amount:</th>
                                    <td class="px-4 py-2 text-center font-bold text-green-600">
                                        ₱{{ totalAmount.toFixed(2) }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="p-6 flex justify-end gap-4 border-t bg-gray-50">
                    <Button type="reset" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
                        Cancel
                    </Button>
                    <Button type="button" @click="toggleLetter" :disabled="purchaseCred.items.length <= 0">
                        Generate Order
                    </Button>
                </div>
            </form>
        </div>
    </section>

    <!-- Letter Modal -->
    <Dialog v-model:open="openLetter">
        <DialogContent class="sm:max-w-[1200px] grid-rows-[auto_minmax(0,1fr)_auto] p-0 max-h-[90dvh]">
            <DialogHeader class="p-6 pb-0 border-b">
                <DialogTitle class="text-xl font-semibold">Purchase Order Overview</DialogTitle>
            </DialogHeader>
            <div class="grid gap-4 py-4 overflow-y-auto px-6">
                <!-- Letter Title -->
                <h1 class="text-center font-bold text-3xl">
                    LapTopia PURCHASE ORDER REQUEST
                </h1>

                <!-- Company Details -->
                <div class="flex flex-col space-y-1 text-gray-700">
                    <h2 class="font-bold text-2xl">LapTopia</h2>
                    <p class="font-medium">Barangay Jlectronics Street</p>
                    <p class="font-medium">Bacoor, Cavite 4102</p>
                    <p class="font-medium">Date: {{ purchaseCred.order_date }}</p>
                </div>

                <!-- Supplier Details -->
                <div v-if="letter.supplier_name" class="flex flex-col space-y-1 mt-6 text-gray-700">
                    <h2 class="font-bold text-2xl">{{ letter.supplier_name }}</h2>
                    <p class="font-medium">{{ letter.address }}</p>
                </div>

                <!-- Letter Body -->
                <div class="flex flex-col space-y-4 mt-8 leading-relaxed text-gray-700">
                    <p class="italic">Dear {{ letter.supplier_name || 'Supplier' }},</p>

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
                            <tr v-for="(item, index) in orderItems" :key="index">
                                <td class="border px-3 py-2">{{ item.product_name }}</td>
                                <td class="border px-3 py-2 text-center">{{ item.quantity }}</td>
                                <td class="border px-3 py-2 text-center">₱{{ item.unit_price }}</td>
                                <td class="border px-3 py-2 text-center">₱{{ (item.unit_price *
                                    item.quantity).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="font-semibold text-right mt-3">
                        Grand Total: ₱{{ totalAmount.toFixed(2) }}
                    </p>

                    <p>
                        We kindly request delivery of the above-mentioned items on or before
                        <span class="font-semibold">{{ purchaseCred.expected_date }}</span>.
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

            <!-- Footer Actions -->
            <DialogFooter class="p-6 pt-5 border-t">
                <Button type="button" @click="handleOrder(purchaseCred)" :disabled="isLoading">
                    <span v-if="isLoading" class="flex justify-center space-x-3 items-center">
                        <VueSpinner size="20" />
                        Sending...
                    </span>
                    <span v-else>
                        Send to Supplier
                    </span>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
