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
    DialogClose
} from "@/components/ui/dialog"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { faFileCsv } from "@fortawesome/free-solid-svg-icons"
import { ref } from "vue"

const emit = defineEmits(['import']);
const fileCred = ref(null);

const handleFileChange = (event) => {
    fileCred.value = event.target.files[0];
}

const handleEmit = async () => {
    await emit('import', fileCred.value);
}

</script>
<template>
    <Dialog>
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Import Categories</DialogTitle>
                <DialogDescription>
                    Select CSV file to import multiple categories at once.
                </DialogDescription>
            </DialogHeader>
            <form id="import-form" @submit.prevent="handleEmit">
                <div class="flex items-center space-x-2">
                    <div class="grid flex-1 gap-2">
                        <Label for="link" class="sr-only">
                            CSV File
                        </Label>
                        <Input type="file" placeholder="Select CSV file" @change="handleFileChange" required
                            accept=".csv" />
                    </div>
                </div>
            </form>
            <DialogFooter class="sm:justify-end">
                <DialogClose as-child>
                    <Button type="button" variant="secondary">
                        Close
                    </Button>
                </DialogClose>
                <Button type="submit" form="import-form">
                    Import
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>