<script setup>
import { getCategories } from '@/composables/useCategories';
import { manageCategories } from '@/composables/useCategories';
import { computed, onMounted, ref, h } from 'vue';
import { RouterLink } from 'vue-router';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faGear, faPen, faEye, faTrash } from '@fortawesome/free-solid-svg-icons';
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
const { categories, isLoading, fetchCategories } = getCategories();
const { categoryCred, addCategory, deleteCategory, updateCategory, errors } = manageCategories();
const columnHelper = createColumnHelper();
const openUpdateModal = ref(false);
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
onMounted(() => {
    fetchCategories();
})

</script>
<template>
    <!-- Loading state -->
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center p-4">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <h1 class="mt-6 font-semibold text-xl sm:text-2xl text-accents">
            Fetching Product Categories...
        </h1>
        <p class="mt-2 text-gray-500 text-sm">
            Please wait while we load the latest categories records.
        </p>
    </section>

    <!-- Supplier Management Table -->
    <section v-else class="container mx-auto p-6">
        <!-- Page Title + Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Product Categories</h1>
            <AddCategoryModal @add-category="handleAddCategory" :errors="errors" />
        </div>

        <!-- Table -->
        <div class="flex flex-row space-x-3 w-1/2 mb-6">
            <Label>Search:</Label>
            <Input type="text" placeholder="Search here..." v-model="globalFilter" />
        </div>
        <div class="overflow-x-auto rounded-xl shadow-sm bg-white">
            <!-- <Table>
                <TableCaption class="text-gray-600 text-sm py-4">
                    All product categories
                </TableCaption>

                <TableHeader class="bg-gray-50">
                    <TableRow>
                        <TableHead class="text-center text-gray-700 font-semibold">
                            Category
                        </TableHead>
                        <TableHead class="text-center text-gray-700 font-semibold">
                            Description
                        </TableHead>
                        <TableHead class="text-center text-gray-700 font-semibold">Created At</TableHead>
                        <TableHead class="text-center text-gray-700 font-semibold">Updated At</TableHead>
                        <TableHead class="text-center text-gray-700 font-semibold">Actions</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <TableRow v-for="category in categories" class="hover:bg-gray-50 transition">
                        <TableCell class="text-center text-gray-600 whitespace-pre-wrap max-w-xs">{{
                            category.category_name }}</TableCell>
                        <TableCell class="text-center text-gray-600 whitespace-pre-wrap max-w-xs">{{
                            category.category_description }}</TableCell>
                        <TableCell class="text-center text-gray-600 whitespace-pre-wrap max-w-xs">
                            {{ new
                                Date(category.created_at).toLocaleDateString("en-US", {
                                    year: "numeric",
                                    month: "short",
                                    day: "numeric",
                                })
                            }}
                        </TableCell>
                        <TableCell class="text-center text-gray-600 whitespace-pre-wrap max-w-xs">
                            {{ new
                                Date(category.updated_at).toLocaleDateString("en-US", {
                                    year: "numeric",
                                    month: "short",
                                    day: "numeric",
                                })
                            }}
                        </TableCell>
                        <TableCell class="text-center">
                            <div class="flex justify-center space-x-3 text-gray-600">
                                <RouterLink to="/" class="hover:text-accents" title="View">
                                    <FontAwesomeIcon :icon="faEye" />
                                </RouterLink>
                                <RouterLink to="/" class="hover:text-accents" title="Edit">
                                    <FontAwesomeIcon :icon="faPen" />
                                </RouterLink>
                                <button class="hover:text-red-600" title="Delete">
                                    <FontAwesomeIcon :icon="faTrash" />
                                </button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table> -->
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-white uppercase bg-accents hover:bg-accents-hover">
                    <tr class="text-center">
                        <th v-for="header in categoriesTable.getFlatHeaders()" :key="header.id"
                            class="border p-3 font-semibold tracking-wide">
                            <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in categoriesTable.getRowModel().rows" :key="row.id"
                        class="text-center transition-colors duration-150 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td v-for="cell in row.getVisibleCells()" :key="cell.id" class="border px-3 py-2">
                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <CategoryModal @update-category="handleUpdateCategory" v-model:open="openUpdateModal"
        :id="categoryCred.encrypted_id" :category_name="categoryCred.category_name"
        :category_description="categoryCred.category_description" :errors="errors" />
</template>