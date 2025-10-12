<script setup>
import { ref, computed, h, onMounted } from 'vue'
import { getStocksMovements } from '@/composables/useStock'
import {
    useVueTable,
    FlexRender,
    createColumnHelper,
    getCoreRowModel,
    getFilteredRowModel,
} from '@tanstack/vue-table'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faArrowUp, faArrowDown } from '@fortawesome/free-solid-svg-icons'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'

// ✅ Fetch Stock Movement data
const { isLoading, movements, fetchMovements } = getStocksMovements()

// ✅ Table setup
const globalFilter = ref('')
const columnHelper = createColumnHelper()

// ✅ Table Columns
const columns = [
    columnHelper.accessor(row => row.reference_no, {
        id: 'Reference No',
        header: 'Reference No',
        cell: info => info.getValue()
    }),
    columnHelper.accessor(row => row.product?.product_name, {
        id: 'Product',
        header: 'Product',
        cell: info => info.getValue() || 'N/A'
    }),
    columnHelper.accessor(row => row.product?.SKU, {
        id: 'SKU',
        header: 'SKU',
        cell: info => info.getValue() || 'N/A'
    }),
    columnHelper.accessor(row => row.movement_type, {
        id: 'Movement Type',
        header: 'Type',
        cell: ({ row }) => {
            const type = row.original.movement_type
            const isIn = type.toLowerCase().includes('in')
            return h(
                'span',
                {
                    class: `inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium ${isIn ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                        }`,
                },
                [
                    h(FontAwesomeIcon, { icon: isIn ? faArrowUp : faArrowDown }),
                    type,
                ]
            )
        },
    }),
    columnHelper.accessor('quantity', {
        id: 'Quantity',
        header: 'Quantity',
        cell: info => info.getValue()
    }),
    columnHelper.accessor('reason', {
        id: 'Reason',
        header: 'Reason',
        cell: info => info.getValue() || '-'
    }),
    columnHelper.accessor(row => row.user?.username, {
        id: 'Processed By',
        header: 'Processed By',
        cell: info => info.getValue() || 'N/A'
    }),
    columnHelper.accessor(row => new Date(row.created_at), {
        id: 'Date',
        header: 'Date',
        cell: ({ row }) => {
            const d = new Date(row.original.created_at)
            return d.toLocaleString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })
        }
    })
]

// ✅ Table Instance
const stockMovementsTable = useVueTable({
    data: computed(() => movements.value || []),
    columns,
    state: {
        get globalFilter() {
            return globalFilter.value
        },
    },
    onGlobalFilterChange: value => {
        globalFilter.value = value
    },
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
})

// ✅ Fetch data on mount
onMounted(async () => {
    await fetchMovements()
})
</script>

<template>
    <!-- ✅ Movements Table -->
    <section class="container mx-auto p-6 min-h-screen">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800 tracking-wide">
                Stock Movements
            </h1>
        </div>

        <!-- Search -->
        <div class="flex flex-row items-center gap-3 mb-5 w-full sm:w-1/2">
            <Label>Search:</Label>
            <Input type="text" placeholder="Search reference, product, or type..." v-model="globalFilter"
                class="w-full" />
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-2xl bg-white shadow-md border border-gray-200 max-h-[500px] overflow-y-auto
            scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            <table class="w-full text-sm text-gray-700 min-w-[800px]">
                <!-- Table Head -->
                <thead class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wide sticky top-0 z-10">
                    <tr>
                        <th v-for="header in stockMovementsTable.getFlatHeaders()" :key="header.id"
                            class="px-4 py-3 text-center font-semibold border-b border-gray-300">
                            <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody v-if="movements.length > 0" class="divide-y divide-gray-100">
                    <tr v-for="row in stockMovementsTable.getRowModel().rows" :key="row.id"
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
                            No stock movements found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </section>
</template>
