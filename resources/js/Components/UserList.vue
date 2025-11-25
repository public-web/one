<script setup lang="ts">
import { onMounted, ref } from 'vue';

const users = ref<Array<{ id: number; name: string; email: string }>>([]);
const loading = ref(false);
const error = ref('');

const fetchUsers = async () => {
    loading.value = true;
    error.value = '';
    
    try {
        const response = await fetch('/api/users');
        if (!response.ok) throw new Error('Error al cargar usuarios');
        
        const data = await response.json();
        users.value = data;
    } catch (e) {
        error.value = e instanceof Error ? e.message : 'Error desconocido';
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchUsers();
});
</script>

<template>
    <div class="user-list">
        <div v-if="loading">Cargando...</div>
        <div v-else-if="error" class="error">{{ error }}</div>
        <ul v-else>
            <li v-for="user in users" :key="user.id">
                {{ user.name }} - {{ user.email }}
            </li>
        </ul>
    </div>
</template>
