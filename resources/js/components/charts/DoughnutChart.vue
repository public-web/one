<script setup lang="ts">
import { computed } from 'vue';
import { Doughnut } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    type ChartOptions
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement);

interface Props {
    data: Array<{ label: string; value: number }>;
    title?: string;
    colors?: string[];
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Gráfico de Dona',
    colors: () => [
        '#3b82f6', // blue
        '#10b981', // green
        '#f59e0b', // amber
        '#ef4444', // red
        '#8b5cf6', // violet
        '#ec4899', // pink
        '#06b6d4', // cyan
        '#84cc16', // lime
    ]
});

const chartData = computed(() => ({
    labels: props.data.map(item => item.label),
    datasets: [
        {
            data: props.data.map(item => item.value),
            backgroundColor: props.colors,
            borderWidth: 0,
            hoverOffset: 8
        }
    ]
}));

const options: ChartOptions<'doughnut'> = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                padding: 16,
                usePointStyle: true,
                pointStyle: 'circle',
                font: {
                    size: 12
                }
            }
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleColor: '#fff',
            bodyColor: '#fff',
            borderColor: 'rgba(255, 255, 255, 0.1)',
            borderWidth: 1,
            cornerRadius: 8,
            callbacks: {
                label: function(context) {
                    const label = context.label || '';
                    const value = context.parsed;
                    const total = context.dataset.data.reduce((acc: number, val) => acc + (val as number), 0);
                    const percentage = ((value / total) * 100).toFixed(1);
                    return `${label}: ${value} (${percentage}%)`;
                }
            }
        }
    },
    cutout: '65%'
};
</script>

<template>
    <div class="w-full h-full">
        <Doughnut :data="chartData" :options="options" />
    </div>
</template>
