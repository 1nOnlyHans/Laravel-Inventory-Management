<script setup>
import { Button } from "@/components/ui/button"
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from "@/components/ui/card"
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from "@/components/ui/carousel"
import Badge from "../ui/badge/Badge.vue";
import { defineProps, defineEmits, ref, onMounted, watch, computed } from "vue";

const props = defineProps({
    product: Object,
    cart: Array,
    product_name: String,
    product_brand: String,
    product_stocks: Number,
    product_price: String,
    product_photos: Array
});

const isAdded = computed(() => {
    return props.cart.some(
        (item) => item.product_id === props.product.encrypted_id
    );
});

const emit = defineEmits(['addItem', 'fetchItem', 'test']);

const handleEmit = (item) => {
    emit("addItem", item);
};

</script>
<template>
    <Card class="w-[300px] bg-white border rounded-2xl shadow-sm hover:shadow-lg transition-all duration-200">
        <!-- Header -->
        <CardHeader class="p-5 pb-3">
            <CardTitle class="flex items-start justify-between gap-2 text-lg font-semibold">
                <div class="flex-1 min-w-0">
                    <h1 class="truncate text-[1rem] leading-snug font-semibold text-gray-800"
                        :title="props.product_name">
                        {{ props.product_name }}
                    </h1>
                </div>
                <div class="flex-shrink-0">
                    <Badge class="whitespace-nowrap text-xs px-2 py-1 font-medium">
                        {{ product_stocks }} in stock
                    </Badge>
                </div>
            </CardTitle>
            <!--  -->
            <p class="text-gray-500 font-medium text-sm">SKU: {{ product.SKU }}</p>
            <CardDescription class="text-sm text-gray-500 mt-1 flex justify-between items-center">
                {{ props.product_brand }}
                <Badge :class="{
                    'bg-green-100 text-green-700': props.product.status === 'Available',
                    'bg-yellow-100 text-yellow-700': props.product.status === 'Low Stock',
                    'bg-red-100 text-red-700': props.product.status === 'Out of Stock',
                    'bg-gray-100 text-gray-700': !['Available', 'Low Stock', 'Out of Stock'].includes(props.product.status),
                }" class="font-semibold px-2 py-1 rounded-full">
                    {{ props.product.status }}
                </Badge>
            </CardDescription>
        </CardHeader>

        <!-- Content -->
        <CardContent class="px-5 py-3 space-y-4">
            <!-- Product Carousel -->
            <Carousel class="relative w-full rounded-lg overflow-hidden">
                <CarouselContent>
                    <CarouselItem v-for="(photo, index) in product_photos" :key="index"
                        class="flex justify-center items-center">
                        <div class="p-2">
                            <Card class="border border-gray-200">
                                <CardContent class="flex aspect-square items-center justify-center p-6 bg-gray-50">
                                    <img :src="'http://127.0.0.1:8000/storage/' + photo.image" alt="">
                                </CardContent>
                            </Card>
                        </div>
                    </CarouselItem>
                </CarouselContent>
                <CarouselPrevious />
                <CarouselNext />
            </Carousel>

            <!-- Product Price -->
            <div class="pt-2">
                <span class="text-gray-500 text-sm">Price:</span>
                <h2 class="text-xl font-semibold text-brand">₱{{ props.product_price }}</h2>
            </div>
        </CardContent>

        <!-- Footer -->
        <CardFooter class="flex flex-col p-5 border-t gap-3">
            <!-- Add to Cart Button -->
            <Button
                class="w-full bg-brand text-white hover:bg-brand-dark transition rounded-lg py-2 font-medium disabled:opacity-70 disabled:cursor-not-allowed"
                @click="handleEmit(product)" :disabled="isAdded || props.product.product_quantity <= 0">
                {{ isAdded ? 'Added to Cart' : 'Add to Cart' }}
            </Button>

            <!-- View Details Button -->
            <RouterLink
                class="w-full bg-accents text-white hover:bg-accents-hover transition rounded-lg py-2 font-medium text-center flex items-center justify-center"
                :to="{ name: 'Staff Product Details', params: { product_id: product.encrypted_id } }">
                View Details
            </RouterLink>
        </CardFooter>
    </Card>

</template>