<script setup>
import { ref } from "vue";
import Button from "../ui/button/Button.vue";
import Input from "../ui/input/Input.vue";
import Label from "../ui/label/Label.vue";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog"

import { defineProps, defineEmits, watch } from "vue";

const props = defineProps({
    isOpen: Boolean,
    product_id: String
});

const isOpen = ref(null);

watch(() => [props.isOpen], () => {
    isOpen.value = props.isOpen
}, {
    immediate: true
})
</script>
<template>
    <Dialog v-model:open="isOpen">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle class="text-center">Select an Action</DialogTitle>
            </DialogHeader>
            <div class="grid gap-4 py-4">
                <div class="grid grid-cols-2 items-center gap-4">
                    <Button class="bg-accents hover:bg-accents-hover">Edit</Button>
                    <Button class="bg-red-500 hover:bg-red-900">Delete</Button>
                    <Button class="bg-green-500 hover:bg-green-900">Restock</Button>
                    <Button class="bg-gray-500 hover:bg-gray-900" @click="isOpen = false">Cancel</Button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>