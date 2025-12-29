<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import type { Permission, RoleData, RoleFormData, RolesPageProps } from '@/types/roles';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<RolesPageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Roles',
        href: '/roles',
    },
];

// Modals state
const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isPermissionsModalOpen = ref(false);
const editingRole = ref<RoleData | null>(null);
const permissionsRole = ref<RoleData | null>(null);

// Form data
const newRole = ref<RoleFormData>({
    name: '',
    permissions: [],
});

const editRole = ref<RoleFormData>({
    name: '',
    permissions: [],
});

const rolePermissions = ref<string[]>([]);

// Form errors
const formErrors = ref<Record<string, string>>({});

// Functions
const resetNewRoleForm = (): void => {
    newRole.value = {
        name: '',
        permissions: [],
    };
    formErrors.value = {};
};

const createRole = (): void => {
    formErrors.value = {};

    router.post('/roles', newRole.value, {
        preserveState: false,
        onSuccess: () => {
            isCreateModalOpen.value = false;
            resetNewRoleForm();
        },
        onError: (errors) => {
            console.error('Error creating role:', errors);
            formErrors.value = errors as Record<string, string>;
        },
    });
};

const openEditModal = (role: RoleData): void => {
    formErrors.value = {};
    editingRole.value = role;
    // Update properties individually to maintain reactivity
    editRole.value.name = role.name;
    editRole.value.permissions = role.permissions ? [...role.permissions] : [];
    isEditModalOpen.value = true;
};

const cancelEdit = (): void => {
    isEditModalOpen.value = false;
    editingRole.value = null;
    formErrors.value = {};
};

const updateRole = (): void => {
    if (!editingRole.value) return;

    formErrors.value = {};

    router.put(`/roles/${editingRole.value.id}`, editRole.value, {
        preserveState: false,
        onSuccess: () => {
            isEditModalOpen.value = false;
            editingRole.value = null;
        },
        onError: (errors) => {
            console.error('Error updating role:', errors);
            formErrors.value = errors as Record<string, string>;
        },
    });
};

const deleteRole = (roleId: number, roleName: string): void => {
    if (!confirm(`¿Estás seguro de que quieres eliminar el rol "${roleName}"?`)) return;

    router.post(`/roles/${roleId}/delete`, { _method: 'DELETE' }, {
        preserveState: false,
        onSuccess: () => {
            console.log('Role deleted successfully');
        },
        onError: (errors) => {
            console.error('Error deleting role:', errors);
        },
    });
};

const openPermissionsModal = (role: RoleData): void => {
    permissionsRole.value = role;
    // Ensure we have an array of permission names
    rolePermissions.value = role.permissions ? [...role.permissions] : [];
    isPermissionsModalOpen.value = true;
};

const togglePermission = (permissionName: string): void => {
    const index = rolePermissions.value.indexOf(permissionName);
    if (index > -1) {
        rolePermissions.value.splice(index, 1);
    } else {
        rolePermissions.value.push(permissionName);
    }
};

const isPermissionChecked = (permissionName: string): boolean => {
    return rolePermissions.value.includes(permissionName);
};

const savePermissions = (): void => {
    if (!permissionsRole.value) return;

    router.post(`/roles/${permissionsRole.value.id}/permissions`, {
        permissions: rolePermissions.value,
    }, {
        preserveState: false,
        onSuccess: () => {
            isPermissionsModalOpen.value = false;
            permissionsRole.value = null;
            rolePermissions.value = [];
        },
        onError: (errors) => {
            console.error('Error updating permissions:', errors);
        },
    });
};

const cancelPermissions = (): void => {
    isPermissionsModalOpen.value = false;
    permissionsRole.value = null;
    rolePermissions.value = [];
};

const toggleNewRolePermission = (permissionName: string): void => {
    const index = newRole.value.permissions.indexOf(permissionName);
    if (index > -1) {
        newRole.value.permissions.splice(index, 1);
    } else {
        newRole.value.permissions.push(permissionName);
    }
};

const toggleEditRolePermission = (permissionName: string): void => {
    const index = editRole.value.permissions.indexOf(permissionName);
    if (index > -1) {
        editRole.value.permissions.splice(index, 1);
    } else {
        editRole.value.permissions.push(permissionName);
    }
};
</script>

<template>
    <Head title="Roles Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Roles Management</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage roles and their permissions</p>
                </div>

                <!-- Create Role Button -->
                <Dialog v-model:open="isCreateModalOpen">
                    <DialogTrigger asChild>
                        <Button size="sm">Create Role</Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-2xl max-h-[80vh] overflow-y-auto">
                        <DialogHeader>
                            <DialogTitle>Create New Role</DialogTitle>
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
                                <label class="text-sm font-medium">Role Name</label>
                                <Input v-model="newRole.name" placeholder="e.g., editor, viewer" class="mt-1" :class="{ 'border-red-500': formErrors.name }" />
                                <p v-if="formErrors.name" class="mt-1 text-xs text-red-600">{{ formErrors.name }}</p>
                            </div>

                            <div>
                                <label class="text-sm font-medium mb-3 block">Permissions</label>
                                <div class="space-y-2 max-h-[300px] overflow-y-auto border rounded-md p-3">
                                    <div
                                        v-for="permission in props.permissions"
                                        :key="permission.id"
                                        class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded"
                                    >
                                        <Checkbox
                                            :model-value="newRole.permissions.includes(permission.name)"
                                            @update:model-value="() => toggleNewRolePermission(permission.name)"
                                        />
                                        <div class="flex-1 cursor-pointer" @click="toggleNewRolePermission(permission.name)">
                                            <label class="text-sm font-medium cursor-pointer">
                                                {{ permission.display_name }}
                                            </label>
                                            <p class="text-xs text-gray-500">{{ permission.name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <Button variant="outline" @click="() => { isCreateModalOpen = false; resetNewRoleForm(); }">Cancel</Button>
                                <Button @click="createRole">Create</Button>
                            </div>
                        </div>
                    </DialogContent>
                </Dialog>
            </div>

            <!-- Roles Table -->
            <Card>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="border-b bg-gray-50/50">
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Role</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Permissions</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Users</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Created</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <tr v-for="role in props.roles" :key="role.id" class="hover:bg-gray-50/50 transition-colors">
                                    <!-- Role Name -->
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-sm font-semibold text-white">
                                                {{ role.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 capitalize">{{ role.name }}</div>
                                                <div class="text-xs text-gray-500">ID: {{ role.id }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Permissions Count -->
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">
                                            {{ role.permissions_count }} permissions
                                        </span>
                                    </td>

                                    <!-- Users Count -->
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                            {{ role.users_count }} users
                                        </span>
                                    </td>

                                    <!-- Created At -->
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-700">{{ new Date(role.created_at).toLocaleDateString() }}</div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 py-4">
                                        <div class="flex justify-end gap-1">
                                            <Button
                                                size="sm"
                                                variant="ghost"
                                                @click="openPermissionsModal(role)"
                                                title="Manage Permissions"
                                                class="h-8 w-8 p-0 text-xs"
                                            >
                                                🔒
                                            </Button>
                                            <Button
                                                size="sm"
                                                variant="ghost"
                                                @click="openEditModal(role)"
                                                title="Edit Role"
                                                class="h-8 w-8 p-0 text-xs"
                                            >
                                                ✏️
                                            </Button>
                                            <Button
                                                v-if="!['superadmin', 'admin', 'user'].includes(role.name)"
                                                size="sm"
                                                variant="ghost"
                                                @click="deleteRole(role.id, role.name)"
                                                title="Delete Role"
                                                class="h-8 w-8 p-0 text-xs text-red-600 hover:bg-red-50 hover:text-red-700"
                                            >
                                                🗑️
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Edit Role Modal -->
            <Dialog v-model:open="isEditModalOpen">
                <DialogContent class="sm:max-w-2xl max-h-[80vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>Edit Role</DialogTitle>
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
                            <label class="text-sm font-medium">Role Name</label>
                            <input
                                v-model="editRole.name"
                                type="text"
                                placeholder="Role name"
                                class="flex h-9 w-full rounded-md border border-gray-300 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-gray-950 disabled:cursor-not-allowed disabled:opacity-50 mt-1"
                                :class="{ 'border-red-500': formErrors.name }"
                            />
                            <p v-if="formErrors.name" class="mt-1 text-xs text-red-600">{{ formErrors.name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium mb-3 block">Permissions</label>
                            <div class="space-y-2 max-h-[300px] overflow-y-auto border rounded-md p-3">
                                <div
                                    v-for="permission in props.permissions"
                                    :key="permission.id"
                                    class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded"
                                >
                                    <Checkbox
                                        :model-value="editRole.permissions.includes(permission.name)"
                                        @update:model-value="() => toggleEditRolePermission(permission.name)"
                                    />
                                    <div class="flex-1 cursor-pointer" @click="toggleEditRolePermission(permission.name)">
                                        <label class="text-sm font-medium cursor-pointer">
                                            {{ permission.display_name }}
                                        </label>
                                        <p class="text-xs text-gray-500">{{ permission.name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <Button variant="outline" @click="cancelEdit">Cancel</Button>
                            <Button @click="updateRole">Update</Button>
                        </div>
                    </div>
                </DialogContent>
            </Dialog>

            <!-- Permissions Modal -->
            <Dialog v-model:open="isPermissionsModalOpen">
                <DialogContent :key="`permissions-${permissionsRole?.id || 0}`" class="sm:max-w-2xl max-h-[80vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>
                            Manage Permissions - {{ permissionsRole?.name }}
                        </DialogTitle>
                    </DialogHeader>

                    <div class="space-y-4">
                        <div class="rounded-md border border-blue-200 bg-blue-50 p-4">
                            <p class="text-sm text-blue-800">
                                Select the permissions for this role. All users with this role will automatically have these permissions.
                            </p>
                        </div>

                        <div class="space-y-2 max-h-[400px] overflow-y-auto border rounded-md p-3">
                            <div
                                v-for="permission in props.permissions"
                                :key="permission.id"
                                class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200 transition-colors"
                            >
                                <Checkbox
                                    :model-value="isPermissionChecked(permission.name)"
                                    @update:model-value="() => togglePermission(permission.name)"
                                />
                                <div class="flex-1 cursor-pointer" @click="togglePermission(permission.name)">
                                    <label class="text-sm font-medium text-gray-900 cursor-pointer">
                                        {{ permission.display_name }}
                                    </label>
                                    <p class="text-xs text-gray-500">{{ permission.name }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 pt-4 border-t">
                            <Button variant="outline" @click="cancelPermissions">Cancel</Button>
                            <Button @click="savePermissions">Save Permissions</Button>
                        </div>
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
