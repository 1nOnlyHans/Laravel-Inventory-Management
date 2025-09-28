<script setup>
import { defineProps } from 'vue';
const props = defineProps({
    error: Object
})
</script>
<template>
    <p class="text-red-500 italic font-semibold text-sm" v-for="error in props.error">{{
        error }}</p>
</template>