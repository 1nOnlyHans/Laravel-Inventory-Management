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
import { Label } from "@/components/ui/label"
import {
    NumberField,
    NumberFieldContent,
    NumberFieldDecrement,
    NumberFieldIncrement,
    NumberFieldInput,
} from "@/components/ui/number-field"
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { defineProps, defineEmits, warn, watch, ref, onMounted } from "vue";
import { faTrash } from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
    product: Object,
    cart: Array,
    index: Number,
});

const emit = defineEmits(['removeItem', 'updateQuantity']);

const handleEmit = (index) => {
    emit('removeItem', index);
}

const quantity = ref(0);

onMounted(() => {
    // initialize from product quantity, not 0
    quantity.value = props.product.quantity ?? 0;
});

watch(quantity, (newQuantity) => {
    emit('updateQuantity', { index: props.index, quantity: newQuantity });
});

</script>
<template>
    <Card class="w-full bg-white border rounded-2xl shadow-sm hover:shadow-lg transition-all duration-200">
        <!-- Header -->
        <CardHeader class="p-5 pb-3">
            <div class="flex justify-between items-start">
                <div>
                    <CardTitle class="text-lg font-semibold truncate">
                        {{ props.product.product_name }}
                    </CardTitle>
                    <CardDescription class="text-sm text-gray-500 mt-1">
                        {{ props.product.brand_name }}
                    </CardDescription>
                </div>

                <Button variant="secondary" size="icon" class="ml-4" @click="handleEmit(index)">
                    <FontAwesomeIcon :icon="faTrash" />
                </Button>
            </div>
        </CardHeader>

        <!-- Content -->
        <CardContent class="px-5 pb-5">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <Label for="quantity" class="text-sm font-medium text-gray-700">
                        Quantity
                    </Label>
                    <NumberField id="quantity" :default-value="1" :min="1" :max="props.product.max_quantity"
                        v-model="quantity">
                        <NumberFieldContent>
                            <NumberFieldDecrement />
                            <NumberFieldInput class="w-16 text-center" />
                            <NumberFieldIncrement />
                        </NumberFieldContent>
                    </NumberField>
                </div>

                <div class="text-right">
                    <h1 class="text-base font-medium text-gray-800">
                        ₱{{ props.product.unit_price }}
                        <span class="text-gray-500 text-sm">/each</span>
                    </h1>
                </div>
            </div>
        </CardContent>
    </Card>
</template>