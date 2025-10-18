<script setup>
import { getSuppliers } from '@/composables/useSuppliers';
import { getCategories } from '@/composables/useCategories';
import { getBrands } from '@/composables/useBrands';
import { getProducts } from '@/composables/useProducts';
import { useRoute } from 'vue-router';
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
import { defineProps, onMounted, reactive, watch, defineEmits, ref } from "vue"
import ErrorLabel from '@/components/Errors/ErrorLabel.vue';
import { RegularSwal } from '@/components/Swals/useSwals';
const { addProduct, errors, deletePhoto, updateProduct } = manageProducts();
const { suppliers, fetchSuppliers } = getSuppliers();
const { categories, fetchCategories } = getCategories();
const { brands, fetchBrands } = getBrands();
const { fetchProductDetailsById, productDetails, isLoading } = getProducts();
const route = useRoute();
const newPhotos = ref(null);
const onfileChange = (event) => {
    newPhotos.value = [...event.target.files];
}

const handleUpdate = async (productCred, newPhotos) => {
    const success = await updateProduct(productCred, newPhotos);
    if (success && success.status === 200) {
        await fetchProductDetailsById(route.params.product_id);
        RegularSwal(success.data);
    }
}

const handleDeletePhoto = async (id) => {
    const success = await deletePhoto(id);
    if (success && success.status === 200) {
        await fetchProductDetailsById(route.params.product_id);
        RegularSwal(success.data);
    }
}

onMounted(async () => {
    await fetchProductDetailsById(route.params.product_id)
    await fetchSuppliers();
    await fetchCategories();
    await fetchBrands();
});

</script>
<template>
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center p-4 bg-gray-50">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <h1 class="mt-6 font-semibold text-xl sm:text-2xl text-gray-800">
            Fetching Data...
        </h1>
        <p class="mt-2 text-gray-600 text-sm">
            Please wait while we load the data.
        </p>
    </section>

    <section v-else class="min-h-screen bg-gray-50 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Title -->
            <header class="mb-10 text-center">
                <h2 class="text-4xl font-bold text-gray-900 mb-2">Edit Product Details</h2>
                <p class="text-gray-600">Update the information for your product below.</p>
            </header>

            <!-- Form -->
            <form id="addProduct" class="bg-white shadow-lg rounded-lg p-8 space-y-8" v-if="productDetails"
                @submit.prevent="handleUpdate(productDetails, newPhotos)">
                <!-- Product Information Section -->
                <div class="border-b border-gray-200 pb-6 mb-8">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-6">Product Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Supplier -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">Supplier</Label>
                            <Select v-model="productDetails.supplier_id" required>
                                <SelectTrigger class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <SelectValue placeholder="Select supplier" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Suppliers</SelectLabel>
                                        <SelectItem v-for="supplier in suppliers" :key="supplier.id"
                                            :value="supplier.id">
                                            {{ supplier.supplier_name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <ErrorLabel v-if="errors?.supplier_id" :error="errors.supplier_id"
                                class="text-red-600 text-sm" />
                        </div>

                        <!-- Category -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">Category</Label>
                            <Select v-model="productDetails.category_id" required>
                                <SelectTrigger class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <SelectValue placeholder="Select category" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Categories</SelectLabel>
                                        <SelectItem v-for="category in categories" :key="category.id"
                                            :value="category.id">
                                            {{ category.category_name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <ErrorLabel v-if="errors?.category_id" :error="errors.category_id"
                                class="text-red-600 text-sm" />
                        </div>

                        <!-- Brand -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">Brand</Label>
                            <Select v-model="productDetails.brand_id" required>
                                <SelectTrigger class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <SelectValue placeholder="Select brand" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Brands</SelectLabel>
                                        <SelectItem v-for="brand in brands" :key="brand.id" :value="brand.id">
                                            {{ brand.brand_name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <ErrorLabel v-if="errors?.brand_id" :error="errors.brand_id" class="text-red-600 text-sm" />
                        </div>

                        <!-- SKU -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">SKU</Label>
                            <Input type="text" placeholder="e.g., SKU-12345" v-model="productDetails.SKU" required
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md" />
                            <ErrorLabel v-if="errors?.SKU" :error="errors.SKU" class="text-red-600 text-sm" />
                        </div>

                        <!-- Model -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">Model</Label>
                            <Input type="text" placeholder="e.g., Inspiron 15 3000" v-model="productDetails.model"
                                required
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md" />
                            <ErrorLabel v-if="errors?.model" :error="errors.model" class="text-red-600 text-sm" />
                        </div>

                        <!-- Product Name -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">Product Name</Label>
                            <Input type="text" placeholder="e.g., Dell Inspiron Laptop"
                                v-model="productDetails.product_name" required
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md" />
                            <ErrorLabel v-if="errors?.product_name" :error="errors.product_name"
                                class="text-red-600 text-sm" />
                        </div>

                        <!-- Quantity -->
                        <div class="space-y-2">
                            <Label for="quantity" class="text-sm font-medium text-gray-700">Quantity</Label>
                            <NumberField id="quantity" :default-value="0" :min="0"
                                v-model="productDetails.product_quantity" required>
                                <NumberFieldContent
                                    class="border border-gray-300 rounded-md focus-within:border-blue-500 focus-within:ring-blue-500">
                                    <NumberFieldDecrement class="px-3 py-2 bg-gray-100 hover:bg-gray-200" />
                                    <NumberFieldInput class="text-center flex-1" />
                                    <NumberFieldIncrement class="px-3 py-2 bg-gray-100 hover:bg-gray-200" />
                                </NumberFieldContent>
                            </NumberField>
                            <ErrorLabel v-if="errors?.product_quantity" :error="errors.product_quantity"
                                class="text-red-600 text-sm" />
                        </div>

                        <!-- Unit Price -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">Unit Price</Label>
                            <Input type="number" placeholder="Price per unit" v-model="productDetails.unit_price"
                                pattern="^\d*\.?\d*$" required
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md" />
                            <ErrorLabel v-if="errors?.unit_price" :error="errors.unit_price"
                                class="text-red-600 text-sm" />
                        </div>

                        <!-- Reorder Level -->
                        <div class="space-y-2">
                            <Label for="reorder_level" class="text-sm font-medium text-gray-700">Reorder Level</Label>
                            <NumberField id="reorder_level" :default-value="0" :min="0"
                                v-model="productDetails.reorder_level" required>
                                <NumberFieldContent
                                    class="border border-gray-300 rounded-md focus-within:border-blue-500 focus-within:ring-blue-500">
                                    <NumberFieldDecrement class="px-3 py-2 bg-gray-100 hover:bg-gray-200" />
                                    <NumberFieldInput class="text-center flex-1" />
                                    <NumberFieldIncrement class="px-3 py-2 bg-gray-100 hover:bg-gray-200" />
                                </NumberFieldContent>
                            </NumberField>
                            <ErrorLabel v-if="errors?.reorder_level" :error="errors.reorder_level"
                                class="text-red-600 text-sm" />
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="border-b border-gray-200 pb-6 mb-8">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-6">Product Description</h3>
                    <div class="space-y-2">
                        <Label class="text-sm font-medium text-gray-700">Description</Label>
                        <Editor api-key="dydc6b7t1h4typ0ffyqdvd15j2ovv325cefo8jwipqlem5qz"
                            v-model="productDetails.product_description" :init="{
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
                    <div v-if="productDetails.photos && productDetails.photos.length"
                        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-6">
                        <div v-for="(image, index) in productDetails.photos" :key="index"
                            class="relative group rounded-lg overflow-hidden shadow-md border border-gray-200 bg-white hover:shadow-lg transition-shadow">
                            <!-- Image -->
                            <img :src="'http://127.0.0.1:8000/storage/' + image.image" alt="Product Photo"
                                class="w-full h-32 object-cover transition-transform duration-300 group-hover:scale-105" />

                            <!-- Delete Button -->
                            <button type="button" @click="handleDeletePhoto(image.id)"
                                class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow">
                                Delete
                            </button>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <Label class="text-sm font-medium text-gray-700">Attach New Photos</Label>
                        <Input type="file" accept="image/*"
                            class="cursor-pointer w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md"
                            multiple @change="onfileChange" />
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-center pt-6">
                    <Button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg shadow-md transition-colors">
                        Confirm Update
                    </Button>
                </div>
            </form>
        </div>
    </section>
</template>