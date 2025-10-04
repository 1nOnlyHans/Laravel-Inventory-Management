<script setup>
import { getSuppliers } from '@/composables/useSuppliers';
import { getCategories } from '@/composables/useCategories';
import { getBrands } from '@/composables/useBrands';

import { manageProducts } from '@/composables/useProducts';
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

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import Textarea from "../ui/textarea/Textarea.vue"
import { defineProps, onMounted, reactive, watch, defineEmits, ref } from "vue"
import ErrorLabel from "../Errors/ErrorLabel.vue"
import { faAdd } from "@fortawesome/free-solid-svg-icons"

const { addProduct } = manageProducts();

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
    reorder_level: "",
    status: "",
    photos: [],
});

const onfileChange = (event) => {
    productCred.value.photos = [...event.target.files];
}

const props = defineProps({
    errors: Object,
});

onMounted(async () => {
    await fetchSuppliers();
    await fetchCategories();
    await fetchBrands();
});

</script>
<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button class="bg-accents hover:bg-accents-hover flex items-center gap-2">
                <FontAwesomeIcon :icon="faAdd" />
                Add Product
            </Button>
        </DialogTrigger>

        <DialogContent class="sm:max-w-[1000px] rounded-2xl shadow-lg">
            <DialogHeader>
                <DialogTitle class="text-2xl font-semibold text-gray-800">Add New Product</DialogTitle>
                <DialogDescription>
                    <p class="text-sm text-gray-500">Fill out the details below to add a new product.</p>
                </DialogDescription>
            </DialogHeader>

            <form id="addProduct" class="mt-4 space-y-6" @submit.prevent="addProduct(productCred)">
                <!-- Grid Layout -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Supplier -->
                    <div class="flex flex-col space-y-2">
                        <Label>Supplier</Label>
                        <Select v-model="productCred.supplier_id">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select supplier" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Suppliers</SelectLabel>
                                    <SelectItem v-for="supplier in suppliers" :value="supplier.encrypted_id">{{
                                        supplier.supplier_name }}</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Category -->
                    <div class="flex flex-col space-y-2">
                        <Label>Category</Label>
                        <Select v-model="productCred.category_id">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select category" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Categories</SelectLabel>
                                    <SelectItem v-for="category in categories" :value="category.encrypted_id">{{
                                        category.category_name }}</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Brand -->
                    <div class="flex flex-col space-y-2">
                        <Label>Brand</Label>
                        <Select v-model="productCred.brand_id">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select brand" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Brands</SelectLabel>
                                    <SelectItem v-for="brand in brands" :value="brand.encrypted_id">{{
                                        brand.brand_name }}</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- SKU -->
                    <div class="flex flex-col space-y-2">
                        <Label>SKU</Label>
                        <Input type="text" placeholder="e.g., SKU-12345" v-model="productCred.SKU" />
                    </div>

                    <!-- Model -->
                    <div class="flex flex-col space-y-2">
                        <Label>Model</Label>
                        <Input type="text" placeholder="e.g., Inspiron 15 3000" v-model="productCred.model" />
                    </div>

                    <!-- Product Name -->
                    <div class="flex flex-col space-y-2">
                        <Label>Product Name</Label>
                        <Input type="text" placeholder="e.g., Dell Inspiron Laptop"
                            v-model="productCred.product_name" />
                    </div>

                    <!-- Quantity -->
                    <div class="flex flex-col space-y-2">
                        <NumberField id="quantity" :default-value="0" :min="0" v-model="productCred.product_quantity">
                            <Label for="quantity">Quantity</Label>
                            <NumberFieldContent>
                                <NumberFieldDecrement />
                                <NumberFieldInput />
                                <NumberFieldIncrement />
                            </NumberFieldContent>
                        </NumberField>
                    </div>

                    <!-- Unit Price -->
                    <div class="flex flex-col space-y-2">
                        <Label>Unit Price</Label>
                        <Input type="number" min="1" placeholder="Price per unit" v-model="productCred.unit_price" />
                    </div>
                    <div class="flex flex-col space-y-2">
                        <Label>Re Order Level</Label>
                        <Input type="number" min="1" placeholder="Re Order Level" v-model="productCred.reorder_level" />
                    </div>
                </div>

                <!-- Description -->
                <div class="flex flex-col space-y-2">
                    <Label>Description</Label>
                    <Textarea placeholder="Write a brief description about the product..." rows="3"
                        v-model="productCred.product_description" />
                </div>

                <!-- Product Photo -->
                <div class="flex flex-col space-y-2">
                    <Label>Product Photo</Label>
                    <Input type="file" accept="image/*" class="cursor-pointer" multiple @change="onfileChange" />
                    <!-- <p class="text-xs text-gray-500">Accepted formats: JPG, PNG, WEBP (Max 2MB)</p> -->
                </div>
            </form>

            <DialogFooter class="mt-6 flex justify-end gap-3">
                <Button variant="outline">Cancel</Button>
                <Button type="submit" form="addProduct" class="bg-accents hover:bg-accents-hover">
                    Add Product
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

</template>