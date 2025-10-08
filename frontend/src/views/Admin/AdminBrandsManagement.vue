<script setup>
import { getBrands } from '@/composables/useBrands';
import { manageBrands } from '@/composables/useBrands';
import { computed, onMounted, ref, h, reactive } from 'vue';
import AddBrandModal from '@/components/Modals/AddBrandModal.vue';
import BrandModal from '@/components/Modals/BrandModal.vue';
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import {
    useVueTable,
    FlexRender,
    getCoreRowModel,
    createColumnHelper,
    getFilteredRowModel,
} from '@tanstack/vue-table';
import { RegularSwal, ConfirmationSwal } from '@/components/Swals/useSwals';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faPenToSquare } from '@fortawesome/free-regular-svg-icons';
import { faTrash } from '@fortawesome/free-solid-svg-icons';
const { brands, fetchBrands, isLoading } = getBrands();
const { errors, addBrand, updateBrand, deleteBrand } = manageBrands();
const columnHelper = createColumnHelper();

const brandCred = reactive({
    encrypted_id: '',
    brand_name: '',
    brand_description: ''
});
const openUpdateModal = ref(false);

const columns = [
    columnHelper.accessor('brand_name', {
        id: "Brand",
        header: "Brand",
        cell: info => info.getValue()
    }),
    columnHelper.accessor('brand_description', {
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
                                brandCred.encrypted_id = row.original.encrypted_id
                                brandCred.brand_name = row.original.brand_name
                                brandCred.brand_description = row.original.brand_description
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
                                await handleDeleteBrand(row.original.encrypted_id)
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

const brandsTable = useVueTable({
    data: computed(() => brands.value),
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
});

const handleAddBrand = async (brandCred) => {
    const success = await addBrand(brandCred);
    if (success && success.status === 200) {
        await fetchBrands();
        RegularSwal(success.data);
    }
}

const handleUpdatebrand = async (brandCred) => {
    const success = await updateBrand(brandCred);
    if (success && success.status === 200) {
        openUpdateModal.value = false
        await fetchBrands();
        RegularSwal(success.data);
    }
}

const handleDeleteBrand = async (id) => {
    ConfirmationSwal({
        icon: "warning",
        title: "Delete Brand?",
        message: "Brand Data will be deleted permanently",
        confirm_text: "Yes! Delete it",
        callBack: async () => {
            const success = await deleteBrand(id)
            if (success && success.status === 200) {
                await fetchBrands();
                RegularSwal(success.data);
            }
        }
    })
}
onMounted(async () => {
    await fetchBrands();
});
</script>
<template>
    <!-- Loading State -->
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center p-4">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <h1 class="mt-6 font-semibold text-xl sm:text-2xl text-accents">
            Fetching Product Brands...
        </h1>
        <p class="mt-2 text-gray-500 text-sm">
            Please wait while we load the latest brands records.
        </p>
    </section>

    <!-- Brand Management Table -->
    <section v-else class="container mx-auto p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800 tracking-wide">
                Product Brands
            </h1>
            <AddBrandModal @add-brand="handleAddBrand" :errors="errors" />
        </div>

        <!-- Search -->
        <div class="flex flex-row items-center gap-3 mb-5 w-full sm:w-1/2">
            <Label>Search:</Label>
            <Input type="text" placeholder="Search brand..." v-model="globalFilter" class="w-full" />
        </div>

        <!-- Table Card -->
        <div class="overflow-x-auto rounded-2xl bg-white shadow-md border border-gray-200">
            <table class="w-full text-sm text-gray-700">
                <!-- Table Head -->
                <thead class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wide">
                    <tr>
                        <th v-for="header in brandsTable.getFlatHeaders()" :key="header.id"
                            class="px-4 py-3 text-center font-semibold border-b border-gray-300">
                            <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody v-if="brands.length > 0" class="divide-y divide-gray-100">
                    <tr v-for="row in brandsTable.getRowModel().rows" :key="row.id"
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
                            No brands found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Update Modal -->
    <BrandModal v-model:open="openUpdateModal" :errors="errors" :id="brandCred.encrypted_id"
        :brand_name="brandCred.brand_name" :brand_description="brandCred.brand_description"
        @update-brand="handleUpdatebrand" />
</template>
