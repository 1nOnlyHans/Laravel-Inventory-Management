<script setup>
import { getEmployees } from '@/composables/useEmployee';
import { manageEmployee } from '@/composables/useEmployee';
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
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
const { updateEmployee, errors, updateEmployeeAccount, deleteEmployee } = manageEmployee();
const image = computed(() => {
    return employeeDetails.value.gender === 'Male' ? maleImg : femaleImg
})
const redirect = useRouter();
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

const handleDelete = async () => {
    const success = await deleteEmployee(route.params.employee_id);
    if (success && success.status === 200) {
        redirect.push('/admin/employees');
    } else {
        RegularSwal({
            icon: 'error',
            title: success.response.data.text
        });
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
    <!-- Loading State -->
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center p-4">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <h1 class="mt-4 font-bold text-xl sm:text-2xl text-accents">
            Fetching Employee...
        </h1>
    </section>

    <!-- Main Content -->
    <section v-else>
        <div class="container mx-auto p-6 space-y-8">
            <!-- Employee Information -->
            <div v-if="employeeDetails" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Profile Card -->
                <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center border border-gray-200">
                    <img :src="image" alt="Profile Image"
                        class="rounded-full w-[180px] h-[180px] object-cover border-4 border-indigo-200 shadow" />

                    <h2 class="mt-4 text-xl font-bold text-gray-800 text-center">
                        {{ employeeDetails.firstname }}
                        <span v-if="employeeDetails.middlename"> {{ employeeDetails.middlename }}</span>
                        {{ employeeDetails.lastname }}
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">{{ employeeDetails.unique_employee_id }}</p>

                    <span :class="[
                        'mt-3 px-3 py-1 rounded-full text-xs font-medium',
                        employeeDetails.status === 'Active'
                            ? 'bg-green-100 text-green-700'
                            : employeeDetails.status === 'Inactive'
                                ? 'bg-red-100 text-red-700'
                                : 'bg-gray-100 text-gray-700'
                    ]">
                        {{ employeeDetails.status }}
                    </span>
                </div>

                <!-- Employee Details -->
                <div class="md:col-span-2 bg-white rounded-xl shadow p-6 border border-gray-200">
                    <!-- Header -->
                    <div class="flex justify-between items-center border-b pb-3 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Employee Details</h2>

                        <!-- Settings Menu -->
                        <div class="relative">
                            <Button @click="toggleSlider">
                                <FontAwesomeIcon :icon="faSliders" />
                            </Button>

                            <Transition enter-active-class="transition duration-300 ease-in-out"
                                enter-from-class="opacity-0 -translate-y-2 scale-95"
                                enter-to-class="opacity-100 translate-y-0 scale-100"
                                leave-active-class="transition duration-200 ease-in-out"
                                leave-from-class="opacity-100 translate-y-0 scale-100"
                                leave-to-class="opacity-0 -translate-y-2 scale-95">
                                <div v-if="slider"
                                    class="absolute right-0 mt-2 w-48 rounded-xl border border-gray-200 bg-white shadow-lg z-50">
                                    <ul class="py-2 text-sm text-gray-700">
                                        <li>
                                            <button type="button"
                                                class="block w-full px-4 py-2 text-left hover:bg-gray-100"
                                                @click="toggleEditMode">
                                                {{ edit ? 'Read Mode' : 'Edit Mode' }}
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </Transition>
                        </div>
                    </div>

                    <!-- View Mode -->
                    <div v-if="!edit" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <span class="block font-semibold text-gray-600">Email</span>
                            <span class="text-gray-800">{{ employeeDetails.email }}</span>
                        </div>
                        <div>
                            <span class="block font-semibold text-gray-600">Gender</span>
                            <span class="text-gray-800">{{ employeeDetails.gender }}</span>
                        </div>
                        <div>
                            <span class="block font-semibold text-gray-600">Date of Birth</span>
                            <span class="text-gray-800">{{ employeeDetails.dob }}</span>
                        </div>
                        <div>
                            <span class="block font-semibold text-gray-600">Status</span>
                            <span class="text-gray-800">{{ employeeDetails.status }}</span>
                        </div>
                        <div>
                            <span class="block font-semibold text-gray-600">Created At</span>
                            <span class="text-gray-800">
                                {{ new Date(employeeDetails.created_at).toLocaleString() }}
                            </span>
                        </div>
                        <div>
                            <span class="block font-semibold text-gray-600">Updated At</span>
                            <span class="text-gray-800">
                                {{ new Date(employeeDetails.updated_at).toLocaleString() }}
                            </span>
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <form v-else @submit.prevent="handleUpdate(employeeDetails)">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div v-for="field in ['firstname', 'middlename', 'lastname']" :key="field"
                                class="space-y-3">
                                <Label :for="field">{{ field.charAt(0).toUpperCase() + field.slice(1) }}</Label>
                                <Input type="text" :id="field" :name="field" :placeholder="`Enter ${field}`"
                                    v-model="employeeDetails[field]" :required="field !== 'middlename'" />
                                <ErrorLabel v-if="errors && errors[field]" :error="errors[field]" />
                            </div>

                            <div class="space-y-3">
                                <Label for="email">Email</Label>
                                <Input type="email" id="email" name="email" placeholder="Enter Email"
                                    v-model="employeeDetails.email" required />
                                <ErrorLabel v-if="errors && errors.email" :error="errors.email" />
                            </div>

                            <div class="space-y-3">
                                <Label for="gender">Gender</Label>
                                <Select id="gender" v-model="employeeDetails.gender" required>
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
                                <ErrorLabel v-if="errors && errors.gender" :error="errors.gender" />
                            </div>

                            <div class="space-y-3">
                                <Label for="dob">Date of Birth</Label>
                                <Input type="date" id="dob" v-model="employeeDetails.dob" required />
                                <ErrorLabel v-if="errors && errors.dob" :error="errors.dob" />
                            </div>

                            <div class="space-y-3">
                                <Label for="status">Status</Label>
                                <Select id="status" v-model="employeeDetails.status" required>
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

                        <!-- Actions -->
                        <div class="mt-6 flex justify-end gap-3">
                            <Button type="button" variant="border" @click="edit = false">Cancel</Button>
                            <Button type="submit">Save Changes</Button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Details -->
            <div v-if="employeeDetails && employeeDetails.user"
                class="bg-white rounded-xl shadow p-6 border border-gray-200">
                <div class="flex justify-between items-center border-b pb-3 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Account Details</h2>

                    <div class="relative">
                        <Button @click="toggleAccountSlider">
                            <FontAwesomeIcon :icon="faSliders" />
                        </Button>

                        <Transition enter-active-class="transition duration-300 ease-in-out"
                            enter-from-class="opacity-0 -translate-y-2 scale-95"
                            enter-to-class="opacity-100 translate-y-0 scale-100"
                            leave-active-class="transition duration-200 ease-in-out"
                            leave-from-class="opacity-100 translate-y-0 scale-100"
                            leave-to-class="opacity-0 -translate-y-2 scale-95">
                            <div v-if="accountSlider"
                                class="absolute right-0 mt-2 w-48 rounded-xl border border-gray-200 bg-white shadow-lg z-50">
                                <ul class="py-2 text-sm text-gray-700">
                                    <li>
                                        <button type="button" class="block w-full px-4 py-2 text-left hover:bg-gray-100"
                                            @click="toggleEditAccountMode">
                                            {{ editAccount ? 'Read Mode' : 'Edit Mode' }}
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </Transition>
                    </div>
                </div>

                <!-- Account Read Mode -->
                <div v-if="!editAccount" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <span class="block font-semibold text-gray-600">Username</span>
                        <span class="text-gray-800">{{ employeeDetails.user.username }}</span>
                    </div>
                    <div>
                        <span class="block font-semibold text-gray-600">Role</span>
                        <span class="text-gray-800">{{ employeeDetails.user.role }}</span>
                    </div>
                    <div>
                        <span class="block font-semibold text-gray-600">Created At</span>
                        <span class="text-gray-800">{{ new Date(employeeDetails.user.created_at).toLocaleString()
                        }}</span>
                    </div>
                    <div>
                        <span class="block font-semibold text-gray-600">Updated At</span>
                        <span class="text-gray-800">{{ new Date(employeeDetails.user.updated_at).toLocaleString()
                        }}</span>
                    </div>
                </div>

                <!-- Account Edit Mode -->
                <form v-else @submit.prevent="handleUpdateAccount(employeeDetails.user)">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <Label for="username">Username</Label>
                            <Input type="text" id="username" name="username" v-model="employeeDetails.user.username"
                                readonly required />
                        </div>
                        <div class="space-y-3">
                            <Label for="role">Role</Label>
                            <Select id="role" v-model="employeeDetails.user.role" required>
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

                    <div class="mt-6 flex justify-end gap-3">
                        <Button type="button" variant="border" @click="editAccount = false">Cancel</Button>
                        <Button type="submit">Save Changes</Button>
                    </div>
                </form>
            </div>

            <!-- Delete Action -->
            <div class="flex justify-start my-10">
                <Button class="bg-red-500 hover:bg-red-600 text-white" @click="handleDelete()">Delete</Button>
            </div>
        </div>
    </section>
</template>
