<script setup>
import { onMounted, ref, watch } from 'vue'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import Button from '@/components/ui/button/Button.vue'
import { manageTax } from '@/composables/useSystem'
import { getConfig } from '@/composables/useSystem'
import { RegularSwal } from '@/components/Swals/useSwals'


const { setTax } = manageTax();
const { config, profit, fetchConfig } = getConfig();

const handleSubmit = async (cred) => {
    const success = await setTax(cred);
    if (success && success.status === 200) {
        await fetchConfig();
        RegularSwal(success.data)
    }
}
onMounted(() => {
    fetchConfig();
});

</script>

<template>
    <section class="min-h-screen bg-gray-50 py-12">
        <div class="container-xl mx-auto px-8 py-10 bg-white rounded-2xl shadow-lg space-y-10">
            <!-- Page Header -->
            <header class="border-b pb-4">
                <h1 class="text-3xl font-bold text-gray-800">System Configuration</h1>
                <p class="text-gray-600 mt-2 text-sm">
                    Manage configuration settings
                </p>
            </header>

            <!-- Tax Configuration -->
            <form @submit.prevent="handleSubmit(config)" class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Tax Rate Configuration</h2>
                    <p class="text-gray-600 text-sm mb-4">
                        Set the current tax rate percentage
                    </p>
                    <Label for="tax_rate" class="font-medium text-gray-700">Tax Rate (%)</Label>
                    <Input id="tax_rate" type="number" min="0" step="0.01" v-model="config.config_value"
                        placeholder="Enter tax rate (e.g., 12)"
                        class="mt-2 w-full border-gray-300 focus:border-brand focus:ring-brand" />
                </div>

                <div class="pt-2">
                    <Button type="submit"
                        class="bg-brand text-white px-6 py-3 rounded-xl hover:bg-brand-dark transition">
                        Save Tax Configuration
                    </Button>
                </div>
            </form>

            <!-- Profit Configuration -->
            <form @submit.prevent="handleSubmit(profit)" class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Profit Rate Configuration</h2>
                    <p class="text-gray-600 text-sm mb-4">
                        Define the profit rate percentage
                    </p>
                    <Label for="profit_rate" class="font-medium text-gray-700">Profit Rate (%)</Label>
                    <Input id="profit_rate" type="number" min="0" step="0.01" v-model="profit.config_value"
                        placeholder="Enter profit rate (e.g., 20)"
                        class="mt-2 w-full border-gray-300 focus:border-brand focus:ring-brand" />
                </div>

                <div class="pt-2">
                    <Button type="submit"
                        class="bg-brand text-white px-6 py-3 rounded-xl hover:bg-brand-dark transition">
                        Save Profit Configuration
                    </Button>
                </div>
            </form>
        </div>
    </section>

</template>
