<script setup>
import { getEmployees } from '@/composables/useEmployee';
import { manageUsers } from '@/composables/useUser';
import { onMounted } from 'vue';

const { userCred, createUser } = manageUsers();
const { employees, fetchEmployees } = getEmployees();
onMounted(() => {
    fetchEmployees();
});
</script>
<template>
    <button type="button" @click="createUser">Test</button>
</template>