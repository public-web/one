<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import type { InertiaErrors, User, UserFormData, UsersPageProps, UserSubmitData } from '@/types/users';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<UsersPageProps>();

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
const editingUser = ref<User | null>(null);

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
    router.post('/users', getUserFormData(newUser.value), {
        preserveState: false,
        onSuccess: () => {
            cancelCreate();
        },
        onError: (errors: InertiaErrors) => {
            console.error('Error creating user:', errors);
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
};
</script>

<template>
    <Head title="User Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold">User Management</h2>

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
                                <div>
                                    <label class="text-sm font-medium">Name</label>
                                    <Input v-model="newUser.name" placeholder="User name" />
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Email</label>
                                    <Input v-model="newUser.email" type="email" placeholder="email@example.com" />
                                </div>
                                <div class="rounded-md border border-blue-200 bg-blue-50 p-3">
                                    <p class="text-sm text-blue-800">
                                        <strong>Note:</strong> A temporary password will be generated automatically and sent to the user via email.
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Checkbox v-model="newUser.active" id="newUserActive" />
                                    <label for="newUserActive" class="text-sm font-medium">Active user</label>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Expiration date (optional)</label>
                                    <Input v-model="newUser.expires_at" type="date" placeholder="Expiration date" />
                                    <p class="mt-1 text-xs text-gray-500">Leave empty for no expiration</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Checkbox v-model="newUser.require_2fa" id="newUser2FA" />
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

                <!-- Users Table -->
                <Card>
                    <CardHeader>
                        <CardTitle>Users List</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="py-2 text-left">ID</th>
                                        <th class="py-2 text-left">Name</th>
                                        <th class="py-2 text-left">Email</th>
                                        <th class="py-2 text-left">Status</th>
                                        <th class="py-2 text-left">Expires</th>
                                        <th class="py-2 text-left">2FA</th>
                                        <th class="py-2 text-left">Roles</th>
                                        <th class="py-2 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users" :key="user.id" class="border-b">
                                        <td class="py-2">{{ user.id }}</td>
                                        <td class="py-2">{{ user.name }}</td>
                                        <td class="py-2">{{ user.email }}</td>
                                        <td class="py-2">
                                            <span
                                                v-if="user.deleted_at"
                                                class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-800"
                                            >
                                                Deleted
                                            </span>
                                            <span
                                                v-else-if="user.expires_at && new Date(user.expires_at) < new Date()"
                                                class="rounded bg-red-100 px-2 py-1 text-xs text-red-800"
                                            >
                                                Expired
                                            </span>
                                            <span v-else-if="user.active" class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">
                                                Active
                                            </span>
                                            <span v-else class="rounded bg-red-100 px-2 py-1 text-xs text-red-800"> Inactive </span>
                                        </td>
                                        <td class="py-2">
                                            <span
                                                v-if="user.expires_at"
                                                :class="
                                                    new Date(user.expires_at) < new Date()
                                                        ? 'bg-red-100 font-semibold text-red-800'
                                                        : 'bg-yellow-100 text-yellow-800'
                                                "
                                                class="rounded px-2 py-1 text-xs"
                                            >
                                                {{ new Date(user.expires_at).toLocaleDateString() }}
                                            </span>
                                            <span v-else class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-800"> No limit </span>
                                        </td>
                                        <td class="py-2">
                                            <span
                                                :class="user.require_2fa ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'"
                                                class="rounded px-2 py-1 text-xs"
                                            >
                                                {{ user.require_2fa ? 'Required' : 'Optional' }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <span
                                                v-for="role in user.roles"
                                                :key="`${user.id}-${role.name}`"
                                                class="mr-1 inline-block rounded bg-blue-100 px-2 py-1 text-xs text-blue-800"
                                            >
                                                {{ role.name }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <div v-if="user.deleted_at" class="flex space-x-2">
                                                <Button size="sm" variant="default" @click="restoreUser(user.id)"> Restore </Button>
                                            </div>
                                            <div v-else class="flex space-x-2">
                                                <Button size="sm" variant="outline" @click="openEditModal(user)"> Edit </Button>
                                                <Button size="sm" variant="destructive" @click="deleteUser(user.id)"> Delete </Button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="users.length === 0">
                                        <td colspan="8" class="py-4 text-center text-gray-500">No users registered</td>
                                    </tr>
                                </tbody>
                            </table>
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
                            <Checkbox v-model="editUser.active" id="editUserActive" />
                            <label for="editUserActive" class="text-sm font-medium">Active user</label>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Expiration date (optional)</label>
                            <Input v-model="editUser.expires_at" type="date" placeholder="Expiration date" />
                            <p class="mt-1 text-xs text-gray-500">Leave empty for no expiration</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <Checkbox v-model="editUser.require_2fa" id="editUser2FA" />
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
        </div>
    </AppLayout>
</template>
