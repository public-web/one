<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :class="className" v-bind="$attrs">
        <!-- Casa 1 -->
        <path d="M3 12 L8 7 L13 12 V19 H3 V12 Z" fill="currentColor"/>
        <!-- Casa 2 -->
        <path d="M11 9 L16 4 L21 9 V19 H11 V9 Z" fill="currentColor"/>
        <!-- Detalles de ventanas/puertas -->
        <rect x="5" y="14" width="2" height="3" fill="white"/>
        <rect x="9" y="14" width="2" height="3" fill="white"/>
        <rect x="13" y="11" width="2" height="3" fill="white"/>
        <rect x="17" y="11" width="2" height="3" fill="white"/>
    </svg>
</template>
