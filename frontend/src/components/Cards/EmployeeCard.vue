<script setup>
import { defineProps, computed } from 'vue'
import Button from '../ui/button/Button.vue'
// Import images so Vite resolves them
import maleImg from '@/assets/images/male.png'
import femaleImg from '@/assets/images/female.png'

const props = defineProps({
    name: String,
    employee_id: String,
    gender: String,
    image: String
})

// Pick image based on gender
const image = props.gender === 'Male' ? maleImg : femaleImg;

</script>

<template>
    <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-6 w-[320px]">
        <div class="flex flex-col items-center space-y-4">
            <!-- Profile Image -->
            <img :src="image" alt="picture"
                class="rounded-full w-[120px] h-[120px] object-cover border-4 border-indigo-200 shadow" />

            <!-- Info -->
            <div class="text-center space-y-2">
                <h2 class="text-xl font-semibold text-gray-800">{{ props.name }}</h2>
                <p class="text-sm text-gray-500">Employee ID: <span class="font-medium">{{ props.employee_id }}</span>
                </p>
            </div>
        </div>

        <!-- Footer / Actions -->
        <div class="mt-6 flex justify-end">
            <Button class="bg-button hover:bg-button-hovered text-white px-4 py-2 rounded-lg shadow">
                Manage
            </Button>
        </div>
    </div>
</template>
