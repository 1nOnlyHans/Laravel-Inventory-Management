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
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import Textarea from "../ui/textarea/Textarea.vue"
import { defineProps, onMounted, reactive, watch, defineEmits } from "vue"
import ErrorLabel from "../Errors/ErrorLabel.vue"
import { manageSupplier } from '@/composables/useSuppliers';

const supplierCred = reactive({
    encrypted_id: "",
    supplier_name: "",
    contact_person: "",
    phone: "",
    email: "",
    address: "",
});

const props = defineProps({
    id: String,
    name: String,
    contact: String,
    phone: String,
    email: String,
    address: String,
    errors: Object,
});

const emit = defineEmits(['updateSupplier']);

const handleUpdate = async (supplierCred) => {
    emit('updateSupplier', supplierCred); // doesn't return shits// main logic at the parent component
}

watch(props, () => {
    supplierCred.encrypted_id = props.id
    supplierCred.supplier_name = props.name
    supplierCred.contact_person = props.contact
    supplierCred.phone = props.phone
    supplierCred.email = props.email
    supplierCred.address = props.address
}, {
    immediate: true
})

</script>
<template>
    <Dialog>
        <DialogContent class="sm:max-w-[650px]">
            <DialogHeader>
                <DialogTitle>{{ props.name }}'s Details</DialogTitle>
                <DialogDescription>
                    View or Make Changes
                </DialogDescription>
            </DialogHeader>
            <form id="updateSupplier" @submit.prevent="handleUpdate(supplierCred)">
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="supplier_name" class="text-right">
                            Name
                        </Label>
                        <Input id="supplier_name" v-model="supplierCred.supplier_name" class="col-span-3" required />
                        <div v-if="props.errors && props.errors.supplier_name">
                            <ErrorLabel :error="props.errors.supplier_name" />
                        </div>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="contact_person" class="text-right">
                            Contact
                        </Label>
                        <Input id="contact_person" v-model="supplierCred.contact_person" class="col-span-3" required />
                        <div v-if="props.errors && props.errors.contact_person">
                            <ErrorLabel :error="props.errors.contact_person" />
                        </div>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="phone" class="text-right">
                            Phone#
                        </Label>
                        <Input id="phone" v-model="supplierCred.phone" class="col-span-3" required />
                        <div v-if="props.errors && props.errors.phone">
                            <ErrorLabel :error="props.errors.phone" />
                        </div>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="email" class="text-right">
                            Email
                        </Label>
                        <Input type="email" id="email" v-model="supplierCred.email" class="col-span-3" required />
                        <div v-if="props.errors && props.errors.email">
                            <ErrorLabel :error="props.errors.email" />
                        </div>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="contact_person" class="text-right">
                            Address
                        </Label>
                        <Textarea placeholder="Address" class="col-span-3" v-model="supplierCred.address" required />
                        <div v-if="props.errors && props.errors.address">
                            <ErrorLabel :error="props.errors.address" />
                        </div>
                    </div>
                </div>
            </form>
            <DialogFooter>
                <Button type="submit" form="updateSupplier" class="bg-accents hover:bg-accents-hover">
                    Save changes
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>