<script setup>
import { getEmployees } from '@/composables/useEmployee';
import { manageEmployee } from '@/composables/useEmployee';
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import Button from '@/components/ui/button/Button.vue';
import maleImg from '@/assets/images/male.png'
import femaleImg from '@/assets/images/female.png'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faSliders, faAdd } from '@fortawesome/free-solid-svg-icons';
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
import { RegularSwal } from '@/components/Swals/useSwals';
const route = useRoute();
const slider = ref(false);
const edit = ref(false);
const editAccount = ref(false);
const accountSlider = ref(false);
const { fetchEmployeesData, employeeDetails, isLoading } = getEmployees();
const { updateEmployee, errors, updateEmployeeAccount } = manageEmployee();
const image = computed(() => {
    return employeeDetails.value.gender === 'Male' ? maleImg : femaleImg
})

const toggleSlider = () => {
    slider.value = !slider.value
}
const toggleAccountSlider = () => {
    accountSlider.value = !accountSlider.value
}

const toggleEditMode = () => {
    edit.value = !edit.value
    toggleSlider();
}
const toggleEditAccountMode = () => {
    editAccount.value = !editAccount.value
    toggleAccountSlider();
}

const handleUpdate = async (employeCred) => {
    const success = await updateEmployee(employeCred);
    if (success && success.status === 200) {
        await fetchEmployeesData(route.params.employee_id);
        RegularSwal(success.data)
        edit.value = !edit.value
    }
}
const handleUpdateAccount = async (employeCred) => {
    const success = await updateEmployeeAccount(employeCred);
    if (success && success.status === 200) {
        await fetchEmployeesData(route.params.employee_id);
        RegularSwal(success.data)
        editAccount.value = !editAccount.value
    }
}

onMounted(async () => {
    await fetchEmployeesData(route.params.employee_id);
    if (!employeeDetails.value.user) {
        userCred.employee_id = employeeDetails.value.encrypted_id;
    }
});

</script>
<template>
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center p-4">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <h1 class="mt-4 font-bold text-xl sm:text-2xl text-accents">
            Fetching Employee...
        </h1>
    </section>

    <section v-else>
        <div class="container mx-auto p-6">
            <div v-if="employeeDetails" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Profile Card -->
                <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center border border-gray-300">
                    <img :src="image" alt="profile_img"
                        class="rounded-full w-[180px] h-[180px] object-cover border-4 border-indigo-200 shadow" />
                    <h2 class="mt-4 text-xl font-bold text-gray-800">
                        {{ employeeDetails.firstname }}
                        <span v-if="employeeDetails.middlename"> {{ employeeDetails.middlename }}</span>
                        {{ employeeDetails.lastname }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">{{ employeeDetails.unique_employee_id }}</p>
                    <span :class="[
                        'mt-3 px-3 py-1 rounded-full text-xs font-medium',
                        employeeDetails.status === 'Active'
                            ? 'bg-green-100 text-green-700'
                            : employeeDetails.status === 'Inactive' ? 'bg-red-100 text-red-700' : 'bg-gray-300 text-gray-700'
                    ]">
                        {{ employeeDetails.status }}
                    </span>
                </div>
                <!-- Details Card -->
                <div class="md:col-span-2 bg-white rounded-xl shadow p-6 border border-gray-300">
                    <div class="flex justify-between items-center border-b mb-6 pb-3">
                        <h2 class="text-2xl font-bold text-gray-800">
                            Employee Details
                        </h2>
                        <div class="relative">
                            <Button @click="toggleSlider" class="bg-accents hover:bg-accents-hover">
                                <FontAwesomeIcon :icon="faSliders" />
                            </Button>
                            <Transition enter-active-class="transition duration-300 ease-in-out"
                                enter-from-class="opacity-0 -translate-y-2 scale-95"
                                enter-to-class="opacity-100 translate-y-0 scale-100"
                                leave-active-class="transition duration-200 ease-in-out"
                                leave-from-class="opacity-100 translate-y-0 scale-100"
                                leave-to-class="opacity-0 -translate-y-2 scale-95">
                                <div v-if="slider"
                                    class="absolute right-0 w-48 rounded-xl border border-gray-200 bg-white shadow-lg z-50">
                                    <ul class="py-2 text-sm text-gray-700">
                                        <li>
                                            <button type="button" class="block px-4 py-2 hover:bg-gray-100 w-full"
                                                @click="toggleEditMode">
                                                {{ edit ? 'Read Mode' : 'Edit Mode' }}
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </Transition>
                        </div>
                    </div>

                    <div v-if="!edit" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Email -->
                        <div>
                            <span class="block font-semibold text-gray-600">Email</span>
                            <span class="text-gray-800">{{ employeeDetails.email }}</span>
                        </div>

                        <!-- Gender -->
                        <div>
                            <span class="block font-semibold text-gray-600">Gender</span>
                            <span class="text-gray-800">{{ employeeDetails.gender }}</span>
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <span class="block font-semibold text-gray-600">Date of Birth</span>
                            <span class="text-gray-800">{{ employeeDetails.dob }}</span>
                        </div>

                        <!-- Status again (optional, keep in profile instead if you like) -->
                        <div>
                            <span class="block font-semibold text-gray-600">Status</span>
                            <span class="text-gray-800">{{ employeeDetails.status }}</span>
                        </div>

                        <!-- Created -->
                        <div>
                            <span class="block font-semibold text-gray-600">Created At</span>
                            <span class="text-gray-800">
                                {{ new Date(employeeDetails.created_at).toLocaleString() }}
                            </span>
                        </div>

                        <!-- Updated -->
                        <div>
                            <span class="block font-semibold text-gray-600">Updated At</span>
                            <span class="text-gray-800">
                                {{ new Date(employeeDetails.updated_at).toLocaleString() }}
                            </span>
                        </div>
                    </div>

                    <div v-else>
                        <!-- Email -->
                        <form @submit.prevent="handleUpdate(employeeDetails)">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="space-y-3">
                                    <Label for="firstname">Firstname</Label>
                                    <Input type="text" placeholder="Enter Firstname" name="firstname" id="firstname"
                                        v-model="employeeDetails.firstname" required />
                                    <div v-if="errors && errors.firstname">
                                        <ErrorLabel :error="errors.firstname" />
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <Label for="middlename">Middlename</Label>
                                    <Input type="text" placeholder="Enter Middlename" name="middlename" id="middlename"
                                        v-model="employeeDetails.middlename" />
                                    <div v-if="errors && errors.middlename">
                                        <ErrorLabel :error="errors.middlename" />
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <Label for="lastname">Lastname</Label>
                                    <Input type="text" placeholder="Enter Lastname" name="lastname" id="lastname"
                                        v-model="employeeDetails.lastname" required />
                                    <div v-if="errors && errors.lastname">
                                        <ErrorLabel :error="errors.lastname" />
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <Label for="email">Email</Label>
                                    <Input type="email" placeholder="Enter Email" name="email" id="email"
                                        v-model="employeeDetails.email" required />
                                    <div v-if="errors && errors.email">
                                        <ErrorLabel :error="errors.email" />
                                    </div>
                                </div>

                                <!-- Gender -->
                                <div class="space-y-3">
                                    <Label for="gender">Gender</Label>
                                    <Select id="gender" name="gender" v-model="employeeDetails.gender" required>
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

                                <!-- Date of Birth -->
                                <div class="space-y-3">
                                    <Label for="dob">Date of Birth</Label>
                                    <Input type="date" name="dob" id="dob" v-model="employeeDetails.dob" required />
                                    <div v-if="errors && errors.dob">
                                        <ErrorLabel :error="errors.dob" />
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="space-y-3">
                                    <Label for="status">Status</Label>
                                    <Select id="status" name="status" v-model="employeeDetails.status" required>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select Status" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem value="Active">Active</SelectItem>
                                                <SelectItem value="Inactive">Inactive</SelectItem>
                                                <SelectItem value="Blocked">Blocked</SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button"
                                    class="px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300"
                                    @click="resetForm">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 rounded-md bg-accents text-white hover:bg-accents-hover">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <section class="container mx-auto p-6">
            <div v-if="employeeDetails && employeeDetails.user"
                class="md:col-span-3 bg-white rounded-xl shadow p-6 border border-gray-300 mt-6">
                <div class="flex justify-between items-center border-b mb-6 pb-3">
                    <h2 class="text-2xl font-bold text-gray-800">
                        Account Details
                    </h2>
                    <div class="relative">
                        <Button @click="toggleAccountSlider" class="bg-accents hover:bg-accents-hover">
                            <FontAwesomeIcon :icon="faSliders" />
                        </Button>
                        <Transition enter-active-class="transition duration-300 ease-in-out"
                            enter-from-class="opacity-0 -translate-y-2 scale-95"
                            enter-to-class="opacity-100 translate-y-0 scale-100"
                            leave-active-class="transition duration-200 ease-in-out"
                            leave-from-class="opacity-100 translate-y-0 scale-100"
                            leave-to-class="opacity-0 -translate-y-2 scale-95">
                            <div v-if="accountSlider"
                                class="absolute right-0 w-48 rounded-xl border border-gray-200 bg-white shadow-lg z-50">
                                <ul class="py-2 text-sm text-gray-700">
                                    <li>
                                        <button type="button" class="block px-4 py-2 hover:bg-gray-100 w-full"
                                            @click="toggleEditAccountMode">
                                            {{ editAccount ? 'Read Mode' : 'Edit Mode' }}
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </Transition>
                    </div>
                </div>

                <!-- Read Mode -->
                <div v-if="!editAccount" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Username -->
                    <div>
                        <span class="block font-semibold text-gray-600">Username</span>
                        <span class="text-gray-800">{{ employeeDetails.user?.username }}</span>
                    </div>

                    <!-- Role -->
                    <div>
                        <span class="block font-semibold text-gray-600">Role</span>
                        <span class="text-gray-800">{{ employeeDetails.user?.role }}</span>
                    </div>

                    <!-- Created -->
                    <div>
                        <span class="block font-semibold text-gray-600">Created At</span>
                        <span class="text-gray-800">
                            {{ new Date(employeeDetails.user?.created_at).toLocaleString() }}
                        </span>
                    </div>

                    <div>
                        <span class="block font-semibold text-gray-600">Updated At</span>
                        <span class="text-gray-800">
                            {{ new Date(employeeDetails.user?.updated_at).toLocaleString() }}
                        </span>
                    </div>
                </div>

                <!-- Edit Mode -->
                <div v-else>
                    <form @submit.prevent="handleUpdateAccount(employeeDetails.user)">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Username -->
                            <div class="space-y-3">
                                <Label for="username">Username</Label>
                                <Input type="text" placeholder="Enter Username" name="username" id="username"
                                    v-model="employeeDetails.user.username" required readonly />
                            </div>
                            <!-- Role -->
                            <div class="space-y-3">
                                <Label for="role">Role</Label>
                                <Select id="role" name="role" v-model="employeeDetails.user.role" required>
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
                            </div>
                        </div>
                        <!-- Form Actions -->
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button"
                                class="px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300"
                                @click="resetForm">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 rounded-md bg-accents text-white hover:bg-accents-hover">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </section>
</template>