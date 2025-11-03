<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import type { PermissionData, PermissionFormData, PermissionsPageProps } from '@/types/permissions';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<PermissionsPageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Permissions',
        href: '/permissions',
    },
];

// Modals state
const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const editingPermission = ref<PermissionData | null>(null);

// Form data
const newPermission = ref<PermissionFormData>({
    name: '',
});

const editPermission = ref<PermissionFormData>({
    name: '',
});

// Form errors
const formErrors = ref<Record<string, string>>({});

// Functions
const resetNewPermissionForm = (): void => {
    newPermission.value = {
        name: '',
    };
    formErrors.value = {};
};

const createPermission = (): void => {
    formErrors.value = {};

    router.post('/permissions', newPermission.value, {
        preserveState: false,
        onSuccess: () => {
            isCreateModalOpen.value = false;
            resetNewPermissionForm();
        },
        onError: (errors) => {
            console.error('Error creating permission:', errors);
            formErrors.value = errors as Record<string, string>;
        },
    });
};

const openEditModal = (permission: PermissionData): void => {
    formErrors.value = {};
    editingPermission.value = permission;
    editPermission.value = {
        name: permission.name,
    };
    isEditModalOpen.value = true;
};

const cancelEdit = (): void => {
    isEditModalOpen.value = false;
    editingPermission.value = null;
    formErrors.value = {};
};

const updatePermission = (): void => {
    if (!editingPermission.value) return;

    formErrors.value = {};

    router.post(`/permissions/${editingPermission.value.id}`, editPermission.value, {
        preserveState: false,
        onSuccess: () => {
            isEditModalOpen.value = false;
            editingPermission.value = null;
        },
        onError: (errors) => {
            console.error('Error updating permission:', errors);
            formErrors.value = errors as Record<string, string>;
        },
    });
};

const deletePermission = (permissionId: number, permissionName: string): void => {
    if (!confirm(`¿Estás seguro de que quieres eliminar el permiso "${permissionName}"?`)) return;

    router.post(`/permissions/${permissionId}/delete`, { _method: 'DELETE' }, {
        preserveState: false,
        onSuccess: () => {
            console.log('Permission deleted successfully');
        },
        onError: (errors) => {
            console.error('Error deleting permission:', errors);
        },
    });
};
</script>

<template>
    <Head title="Permissions Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Permissions Management</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage system permissions</p>
                </div>

                <!-- Create Permission Button -->
                <Dialog v-model:open="isCreateModalOpen">
                    <DialogTrigger asChild>
                        <Button size="sm">Create Permission</Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-md">
                        <DialogHeader>
                            <DialogTitle>Create New Permission</DialogTitle>
                        </DialogHeader>
                        <div class="space-y-4">
                            <!-- Show general errors -->
                            <div v-if="Object.keys(formErrors).length > 0" class="rounded-md border border-red-200 bg-red-50 p-3">
                                <p class="text-sm font-semibold text-red-800">Please fix the following errors:</p>
                                <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                                    <li v-for="(error, field) in formErrors" :key="field">{{ error }}</li>
                                </ul>
                            </div>

                            <div>
                                <label class="text-sm font-medium">Permission Name</label>
                                <Input v-model="newPermission.name" placeholder="e.g., projects.create, users.edit" class="mt-1" :class="{ 'border-red-500': formErrors.name }" />
                                <p v-if="formErrors.name" class="mt-1 text-xs text-red-600">{{ formErrors.name }}</p>
                                <p class="mt-1 text-xs text-gray-500">Use dot notation (e.g., module.action)</p>
                            </div>

                            <div class="rounded-md border border-blue-200 bg-blue-50 p-3">
                                <p class="text-sm text-blue-800">
                                    <strong>Tip:</strong> Use a consistent naming convention like "resource.action" (e.g., projects.list, projects.create, projects.edit)
                                </p>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <Button variant="outline" @click="() => { isCreateModalOpen = false; resetNewPermissionForm(); }">Cancel</Button>
                                <Button @click="createPermission">Create</Button>
                            </div>
                        </div>
                    </DialogContent>
                </Dialog>
            </div>

            <!-- Permissions Table -->
            <Card>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="border-b bg-gray-50/50">
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Permission</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Display Name</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Roles</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Users</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Created</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <tr v-for="permission in permissions" :key="permission.id" class="hover:bg-gray-50/50 transition-colors">
                                    <!-- Permission Name -->
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-sm font-semibold text-white">
                                                {{ permission.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 font-mono">{{ permission.name }}</div>
                                                <div class="text-xs text-gray-500">ID: {{ permission.id }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Display Name -->
                                    <td class="px-4 py-4 text-center">
                                        <span class="text-sm text-gray-700 capitalize">{{ permission.display_name }}</span>
                                    </td>

                                    <!-- Roles Count -->
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-800">
                                            {{ permission.roles_count }} roles
                                        </span>
                                    </td>

                                    <!-- Users Count -->
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">
                                            {{ permission.users_count }} users
                                        </span>
                                    </td>

                                    <!-- Created At -->
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-700">{{ new Date(permission.created_at).toLocaleDateString() }}</div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 py-4">
                                        <div class="flex justify-end gap-1">
                                            <Button
                                                size="sm"
                                                variant="ghost"
                                                @click="openEditModal(permission)"
                                                title="Edit Permission"
                                                class="h-8 w-8 p-0 text-xs"
                                            >
                                                ✏️
                                            </Button>
                                            <Button
                                                v-if="permission.roles_count === 0 && permission.users_count === 0"
                                                size="sm"
                                                variant="ghost"
                                                @click="deletePermission(permission.id, permission.name)"
                                                title="Delete Permission"
                                                class="h-8 w-8 p-0 text-xs text-red-600 hover:bg-red-50 hover:text-red-700"
                                            >
                                                🗑️
                                            </Button>
                                            <span
                                                v-else
                                                title="Cannot delete permission assigned to roles or users"
                                                class="inline-flex h-8 w-8 items-center justify-center text-xs text-gray-400 cursor-not-allowed"
                                            >
                                                🔒
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="permissions.length === 0">
                                    <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="text-3xl">🔑</span>
                                            <p class="text-sm font-medium">No permissions found</p>
                                            <p class="text-xs text-gray-400">Create your first permission to get started</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Edit Permission Modal -->
            <Dialog v-model:open="isEditModalOpen">
                <DialogContent class="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>Edit Permission</DialogTitle>
                    </DialogHeader>
                    <div class="space-y-4">
                        <!-- Show general errors -->
                        <div v-if="Object.keys(formErrors).length > 0" class="rounded-md border border-red-200 bg-red-50 p-3">
                            <p class="text-sm font-semibold text-red-800">Please fix the following errors:</p>
                            <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                                <li v-for="(error, field) in formErrors" :key="field">{{ error }}</li>
                            </ul>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Permission Name</label>
                            <Input v-model="editPermission.name" placeholder="Permission name" class="mt-1" :class="{ 'border-red-500': formErrors.name }" />
                            <p v-if="formErrors.name" class="mt-1 text-xs text-red-600">{{ formErrors.name }}</p>
                            <p class="mt-1 text-xs text-gray-500">Use dot notation (e.g., module.action)</p>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <Button variant="outline" @click="cancelEdit">Cancel</Button>
                            <Button @click="updatePermission">Update</Button>
                        </div>
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
