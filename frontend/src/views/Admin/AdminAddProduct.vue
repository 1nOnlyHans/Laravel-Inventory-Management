<script setup>
import { getSuppliers } from '@/composables/useSuppliers';
import { getCategories } from '@/composables/useCategories';
import { getBrands } from '@/composables/useBrands';

import Editor from '@tinymce/tinymce-vue';
import { manageProducts } from '@/composables/useProducts';

import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select"

import {
    NumberField,
    NumberFieldContent,
    NumberFieldDecrement,
    NumberFieldIncrement,
    NumberFieldInput,
} from "@/components/ui/number-field"
import Button from '@/components/ui/button/Button.vue';
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { defineProps, onMounted, reactive, watch, defineEmits, ref } from "vue"
import ErrorLabel from '@/components/Errors/ErrorLabel.vue';
import { faAdd } from "@fortawesome/free-solid-svg-icons"
import { RegularSwal } from '@/components/Swals/useSwals';
const { addProduct, errors } = manageProducts();
const { suppliers, fetchSuppliers } = getSuppliers();
const { categories, fetchCategories } = getCategories();
const { brands, fetchBrands } = getBrands();
const productCred = ref({
    supplier_id: "",
    category_id: "",
    brand_id: "",
    SKU: "",
    model: "",
    product_name: "",
    product_description: "",
    product_quantity: 0,
    unit_price: 0,
    reorder_level: 0,
    status: "",
    photos: [],
});

const handleAddProduct = async (productCred) => {
    const success = await addProduct(productCred);
    if (success && success.status === 200) {
        RegularSwal(success.data);
        productCred.supplier_id = ""
        productCred.category_id = ""
        productCred.brand_id = ""
        productCred.SKU = ""
        productCred.model = ""
        productCred.product_name = ""
        productCred.product_description = ""
        productCred.product_quantity = 0
        productCred.unit_price = 0
        productCred.reorder_level = 0
        productCred.status = ""
        productCred.photos = []
    }
}

const onfileChange = (event) => {
    productCred.value.photos = [...event.target.files];
}

onMounted(async () => {
    await fetchSuppliers();
    await fetchCategories();
    await fetchBrands();
});

</script>
<template>
    <section class="min-h-screen bg-gray-50 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Title -->
            <header class="mb-10 text-center">
                <h2 class="text-4xl font-bold text-gray-900 mb-2">Add New Product</h2>
                <p class="text-gray-600">Fill out the details below to add a new product to the inventory.</p>
            </header>

            <!-- Form -->
            <form id="addProduct" class="bg-white shadow-lg rounded-lg p-8 space-y-8"
                @submit.prevent="handleAddProduct(productCred)">
                <!-- Product Information Section -->
                <div class="border-b border-gray-200 pb-6 mb-8">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-6">Product Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Supplier -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">Supplier</Label>
                            <Select v-model="productCred.supplier_id">
                                <SelectTrigger
                                    class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md">
                                    <SelectValue placeholder="Select supplier" />
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

                        <!-- Category -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">Category</Label>
                            <Select v-model="productCred.category_id">
                                <SelectTrigger
                                    class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md">
                                    <SelectValue placeholder="Select category" />
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
                            </Select>
                        </div>

                        <!-- Brand -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">Brand</Label>
                            <Select v-model="productCred.brand_id">
                                <SelectTrigger
                                    class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md">
                                    <SelectValue placeholder="Select brand" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Brands</SelectLabel>
                                        <SelectItem v-for="brand in brands" :key="brand.encrypted_id"
                                            :value="brand.encrypted_id">
                                            {{ brand.brand_name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- SKU -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">SKU</Label>
                            <Input type="text" placeholder="e.g., SKU-12345" v-model="productCred.SKU"
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md" />
                        </div>

                        <!-- Model -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">Model</Label>
                            <Input type="text" placeholder="e.g., Inspiron 15 3000" v-model="productCred.model"
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md" />
                        </div>

                        <!-- Product Name -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">Product Name</Label>
                            <Input type="text" placeholder="e.g., Dell Inspiron Laptop"
                                v-model="productCred.product_name"
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md" />
                        </div>

                        <!-- Quantity -->
                        <div class="space-y-2">
                            <Label for="quantity" class="text-sm font-medium text-gray-700">Quantity</Label>
                            <NumberField id="quantity" :default-value="0" :min="0"
                                v-model="productCred.product_quantity">
                                <NumberFieldContent
                                    class="border border-gray-300 rounded-md focus-within:border-blue-500 focus-within:ring-blue-500">
                                    <NumberFieldDecrement class="px-3 py-2 bg-gray-100 hover:bg-gray-200" />
                                    <NumberFieldInput class="text-center flex-1" />
                                    <NumberFieldIncrement class="px-3 py-2 bg-gray-100 hover:bg-gray-200" />
                                </NumberFieldContent>
                            </NumberField>
                        </div>

                        <!-- Unit Price -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">Unit Price</Label>
                            <Input type="number" min="1" placeholder="Price per unit" v-model="productCred.unit_price"
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md" />
                        </div>

                        <!-- Reorder Level -->
                        <div class="space-y-2">
                            <Label for="reorder_level" class="text-sm font-medium text-gray-700">Reorder Level</Label>
                            <NumberField id="reorder_level" :default-value="0" :min="0"
                                v-model="productCred.reorder_level">
                                <NumberFieldContent
                                    class="border border-gray-300 rounded-md focus-within:border-blue-500 focus-within:ring-blue-500">
                                    <NumberFieldDecrement class="px-3 py-2 bg-gray-100 hover:bg-gray-200" />
                                    <NumberFieldInput class="text-center flex-1" />
                                    <NumberFieldIncrement class="px-3 py-2 bg-gray-100 hover:bg-gray-200" />
                                </NumberFieldContent>
                            </NumberField>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="border-b border-gray-200 pb-6 mb-8">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-6">Product Description</h3>
                    <div class="space-y-2">
                        <Label class="text-sm font-medium text-gray-700">Description</Label>
                        <Editor api-key="dydc6b7t1h4typ0ffyqdvd15j2ovv325cefo8jwipqlem5qz"
                            v-model="productCred.product_description" :init="{
                                height: 400,
                                menubar: true,
                                plugins: 'link image code lists table',
                                toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | link image | code',
                                branding: false,
                                skin: 'oxide',
                                content_css: 'default'
                            }"
                            class="border border-gray-300 rounded-md focus-within:border-blue-500 focus-within:ring-blue-500" />
                    </div>
                </div>

                <!-- Product Photos Section -->
                <div class="border-b border-gray-200 pb-6 mb-8">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-6">Product Photos</h3>
                    <div class="space-y-2">
                        <Label class="text-sm font-medium text-gray-700">Attach Photos</Label>
                        <Input type="file" accept="image/*"
                            class="cursor-pointer w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md"
                            multiple @change="onfileChange" />
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-center pt-6">
                    <Button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg shadow-md transition-colors">
                        Add Product
                    </Button>
                </div>
            </form>
        </div>
    </section>
</template>