<script setup>
import { onlinePayment } from '@/composables/usePO';
import { computed, onMounted, ref } from 'vue';

const { checkIfSuccessTransaction, fetchLatestSession, isLoading } = onlinePayment();
const session_id = ref(null);
onMounted(async () => {
    session_id.value = await fetchLatestSession();
    if (session_id.value) {
        await checkIfSuccessTransaction(session_id.value.data[0].session_id, session_id.value.data[0].purchase_id);
    }

})
</script>
<template>
    <section v-if="isLoading" class="min-h-screen flex flex-col items-center justify-center text-center p-4">
        <VueSpinnerOval size="80" color="#3b82f6" />
        <h1 class="mt-4 font-bold text-xl sm:text-2xl text-accents">
            Payment Success Please Wait We Will redirect you in purchase history page
        </h1>
    </section>
</template>