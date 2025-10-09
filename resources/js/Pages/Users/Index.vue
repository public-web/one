<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';
import { ref } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
    active: boolean;
    expires_at?: string;
    require_2fa: boolean;
    roles: Array<{name: string}>;
}

interface Role {
    id: number;
    name: string;
}

interface Props {
    users: User[];
    availableRoles: Role[];
}

const props = defineProps<Props>();

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

const newUser = ref({
    name: '',
    email: '',
    active: true,
    expires_at: '',
    require_2fa: false,
    role: 'user'
});

const editUser = ref({
    name: '',
    email: '',
    active: true,
    expires_at: '',
    require_2fa: false,
    role: 'user'
});

const createUser = () => {
    router.post('/users', {
        name: newUser.value.name,
        email: newUser.value.email,
        active: newUser.value.active,
        expires_at: newUser.value.expires_at || null,
        require_2fa: newUser.value.require_2fa,
        role: newUser.value.role
    }, {
        preserveState: false,
        onSuccess: () => {
            cancelCreate();
        },
        onError: (errors: any) => {
            console.error('Error creating user:', errors);
        }
    });
};

const updateUser = () => {
    if (!editingUser.value) {
        console.error('No user selected for editing');
        return;
    }

    router.put(`/users/${editingUser.value.id}`, {
        name: editUser.value.name,
        email: editUser.value.email,
        active: editUser.value.active,
        expires_at: editUser.value.expires_at || null,
        require_2fa: editUser.value.require_2fa,
        role: editUser.value.role
    }, {
        preserveState: false,
        onSuccess: () => {
            cancelEdit();
        },
        onError: (errors: any) => {
            console.error('Error updating user:', errors);
        }
    });
};

const deleteUser = (userId: number) => {
    if (!confirm('¿Estás seguro de que quieres eliminar este usuario?')) return;

    router.delete(`/users/${userId}`, {
        onSuccess: () => {
            // Usuario eliminado correctamente
        },
        onError: (errors: any) => {
            console.error('Error deleting user:', errors);
        }
    });
};

const openEditModal = (user: User) => {
    editingUser.value = user;
    editUser.value = {
        name: user.name,
        email: user.email,
        active: user.active,
        expires_at: user.expires_at ? user.expires_at.split('T')[0] : '',
        require_2fa: user.require_2fa || false,
        role: user.roles.length > 0 ? user.roles[0].name : 'user'
    };
    isEditModalOpen.value = true;
};

const cancelEdit = () => {
    isEditModalOpen.value = false;
    editingUser.value = null;
    editUser.value = {
        name: '',
        email: '',
        active: true,
        expires_at: '',
        require_2fa: false,
        role: 'user'
    };
};

const cancelCreate = () => {
    isCreateModalOpen.value = false;
    newUser.value = {
        name: '',
        email: '',
        active: true,
        expires_at: '',
        require_2fa: false,
        role: 'user'
    };
};
</script>

<template>
    <Head title="User Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="space-y-4">
                <div class="flex justify-between items-center">
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
                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-md">
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
                                    <p class="text-xs text-gray-500 mt-1">Leave empty for no expiration</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Checkbox v-model="newUser.require_2fa" id="newUser2FA" />
                                    <label for="newUser2FA" class="text-sm font-medium">Require 2FA authentication</label>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Role</label>
                                    <select v-model="newUser.role" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                                        <th class="text-left py-2">ID</th>
                                        <th class="text-left py-2">Name</th>
                                        <th class="text-left py-2">Email</th>
                                        <th class="text-left py-2">Status</th>
                                        <th class="text-left py-2">Expires</th>
                                        <th class="text-left py-2">2FA</th>
                                        <th class="text-left py-2">Roles</th>
                                        <th class="text-left py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users" :key="user.id" class="border-b">
                                        <td class="py-2">{{ user.id }}</td>
                                        <td class="py-2">{{ user.name }}</td>
                                        <td class="py-2">{{ user.email }}</td>
                                        <td class="py-2">
                                            <span v-if="user.expires_at && new Date(user.expires_at) < new Date()" class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">
                                                Expired
                                            </span>
                                            <span v-else-if="user.active" class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                                Active
                                            </span>
                                            <span v-else class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">
                                                Inactive
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <span v-if="user.expires_at" :class="new Date(user.expires_at) < new Date() ? 'bg-red-100 text-red-800 font-semibold' : 'bg-yellow-100 text-yellow-800'" class="text-xs px-2 py-1 rounded">
                                                {{ new Date(user.expires_at).toLocaleDateString() }}
                                            </span>
                                            <span v-else class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-800">
                                                No limit
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <span :class="user.require_2fa ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'" class="text-xs px-2 py-1 rounded">
                                                {{ user.require_2fa ? 'Required' : 'Optional' }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <span v-for="role in user.roles" :key="role.name" class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1">
                                                {{ role.name }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <div class="flex space-x-2">
                                                <Button size="sm" variant="outline" @click="openEditModal(user)">
                                                    Edit
                                                </Button>
                                                <Button size="sm" variant="destructive" @click="deleteUser(user.id)">
                                                    Delete
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="users.length === 0">
                                        <td colspan="8" class="text-center py-4 text-gray-500">
                                            No users registered
                                        </td>
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
                            <p class="text-xs text-gray-500 mt-1">Leave empty for no expiration</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <Checkbox v-model="editUser.require_2fa" id="editUser2FA" />
                            <label for="editUser2FA" class="text-sm font-medium">Require 2FA authentication</label>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Role</label>
                            <select v-model="editUser.role" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
