<script setup>
import { getEmployees } from '@/composables/useEmployee'
import { computed, onMounted, ref, watch } from 'vue'
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import EmployeeCard from '@/components/Cards/EmployeeCard.vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import axios from '@/axios';
import { faAdd } from '@fortawesome/free-solid-svg-icons';
import { SearchCheck } from 'lucide-vue-next';
const { employees, isLoading, fetchEmployees, pagination } = getEmployees()

const searchQuery = ref("");
const searchedEmployee = ref([]);
const isSearched = ref(false);
// Paginationskiee
const getEmployeesPagination = async (page) => {
    try {
        const response = await axios.get("http://localhost:8000/api/employees/index?page=" + page);
        console.log(response);
        employees.value = response.data.employees.data
        pagination.value = response.data.employees
    }
    catch (error) {
        console.log(error);
    }
}

//Changing of page
const changePage = (page) => {
    if (page >= 1 && page <= pagination.value.last_page) {
        getEmployeesPagination(page)
    }
}

// Initial Fetching of Employees
onMounted(async () => {
    await fetchEmployees()
})

const search = () => {
    searchedEmployee.value = employees.value.filter((e) => e.unique_employee_id === searchQuery.value.trim());
    isSearched.value = true;
    console.log(searchedEmployee.value.length)
}

watch(searchQuery, () => {
    isSearched.value = false
    if (searchQuery.value === '') {
        searchedEmployee.value = [];
    }
})
</script>

<template>
    <!-- Loading State -->
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center">
        <VueSpinnerHourglass size="100" />
        <h1 class="mt-4 font-bold text-2xl">Fetching Employees...</h1>
    </section>

    <!-- Employees Grid -->
    <section v-else class="container mx-auto p-3">
        <div class="flex justify-between items-center">
            <div class="flex justify-center space-x-3">
                <h1 class="font-bold text-2xl">Staffs</h1>
                <Input type="text" placeholder="Search Employee ID..." class="bg-gray-300 focus:bg-white"
                    v-model="searchQuery" />
                <Button class="bg-button hover:bg-button-hovered" @click="search">
                    Search
                </Button>
            </div>

            <Button class="bg-button hover:bg-button-hovered">
                <FontAwesomeIcon :icon="faAdd" /> Employee
            </Button>
        </div>

        <div v-if="!isSearched && employees.length >= 0"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-12">
            <EmployeeCard v-for="employee in employees" :key="employee.unique_employee_id"
                :name="`${employee.firstname} ${employee.lastname}`" :gender="employee.gender"
                :employee_id="employee.unique_employee_id" />
        </div>

        <div v-else class="flex justify-center items-center">
            <div v-if="searchedEmployee.length > 0"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-12">
                <EmployeeCard v-for="employee in searchedEmployee" :key="employee.unique_employee_id"
                    :name="`${employee.firstname} ${employee.lastname}`" :gender="employee.gender"
                    :employee_id="employee.unique_employee_id" />
            </div>
            <div v-else>
                <h1>No employee found</h1>
            </div>
        </div>

        <div v-if="pagination" class="flex gap-2 mt-4">
            <Button :disabled="!pagination.prev_page_url" @click="changePage(pagination.current_page - 1)"
                class="bg-button hover:bg-button-hovered">Prev</Button>

            <span>Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>

            <Button :disabled="!pagination.next_page_url" @click="changePage(pagination.current_page + 1)"
                class="bg-button hover:bg-button-hovered">Next</Button>
        </div>
    </section>
</template>
