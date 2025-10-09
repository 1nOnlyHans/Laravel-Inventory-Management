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
    <section class="min-h-screen bg-gray-50 flex justify-center py-10">
        <div class="w-full max-w-6xl bg-white shadow-lg rounded-2xl p-8">
            <!-- Title -->
            <header class="mb-8 text-center">
                <h2 class="text-3xl font-semibold text-gray-800">Add New Product</h2>
                <p class="text-gray-500 mt-1 text-sm">Fill out the details below to add a new product to the inventory.
                </p>
            </header>

            <!-- Form -->
            <form id="addProduct" class="space-y-8" @submit.prevent="handleAddProduct(productCred)">
                <!-- Grid Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Supplier -->
                    <div>
                        <Label>Supplier</Label>
                        <Select v-model="productCred.supplier_id">
                            <SelectTrigger class="w-full">
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
                    <div>
                        <Label>Category</Label>
                        <Select v-model="productCred.category_id">
                            <SelectTrigger class="w-full">
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
                    <div>
                        <Label>Brand</Label>
                        <Select v-model="productCred.brand_id">
                            <SelectTrigger class="w-full">
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
                    <div>
                        <Label>SKU</Label>
                        <Input type="text" placeholder="e.g., SKU-12345" v-model="productCred.SKU" />
                    </div>

                    <!-- Model -->
                    <div>
                        <Label>Model</Label>
                        <Input type="text" placeholder="e.g., Inspiron 15 3000" v-model="productCred.model" />
                    </div>

                    <!-- Product Name -->
                    <div>
                        <Label>Product Name</Label>
                        <Input type="text" placeholder="e.g., Dell Inspiron Laptop"
                            v-model="productCred.product_name" />
                    </div>

                    <!-- Quantity -->
                    <div>
                        <Label for="quantity">Quantity</Label>
                        <NumberField id="quantity" :default-value="0" :min="0" v-model="productCred.product_quantity">
                            <NumberFieldContent>
                                <NumberFieldDecrement />
                                <NumberFieldInput />
                                <NumberFieldIncrement />
                            </NumberFieldContent>
                        </NumberField>
                    </div>

                    <!-- Unit Price -->
                    <div>
                        <Label>Unit Price</Label>
                        <Input type="number" min="1" placeholder="Price per unit" v-model="productCred.unit_price" />
                    </div>

                    <!-- Reorder Level -->
                    <div>
                        <Label>Reorder Level</Label>
                        <NumberField id="reorder_level" :default-value="0" :min="0" v-model="productCred.reorder_level">
                            <NumberFieldContent>
                                <NumberFieldDecrement />
                                <NumberFieldInput />
                                <NumberFieldIncrement />
                            </NumberFieldContent>
                        </NumberField>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <Label>Description</Label>
                    <Editor api-key="dydc6b7t1h4typ0ffyqdvd15j2ovv325cefo8jwipqlem5qz"
                        v-model="productCred.product_description" :init="{
                            height: 400,
                            menubar: true,
                            plugins: 'link image code lists table',
                            toolbar:
                                'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | link image | code',
                            branding: false,
                        }" />
                </div>

                <!-- Product Photo -->
                <div>
                    <Label>Product Photo</Label>
                    <Input type="file" accept="image/*" class="cursor-pointer" multiple @change="onfileChange" />
                </div>

                <!-- Submit -->
                <div class="flex justify-center pt-4">
                    <Button type="submit"
                        class="bg-accents hover:bg-accents-hover text-white font-medium px-6 py-2 rounded-lg">
                        Add Product
                    </Button>
                </div>
            </form>
        </div>
    </section>
</template>