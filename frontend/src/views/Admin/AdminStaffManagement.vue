<script setup>
import axios from '@/axios';
import { getEmployees, manageEmployee } from '@/composables/useEmployee'
import { computed, onMounted, ref, watch } from 'vue'
import { Button } from '@/components/ui/button';
import EmployeeCard from '@/components/Cards/EmployeeCard.vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faAdd } from '@fortawesome/free-solid-svg-icons';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import ErrorLabel from '@/components/Errors/ErrorLabel.vue';
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
import Swal from 'sweetalert2';
import { RegularSwal } from '@/components/Swals/useSwals';
const { employees, isLoading, fetchEmployees, pagination } = getEmployees()
const { employeCred, addEmployee, errors, resetEmployeeCred } = manageEmployee();
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
    console.log(searchedEmployee.value.length)
}
const handleAddEmployee = async () => {
    const success = await addEmployee();
    if (success) {
        await fetchEmployees();
        RegularSwal(success.data);
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
        <VueSpinnerHourglass size="80" color="#422ac7" />
        <h1 class="mt-4 font-bold text-xl sm:text-2xl text-button">
            Fetching Employees...
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
                    <Button class="bg-button hover:bg-button-hovered w-full sm:w-auto" @click="search"
                        :disabled="searchQuery === ''">
                        Search
                    </Button>
                </div>
            </div>

            <!-- Right: Add button -->
            <section class="container mx-auto p-5">

            </section>
            <Dialog>
                <DialogTrigger>
                    <Button
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow w-full sm:w-auto"
                        @click="resetEmployeeCred">
                        <FontAwesomeIcon :icon="faAdd" class="mr-2" />
                        Add Employee
                    </Button>
                </DialogTrigger>

                <!-- Dialog Content -->
                <DialogContent class="max-w-2xl w-full mx-2">
                    <DialogHeader>
                        <DialogTitle class="text-xl sm:text-2xl font-semibold text-gray-800">
                            Add New Employee
                        </DialogTitle>
                        <p class="text-sm text-gray-500">
                            Please fill out the form below to register a new employee.
                        </p>
                    </DialogHeader>

                    <!-- Form -->
                    <form class="mt-6 space-y-8" @submit.prevent="handleAddEmployee">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Firstname -->
                            <div class="flex flex-col space-y-2">
                                <Label for="firstname">Firstname</Label>
                                <Input type="text" placeholder="Firstname" name="firstname" id="firstname"
                                    v-model="employeCred.firstname" />
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
                                    v-model="employeCred.lastname" />
                                <div v-if="errors && errors.lastname">
                                    <ErrorLabel :error="errors.lastname" />
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="flex flex-col space-y-2">
                                <Label for="email">Email</Label>
                                <Input type="email" placeholder="Email" name="email" id="email"
                                    v-model="employeCred.email" />
                                <div v-if="errors && errors.email">
                                    <ErrorLabel :error="errors.email" />
                                </div>
                            </div>

                            <!-- Gender -->
                            <div class="flex flex-col space-y-2">
                                <Label for="gender">Gender</Label>
                                <Select id="gender" name="gender" v-model="employeCred.gender">
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
                                <Input type="date" name="dob" id="dob" v-model="employeCred.dob" />
                                <div v-if="errors && errors.dob">
                                    <ErrorLabel :error="errors.dob" />
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <DialogFooter class="mt-8 flex flex-col sm:flex-row justify-end gap-3">
                            <DialogClose as-child>
                                <Button type="button" variant="secondary" @click="resetEmployeeCred">
                                    Close
                                </Button>
                            </DialogClose>
                            <Button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg shadow w-full sm:w-auto">
                                Add Employee
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>

        <!-- Employees Grid -->
        <div v-if="!isSearched && employees.length >= 0"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-6">
            <EmployeeCard v-for="employee in employees" :key="employee.unique_employee_id"
                :name="`${employee.firstname} ${employee.lastname}`" :gender="employee.gender"
                :employee_id="employee.unique_employee_id" :uniqid="employee.encrypted_id" />
        </div>

        <!-- Search Results -->
        <div v-else class="flex justify-center items-center mt-6">
            <div v-if="searchedEmployee.length > 0"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <EmployeeCard v-for="employee in searchedEmployee" :key="employee.unique_employee_id"
                    :name="`${employee.firstname} ${employee.lastname}`" :gender="employee.gender"
                    :employee_id="employee.unique_employee_id" :uniqid="employee.encrypted_id" />
            </div>
            <div v-else>
                <h1 class="text-gray-500 text-center">No employee found</h1>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination" class="flex flex-col sm:flex-row justify-center items-center gap-3 mt-6">
            <Button :disabled="!pagination.prev_page_url" @click="changePage(pagination.current_page - 1)"
                class="bg-button hover:bg-button-hovered w-full sm:w-auto">
                Prev
            </Button>
            <span class="text-sm sm:text-base">
                Page {{ pagination.current_page }} of {{ pagination.last_page }}
            </span>
            <Button :disabled="!pagination.next_page_url" @click="changePage(pagination.current_page + 1)"
                class="bg-button hover:bg-button-hovered w-full sm:w-auto">
                Next
            </Button>
        </div>
    </section>
</template>
