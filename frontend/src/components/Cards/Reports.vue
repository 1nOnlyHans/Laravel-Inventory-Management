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
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select"
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { Label } from "@/components/ui/label"
import { ref } from "vue"

const reportFilter = ref('Overall');

const props = defineProps({
    icon: Object,
    title: String,
    description: String,
})
const emit = defineEmits(['generate'])

const handleEmit = async () => {
    await emit('generate', reportFilter.value);
}

</script>
<template>
    <Card
        class="w-full max-w-sm md:max-w-md lg:max-w-lg mx-auto shadow-lg hover:shadow-xl transition-shadow duration-300 rounded-xl bg-white border border-gray-200">
        <CardHeader class="pb-4">
            <CardTitle class="flex justify-between items-center text-gray-900">
                <FontAwesomeIcon :icon="icon" class="text-3xl" />
                <span class="text-2xl font-bold">{{ title }}</span>
            </CardTitle>
        </CardHeader>

        <CardContent class="space-y-6">
            <div class="text-center">
                <span class="text-lg font-medium text-gray-700">{{ description }}</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <Select v-model="reportFilter">
                    <SelectTrigger
                        class="w-full sm:w-[220px] border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md">
                        <SelectValue placeholder="Filter Reports By" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectLabel>Filter By</SelectLabel>
                            <SelectItem value="Overall">
                                Overall Reports
                            </SelectItem>
                            <SelectItem value="Daily">
                                Daily Reports
                            </SelectItem>
                            <SelectItem value="Weekly">
                                Weekly Reports
                            </SelectItem>
                            <SelectItem value="Monthly">
                                Monthly Reports
                            </SelectItem>
                            <SelectItem value="Yearly">
                                Yearly Reports
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
                <Button @click="handleEmit">
                    Generate
                </Button>
            </div>
        </CardContent>
    </Card>
</template>