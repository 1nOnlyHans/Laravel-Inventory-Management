<script setup>
import { getSuppliers } from '@/composables/useSuppliers';
import { manageSupplier } from '@/composables/useSuppliers';
import { computed, onMounted, ref, h, reactive } from 'vue';
import { RouterLink } from 'vue-router';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faGear, faPen, faEye, faTrash, } from '@fortawesome/free-solid-svg-icons';
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
// TanStack Table
import {
    useVueTable,
    FlexRender,
    getCoreRowModel,
    createColumnHelper,
    getFilteredRowModel,
} from '@tanstack/vue-table';
// Dialog
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog"
import SupplierModal from '@/components/Modals/SupplierModal.vue';
import AddSupplierModal from '@/components/Modals/AddSupplierModal.vue';
import Button from '@/components/ui/button/Button.vue';
import { faPenToSquare } from '@fortawesome/free-regular-svg-icons';
import { ConfirmationSwal, RegularSwal } from '@/components/Swals/useSwals';
const { suppliers, isLoading, fetchSuppliers } = getSuppliers();
const { updateSupplier, errors, deleteSupplier, addSupplier } = manageSupplier();

const supplierCred = reactive({
    encrypted_id: "",
    supplier_name: "",
    contact_person: "",
    phone: "",
    email: "",
    address: "",
});
const openUpdateModal = ref(false);
const columnHelper = createColumnHelper();

const columns = [
    columnHelper.accessor('supplier_name', {
        id: "Supplier Name",
        header: "Supplier Name",
        cell: info => info.getValue()
    }),
    columnHelper.accessor('contact_person', {
        id: "Contact Person",
        header: "Contact Person",
        cell: info => info.getValue()
    }),
    columnHelper.accessor('phone', {
        id: "Phone",
        header: "Phone#",
        cell: info => info.getValue()
    }),
    columnHelper.accessor('email', {
        id: "Email",
        header: "Email#",
        cell: info => info.getValue()
    }),
    columnHelper.accessor('address', {
        id: "Address",
        header: "Address#",
        cell: info => info.getValue()
    }),
    columnHelper.accessor(row => new Date(row.created_at).toLocaleDateString('en-us', { year: "numeric", month: "short", day: "numeric" }), {
        id: "Created At",
        header: "Created At",
        cell: ({ row }) => {
            const formmattedDate = new Date(row.original.created_at).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
            return formmattedDate
        }
    }),
    columnHelper.accessor(row => new Date(row.updated_at).toLocaleDateString('en-us', { year: "numeric", month: "short", day: "numeric" }), {
        id: "Updated At",
        header: "Updated At",
        cell: ({ row }) => {
            const formmattedDate = new Date(row.original.updated_at).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
            return formmattedDate
        }
    }),
    {
        id: "Actions",
        header: "Actions",
        cell: ({ row }) => {
            return h(
                "div",
                {
                    class: "flex justify-center items-center space-x-3 text-gray-600"
                },
                [
                    h(
                        Button,
                        {
                            onClick: () => {
                                openUpdateModal.value = true
                                supplierCred.encrypted_id = row.original.encrypted_id
                                supplierCred.supplier_name = row.original.supplier_name
                                supplierCred.contact_person = row.original.contact_person
                                supplierCred.phone = row.original.phone
                                supplierCred.email = row.original.email
                                supplierCred.address = row.original.address
                            },
                            variant: "none"
                        },
                        () => h(FontAwesomeIcon, { icon: faPenToSquare })
                    ),
                    , h(
                        Button, {
                        onClick: () => {
                            handleRemoveSupplier(row.original.encrypted_id);
                        },
                        variant: "none"
                    },
                        () => h(FontAwesomeIcon, { icon: faTrash })
                    )
                ]
            );
        }
    }
];

// REFACTOR THIS SHIT 
const handleAddSupplier = async (supplierCred) => {
    const success = await addSupplier(supplierCred);
    if (success && success.status === 200) {
        openUpdateModal.value = false
        await fetchSuppliers();
        RegularSwal(success.data);
    }
}
//====================================================
const handleUpdateSupplier = async (supplierCred) => {
    const success = await updateSupplier(supplierCred);
    if (success && success.status === 200) {
        openUpdateModal.value = false
        await fetchSuppliers();
        RegularSwal(success.data);
    }
}

const handleRemoveSupplier = async (id) => {
    ConfirmationSwal({
        icon: "warning",
        title: "Remove Supplier?",
        message: "Selected Supplier Datas will be permanently removed",
        confirm_text: "Yes! Delete it",
        callBack: async () => {
            const success = await deleteSupplier(id);
            if (success && success.status === 200) {
                await fetchSuppliers();
                RegularSwal(success.data);
            }
        }
    })
}
const globalFilter = ref("");
const suppliersTable = useVueTable({
    data: computed(() => suppliers.value),
    columns,
    state: {
        get globalFilter() {
            return globalFilter.value
        }
    },
    onGlobalFilterChange: value => {
        globalFilter.value = value;
    },
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel()
});
onMounted(() => {
    fetchSuppliers();
});
</script>

<template>
    <!-- Loading state -->
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center p-4">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <h1 class="mt-6 font-semibold text-xl sm:text-2xl text-accents">
            Fetching Suppliers...
        </h1>
        <p class="mt-2 text-gray-500 text-sm">
            Please wait while we load the latest supplier records.
        </p>
    </section>

    <!-- Supplier Management Table -->
    <section v-else class="container mx-auto p-6">
        <!-- Page Title + Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Supplier Management</h1>
            <!-- DI KO TRIP TO -->
            <AddSupplierModal @add-supplier="handleAddSupplier" :errors="errors" :supplier="supplierCred" />
        </div>
        <!-- Table -->
        <div class="flex flex-row space-x-3 w-1/2 mb-6">
            <Label>Search:</Label>
            <Input type="text" placeholder="Search here..." v-model="globalFilter" />
        </div>
        <div class="overflow-x-auto rounded-xl shadow-sm bg-white">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-200">
                    <tr class="text-center">
                        <th v-for="header in suppliersTable.getFlatHeaders()" :key="header.id"
                            class="border p-3 font-semibold tracking-wide">
                            <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in suppliersTable.getRowModel().rows" :key="row.id"
                        class="text-center transition-colors duration-150 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td v-for="cell in row.getVisibleCells()" :key="cell.id" class="border px-3 py-2">
                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Modal -->
    <SupplierModal v-model:open="openUpdateModal" :id="supplierCred.encrypted_id" :name="supplierCred.supplier_name"
        :contact="supplierCred.contact_person" :phone="supplierCred.phone" :email="supplierCred.email"
        :address="supplierCred.address" @update-supplier="handleUpdateSupplier" :errors="errors" />
</template>
