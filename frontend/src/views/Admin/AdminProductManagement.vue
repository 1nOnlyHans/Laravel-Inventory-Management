<script setup>
import { getProducts } from '@/composables/useProducts';
import { sendPO } from '@/composables/usePO';
import { onMounted } from 'vue';

const { products, isLoading, fetchProducts } = getProducts();
onMounted(async () => {
    await getProducts();

});

</script>
<template>
    <h1>Products</h1>
</template>