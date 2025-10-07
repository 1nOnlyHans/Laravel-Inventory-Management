<script setup>
import { onlinePayment } from '@/composables/usePO';
import { computed, onMounted, ref } from 'vue';

const { checkIfSuccessTransaction, fetchLatestSession } = onlinePayment();
const session_id = ref(null);
onMounted(async () => {
    session_id.value = await fetchLatestSession();
    if (session_id.value) {
        await checkIfSuccessTransaction(session_id.value.data[0].session_id,session_id.value.data[0].purchase_id);
    }

})
</script>
<template>
    <h1>Success Payment</h1>
</template>