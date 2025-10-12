<script setup>
import { getProducts } from '@/composables/useProducts';
import { getCategories } from '@/composables/useCategories';
import { managePOS } from '@/composables/usePOS';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select"
import ProductPOScard from '@/components/Cards/ProductPOScard.vue';
import CartItemCard from '@/components/Cards/CartItemCard.vue';
import { computed, onMounted, ref, watch } from 'vue';
import POSCheckoutModal from '@/components/Modals/POSCheckoutModal.vue';
import { ConfirmationSwal, RegularSwal } from '@/components/Swals/useSwals';
const { categories, fetchCategories } = getCategories();
const { products, fetchProducts } = getProducts();
const { cart, addItem, removeItem, clearItem, sale } = managePOS();
const filter = ref("");
const selectedCategory = ref("");
const paymentModal = ref(false);
const togglePaymentModal = () => {
    paymentModal.value = !paymentModal.value
}

onMounted(() => {
    fetchCategories();
    fetchProducts();
    clearItem();
})

const handleAddItem = (item) => {
    addItem(item);
}

const updateQuantity = ({ index, quantity }) => {
    cart.value[index].quantity = quantity;
    cart.value[index].total_price = Number(quantity) * Number(cart.value[index].unit_price);
    localStorage.setItem('cart', JSON.stringify(cart.value));
};

const handleSale = async (saleCred) => {
    paymentModal.value = false;
    ConfirmationSwal({
        icon: 'question',
        title: 'Confirm Transaction?',
        message: '',
        confirm_text: 'Yes!',
        callBack: async () => {
            const success = await sale(saleCred);
            if (success && success.status === 200) {
                clearItem();
                paymentModal.value = false;
                fetchCategories();
                fetchProducts();
                RegularSwal(success.data);
            }
            else {
                RegularSwal({
                    icon: "error",
                    title: "Transaction Failed",
                    text: success.response.data.message
                });
            }
        }
    })
}


const handleRemoveItem = (index) => {
    removeItem(index);
}

const filteredProducts = computed(() => {
    let result = products.value;
    // Filter by search text
    if (filter.value.trim() !== "") {
        const search = filter.value.toLowerCase();
        result = result.filter((item) =>
            item.product_name.toLowerCase().includes(search) ||
            item.brand.brand_name.toLowerCase().includes(search) ||
            item.SKU.toLowerCase().includes(search)
        );
    }
    return result;
});

</script>
<template>
    <section class="p-4 md:p-6 bg-gray-50 min-h-screen">
        <div class="container-xl mx-auto">
            <div class="flex flex-col md:flex-row gap-6">

                <!-- LEFT: POS Section -->
                <div class="flex-1 bg-white border rounded-2xl shadow-sm p-5">
                    <h1 class="text-2xl font-bold mb-4 text-gray-800">Point of Sale</h1>

                    <!-- Search & Filter Row -->
                    <div class="flex flex-col sm:flex-row gap-3 mb-4">
                        <Input type="text" placeholder="Search by model, brand, or SKU" class="flex-1"
                            v-model="filter" />

                        <!-- <Select v-model="selectedCategory">
                            <SelectTrigger class="w-[200px]">
                                <SelectValue placeholder="Select a category" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Categories</SelectLabel>
                                    <SelectItem v-for="category in categories" :key="category.encrypted_id"
                                        :value="category.encrypted_id">
                                        {{ category.category_name }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select> -->
                    </div>

                    <!-- Product display -->
                    <div class="border-t pt-4 text-gray-500">
                        <div v-if="products.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-3 max-h-[550px] overflow-y-auto pr-2 
                                scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                            <ProductPOScard v-for="product in filteredProducts" :key="product.encrypted_id"
                                :product_name="product.product_name" :product_brand="product.brand.brand_name"
                                :product_stocks="product.product_quantity" :product_price="product.unit_price"
                                :product_photos="product.photos" :product="product" :cart="cart"
                                @add-item="handleAddItem" />
                        </div>
                        <p v-else class="text-center text-sm text-gray-400 py-4">No items displayed yet...</p>
                    </div>
                </div>

                <!-- RIGHT: Cart Section -->
                <div class="w-full md:w-1/3 bg-white border rounded-2xl shadow-sm flex flex-col p-5 max-h-[80vh]">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">
                            Cart ({{ cart.length }})
                        </h2>
                        <Button variant="outline" size="sm" v-if="cart.length > 0" @click="clearItem">
                            Clear All
                        </Button>
                    </div>

                    <!-- Scrollable Cart List -->
                    <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
                        <div v-if="cart.length > 0" class="space-y-3">
                            <CartItemCard v-for="(item, index) in cart" :key="index" :product="item" :index="index"
                                :cart="cart" @remove-item="handleRemoveItem" @updateQuantity="updateQuantity" />
                        </div>
                        <p v-else class="text-gray-500 text-sm">No items in cart...</p>
                    </div>

                    <!-- Footer / Checkout -->
                    <div class="pt-4 border-t mt-4">
                        <Button class="w-full bg-brand text-white font-medium py-2 rounded-lg hover:bg-brand/90"
                            :disabled="cart.length === 0" @click="togglePaymentModal">
                            Proceed to Payment
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <POSCheckoutModal v-model:open="paymentModal" :items="cart" @sale="handleSale" />
</template>