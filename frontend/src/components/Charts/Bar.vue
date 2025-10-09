<script setup>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)
const props = defineProps({
    chartId: {
        type: String,
        default: 'bar-chart',
    },
    chartData: {
        type: Object,
        required: true
    },
    chartOptions: {
        type: Object,
        default: () => ({ responsive: true, maintainAspectRatio: false })
    }
})
</script>
<template>
    <Bar :id="chartId" :data="chartData" :options="chartOptions" />
</template>
