<script setup>
import { getSuppliers } from '@/composables/useSuppliers';
import { manageSupplier } from '@/composables/useSuppliers';
import { CSVImport } from '@/composables/useCsv';
import { computed, onMounted, ref, h, reactive } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faGear, faPen, faEye, faTrash, faFileCsv } from '@fortawesome/free-solid-svg-icons';
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
import SupplierModal from '@/components/Modals/SupplierModal.vue';
import AddSupplierModal from '@/components/Modals/AddSupplierModal.vue';
import Button from '@/components/ui/button/Button.vue';
import { faPenToSquare } from '@fortawesome/free-regular-svg-icons';
import { ConfirmationSwal, RegularSwal } from '@/components/Swals/useSwals';
import CSVSupplierModal from '@/components/Modals/CSVSupplierModal.vue';
const { suppliers, isLoading, fetchSuppliers } = getSuppliers();
const { updateSupplier, errors, deleteSupplier, addSupplier } = manageSupplier();
const { loadingImport, importSuppliers } = CSVImport();

const openCsvModal = ref(false);
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
        header: "Email",
        cell: info => info.getValue()
    }),
    columnHelper.accessor('address', {
        id: "Address",
        header: "Address",
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
                                errors.value = null
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
                            errors.value = null
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
    errors.value = null
    const success = await addSupplier(supplierCred);
    if (success && success.status === 200) {
        openUpdateModal.value = false
        await fetchSuppliers();
        RegularSwal(success.data);
    }
}

//====================================================
const handleUpdateSupplier = async (supplierCred) => {
    errors.value = null
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

const handleImport = async (file) => {
    const success = await importSuppliers(file);
    if (success && success.status === 200) {
        openCsvModal.value = false;
        await fetchSuppliers();
        RegularSwal(success.data);
    } else {
        openCsvModal.value = false;
        RegularSwal({
            icon: 'error',
            title: 'Import Error'
        });
    }
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
    <!-- Loading State -->
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center p-4">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <h1 class="mt-6 font-semibold text-xl sm:text-2xl text-accents">
            Fetching Suppliers...
        </h1>
        <p class="mt-2 text-gray-500 text-sm">
            Please wait while we load the latest supplier records.
        </p>
    </section>

    <section v-else-if="loadingImport" class="min-h-screen flex flex-col items-center justify-center text-center p-4">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <h1 class="mt-4 font-bold text-xl sm:text-2xl text-accents">
            Importing Data...
        </h1>
    </section>

    <!-- Supplier Management Table -->
    <section v-else class="container mx-auto p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div class="">
                <h1 class="text-2xl font-bold text-gray-800 tracking-wide">
                    Suppliers
                </h1>
                <p class="text-sm text-gray-500">Manage Suppliers</p>
            </div>
            <div class="flex justify-center space-x-3">
                <AddSupplierModal @add-supplier="handleAddSupplier" :errors="errors" :supplier="supplierCred" />
                <Button @click="openCsvModal = true">
                    <FontAwesomeIcon :icon="faFileCsv" />
                    Bulk Import
                </Button>
            </div>
        </div>

        <!-- Search -->
        <div class="flex flex-row items-center gap-3 mb-5 w-full sm:w-1/2">
            <Label>Search:</Label>
            <Input type="text" placeholder="Search supplier..." v-model="globalFilter" class="w-full" />
        </div>

        <!-- Table Card -->
        <div class="overflow-x-auto rounded-2xl bg-white shadow-md border border-gray-200">
            <table class="w-full text-sm text-gray-700">
                <!-- Table Head -->
                <thead class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wide">
                    <tr>
                        <th v-for="header in suppliersTable.getFlatHeaders()" :key="header.id"
                            class="px-4 py-3 text-center font-semibold border-b border-gray-300">
                            <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody v-if="suppliers.length > 0" class="divide-y divide-gray-100">
                    <tr v-for="row in suppliersTable.getRowModel().rows" :key="row.id"
                        class="hover:bg-gray-50 transition-all duration-150 text-center">
                        <td v-for="cell in row.getVisibleCells()" :key="cell.id" class="px-4 py-2 border-b">
                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                        </td>
                    </tr>
                </tbody>

                <!-- Empty State -->
                <tbody v-else>
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-400">
                            No suppliers found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Update Modal -->
        <SupplierModal v-model:open="openUpdateModal" :id="supplierCred.encrypted_id" :name="supplierCred.supplier_name"
            :contact="supplierCred.contact_person" :phone="supplierCred.phone" :email="supplierCred.email"
            :address="supplierCred.address" @update-supplier="handleUpdateSupplier" :errors="errors" />
        <CSVSupplierModal v-model:open="openCsvModal" v-on:import="handleImport" />
    </section>


</template>
