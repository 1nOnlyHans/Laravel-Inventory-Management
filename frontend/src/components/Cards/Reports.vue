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
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { Label } from "@/components/ui/label"

const props = defineProps({
    icon: Object,
    title: String,
    description: String,
})
const emit = defineEmits(['generate'])

const handleEmit = async () => {
    await emit('generate');
}

</script>
<template>
    <Card class="w-[350px] shadow-md hover:shadow-lg transition-shadow duration-200 rounded-2xl md:w-[400px]">
        <CardHeader class="pb-2">
            <CardTitle class="flex justify-between items-center text-gray-800">
                <FontAwesomeIcon :icon="icon" class="text-gray-500 text-3xl" />
                <span class="text-2xl font-semibold">{{ title }}</span>
            </CardTitle>
        </CardHeader>

        <CardContent>
            <div class="flex flex-col gap-1">
                <span class="text-lg font-regular text-gray-900">{{ description }}</span>
            </div>
            <div class="flex justify-end mt-5">
                <Button @click="handleEmit">
                    Generate
                </Button>
            </div>
        </CardContent>
    </Card>
</template>