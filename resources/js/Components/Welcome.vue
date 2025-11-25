<script setup lang="ts">
defineProps<{
    message?: string;
}>();

const defaultMessage = 'Bienvenido a Vue Testing';
</script>

<template>
    <div class="welcome">
        <h1>{{ message || defaultMessage }}</h1>
        <button @click="$emit('clicked')">Click me</button>
    </div>
</template>
