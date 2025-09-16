<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';
import { ref, computed } from 'vue';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

interface User {
    id: number;
    name: string;
    email: string;
    active: boolean;
    require_2fa: boolean;
    roles: Array<{name: string}>;
}

interface Role {
    id: number;
    name: string;
}

interface PageProps {
    canManageUsers: boolean;
    availableRoles: Role[];
    users?: User[];
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const page = usePage<PageProps>();
const canManageUsers = computed(() => page.props.canManageUsers);
const availableRoles = computed(() => page.props.availableRoles || []);
const users = computed(() => page.props.users || []);
const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const editingUser = ref<User | null>(null);

const newUser = ref({
    name: '',
    email: '',
    password: '',
    active: true,
    require_2fa: false,
    role: 'user'
});

const editUser = ref({
    name: '',
    email: '',
    active: true,
    require_2fa: false,
    role: 'user'
});


const createUser = () => {
    router.post('/users', {
        name: newUser.value.name,
        email: newUser.value.email,
        password: newUser.value.password,
        active: newUser.value.active,
        require_2fa: newUser.value.require_2fa,
        role: newUser.value.role
    }, {
        onSuccess: () => {
            isCreateModalOpen.value = false;
            newUser.value = { name: '', email: '', password: '', active: true, require_2fa: false, role: 'user' };
        },
        onError: (errors) => {
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
        require_2fa: editUser.value.require_2fa,
        role: editUser.value.role
    }, {
        onSuccess: () => {
            isEditModalOpen.value = false;
            editingUser.value = null;
        },
        onError: (errors) => {
            console.error('Error updating user:', errors);
        }
    });
};

const deleteUser = (userId: number) => {
    if (!confirm('¿Estás seguro de que quieres eliminar este usuario?')) return;

    router.delete(`/users/${userId}`, {}, {
        onSuccess: () => {
            // Usuario eliminado correctamente
        },
        onError: (errors) => {
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
        require_2fa: user.require_2fa || false,
        role: user.roles.length > 0 ? user.roles[0].name : 'user'
    };
    isEditModalOpen.value = true;
};

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Gestión de Usuarios - Solo visible para superadmin -->
            <div v-if="canManageUsers" class="space-y-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">Gestión de Usuarios</h2>

                    <!-- Botón Crear Usuario -->
                    <Dialog v-model:open="isCreateModalOpen">
                        <DialogTrigger asChild>
                            <Button>Crear Usuario</Button>
                        </DialogTrigger>
                        <DialogContent class="sm:max-w-md">
                            <DialogHeader>
                                <DialogTitle>Crear Nuevo Usuario</DialogTitle>
                            </DialogHeader>
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium">Nombre</label>
                                    <Input v-model="newUser.name" placeholder="Nombre del usuario" />
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Email</label>
                                    <Input v-model="newUser.email" type="email" placeholder="email@ejemplo.com" />
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Contraseña</label>
                                    <Input v-model="newUser.password" type="password" placeholder="Contraseña" />
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Checkbox v-model="newUser.active" id="newUserActive" />
                                    <label for="newUserActive" class="text-sm font-medium">Usuario activo</label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Checkbox v-model="newUser.require_2fa" id="newUser2FA" />
                                    <label for="newUser2FA" class="text-sm font-medium">Requerir autenticación 2FA</label>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Rol</label>
                                    <select v-model="newUser.role" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option v-for="role in availableRoles" :key="role.id" :value="role.name">
                                            {{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}
                                        </option>
                                    </select>
                                </div>
                                <div class="flex justify-end space-x-2">
                                    <Button variant="outline" @click="isCreateModalOpen = false">Cancelar</Button>
                                    <Button @click="createUser">Crear</Button>
                                </div>
                            </div>
                        </DialogContent>
                    </Dialog>
                </div>

                <!-- Tabla de Usuarios -->
                <Card>
                    <CardHeader>
                        <CardTitle>Lista de Usuarios</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2">ID</th>
                                        <th class="text-left py-2">Nombre</th>
                                        <th class="text-left py-2">Email</th>
                                        <th class="text-left py-2">Estado</th>
                                        <th class="text-left py-2">2FA</th>
                                        <th class="text-left py-2">Roles</th>
                                        <th class="text-left py-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users" :key="user.id" class="border-b">
                                        <td class="py-2">{{ user.id }}</td>
                                        <td class="py-2">{{ user.name }}</td>
                                        <td class="py-2">{{ user.email }}</td>
                                        <td class="py-2">
                                            <span :class="user.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="text-xs px-2 py-1 rounded">
                                                {{ user.active ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <span :class="user.require_2fa ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'" class="text-xs px-2 py-1 rounded">
                                                {{ user.require_2fa ? 'Requerido' : 'Opcional' }}
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
                                                    Editar
                                                </Button>
                                                <Button size="sm" variant="destructive" @click="deleteUser(user.id)">
                                                    Eliminar
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="users.length === 0">
                                        <td colspan="7" class="text-center py-4 text-gray-500">
                                            No hay usuarios registrados
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Modal de Edición -->
            <Dialog v-model:open="isEditModalOpen">
                <DialogContent class="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>Editar Usuario</DialogTitle>
                    </DialogHeader>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium">Nombre</label>
                            <Input v-model="editUser.name" placeholder="Nombre del usuario" />
                        </div>
                        <div>
                            <label class="text-sm font-medium">Email</label>
                            <Input v-model="editUser.email" type="email" placeholder="email@ejemplo.com" />
                        </div>
                        <div class="flex items-center space-x-2">
                            <Checkbox v-model="editUser.active" id="editUserActive" />
                            <label for="editUserActive" class="text-sm font-medium">Usuario activo</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <Checkbox v-model="editUser.require_2fa" id="editUser2FA" />
                            <label for="editUser2FA" class="text-sm font-medium">Requerir autenticación 2FA</label>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Rol</label>
                            <select v-model="editUser.role" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option v-for="role in availableRoles" :key="role.id" :value="role.name">
                                    {{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}
                                </option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <Button variant="outline" @click="isEditModalOpen = false">Cancelar</Button>
                            <Button @click="updateUser">Actualizar</Button>
                        </div>
                    </div>
                </DialogContent>
            </Dialog>

            <!-- Contenido anterior del dashboard (solo si no puede gestionar usuarios) -->
            <div v-if="!canManageUsers" class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div v-if="!canManageUsers" class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
