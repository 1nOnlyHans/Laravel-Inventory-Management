<script setup>
import { getCategories } from '@/composables/useCategories';
import { manageCategories } from '@/composables/useCategories';
import { CSVImport } from '@/composables/useCsv';
import { computed, onMounted, ref, h } from 'vue';
import { RouterLink } from 'vue-router';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faGear, faPen, faEye, faTrash, faFileCsv } from '@fortawesome/free-solid-svg-icons';
import Button from '@/components/ui/button/Button.vue';
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
// TanStack Table
import {
    useVueTable,
    FlexRender,
    getCoreRowModel,
    createColumnHelper,
    getFilteredRowModel,
} from '@tanstack/vue-table';
import { faPenToSquare } from '@fortawesome/free-regular-svg-icons';
import AddCategoryModal from '@/components/Modals/AddCategoryModal.vue';
import CategoryModal from '@/components/Modals/CategoryModal.vue';
import { ConfirmationSwal, RegularSwal } from '@/components/Swals/useSwals';
import CSVCategoriesModal from '@/components/Modals/CSVCategoriesModal.vue';
const { categories, isLoading, fetchCategories } = getCategories();
const { categoryCred, addCategory, deleteCategory, updateCategory, errors } = manageCategories();
const { loadingImport, importCategories } = CSVImport();
const columnHelper = createColumnHelper();
const openUpdateModal = ref(false);
const openCsvModal = ref(false);
const columns = [
    columnHelper.accessor('category_name', {
        id: "Category",
        header: "Category",
        cell: info => info.getValue()
    }),
    columnHelper.accessor('category_description', {
        id: "Description",
        header: "Description",
        cell: info => info.getValue()
    }),
    columnHelper.accessor(row => new Date(row.created_at).toLocaleDateString('en-us', {
        year: "numeric", month: "short", day: "numeric"
    }), {
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
    columnHelper.accessor(row => new Date(row.updated_at).toLocaleDateString('en-us', {
        year: "numeric", month: "short", day: "numeric"
    }), {
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
                            variant: "none",
                            onClick: () => {
                                errors.value = null
                                openUpdateModal.value = true
                                categoryCred.encrypted_id = row.original.encrypted_id
                                categoryCred.category_name = row.original.category_name
                                categoryCred.category_description = row.original.category_description
                            }
                        },
                        () => h(FontAwesomeIcon, { icon: faPenToSquare })
                    ),
                    h(
                        Button,
                        {
                            variant: "none",
                            onClick: async () => {
                                errors.value = null
                                await handleDeleteCategory(row.original.encrypted_id)
                            }
                        },
                        () => h(FontAwesomeIcon, { icon: faTrash })
                    )
                ]
            )
        }
    }
];

const globalFilter = ref("");
const categoriesTable = useVueTable({
    data: computed(() => categories.value),
    columns,
    state: {
        get globalFilter() {
            return globalFilter.value
        }
    },
    onGlobalFilterChange: value => {
        globalFilter.value = value
    },
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel()
})

const handleAddCategory = async (categoryCred) => {
    errors.value = null
    const success = await addCategory(categoryCred);
    if (success && success.status === 200) {
        await fetchCategories();
        RegularSwal(success.data);
    }
}

const handleUpdateCategory = async (categoryCred) => {
    errors.value = null
    const success = await updateCategory(categoryCred);
    if (success && success.status === 200) {
        openUpdateModal.value = false
        await fetchCategories();
        RegularSwal(success.data);
    }
}

const handleDeleteCategory = async (id) => {
    ConfirmationSwal({
        icon: "warning",
        title: "Delete Category?",
        message: "Category Data will be deleted permanently",
        confirm_text: "Yes! Delete it",
        callBack: async () => {
            const success = await deleteCategory(id)
            if (success && success.status === 200) {
                await fetchCategories();
                RegularSwal(success.data);
            }
        }
    })
}

const handleImport = async (file) => {
    const success = await importCategories(file);
    if (success && success.status === 200) {
        openCsvModal.value = false;
        await fetchCategories();
        RegularSwal(success.data);
    } else {
        openCsvModal.value = false;
        RegularSwal({
            icon: 'error',
            title: 'Import Error'
        });
    }
}

onMounted(() => {
    fetchCategories();
})

</script>
<template>
    <!-- Loading State -->
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center p-4">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <h1 class="mt-6 font-semibold text-xl sm:text-2xl text-accents">
            Fetching Product Categories...
        </h1>
        <p class="mt-2 text-gray-500 text-sm">
            Please wait while we load the latest categories records.
        </p>
    </section>

    <section v-else-if="loadingImport" class="min-h-screen flex flex-col items-center justify-center text-center p-4">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <h1 class="mt-4 font-bold text-xl sm:text-2xl text-accents">
            Importing Data...
        </h1>
    </section>

    <!-- Category Management Table -->
    <section v-else class="container mx-auto p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800 tracking-wide">
                Product Categories
            </h1>
            <div class="flex justify-center space-x-3">
                <AddCategoryModal @add-category="handleAddCategory" :errors="errors" />
                <Button @click="openCsvModal = true">
                    <FontAwesomeIcon :icon="faFileCsv" />
                    Bulk Import
                </Button>
            </div>

        </div>

        <!-- Search -->
        <div class="flex flex-row items-center gap-3 mb-5 w-full sm:w-1/2">
            <Label>Search:</Label>
            <Input type="text" placeholder="Search category..." v-model="globalFilter" class="w-full" />
        </div>

        <!-- Table Card -->
        <div class="overflow-x-auto rounded-2xl bg-white shadow-md border border-gray-200">
            <table class="w-full text-sm text-gray-700">
                <!-- Table Head -->
                <thead class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wide">
                    <tr>
                        <th v-for="header in categoriesTable.getFlatHeaders()" :key="header.id"
                            class="px-4 py-3 text-center font-semibold border-b border-gray-300">
                            <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody v-if="categories.length > 0" class="divide-y divide-gray-100">
                    <tr v-for="row in categoriesTable.getRowModel().rows" :key="row.id"
                        class="hover:bg-gray-50 transition-all duration-150 text-center">
                        <td v-for="cell in row.getVisibleCells()" :key="cell.id" class="px-4 py-2 border-b">
                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                        </td>
                    </tr>
                </tbody>

                <!-- Empty State -->
                <tbody v-else>
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-400">
                            No categories found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Update Modal -->
        <CategoryModal @update-category="handleUpdateCategory" v-model:open="openUpdateModal"
            :id="categoryCred.encrypted_id" :category_name="categoryCred.category_name"
            :category_description="categoryCred.category_description" :errors="errors" />
        <CSVCategoriesModal v-model:open="openCsvModal" v-on:import="handleImport" />
    </section>


</template>
