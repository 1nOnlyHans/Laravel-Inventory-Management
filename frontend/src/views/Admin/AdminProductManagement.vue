<script setup>
import { getProducts } from '@/composables/useProducts';
import { onMounted } from 'vue';

const { products, isLoading, fetchProducts } = getProducts();

onMounted(() => {
    getProducts();
});

</script>
<template>
    <h1>Products</h1>
</template>