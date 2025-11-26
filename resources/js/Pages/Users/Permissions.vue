<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { ArrowLeft, Save, Shield, Info } from 'lucide-vue-next';

interface Permission {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
    email: string;
}

interface PermissionsData {
    all_permissions: Permission[];
    role_permissions: string[];
    direct_permissions: string[];
    user: {
        id: number;
        name: string;
        roles: string[];
    };
}

const props = defineProps<{
    user: User;
    permissions: PermissionsData;
    availablePermissions: Permission[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Users',
        href: '/users',
    },
    {
        title: 'Permissions',
        href: `/users/${props.user.id}/permissions`,
    },
];

// Form for direct permissions
const form = useForm({
    permissions: [...props.permissions.direct_permissions],
});

// Group permissions by prefix (e.g., "users.create" -> "users")
const groupedPermissions = computed(() => {
    const groups: Record<string, Permission[]> = {};
    
    props.availablePermissions.forEach(permission => {
        const parts = permission.name.split('.');
        const groupName = parts.length > 1 ? parts[0] : 'Other';
        
        if (!groups[groupName]) {
            groups[groupName] = [];
        }
        groups[groupName].push(permission);
    });
    
    return groups;
});

const isPermissionInherited = (permissionName: string) => {
    return props.permissions.role_permissions.includes(permissionName);
};

const togglePermission = (permissionName: string, checked: boolean) => {
    if (checked) {
        if (!form.permissions.includes(permissionName)) {
            form.permissions.push(permissionName);
        }
    } else {
        form.permissions = form.permissions.filter(p => p !== permissionName);
    }
};

const savePermissions = () => {
    form.post(route('users.permissions.sync', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Show toast notification
        },
    });
};

const goBack = () => {
    router.visit('/users');
};
</script>

<template>
    <Head title="Manage User Permissions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <Button variant="ghost" size="icon" @click="goBack" class="-ml-2">
                            <ArrowLeft class="h-5 w-5" />
                        </Button>
                        <h1 class="text-3xl font-bold tracking-tight">User Permissions</h1>
                    </div>
                    <p class="text-sm text-gray-500 mt-1 ml-10">
                        Manage permissions for <span class="font-medium text-gray-900">{{ user.name }}</span> ({{ user.email }})
                    </p>
                </div>
                
                <div class="flex items-center gap-2">
                    <Button variant="outline" @click="goBack">Cancel</Button>
                    <Button @click="savePermissions" :disabled="form.processing">
                        <Save class="mr-2 h-4 w-4" />
                        Save Changes
                    </Button>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <!-- Info Card -->
                <div class="md:col-span-1 space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Shield class="h-5 w-5 text-blue-600" />
                                Access Overview
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Assigned Roles</h4>
                                <div class="flex flex-wrap gap-2">
                                    <Badge 
                                        v-for="role in permissions.user.roles" 
                                        :key="role"
                                        variant="secondary"
                                        class="capitalize"
                                    >
                                        {{ role }}
                                    </Badge>
                                    <span v-if="permissions.user.roles.length === 0" class="text-sm text-gray-400 italic">
                                        No roles assigned
                                    </span>
                                </div>
                            </div>
                            
                            <div class="rounded-md bg-blue-50 p-4 border border-blue-100">
                                <div class="flex gap-3">
                                    <Info class="h-5 w-5 text-blue-600 shrink-0" />
                                    <div class="text-sm text-blue-800">
                                        <p class="font-medium mb-1">How permissions work</p>
                                        <p class="mb-2">
                                            Users receive permissions from two sources:
                                        </p>
                                        <ul class="list-disc list-inside space-y-1 ml-1">
                                            <li><span class="font-semibold">Roles:</span> Inherited automatically (Read-only here).</li>
                                            <li><span class="font-semibold">Direct:</span> Assigned specifically to this user.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Permissions Grid -->
                <div class="md:col-span-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Permissions</CardTitle>
                            <CardDescription>
                                Grant or revoke specific permissions for this user.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-8">
                                <div v-for="(groupPermissions, groupName) in groupedPermissions" :key="groupName">
                                    <h3 class="text-lg font-semibold capitalize mb-4 pb-2 border-b border-gray-100">
                                        {{ groupName }} Module
                                    </h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div 
                                            v-for="permission in groupPermissions" 
                                            :key="permission.id"
                                            class="flex items-start space-x-3 p-2 rounded-lg hover:bg-gray-50 transition-colors"
                                        >
                                            <Checkbox 
                                                :id="`perm-${permission.id}`"
                                                :checked="isPermissionInherited(permission.name) || form.permissions.includes(permission.name)"
                                                :disabled="isPermissionInherited(permission.name)"
                                                @update:checked="(checked) => togglePermission(permission.name, checked)"
                                            />
                                            <div class="grid gap-1.5 leading-none">
                                                <label 
                                                    :for="`perm-${permission.id}`"
                                                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer"
                                                >
                                                    {{ permission.name }}
                                                </label>
                                                <p v-if="isPermissionInherited(permission.name)" class="text-xs text-blue-600 font-medium">
                                                    Inherited from Role
                                                </p>
                                                <p v-else class="text-xs text-gray-500">
                                                    Direct assignment
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
