<script setup>
import { Button } from "@/components/ui/button"
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog"
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import Textarea from "../ui/textarea/Textarea.vue"
import { defineProps, onMounted, reactive, watch, defineEmits, ref } from "vue"
import ErrorLabel from "../Errors/ErrorLabel.vue"
import { faAdd } from "@fortawesome/free-solid-svg-icons"

const brandCred = reactive({
    brand_name: "",
    brand_description: "",
});

const emit = defineEmits(['addBrand']);

const handleSubmit = async (brandCred) => {
    await emit('addBrand', brandCred);
}

const props = defineProps({
    errors: Object,
});

</script>
<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button>
                <FontAwesomeIcon :icon="faAdd" /> Brand
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[650px]">
            <DialogHeader>
                <DialogTitle>Add New Brand</DialogTitle>
                <DialogDescription>
                    Add new Brand
                </DialogDescription>
            </DialogHeader>
            <form id="addCategory" @submit.prevent="handleSubmit(brandCred)">
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="category_name" class="text-right">
                            Brand Name
                        </Label>
                        <Input id="category_name" v-model="brandCred.brand_name" class="col-span-3" required
                            placeholder="Enter Category Name" />
                        <div v-if="props.errors && props.errors.brand_name">
                            <ErrorLabel :error="props.errors.brand_name" />
                        </div>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="category_description" class="text-right">
                            Description
                        </Label>
                        <Textarea placeholder="Enter Category Description" class="col-span-3"
                            v-model="brandCred.brand_description" required />
                        <div v-if="props.errors && props.errors.brand_description">
                            <ErrorLabel :error="props.errors.brand_description" />
                        </div>
                    </div>
                </div>
            </form>
            <DialogFooter>
                <Button type="submit" form="addCategory">
                    Add Brand
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>