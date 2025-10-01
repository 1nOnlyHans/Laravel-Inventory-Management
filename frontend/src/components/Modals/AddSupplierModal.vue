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
const supplierCred = reactive({
    supplier_name: "",
    contact_person: "",
    phone: "",
    email: "",
    address: "",
});

const emit = defineEmits(['addSupplier']);

const handleSubmit = async (supplierCred) => {
    emit('addSupplier', supplierCred)
}

const props = defineProps({
    errors: Object,
});

</script>
<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button class="bg-accents hover:bg-accents-hover">
                <FontAwesomeIcon :icon="faAdd" /> Supplier
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[650px]">
            <DialogHeader>
                <DialogTitle>Add New Supplier</DialogTitle>
                <DialogDescription>
                    Add new supplier
                </DialogDescription>
            </DialogHeader>
            <form id="addSupplier" @submit.prevent="handleSubmit(supplierCred)">
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="supplier_name" class="text-right">
                            Name
                        </Label>
                        <Input id="supplier_name" v-model="supplierCred.supplier_name" class="col-span-3" required
                            placeholder="Enter Supplier Name" />
                        <div v-if="props.errors && props.errors.supplier_name">
                            <ErrorLabel :error="props.errors.supplier_name" />
                        </div>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="contact_person" class="text-right">
                            Contact
                        </Label>
                        <Input id="contact_person" v-model="supplierCred.contact_person" class="col-span-3" required
                            placeholder="Enter Supplier Contact Person" />
                        <div v-if="props.errors && props.errors.contact_person">
                            <ErrorLabel :error="props.errors.contact_person" />
                        </div>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="phone" class="text-right">
                            Phone#
                        </Label>
                        <Input id="phone" v-model="supplierCred.phone" class="col-span-3" required
                            placeholder="Enter Supplier Phone#" />
                        <div v-if="props.errors && props.errors.phone">
                            <ErrorLabel :error="props.errors.phone" />
                        </div>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="email" class="text-right">
                            Email
                        </Label>
                        <Input type="email" id="email" v-model="supplierCred.email" class="col-span-3" required
                            placeholder="Enter Supplier Email" />
                        <div v-if="props.errors && props.errors.email">
                            <ErrorLabel :error="props.errors.email" />
                        </div>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="contact_person" class="text-right">
                            Address
                        </Label>
                        <Textarea placeholder="Enter Supplier Address" class="col-span-3" v-model="supplierCred.address"
                            required />
                        <div v-if="props.errors && props.errors.address">
                            <ErrorLabel :error="props.errors.address" />
                        </div>
                    </div>
                </div>
            </form>
            <DialogFooter>
                <Button type="submit" form="addSupplier" class="bg-accents hover:bg-accents-hover">
                    Add Supplier
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>