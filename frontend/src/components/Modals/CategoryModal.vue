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
// REFACTOR THE ADDING OF SUPPLIER DI KO TRIP POTA
const categoryCred = reactive({
    encrypted_id: "",
    category_name: "",
    category_description: "",
});

const emit = defineEmits(['updateCategory']);


const props = defineProps({
    id: String,
    category_name: String,
    category_description: String,
    errors: Object,
});

const handleUpdate = async (categoryCred) => {
    await emit('updateCategory', categoryCred);
}

watch(props, () => {
    categoryCred.encrypted_id = props.id
    categoryCred.category_name = props.category_name
    categoryCred.category_description = props.category_description
},
    {
        immediate: true
    }
);

</script>
<template>
    <Dialog>
        <DialogContent class="sm:max-w-[650px]">
            <DialogHeader>
                <DialogTitle>{{ props.category_name }}' details</DialogTitle>
                <DialogDescription>
                    View Or Make changes
                </DialogDescription>
            </DialogHeader>
            <form id="addCategory" @submit.prevent="handleUpdate(categoryCred)">
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="category_name" class="text-right">
                            Category Name
                        </Label>
                        <Input id="category_name" v-model="categoryCred.category_name" class="col-span-3" required
                            placeholder="Enter Category Name" />
                        <div v-if="props.errors && props.errors.category_name">
                            <ErrorLabel :error="props.errors.category_name" />
                        </div>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="category_description" class="text-right">
                            Description
                        </Label>
                        <Textarea placeholder="Enter Category Description" class="col-span-3"
                            v-model="categoryCred.category_description" required />
                        <div v-if="props.errors && props.errors.category_description">
                            <ErrorLabel :error="props.errors.category_description" />
                        </div>
                    </div>
                </div>
            </form>
            <DialogFooter>
                <Button type="submit" form="addCategory">
                    Add Category
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>