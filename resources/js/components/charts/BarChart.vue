<script setup lang="ts">
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    type ChartOptions
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

interface Props {
    data: Array<{ label: string; value: number }>;
    title?: string;
    color?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Gráfico de Barras',
    color: '#3b82f6'
});

const chartData = computed(() => ({
    labels: props.data.map(item => item.label),
    datasets: [
        {
            label: 'Cantidad',
            backgroundColor: props.color,
            borderColor: props.color,
            data: props.data.map(item => item.value),
            borderRadius: 8,
            borderWidth: 0,
        }
    ]
}));

const options: ChartOptions<'bar'> = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleColor: '#fff',
            bodyColor: '#fff',
            borderColor: 'rgba(255, 255, 255, 0.1)',
            borderWidth: 1,
            cornerRadius: 8,
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                precision: 0
            },
            grid: {
                color: 'rgba(0, 0, 0, 0.05)'
            }
        },
        x: {
            grid: {
                display: false
            }
        }
    }
};
</script>

<template>
    <div class="w-full h-full">
        <Bar :data="chartData" :options="options" />
    </div>
</template>
