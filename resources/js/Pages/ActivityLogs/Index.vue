<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Activity, Filter, RefreshCw, Search, User } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';

interface ActivityLog {
    id: number;
    log_name: string;
    description: string;
    subject_type: string | null;
    subject_id: number | null;
    event: string | null;
    causer_type: string | null;
    causer_id: number | null;
    properties: Record<string, any>;
    changes: {
        attributes?: Record<string, any>;
        old?: Record<string, any>;
    };
    causer: {
        id: number;
        name: string;
    } | null;
    subject: {
        id: number;
        name: string;
    } | null;
    created_at: string;
    created_at_human: string;
}

interface PageProps {
    canManageUsers: boolean;
}

const props = defineProps<PageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Logs del Sistema', href: '/activity-logs' },
];

const activities = ref<ActivityLog[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);
const searchQuery = ref('');
const filterEvent = ref<string>('all');
const filterLogName = ref<string>('all');

const fetchActivities = async () => {
    loading.value = true;
    error.value = null;

    try {
        const response = await axios.get('/activity-logs', {
            params: {
                search: searchQuery.value || undefined,
                event: filterEvent.value !== 'all' ? filterEvent.value : undefined,
                log_name: filterLogName.value !== 'all' ? filterLogName.value : undefined,
            },
        });
        activities.value = response.data;
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Failed to load activity logs';
        console.error('Error fetching activity logs:', err);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchActivities();
});

const handleSearch = () => {
    fetchActivities();
};

const handleReset = () => {
    searchQuery.value = '';
    filterEvent.value = 'all';
    filterLogName.value = 'all';
    fetchActivities();
};

const getEventColor = (event: string | null) => {
    if (!event) return 'bg-gray-100 text-gray-800';

    const colors: Record<string, string> = {
        created: 'bg-green-100 text-green-800',
        updated: 'bg-blue-100 text-blue-800',
        deleted: 'bg-red-100 text-red-800',
    };
    return colors[event] || 'bg-gray-100 text-gray-800';
};

const getLogNameColor = (logName: string) => {
    const colors: Record<string, string> = {
        user: 'bg-purple-100 text-purple-800',
        auth: 'bg-indigo-100 text-indigo-800',
        system: 'bg-orange-100 text-orange-800',
    };
    return colors[logName] || 'bg-gray-100 text-gray-800';
};

const formatFieldName = (field: string) => {
    return field
        .replace(/_/g, ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase());
};

const formatValue = (value: any) => {
    if (value === null || value === undefined) {
        return 'null';
    }
    if (typeof value === 'boolean') {
        return value ? 'Yes' : 'No';
    }
    if (typeof value === 'object') {
        return JSON.stringify(value);
    }
    return String(value);
};

const hasChanges = (activity: ActivityLog) => {
    return activity.changes?.attributes || activity.changes?.old;
};

const availableEvents = computed(() => {
    const events = new Set<string>();
    activities.value.forEach((activity) => {
        if (activity.event) events.add(activity.event);
    });
    return Array.from(events).sort();
});

const availableLogNames = computed(() => {
    const logNames = new Set<string>();
    activities.value.forEach((activity) => {
        logNames.add(activity.log_name);
    });
    return Array.from(logNames).sort();
});
</script>

<template>
    <Head title="Logs del Sistema" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Logs del Sistema</h2>
                    <p class="text-muted-foreground">Historial completo de actividad del sistema</p>
                </div>
                <Button @click="fetchActivities" variant="outline">
                    <RefreshCw class="mr-2 h-4 w-4" />
                    Actualizar
                </Button>
            </div>

            <!-- Filters -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Filter class="h-5 w-5" />
                        Filtros
                    </CardTitle>
                    <CardDescription>Busca y filtra los registros de actividad</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-4">
                        <!-- Search -->
                        <div class="col-span-2">
                            <div class="relative">
                                <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                                <Input
                                    v-model="searchQuery"
                                    placeholder="Buscar por descripción, usuario..."
                                    class="pl-8"
                                    @keyup.enter="handleSearch"
                                />
                            </div>
                        </div>

                        <!-- Event Filter -->
                        <div>
                            <select
                                v-model="filterEvent"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            >
                                <option value="all">Todos los eventos</option>
                                <option value="created">Creados</option>
                                <option value="updated">Actualizados</option>
                                <option value="deleted">Eliminados</option>
                            </select>
                        </div>

                        <!-- Log Name Filter -->
                        <div>
                            <select
                                v-model="filterLogName"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            >
                                <option value="all">Todas las categorías</option>
                                <option value="user">Usuarios</option>
                                <option value="auth">Autenticación</option>
                                <option value="system">Sistema</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <Button @click="handleSearch" size="sm">
                            <Search class="mr-2 h-4 w-4" />
                            Buscar
                        </Button>
                        <Button @click="handleReset" variant="outline" size="sm">
                            Limpiar Filtros
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Activity Logs -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Activity class="h-5 w-5" />
                        Registros de Actividad
                    </CardTitle>
                    <CardDescription>{{ activities.length }} registros encontrados</CardDescription>
                </CardHeader>
                <CardContent>
                    <!-- Loading State -->
                    <div v-if="loading" class="flex items-center justify-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                    </div>

                    <!-- Error State -->
                    <div v-else-if="error" class="rounded-md bg-red-50 p-4">
                        <p class="text-sm text-red-800">{{ error }}</p>
                    </div>

                    <!-- Empty State -->
                    <div v-else-if="activities.length === 0" class="py-12 text-center text-gray-500">
                        <Activity class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                        <p class="text-lg font-medium">No hay registros de actividad</p>
                        <p class="text-sm">Los registros aparecerán aquí cuando se realicen acciones en el sistema</p>
                    </div>

                    <!-- Activities List -->
                    <div v-else class="space-y-4">
                        <div
                            v-for="activity in activities"
                            :key="activity.id"
                            class="border-l-4 border-gray-200 pl-4 py-3 hover:border-blue-500 transition-colors rounded-r-lg hover:bg-gray-50"
                        >
                            <!-- Activity Header -->
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <Badge :class="getLogNameColor(activity.log_name)">
                                        {{ activity.log_name }}
                                    </Badge>
                                    <Badge v-if="activity.event" :class="getEventColor(activity.event)">
                                        {{ activity.event }}
                                    </Badge>
                                    <span class="text-sm font-medium">{{ activity.description }}</span>
                                </div>
                                <span class="text-xs text-gray-500 whitespace-nowrap ml-2" :title="activity.created_at">
                                    {{ activity.created_at_human }}
                                </span>
                            </div>

                            <!-- Subject and Causer Info -->
                            <div class="flex items-center gap-4 text-xs text-gray-600 mb-2">
                                <!-- Causer -->
                                <div v-if="activity.causer" class="flex items-center gap-1">
                                    <User class="h-3 w-3" />
                                    <span>Realizado por:</span>
                                    <span class="font-medium">{{ activity.causer.name }}</span>
                                </div>

                                <!-- Subject -->
                                <div v-if="activity.subject && activity.subject_type" class="flex items-center gap-1">
                                    <span>Afectó a:</span>
                                    <span class="font-medium">{{ activity.subject.name }}</span>
                                    <span class="text-gray-400">(ID: {{ activity.subject_id }})</span>
                                </div>
                            </div>

                            <!-- Changes Details -->
                            <div v-if="hasChanges(activity)" class="mt-3 space-y-2">
                                <template v-if="activity.changes?.old && activity.changes?.attributes">
                                    <!-- Show what changed -->
                                    <div
                                        v-for="(value, field) in activity.changes.attributes"
                                        :key="field"
                                        class="text-sm bg-gray-50 rounded p-3"
                                    >
                                        <span class="font-medium text-gray-700">{{ formatFieldName(String(field)) }}:</span>
                                        <div class="ml-4 mt-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-red-600 line-through">
                                                    {{ formatValue(activity.changes.old[field]) }}
                                                </span>
                                                <span class="text-gray-400">→</span>
                                                <span class="text-green-600">
                                                    {{ formatValue(value) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template v-else-if="activity.changes?.attributes">
                                    <!-- Show only new values (for created events) -->
                                    <div class="text-sm bg-gray-50 rounded p-3 space-y-1">
                                        <div
                                            v-for="(value, field) in activity.changes.attributes"
                                            :key="field"
                                            class="flex items-baseline gap-2"
                                        >
                                            <span class="font-medium text-gray-700">{{ formatFieldName(String(field)) }}:</span>
                                            <span class="text-gray-900">{{ formatValue(value) }}</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
