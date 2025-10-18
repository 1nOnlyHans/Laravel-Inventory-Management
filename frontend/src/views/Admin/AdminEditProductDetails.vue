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
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center p-4">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <h1 class="mt-6 font-semibold text-xl sm:text-2xl text-accents">
            Fetching Datas...
        </h1>
        <p class="mt-2 text-gray-500 text-sm">
            Please wait while we load the datas.
        </p>
    </section>

    <section v-else class="min-h-screen bg-gray-50 flex justify-center py-10">
        <div class="w-full max-w-6xl p-8">
            <!-- Title -->
            <header class="mb-8 text-center">
                <h2 class="text-3xl font-semibold text-gray-800">Edit Product Details</h2>
            </header>

            <!-- Form -->
            <form id="addProduct" class="space-y-8" v-if="productDetails"
                @submit.prevent="handleUpdate(productDetails, newPhotos)">
                <!-- Grid Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Supplier -->
                    <div>
                        <Label>Supplier</Label>
                        <Select v-model="productDetails.supplier_id" required>
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select supplier" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Suppliers</SelectLabel>
                                    <SelectItem v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                        {{ supplier.supplier_name }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <ErrorLabel v-if="errors?.supplier_id" :error="errors.supplier_id" />
                    </div>

                    <!-- Category -->
                    <div>
                        <Label>Category</Label>
                        <Select v-model="productDetails.category_id" required>
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select category" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Categories</SelectLabel>
                                    <SelectItem v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.category_name }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <ErrorLabel v-if="errors?.category_id" :error="errors.category_id" />
                    </div>

                    <!-- Brand -->
                    <div>
                        <Label>Brand</Label>
                        <Select v-model="productDetails.brand_id" required>
                            <SelectTrigger class="w-full">
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
                        <ErrorLabel v-if="errors?.brand_id" :error="errors.brand_id" />
                    </div>

                    <!-- SKU -->
                    <div>
                        <Label>SKU</Label>
                        <Input type="text" placeholder="e.g., SKU-12345" v-model="productDetails.SKU" required />
                        <ErrorLabel v-if="errors?.SKU" :error="errors.SKU" />
                    </div>

                    <!-- Model -->
                    <div>
                        <Label>Model</Label>
                        <Input type="text" placeholder="e.g., Inspiron 15 3000" v-model="productDetails.model"
                            required />
                        <ErrorLabel v-if="errors?.model" :error="errors.model" />
                    </div>

                    <!-- Product Name -->
                    <div>
                        <Label>Product Name</Label>
                        <Input type="text" placeholder="e.g., Dell Inspiron Laptop"
                            v-model="productDetails.product_name" required />
                        <ErrorLabel v-if="errors?.product_name" :error="errors.product_name" />
                    </div>

                    <!-- Quantity -->
                    <div>
                        <Label for="quantity">Quantity</Label>
                        <NumberField id="quantity" :default-value="0" :min="0" v-model="productDetails.product_quantity"
                            required="">
                            <NumberFieldContent>
                                <NumberFieldDecrement />
                                <NumberFieldInput />
                                <NumberFieldIncrement />
                            </NumberFieldContent>
                        </NumberField>
                        <ErrorLabel v-if="errors?.product_quantity" :error="errors.product_quantity" />
                    </div>

                    <!-- Unit Price -->
                    <div>
                        <Label>Unit Price</Label>
                        <Input type="number" placeholder="Price per unit" v-model="productDetails.unit_price"
                            pattern="^\d*\.?\d*$" inputmode="decimal" required />
                        <ErrorLabel v-if="errors?.unit_price" :error="errors.unit_price" />
                    </div>

                    <!-- Reorder Level -->
                    <div>
                        <Label>Reorder Level</Label>
                        <NumberField id="reorder_level" :default-value="0" :min="0"
                            v-model="productDetails.reorder_level" required="">
                            <NumberFieldContent>
                                <NumberFieldDecrement />
                                <NumberFieldInput />
                                <NumberFieldIncrement />
                            </NumberFieldContent>
                        </NumberField>
                        <ErrorLabel v-if="errors?.reorder_level" :error="errors.reorder_level" />
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <Label>Description</Label>
                    <Editor api-key="dydc6b7t1h4typ0ffyqdvd15j2ovv325cefo8jwipqlem5qz"
                        v-model="productDetails.product_description" :init="{
                            height: 800,
                            menubar: true,
                            plugins: 'link image code lists table',
                            toolbar:
                                'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | link image | code',
                            branding: false,
                        }" />
                </div>


                <div>
                    <h1 class="text-xl font-semibold mb-4 text-gray-800">Product Photos</h1>
                    <div class="flex flex-wrap gap-4 justify-center items-center">
                        <div v-for="(image, index) in productDetails.photos" :key="index"
                            class="relative group rounded-lg overflow-hidden shadow-md border bg-white">
                            <!-- Image -->
                            <img :src="'http://127.0.0.1:8000/storage/' + image.image" alt="Product Photo"
                                class="w-[150px] h-[150px] object-cover transition-transform duration-300 group-hover:scale-105" />

                            <!-- Delete Button -->
                            <button type="button" @click="handleDeletePhoto(image.id)"
                                class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white text-xs px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product Photo -->
                <div>
                    <Label>Attach Photo</Label>
                    <Input type="file" accept="image/*" class="cursor-pointer" multiple @change="onfileChange" />
                </div>

                <!-- Submit -->
                <div class="flex justify-center pt-4">
                    <Button type="submit">
                        Confirm Update
                    </Button>
                </div>
            </form>
        </div>
    </section>
</template>