<script setup>
import { getLogs } from '@/composables/useLogs';
import { computed, onMounted, ref } from 'vue';
import {
    useVueTable,
    FlexRender,
    getCoreRowModel,
    getFilteredRowModel,
    createColumnHelper,
} from '@tanstack/vue-table';
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import { VueSpinnerOval } from 'vue3-spinners';

// composable
const { logs, isLoading, fetchLogs } = getLogs();

// table setup
const columnHelper = createColumnHelper();
const columns = [
    columnHelper.accessor('user.username', {
        id: 'User',
        header: 'User',
        cell: info => info.getValue() || 'Unknown',
    }),
    columnHelper.accessor('user.role', {
        id: 'Role',
        header: 'Role',
        cell: info => info.getValue() || '-',
    }),
    columnHelper.accessor('action', {
        id: 'Action',
        header: 'Action',
        cell: info => info.getValue(),
    }),
    columnHelper.accessor('details', {
        id: 'Details',
        header: 'Details',
        cell: info => info.getValue(),
    }),
    columnHelper.accessor(row => new Date(row.created_at).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }), {
        id: 'Date',
        header: 'Date & Time',
        cell: ({ row }) => {
            const date = new Date(row.original.created_at);
            return date.toLocaleString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        },
    }),
];

const globalFilter = ref('');
const logsTable = useVueTable({
    data: computed(() => logs.value),
    columns,
    state: {
        get globalFilter() {
            return globalFilter.value;
        },
    },
    onGlobalFilterChange: value => {
        globalFilter.value = value;
    },
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
});

onMounted(() => {
    fetchLogs();
});
</script>

<template>
    <!-- Loading State -->
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center p-4">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <h1 class="mt-6 font-semibold text-xl sm:text-2xl text-gray-700">
            Fetching Audit Logs...
        </h1>
        <p class="mt-2 text-gray-500 text-sm">
            Please wait while we load your latest system logs.
        </p>
    </section>

    <!-- Audit Logs Table -->
    <section v-else class="container-xl mx-auto p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800 tracking-wide">
                Audit Logs
            </h1>
        </div>

        <!-- Search -->
        <div class="flex flex-row items-center gap-3 mb-5 w-full sm:w-1/2">
            <Label>Search:</Label>
            <Input type="text" placeholder="Search logs..." v-model="globalFilter" class="w-full" />
        </div>

        <!-- Table Card -->
        <div
            class="relative rounded-2xl bg-white shadow-md border border-gray-200 overflow-x-auto max-h-[600px] overflow-y-auto">
            <table class="w-full text-sm text-gray-700 min-w-max">
                <!-- Table Head -->
                <thead class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wide sticky top-0 z-10">
                    <tr>
                        <th v-for="header in logsTable.getFlatHeaders()" :key="header.id"
                            class="px-4 py-3 text-center font-semibold border-b border-gray-300 bg-gray-100">
                            <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody v-if="logs.length > 0" class="divide-y divide-gray-100">
                    <tr v-for="row in logsTable.getRowModel().rows" :key="row.id"
                        class="hover:bg-gray-50 transition-all duration-150 text-center">
                        <td v-for="cell in row.getVisibleCells()" :key="cell.id" class="px-4 py-2 border-b">
                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                        </td>
                    </tr>
                </tbody>

                <!-- Empty State -->
                <tbody v-else>
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-400">
                            <i class="fa-solid fa-clipboard-list text-3xl mb-2"></i>
                            <p>No audit logs found.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
