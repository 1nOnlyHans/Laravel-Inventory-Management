<script setup>
import { defineProps, computed } from 'vue'
import Button from '../ui/button/Button.vue'
// Import images so Vite resolves them
import maleImg from '@/assets/images/male.png'
import femaleImg from '@/assets/images/female.png'

const props = defineProps({
    name: String,
    employee_id: String,
    uniqid: String,
    gender: String,
    image: String,
    role: String
})

// Pick image based on gender
const image = props.gender === 'Male' ? maleImg : femaleImg;

</script>

<template>
    <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-6 w-[320px] border border-border">
        <div class="flex flex-col items-center space-y-4">
            <!-- Profile Image -->
            <img :src="image" alt="picture"
                class="rounded-full w-[120px] h-[120px] object-cover border-4 border-indigo-200 shadow" />

            <!-- Info -->
            <div class="text-center space-y-2">
                <h2 class="text-xl font-semibold text-gray-800">{{ props.name }}</h2>
                <p class="text-sm text-text-secondary">Employee ID: <span class="font-medium">{{ props.employee_id }}</span>
                </p>
                <p class="text-sm text-text-secondary">Role: <span class="font-medium">{{ props.role }}</span>
                </p>
            </div>
        </div>

        <!-- Footer / Actions -->
        <div class="mt-6 flex justify-end">
            <RouterLink class="bg-accents hover:bg-accents text-white px-3 py-2 rounded-lg shadow text-sm"
                :to="{ name: 'Admin Employee Details', params: { employee_id: props.uniqid } }">Manage
            </RouterLink>
        </div>
    </div>
</template>
