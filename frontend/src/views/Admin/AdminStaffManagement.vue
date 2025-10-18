<script setup>
import axios from '@/axios';
import { getEmployees, manageEmployee } from '@/composables/useEmployee'
import { CSVImport } from '@/composables/useCsv';
import { computed, onMounted, ref, watch } from 'vue'
import { Button } from '@/components/ui/button';
import EmployeeCard from '@/components/Cards/EmployeeCard.vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faAdd } from '@fortawesome/free-solid-svg-icons';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import ErrorLabel from '@/components/Errors/ErrorLabel.vue';
import CsvStaffModal from '@/components/Modals/CsvStaffModal.vue';
import { faFileCsv } from '@fortawesome/free-solid-svg-icons';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    DialogClose
} from '@/components/ui/dialog'

import { RegularSwal } from '@/components/Swals/useSwals';
const { employees, isLoading, fetchEmployees, pagination } = getEmployees()
const { employeCred, addEmployee, errors, resetEmployeeCred } = manageEmployee();
const { loadingImport, importStaff } = CSVImport();
const openCsvModal = ref(false);
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
    if (page >= 1 && page <= pagination.value.last_page) { getEmployeesPagination(page) }
} // Initial Fetching of Employees
onMounted(async () => {
    await fetchEmployees()
})

const search = () => {
    searchedEmployee.value = employees.value.filter((e) => e.unique_employee_id === searchQuery.value.trim());
    isSearched.value = true;
}
const handleAddEmployee = async () => {
    const success = await addEmployee();
    if (success) {
        await fetchEmployees();
        RegularSwal(success.data);
    }
}

const handleImportStaff = async (file) => {
    const success = await importStaff(file);
    if (success && success.status === 200) {
        openCsvModal.value = false;
        await fetchEmployees();
        RegularSwal(success.data);
    } else {
        openCsvModal.value = false;
        RegularSwal({
            icon: 'error',
            title: 'Import Error'
        });
    }
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
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center p-4">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <h1 class="mt-4 font-bold text-xl sm:text-2xl text-accents">
            Fetching Employees...
        </h1>
    </section>

    <section v-else-if="loadingImport" class="min-h-screen flex flex-col items-center justify-center text-center p-4">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <h1 class="mt-4 font-bold text-xl sm:text-2xl text-accents">
            Importing Data...
        </h1>
    </section>

    <!-- Employees Grid -->
    <section v-else class="container mx-auto p-3">
        <!-- Header: title + search + add -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <!-- Left: title + search -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                <h1 class="font-bold text-xl sm:text-2xl">Staffs</h1>

                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <Input type="text" placeholder="Search Employee ID..."
                        class="bg-gray-300 focus:bg-white w-full sm:w-64" v-model="searchQuery" />
                    <Button @click="search" :disabled="searchQuery === ''">
                        Search
                    </Button>
                </div>
            </div>

            <!-- Right: Add button -->
            <section class="container mx-auto p-5">

            </section>
            <Dialog>
                <DialogTrigger>
                    <Button @click="resetEmployeeCred">
                        <FontAwesomeIcon :icon="faAdd" class="mr-2" />
                        Add Employee
                    </Button>
                </DialogTrigger>

                <!-- Dialog Content -->
                <DialogContent class="max-w-2xl w-full mx-2 max-h-[85vh] flex flex-col rounded-lg">
                    <!-- Header (fixed) -->
                    <DialogHeader class="shrink-0">
                        <DialogTitle class="text-xl sm:text-2xl font-semibold text-gray-800">
                            Add New Employee
                        </DialogTitle>
                        <DialogDescription>
                            <p class="text-sm text-gray-500">
                                Please fill out the form below to register a new employee.
                            </p>
                        </DialogDescription>
                    </DialogHeader>

                    <!-- Scrollable Form Body -->
                    <div class="flex-1 overflow-y-auto mt-4 pr-2">
                        <form class="space-y-8" @submit.prevent="handleAddEmployee" name="addEmployeeForm"
                            id="addEmployeeForm">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <!-- Firstname -->
                                <div class="flex flex-col space-y-2">
                                    <Label for="firstname">Firstname</Label>
                                    <Input type="text" placeholder="Firstname" name="firstname" id="firstname"
                                        v-model="employeCred.firstname" required />
                                    <div v-if="errors && errors.firstname">
                                        <ErrorLabel :error="errors.firstname" />
                                    </div>
                                </div>

                                <!-- Middlename -->
                                <div class="flex flex-col space-y-2">
                                    <Label for="middlename">Middlename</Label>
                                    <Input type="text" placeholder="Middlename" name="middlename" id="middlename"
                                        v-model="employeCred.middlename" />
                                </div>

                                <!-- Lastname -->
                                <div class="flex flex-col space-y-2">
                                    <Label for="lastname">Lastname</Label>
                                    <Input type="text" placeholder="Lastname" name="lastname" id="lastname"
                                        v-model="employeCred.lastname" required />
                                    <div v-if="errors && errors.lastname">
                                        <ErrorLabel :error="errors.lastname" />
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="flex flex-col space-y-2">
                                    <Label for="email">Email</Label>
                                    <Input type="email" placeholder="Email" name="email" id="email"
                                        v-model="employeCred.email" required />
                                    <div v-if="errors && errors.email">
                                        <ErrorLabel :error="errors.email" />
                                    </div>
                                </div>

                                <!-- Gender -->
                                <div class="flex flex-col space-y-2">
                                    <Label for="gender">Gender</Label>
                                    <Select id="gender" name="gender" v-model="employeCred.gender" required>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select Gender" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem value="Male">Male</SelectItem>
                                                <SelectItem value="Female">Female</SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <div v-if="errors && errors.gender">
                                        <ErrorLabel :error="errors.gender" />
                                    </div>
                                </div>

                                <!-- DOB -->
                                <div class="flex flex-col space-y-2">
                                    <Label for="dob">Date of Birth</Label>
                                    <Input type="date" name="dob" id="dob" v-model="employeCred.dob" required />
                                    <div v-if="errors && errors.dob">
                                        <ErrorLabel :error="errors.dob" />
                                    </div>
                                </div>
                                <!-- Role -->
                                <div class="flex flex-col space-y-2">
                                    <Label for="role">Role</Label>
                                    <Select id="role" name="role" v-model="employeCred.role" required>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select Role" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem value="Admin">Admin</SelectItem>
                                                <SelectItem value="Staff">Staff</SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <div v-if="errors && errors.role">
                                        <ErrorLabel :error="errors.role" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Footer (fixed) -->
                    <DialogFooter class="shrink-0 mt-4 flex flex-col sm:flex-row justify-end gap-3">
                        <DialogClose as-child>
                            <Button type="button" variant="secondary" @click="resetEmployeeCred">
                                Close
                            </Button>
                        </DialogClose>
                        <Button type="submit" form="addEmployeeForm">
                            Add Employee
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
            <Button @click="openCsvModal = true"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition-colors">
                <FontAwesomeIcon :icon="faFileCsv" class="mr-2" />
                Bulk Import
            </Button>
            <CsvStaffModal @import="handleImportStaff" v-model:open="openCsvModal" />
        </div>

        <!-- Employees Grid -->
        <div v-if="!isSearched && employees.length >= 0"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-6">
            <EmployeeCard v-for="employee in employees" :key="employee.unique_employee_id"
                :name="`${employee.firstname} ${employee.lastname}`" :gender="employee.gender"
                :employee_id="employee.unique_employee_id" :uniqid="employee.encrypted_id"
                :role="employee.user?.role" />
        </div>

        <!-- Search Results -->
        <div v-else class="flex justify-center items-center mt-6">
            <div v-if="searchedEmployee.length > 0"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <EmployeeCard v-for="employee in searchedEmployee" :key="employee.unique_employee_id"
                    :name="`${employee.firstname} ${employee.lastname}`" :gender="employee.gender"
                    :employee_id="employee.unique_employee_id" :uniqid="employee.encrypted_id"
                    :role="employee.user?.role" />
            </div>
            <div v-else>
                <h1 class="text-gray-500 text-center">No employee found</h1>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination" class="flex flex-col sm:flex-row justify-center items-center gap-3 mt-6">
            <Button :disabled="!pagination.prev_page_url" @click="changePage(pagination.current_page - 1)">
                Prev
            </Button>
            <span class="text-sm sm:text-base">
                Page {{ pagination.current_page }} of {{ pagination.last_page }}
            </span>
            <Button :disabled="!pagination.next_page_url" @click="changePage(pagination.current_page + 1)">
                Next
            </Button>
        </div>
    </section>
</template>
