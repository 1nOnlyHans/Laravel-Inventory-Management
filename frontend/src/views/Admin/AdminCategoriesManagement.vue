<script setup>
import { getCategories } from '@/composables/useCategories';
import { onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faGear, faPen, faEye, faTrash } from '@fortawesome/free-solid-svg-icons';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";

const { categories, isLoading, fetchCategories } = getCategories();

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
            <RouterLink to="/admin/suppliers/create"
                class="mt-3 sm:mt-0 bg-accents hover:bg-accents-hover text-white px-4 py-2 rounded-lg shadow text-sm font-medium transition">
                + Add Category
            </RouterLink>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-xl shadow-sm bg-white">
            <Table>
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
            </Table>
        </div>
    </section>
</template>