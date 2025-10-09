<script setup>
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { defineProps } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

const props = defineProps({
    title: String,
    icon: Object,
    value: [Number, String],
    subtitle: String,
});
</script>

<template>
    <Card class="w-[350px] shadow-md hover:shadow-lg transition-shadow duration-200 rounded-2xl">
        <CardHeader class="pb-2">
            <CardTitle class="flex justify-between items-center text-gray-800">
                <span class="text-lg font-semibold">{{ title }}</span>
                <FontAwesomeIcon :icon="icon" class="text-gray-500 text-xl" />
            </CardTitle>
        </CardHeader>

        <CardContent>
            <div class="flex flex-col gap-1">
                <span class="text-3xl font-regular text-gray-900">{{ props.title === 'Inventory Value' ? '₱' : '' }}{{ value
                    }}</span>
                <span class="text-sm text-gray-500">{{ subtitle }}</span>
            </div>
        </CardContent>
    </Card>
</template>
