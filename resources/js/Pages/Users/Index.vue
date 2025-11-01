<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import ActivityLog from '@/components/ActivityLog.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import type { InertiaErrors, User, UserFormData, UsersPageProps, UserSubmitData } from '@/types/users';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { useDebounceFn } from '@vueuse/core';

const props = defineProps<UsersPageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Users',
        href: '/users',
    },
];

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isActivityModalOpen = ref(false);
const editingUser = ref<User | null>(null);
const viewingUser = ref<User | null>(null);
const formErrors = ref<Record<string, string>>({});
const isImportModalOpen = ref(false);
const selectedFile = ref<File | null>(null);

// Helper function to get default user form values
const getDefaultUserForm = (): UserFormData => ({
    name: '',
    email: '',
    active: true,
    expires_at: '',
    require_2fa: false,
    role: 'user',
});

const newUser = ref<UserFormData>(getDefaultUserForm());
const editUser = ref<UserFormData>(getDefaultUserForm());

// Helper function to reset form
const resetUserForm = (formRef: typeof newUser | typeof editUser): void => {
    Object.assign(formRef.value, getDefaultUserForm());
};

// Helper function to get user data for submission
const getUserFormData = (formData: UserFormData): UserSubmitData => ({
    name: formData.name,
    email: formData.email,
    active: formData.active,
    expires_at: formData.expires_at || null,
    require_2fa: formData.require_2fa,
    role: formData.role,
});

const createUser = (): void => {
    const formData = getUserFormData(newUser.value);
    console.log('🔍 Creating user with data:', {
        formData,
        newUserValue: newUser.value,
    });

    formErrors.value = {};

    router.post('/users', formData, {
        preserveState: false,
        preserveScroll: false,
        onSuccess: () => {
            console.log('✅ User created successfully');
            formErrors.value = {};
            isCreateModalOpen.value = false;
            resetUserForm(newUser);
            // Force reload to get fresh data
            router.visit('/users', {
                preserveState: false,
                preserveScroll: false,
            });
        },
        onError: (errors: InertiaErrors) => {
            console.error('❌ Error creating user:', errors);
            formErrors.value = errors as Record<string, string>;
        },
    });
};

const updateUser = (): void => {
    if (!editingUser.value) {
        console.error('No user selected for editing');
        return;
    }

    const url = `/users/${editingUser.value.id}`;
    const data = {
        ...getUserFormData(editUser.value),
        _method: 'PUT'
    };

    console.log('🔍 Updating user:', {
        url,
        userId: editingUser.value.id,
        data
    });

    router.post(url, data, {
        preserveState: false,
        onSuccess: () => {
            console.log('✅ User updated successfully');
            cancelEdit();
        },
        onError: (errors: InertiaErrors) => {
            console.error('❌ Error updating user:', errors);
        },
    });
};

const deleteUser = (userId: number): void => {
    if (!confirm('¿Estás seguro de que quieres eliminar este usuario?')) return;

    router.post(`/users/${userId}`, { _method: 'DELETE' }, {
        preserveState: false,
        onSuccess: () => {
            console.log('✅ User deleted successfully');
        },
        onError: (errors: InertiaErrors) => {
            console.error('❌ Error deleting user:', errors);
        },
    });
};

const restoreUser = (userId: number): void => {
    if (!confirm('¿Estás seguro de que quieres restaurar este usuario?')) return;

    router.post(`/users/${userId}/restore`, {}, {
        preserveState: false,
        onSuccess: () => {
            console.log('✅ User restored successfully');
        },
        onError: (errors: InertiaErrors) => {
            console.error('❌ Error restoring user:', errors);
        },
    });
};

const openEditModal = (user: User): void => {
    editingUser.value = user;
    editUser.value = {
        name: user.name,
        email: user.email,
        active: user.active,
        expires_at: user.expires_at ? user.expires_at.split('T')[0] : '',
        require_2fa: user.require_2fa || false,
        role: user.roles.length > 0 ? user.roles[0].name : 'user',
    };
    isEditModalOpen.value = true;
};

const cancelEdit = (): void => {
    isEditModalOpen.value = false;
    editingUser.value = null;
    resetUserForm(editUser);
};

const cancelCreate = (): void => {
    isCreateModalOpen.value = false;
    resetUserForm(newUser);
    formErrors.value = {};
};

const openActivityModal = (user: User): void => {
    viewingUser.value = user;
    isActivityModalOpen.value = true;
};

const cancelActivityView = (): void => {
    isActivityModalOpen.value = false;
    viewingUser.value = null;
};

// Filters
const filters = ref({
    search: (props.filters?.search as string) || '',
    role: (props.filters?.role as string) || '',
    status: (props.filters?.status as string) || '',
    expiring: (props.filters?.expiring as string) || '',
    per_page: (props.filters?.per_page as number) || 15,
});

// Apply filters with URL update
const applyFilters = (): void => {
    router.get('/users', filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Debounced search
const debouncedApplyFilters = useDebounceFn(() => {
    applyFilters();
}, 500);

// Watch non-search filters (immediate apply)
watch(() => [filters.value.role, filters.value.status, filters.value.expiring, filters.value.per_page], () => {
    applyFilters();
}, { deep: true });

// Reset all filters
const resetFilters = (): void => {
    filters.value = {
        search: '',
        role: '',
        status: '',
        expiring: '',
        per_page: 15,
    };
    applyFilters();
};

// Pagination helper
const changePage = (url: string | null): void => {
    if (url) {
        router.visit(url, {
            preserveState: true,
            preserveScroll: true,
        });
    }
};

// Export functions
const exportUsers = (format: 'csv' | 'xlsx'): void => {
    const params = new URLSearchParams({
        format,
        ...filters.value as any,
    });

    window.location.href = `/users/export?${params.toString()}`;
};

// Import functions
const handleFileSelect = (event: Event): void => {
    const target = event.target as HTMLInputElement;
    selectedFile.value = target.files?.[0] || null;
};

const submitImport = (): void => {
    if (!selectedFile.value) return;

    const formData = new FormData();
    formData.append('file', selectedFile.value);

    router.post('/users/import', formData, {
        preserveState: false,
        onSuccess: () => {
            isImportModalOpen.value = false;
            selectedFile.value = null;
        },
        onError: (errors) => {
            console.error('Import errors:', errors);
        },
    });
};

const downloadTemplate = (): void => {
    window.location.href = '/users/import/template';
};
</script>

<template>
    <Head title="User Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="space-y-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-2xl font-bold">User Management</h2>

                    <div class="flex flex-wrap gap-2">
                        <!-- Export Buttons -->
                        <Button @click="exportUsers('xlsx')" variant="outline" size="sm">
                            📊 Export Excel
                        </Button>
                        <Button @click="exportUsers('csv')" variant="outline" size="sm">
                            📄 Export CSV
                        </Button>

                        <!-- Import Button -->
                        <Dialog v-model:open="isImportModalOpen">
                            <DialogTrigger asChild>
                                <Button variant="outline" size="sm">
                                    📥 Import Users
                                </Button>
                            </DialogTrigger>
                            <DialogContent class="sm:max-w-lg">
                                <DialogHeader>
                                    <DialogTitle>Import Users from CSV/Excel</DialogTitle>
                                </DialogHeader>
                                <div class="space-y-4">
                                    <!-- Download Template -->
                                    <div class="rounded-md border border-blue-200 bg-blue-50 p-4">
                                        <p class="text-sm text-blue-800">
                                            <strong>First time importing?</strong> Download our template to see the required format.
                                        </p>
                                        <Button
                                            variant="link"
                                            @click="downloadTemplate"
                                            class="mt-2 text-blue-600"
                                        >
                                            📥 Download Template
                                        </Button>
                                    </div>

                                    <!-- File Upload -->
                                    <div>
                                        <label class="text-sm font-medium">Select File</label>
                                        <input
                                            type="file"
                                            @change="handleFileSelect"
                                            accept=".csv,.xlsx,.xls"
                                            class="mt-1 w-full rounded-md border border-gray-300 p-2 text-sm"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">
                                            Accepted formats: CSV, XLSX, XLS (Max 10MB)
                                        </p>
                                    </div>

                                    <!-- Selected File Info -->
                                    <div v-if="selectedFile" class="rounded-md border border-green-200 bg-green-50 p-3">
                                        <p class="text-sm text-green-800">
                                            ✅ Selected: <strong>{{ selectedFile.name }}</strong>
                                        </p>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex justify-end gap-2">
                                        <Button variant="outline" @click="isImportModalOpen = false">
                                            Cancel
                                        </Button>
                                        <Button @click="submitImport" :disabled="!selectedFile">
                                            Import Users
                                        </Button>
                                    </div>
                                </div>
                            </DialogContent>
                        </Dialog>

                        <!-- Create User Button -->
                        <Dialog v-model:open="isCreateModalOpen">
                            <DialogTrigger asChild>
                                <Button>Create User</Button>
                            </DialogTrigger>
                            <DialogContent class="sm:max-w-md">
                            <DialogHeader>
                                <DialogTitle>Create New User</DialogTitle>
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
                                    <label class="text-sm font-medium">Name</label>
                                    <Input v-model="newUser.name" placeholder="User name" :class="{ 'border-red-500': formErrors.name }" />
                                    <p v-if="formErrors.name" class="mt-1 text-xs text-red-600">{{ formErrors.name }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Email</label>
                                    <Input v-model="newUser.email" type="email" placeholder="email@example.com" :class="{ 'border-red-500': formErrors.email }" />
                                    <p v-if="formErrors.email" class="mt-1 text-xs text-red-600">{{ formErrors.email }}</p>
                                </div>
                                <div class="rounded-md border border-blue-200 bg-blue-50 p-3">
                                    <p class="text-sm text-blue-800">
                                        <strong>Note:</strong> A temporary password will be generated automatically and sent to the user via email.
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Checkbox :checked="newUser.active" @update:checked="(value: boolean) => newUser.active = value" id="newUserActive" />
                                    <label for="newUserActive" class="text-sm font-medium">Active user</label>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Expiration date (optional)</label>
                                    <Input v-model="newUser.expires_at" type="date" placeholder="Expiration date" />
                                    <p class="mt-1 text-xs text-gray-500">Leave empty for no expiration</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Checkbox :checked="newUser.require_2fa" @update:checked="(value: boolean) => newUser.require_2fa = value" id="newUser2FA" />
                                    <label for="newUser2FA" class="text-sm font-medium">Require 2FA authentication</label>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Role</label>
                                    <select
                                        v-model="newUser.role"
                                        class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    >
                                        <option v-for="role in availableRoles" :key="role.id" :value="role.name">
                                            {{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}
                                        </option>
                                    </select>
                                </div>
                                <div class="flex justify-end space-x-2">
                                    <Button variant="outline" @click="cancelCreate">Cancel</Button>
                                    <Button @click="createUser">Create</Button>
                                </div>
                            </div>
                        </DialogContent>
                    </Dialog>
                </div>

                <!-- Filters Section -->
                <Card>
                    <CardHeader>
                        <CardTitle>Filters</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                            <!-- Search -->
                            <div>
                                <label class="text-sm font-medium">Search</label>
                                <Input
                                    v-model="filters.search"
                                    @input="debouncedApplyFilters"
                                    placeholder="Name or email..."
                                    class="mt-1"
                                />
                            </div>

                            <!-- Role Filter -->
                            <div>
                                <label class="text-sm font-medium">Role</label>
                                <select
                                    v-model="filters.role"
                                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                >
                                    <option value="">All Roles</option>
                                    <option v-for="role in availableRoles" :key="role.id" :value="role.name">
                                        {{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}
                                    </option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label class="text-sm font-medium">Status</label>
                                <select
                                    v-model="filters.status"
                                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                >
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="deleted">Deleted</option>
                                </select>
                            </div>

                            <!-- Expiration Filter -->
                            <div>
                                <label class="text-sm font-medium">Expiration</label>
                                <select
                                    v-model="filters.expiring"
                                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                >
                                    <option value="">All</option>
                                    <option value="soon">Expiring Soon (30 days)</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>

                            <!-- Reset Button -->
                            <div class="flex items-end">
                                <Button @click="resetFilters" variant="outline" class="w-full">
                                    Clear Filters
                                </Button>
                            </div>
                        </div>

                        <!-- Active Filters Display -->
                        <div v-if="filters.search || filters.role || filters.status || filters.expiring" class="mt-4 flex flex-wrap gap-2">
                            <span class="text-sm font-medium text-gray-600">Active filters:</span>
                            <span v-if="filters.search" class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-800">
                                Search: {{ filters.search }}
                            </span>
                            <span v-if="filters.role" class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-800">
                                Role: {{ filters.role }}
                            </span>
                            <span v-if="filters.status" class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-800">
                                Status: {{ filters.status }}
                            </span>
                            <span v-if="filters.expiring" class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-800">
                                Expiration: {{ filters.expiring }}
                            </span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Users Table -->
                <Card>
                    <CardHeader>
                        <CardTitle>Users List</CardTitle>
                    </CardHeader>
                    <CardContent class="p-0">
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="border-b bg-gray-50">
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">User</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Email</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Status</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Role</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">2FA</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Expires</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="user in (users.data || users)" :key="user.id" class="hover:bg-gray-50 transition-colors">
                                        <!-- User Column (Name + ID) -->
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-sm font-semibold text-white">
                                                    {{ user.name.charAt(0).toUpperCase() }}
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ user.name }}</div>
                                                    <div class="text-xs text-gray-500">ID: {{ user.id }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Email Column -->
                                        <td class="px-4 py-4">
                                            <div class="text-sm text-gray-900">{{ user.email }}</div>
                                        </td>

                                        <!-- Status Column -->
                                        <td class="px-4 py-4 text-center">
                                            <span
                                                v-if="user.deleted_at"
                                                class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800"
                                            >
                                                🗑️ Deleted
                                            </span>
                                            <span
                                                v-else-if="user.expires_at && new Date(user.expires_at) < new Date()"
                                                class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800"
                                            >
                                                ⚠️ Expired
                                            </span>
                                            <span
                                                v-else-if="user.active"
                                                class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800"
                                            >
                                                ✓ Active
                                            </span>
                                            <span
                                                v-else
                                                class="inline-flex items-center rounded-full bg-orange-100 px-3 py-1 text-xs font-medium text-orange-800"
                                            >
                                                ⏸ Inactive
                                            </span>
                                        </td>

                                        <!-- Role Column -->
                                        <td class="px-4 py-4 text-center">
                                            <span
                                                v-for="role in user.roles"
                                                :key="`${user.id}-${role.name}`"
                                                :class="{
                                                    'bg-purple-100 text-purple-800': role.name === 'superadmin',
                                                    'bg-blue-100 text-blue-800': role.name === 'admin',
                                                    'bg-gray-100 text-gray-800': role.name === 'user',
                                                }"
                                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium capitalize"
                                            >
                                                {{ role.name }}
                                            </span>
                                        </td>

                                        <!-- 2FA Column -->
                                        <td class="px-4 py-4 text-center">
                                            <span
                                                v-if="user.require_2fa"
                                                class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800"
                                            >
                                                🔒 Required
                                            </span>
                                            <span
                                                v-else
                                                class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600"
                                            >
                                                Optional
                                            </span>
                                        </td>

                                        <!-- Expires Column -->
                                        <td class="px-4 py-4 text-center">
                                            <span
                                                v-if="user.expires_at"
                                                :class="
                                                    new Date(user.expires_at) < new Date()
                                                        ? 'bg-red-100 text-red-800'
                                                        : 'bg-yellow-100 text-yellow-800'
                                                "
                                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                            >
                                                {{ new Date(user.expires_at).toLocaleDateString() }}
                                            </span>
                                            <span
                                                v-else
                                                class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600"
                                            >
                                                No limit
                                            </span>
                                        </td>

                                        <!-- Actions Column -->
                                        <td class="px-4 py-4">
                                            <div v-if="user.deleted_at" class="flex justify-end">
                                                <Button size="sm" variant="default" @click="restoreUser(user.id)" class="whitespace-nowrap">
                                                    Restore
                                                </Button>
                                            </div>
                                            <div v-else class="flex justify-end gap-1">
                                                <Button
                                                    size="sm"
                                                    variant="ghost"
                                                    @click="openActivityModal(user)"
                                                    title="View Activity Log"
                                                    class="h-8 w-8 p-0"
                                                >
                                                    📋
                                                </Button>
                                                <Button
                                                    size="sm"
                                                    variant="ghost"
                                                    @click="openEditModal(user)"
                                                    title="Edit User"
                                                    class="h-8 w-8 p-0"
                                                >
                                                    ✏️
                                                </Button>
                                                <Button
                                                    size="sm"
                                                    variant="ghost"
                                                    @click="deleteUser(user.id)"
                                                    title="Delete User"
                                                    class="h-8 w-8 p-0 text-red-600 hover:bg-red-50 hover:text-red-700"
                                                >
                                                    🗑️
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="users.data && users.data.length === 0">
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            <div class="flex flex-col items-center gap-2">
                                                <span class="text-4xl">👥</span>
                                                <p class="text-sm font-medium">No users found</p>
                                                <p class="text-xs text-gray-400">Try adjusting your filters</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="users.data && users.data.length > 0" class="mt-4 space-y-4">
                            <!-- Results summary and per-page selector -->
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-sm text-gray-600">
                                    Showing {{ users.from }} to {{ users.to }} of {{ users.total }} users
                                </div>

                                <div class="flex items-center gap-2">
                                    <label class="text-sm font-medium text-gray-600">Per page:</label>
                                    <select
                                        v-model.number="filters.per_page"
                                        class="rounded-md border border-gray-300 px-3 py-1 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    >
                                        <option :value="10">10</option>
                                        <option :value="15">15</option>
                                        <option :value="25">25</option>
                                        <option :value="50">50</option>
                                        <option :value="100">100</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Pagination buttons -->
                            <div class="flex flex-wrap items-center justify-center gap-2">
                                <Button
                                    v-for="(link, index) in users.links"
                                    :key="index"
                                    @click="changePage(link.url)"
                                    :disabled="!link.url || link.active"
                                    :variant="link.active ? 'default' : 'outline'"
                                    size="sm"
                                    v-html="link.label"
                                    class="min-w-[40px]"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Edit Modal -->
            <Dialog v-model:open="isEditModalOpen">
                <DialogContent class="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>Edit User</DialogTitle>
                    </DialogHeader>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium">Name</label>
                            <Input v-model="editUser.name" placeholder="User name" />
                        </div>
                        <div>
                            <label class="text-sm font-medium">Email</label>
                            <Input v-model="editUser.email" type="email" placeholder="email@example.com" />
                        </div>
                        <div class="flex items-center space-x-2">
                            <Checkbox :checked="editUser.active" @update:checked="(value: boolean) => editUser.active = value" id="editUserActive" />
                            <label for="editUserActive" class="text-sm font-medium">Active user</label>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Expiration date (optional)</label>
                            <Input v-model="editUser.expires_at" type="date" placeholder="Expiration date" />
                            <p class="mt-1 text-xs text-gray-500">Leave empty for no expiration</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <Checkbox :checked="editUser.require_2fa" @update:checked="(value: boolean) => editUser.require_2fa = value" id="editUser2FA" />
                            <label for="editUser2FA" class="text-sm font-medium">Require 2FA authentication</label>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Role</label>
                            <select
                                v-model="editUser.role"
                                class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            >
                                <option v-for="role in availableRoles" :key="role.id" :value="role.name">
                                    {{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}
                                </option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <Button variant="outline" @click="cancelEdit">Cancel</Button>
                            <Button @click="updateUser">Update</Button>
                        </div>
                    </div>
                </DialogContent>
            </Dialog>

            <!-- Activity Log Modal -->
            <Dialog v-model:open="isActivityModalOpen">
                <DialogContent class="sm:max-w-4xl max-h-[80vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>
                            Activity Log - {{ viewingUser?.name }}
                        </DialogTitle>
                    </DialogHeader>
                    <div v-if="viewingUser">
                        <ActivityLog :user-id="viewingUser.id" />
                    </div>
                    <div class="flex justify-end mt-4">
                        <Button variant="outline" @click="cancelActivityView">Close</Button>
                    </div>
                </DialogContent>
            </Dialog>
            </div>
        </div>
    </AppLayout>
</template>
