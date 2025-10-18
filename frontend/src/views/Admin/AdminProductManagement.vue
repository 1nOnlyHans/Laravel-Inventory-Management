<script setup>
import { getProducts } from '@/composables/useProducts';
import { manageProducts } from '@/composables/useProducts';
import { computed, onMounted, ref, h, reactive, onUnmounted } from 'vue';
import AddProductModal from '@/components/Modals/AddProductModal.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faGear, faPen, faEye, faTrash, faAdd } from '@fortawesome/free-solid-svg-icons';
import { Button } from "@/components/ui/button";
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import ProductActionModal from '@/components/Modals/ProductActionModal.vue';
import ProductOptionSilder from '@/components/Sliders/ProductOptionSilder.vue';
const { products, isLoading, fetchProducts } = getProducts();
const { errors, addProduct, deleteProduct } = manageProducts();
// TanStack Table
import {
    useVueTable,
    FlexRender,
    getCoreRowModel,
    createColumnHelper,
    getFilteredRowModel,
} from '@tanstack/vue-table';
import { RegularSwal } from '@/components/Swals/useSwals';

const productCred = ref({
    supplier_id: "",
    category_id: "",
    brand_id: "",
    SKU: "",
    model: "",
    product_name: "",
    product_description: "",
    product_quantity: 0,
    unit_price: 0,
    reorder_level: 0,
    status: "",
    photos: [],
});

const handleDelete = async (id) => {
    const success = await deleteProduct(id)
    if (success && success.status === 200) {
        await fetchProducts();
        RegularSwal(success.data);
    }
}
const columnHelper = createColumnHelper();

const columns = [
    columnHelper.accessor(null, {
        id: "Image",
        header: "Image",
        cell: ({ row }) => {
            let image = null;
            if (row.original.photos.length > 0) {
                image = row.original.photos[0].image;
            }

            if (image) {
                return h(
                    "div",
                    { class: "flex justify-center items-center" }, // centers the image
                    [
                        h("img", {
                            src: "http://127.0.0.1:8000/storage/" + image,
                            alt: image,
                            class: "w-[100px] h-[100px] object-cover rounded-md"
                        })
                    ]
                );
            }
        }
    }),
    columnHelper.accessor('SKU', {
        id: "SKU",
        header: "SKU",
        cell: info => info.getValue()
    }),
    columnHelper.accessor(row => row.category.category_name, {
        id: "Category",
        header: "Category",
        cell: info => info.getValue()
    }),
    columnHelper.accessor('product_name', {
        id: "Item",
        header: "Item",
        cell: info => info.getValue()
    }),
    columnHelper.accessor('model', {
        id: "Model",
        header: "Model",
        cell: info => info.getValue()
    }),
    columnHelper.accessor(row => row.brand.brand_name, {
        id: "Brand",
        header: "Brand",
        cell: info => info.getValue()
    }),
    columnHelper.accessor('product_quantity', {
        id: "quantity",
        header: "quantity",
        cell: info => info.getValue()
    }),
    columnHelper.accessor('unit_price', {
        id: "Unit Price",
        header: "Unit Price",
        cell: info => info.getValue()
    }),
    columnHelper.accessor('status', {
        id: "Status",
        header: "Status",
        cell: info => {
            const value = info.getValue();

            const colorClass =
                value === "Available"
                    ? "bg-green-100 text-green-700 border border-green-300"
                    : value === "Unavailable"
                        ? "bg-red-100 text-red-700 border border-red-300"
                        : value === "Low Stock"
                            ? "bg-yellow-100 text-yellow-700 border border-yellow-300"
                            : value === "Out of Stock"
                                ? "bg-gray-200 text-gray-700 border border-gray-300"
                                : "bg-gray-100 text-gray-600 border border-gray-200";

            return h(
                'span',
                { class: `px-3 py-1 rounded-full text-sm font-medium ${colorClass}` },
                value
            );
        },
    }),
    {
        id: "Actions",
        header: "Actions",
        cell: ({ row }) => {
            return h(
                ProductOptionSilder, {
                class: "absolute",
                product_id: row.original.encrypted_id,
                onDeleteProduct: (id) => handleDelete(id)
            }
            )
        }
    }

];

const globalFilter = ref("");
const productsTable = useVueTable({
    data: computed(() => products.value),
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

onMounted(async () => {
    await fetchProducts();
});
</script>

<template>
    <!-- Loading Screen -->
    <section v-if="isLoading"
        class="min-h-screen flex flex-col items-center justify-center text-center p-6 space-y-3 bg-gray-50">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <p class="text-gray-600 text-base">Loading the latest data, please wait...</p>
    </section>

    <!-- Inventory Section -->
    <section v-else class="min-h-screen bg-gray-50 py-10 px-4">
        <div class="mx-auto p-6 space-y-6">

            <!-- Header -->
            <div class="flex flex-wrap justify-between items-center border-b border-gray-200 pb-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Inventory</h1>
                    <p class="text-gray-500 text-sm">Manage Products</p>
                </div>
                <RouterLink :to="{ name: 'Admin Add Products' }"
                    class="flex items-center bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-800 transition">
                    <FontAwesomeIcon :icon="faAdd" class="mr-2" />
                    Add Product
                </RouterLink>
            </div>

            <!-- Search -->
            <div class="flex items-center space-x-3 w-full md:w-1/2">
                <Label class="font-medium text-gray-700">Search:</Label>
                <Input type="text" placeholder="Search laptops..." v-model="globalFilter"
                    class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wide">
                        <tr>
                            <th v-for="header in productsTable.getFlatHeaders()" :key="header.id"
                                class="px-3 py-2 border text-center font-semibold">
                                <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                            </th>
                        </tr>
                    </thead>

                    <tbody v-if="products.length > 0" class="divide-y divide-gray-100">
                        <tr v-for="row in productsTable.getRowModel().rows" :key="row.id"
                            class="hover:bg-gray-50 transition duration-150 text-center">
                            <td v-for="cell in row.getVisibleCells()" :key="cell.id"
                                class="px-4 py-3 border whitespace-nowrap">
                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </td>
                        </tr>
                    </tbody>

                    <tbody v-else>
                        <tr>
                            <td colspan="8" class="text-center py-10 text-gray-500 italic">
                                No Items Found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</template>
