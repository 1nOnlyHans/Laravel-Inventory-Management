<script setup>
import { getProducts } from '@/composables/useProducts';
import { manageProducts } from '@/composables/useProducts';
import { computed, onMounted, ref, h, reactive, onUnmounted } from 'vue';
import { Button } from "@/components/ui/button";
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import { generateReports } from '@/composables/useReports';
const { products, isLoading, fetchProducts } = getProducts();
const { errors, addProduct, deleteProduct } = manageProducts();
const { generateInventoryReports } = generateReports();
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
    columnHelper.accessor('SKU', {
        id: "SKU",
        header: "SKU",
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
    columnHelper.accessor(row => row.category.category_name, {
        id: "Category",
        header: "Category",
        cell: info => info.getValue()
    }),
    columnHelper.accessor(row => row.brand.brand_name, {
        id: "Brand",
        header: "Brand",
        cell: info => info.getValue()
    }),
    columnHelper.accessor('product_quantity', {
        id: "Current Stock",
        header: "Current Stock",
        cell: info => info.getValue()
    }),
    columnHelper.accessor('unit_price', {
        id: "Unit Price",
        header: "Unit Price",
        cell: info => info.getValue()
    }),
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
    <!-- Inventory Section -->
    <section class="min-h-screen bg-gray-50 py-10 px-4">
        <div class="mx-auto bg-white rounded-2xl shadow-md p-6 space-y-6 border border-gray-100">
            <!-- Header -->
            <div class="flex flex-wrap justify-between items-center border-b border-gray-200 pb-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Inventory Valuation</h1>
                </div>

            </div>
            <!-- Search -->
            <div class="flex items-center space-x-3 w-full md:w-1/2">
                <Label class="font-medium text-gray-700">Search:</Label>
                <Input type="text" placeholder="Search laptops..." v-model="globalFilter"
                    class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm" id="printable">
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
                    <div v-if="products.length > 0" class="flex justify-end pt-4 border-t border-gray-200">
                        <p class="text-lg font-semibold text-gray-700">
                            Total Inventory Value:
                            <span class="text-gray-900">
                                ₱{{
                                    products.reduce((sum, item) => {
                                        const unit_price = Number(item.unit_price) || 0;
                                        const quantity = Number(item.product_quantity) || 0;
                                        return sum + unit_price * quantity;
                                    }, 0).toLocaleString()
                                }}
                            </span>
                        </p>
                    </div>
                </table>
            </div>
        </div>
    </section>
</template>
