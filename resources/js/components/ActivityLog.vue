<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';

interface ActivityLog {
    id: number;
    description: string;
    event: string;
    properties: Record<string, any>;
    changes: {
        attributes?: Record<string, any>;
        old?: Record<string, any>;
    };
    causer: {
        id: number;
        name: string;
    } | null;
    created_at: string;
    created_at_human: string;
}

const props = defineProps<{
    userId: number;
}>();

const activities = ref<ActivityLog[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

const fetchActivities = async () => {
    loading.value = true;
    error.value = null;

    try {
        const response = await axios.get(`/users/${props.userId}/activity-logs`);
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

const getEventColor = (event: string) => {
    const colors: Record<string, string> = {
        created: 'bg-green-100 text-green-800',
        updated: 'bg-blue-100 text-blue-800',
        deleted: 'bg-red-100 text-red-800',
    };
    return colors[event] || 'bg-gray-100 text-gray-800';
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

const hasChanges = computed(() => {
    return (activity: ActivityLog) => {
        return activity.changes?.attributes || activity.changes?.old;
    };
});
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Activity Log</CardTitle>
        </CardHeader>
        <CardContent>
            <!-- Loading State -->
            <div v-if="loading" class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="rounded-md bg-red-50 p-4">
                <p class="text-sm text-red-800">{{ error }}</p>
            </div>

            <!-- Empty State -->
            <div v-else-if="activities.length === 0" class="py-8 text-center text-gray-500">
                <p>No activity logs found for this user.</p>
            </div>

            <!-- Activities List -->
            <div v-else class="space-y-4">
                <div
                    v-for="activity in activities"
                    :key="activity.id"
                    class="border-l-4 border-gray-200 pl-4 py-2 hover:border-blue-500 transition-colors"
                >
                    <!-- Activity Header -->
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <Badge :class="getEventColor(activity.event)">
                                {{ activity.event }}
                            </Badge>
                            <span class="text-sm font-medium">{{ activity.description }}</span>
                        </div>
                        <span class="text-xs text-gray-500" :title="activity.created_at">
                            {{ activity.created_at_human }}
                        </span>
                    </div>

                    <!-- Causer Info -->
                    <div v-if="activity.causer" class="text-xs text-gray-600 mb-2">
                        Performed by: <span class="font-medium">{{ activity.causer.name }}</span>
                    </div>

                    <!-- Changes Details -->
                    <div v-if="hasChanges(activity)" class="mt-2 space-y-1">
                        <template v-if="activity.changes?.old && activity.changes?.attributes">
                            <!-- Show what changed -->
                            <div
                                v-for="(value, field) in activity.changes.attributes"
                                :key="field"
                                class="text-sm bg-gray-50 rounded p-2"
                            >
                                <span class="font-medium text-gray-700">{{ formatFieldName(String(field)) }}:</span>
                                <div class="ml-4 mt-1">
                                    <div class="flex items-center gap-2">
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
                            <div class="text-sm bg-gray-50 rounded p-2">
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
</template>
