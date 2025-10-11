<script setup>
import { getProducts } from '@/composables/useProducts';
import { onMounted } from 'vue';
import { useRoute, RouterLink } from 'vue-router';
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
} from '@/components/ui/carousel'
import Button from '@/components/ui/button/Button.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faArrowLeft } from '@fortawesome/free-solid-svg-icons';

const { fetchProductDetailsById, productDetails, isLoading } = getProducts();
const route = useRoute();

onMounted(async () => {
    await fetchProductDetailsById(route.params.product_id);
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

    <section v-else-if="!isLoading && productDetails" class="py-10 bg-gray-50 border-b">
        <div class="max-w-6xl mx-auto bg-white shadow-sm rounded-2xl p-8">

            <!-- Go Back Button -->
            <div class="mb-6">
                <RouterLink :to="{ name: 'Staff POS' }"
                    class="inline-flex items-center gap-2 text-gray-700 hover:text-blue-600 transition-colors font-medium">
                    <FontAwesomeIcon :icon="faArrowLeft" />
                    Go Back
                </RouterLink>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 items-start">
                <!-- Product Images -->
                <div class="flex justify-center">
                    <div class="w-full max-w-sm mx-auto">
                        <Carousel v-if="productDetails.photos.length > 0" class="relative w-full">
                            <CarouselContent>
                                <CarouselItem v-for="(image, index) in productDetails.photos" :key="index" class="p-1">
                                    <Card class="rounded-xl shadow-md overflow-hidden">
                                        <CardContent
                                            class="flex aspect-square items-center justify-center p-4 bg-gray-100">
                                            <img :src="'http://127.0.0.1:8000/storage/' + image.image"
                                                alt="Product photo" class="rounded-lg object-contain w-full h-full" />
                                        </CardContent>
                                    </Card>
                                </CarouselItem>
                            </CarouselContent>

                            <!-- Navigation -->
                            <CarouselPrevious
                                class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-700 hover:text-gray-900" />
                            <CarouselNext
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-700 hover:text-gray-900" />
                        </Carousel>

                        <!-- Fallback if no photos -->
                        <Card v-else class="rounded-xl shadow-md mt-2">
                            <CardContent class="flex aspect-square items-center justify-center p-6 bg-gray-100">
                                <h1 class="text-gray-500 font-medium">No Photos Available</h1>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-span-2 space-y-6">
                    <!-- Title & Basic Info -->
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-1">
                            {{ productDetails.product_name }}
                        </h1>
                        <p class="text-sm text-gray-500">SKU: {{ productDetails.SKU }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-700 text-base">
                        <p>
                            <span class="font-semibold text-gray-900">Brand:</span>
                            {{ productDetails.brand.brand_name }}
                        </p>
                        <p>
                            <span class="font-semibold text-gray-900">Category:</span>
                            {{ productDetails.category.category_name }}
                        </p>
                        <p>
                            <span class="font-semibold text-gray-900">Model:</span>
                            {{ productDetails.model }}
                        </p>
                        <p>
                            <span class="font-semibold text-gray-900">Supplier:</span>
                            {{ productDetails.supplier.supplier_name }}
                        </p>
                    </div>

                    <!-- Price -->
                    <div class="mt-4">
                        <p class="text-gray-500 text-sm uppercase tracking-wide">Unit Price</p>
                        <h2 class="text-4xl font-extrabold text-green-600">₱{{ productDetails.unit_price }}</h2>
                    </div>

                    <!-- Quantity and Reorder Level -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                        <div class="p-4 bg-gray-50 rounded-lg border">
                            <p class="text-sm text-gray-500">Current Quantity</p>
                            <p :class="[
                                'text-2xl font-semibold',
                                productDetails.product_quantity > productDetails.reorder_level
                                    ? 'text-green-600'
                                    : 'text-yellow-600'
                            ]">
                                {{ productDetails.product_quantity }}
                            </p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg border">
                            <p class="text-sm text-gray-500">Reorder Level</p>
                            <p class="text-2xl font-semibold text-blue-600">
                                {{ productDetails.reorder_level }}
                            </p>
                        </div>
                    </div>

                    <!-- Stock Status -->
                    <div class="pt-3 flex justify-center space-x-6 items-center">
                        <span :class="[
                            'inline-block px-4 py-2 text-sm font-semibold rounded-full shadow-sm',
                            productDetails.status === 'Available'
                                ? 'bg-green-100 text-green-700'
                                : productDetails.status === 'Low Stock'
                                    ? 'bg-yellow-100 text-yellow-700'
                                    : productDetails.status === 'Out of Stock'
                                        ? 'bg-red-100 text-red-700'
                                        : 'bg-gray-100 text-gray-700'
                        ]">
                            {{ productDetails.status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Description & Specs -->
    <section v-if="productDetails" class="mt-10 max-w-6xl mx-auto bg-white shadow-sm rounded-2xl p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b pb-2">Description & Specifications</h2>
        <div v-html="productDetails.product_description" class="prose max-w-none text-gray-700 leading-relaxed"></div>
    </section>
</template>
