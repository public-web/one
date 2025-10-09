<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

interface User {
    id: number;
    name: string;
    email: string;
    active: boolean;
    expires_at?: string;
    require_2fa: boolean;
    roles: Array<{ name: string }>;
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
    active: true,
    expires_at: '',
    require_2fa: false,
    role: 'user',
});

const editUser = ref({
    name: '',
    email: '',
    active: true,
    expires_at: '',
    require_2fa: false,
    role: 'user',
});

const createUser = () => {
    router.post(
        '/users',
        {
            name: newUser.value.name,
            email: newUser.value.email,
            active: newUser.value.active,
            expires_at: newUser.value.expires_at || null,
            require_2fa: newUser.value.require_2fa,
            role: newUser.value.role,
        },
        {
            preserveState: false,
            onSuccess: () => {
                cancelCreate();
            },
            onError: (errors: any) => {
                console.error('Error creating user:', errors);
            },
        },
    );
};

const updateUser = () => {
    if (!editingUser.value) {
        console.error('No user selected for editing');
        return;
    }

    router.put(
        `/users/${editingUser.value.id}`,
        {
            name: editUser.value.name,
            email: editUser.value.email,
            active: editUser.value.active,
            expires_at: editUser.value.expires_at || null,
            require_2fa: editUser.value.require_2fa,
            role: editUser.value.role,
        },
        {
            preserveState: false,
            onSuccess: () => {
                cancelEdit();
            },
            onError: (errors: any) => {
                console.error('Error updating user:', errors);
            },
        },
    );
};

const deleteUser = (userId: number) => {
    if (!confirm('¿Estás seguro de que quieres eliminar este usuario?')) return;

    router.delete(`/users/${userId}`, {
        onSuccess: () => {
            // Usuario eliminado correctamente
        },
        onError: (errors: any) => {
            console.error('Error deleting user:', errors);
        },
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
        role: user.roles.length > 0 ? user.roles[0].name : 'user',
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
        role: 'user',
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
        role: 'user',
    };
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Gestión de Usuarios - Solo visible para superadmin -->
            <div v-if="canManageUsers" class="space-y-4">
                <div class="flex items-center justify-between">
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
                                <div class="rounded-md border border-blue-200 bg-blue-50 p-3">
                                    <p class="text-sm text-blue-800">
                                        <strong>Nota:</strong> Se generará una contraseña temporal automáticamente y se enviará por correo electrónico
                                        al usuario.
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Checkbox v-model="newUser.active" id="newUserActive" />
                                    <label for="newUserActive" class="text-sm font-medium">Usuario activo</label>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Fecha de caducidad (opcional)</label>
                                    <Input v-model="newUser.expires_at" type="date" placeholder="Fecha de caducidad" />
                                    <p class="mt-1 text-xs text-gray-500">Dejar vacío para cuenta sin caducidad</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Checkbox v-model="newUser.require_2fa" id="newUser2FA" />
                                    <label for="newUser2FA" class="text-sm font-medium">Requerir autenticación 2FA</label>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Rol</label>
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
                                    <Button variant="outline" @click="cancelCreate">Cancelar</Button>
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
                                        <th class="py-2 text-left">ID</th>
                                        <th class="py-2 text-left">Nombre</th>
                                        <th class="py-2 text-left">Email</th>
                                        <th class="py-2 text-left">Estado</th>
                                        <th class="py-2 text-left">Expira</th>
                                        <th class="py-2 text-left">2FA</th>
                                        <th class="py-2 text-left">Roles</th>
                                        <th class="py-2 text-left">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users" :key="user.id" class="border-b">
                                        <td class="py-2">{{ user.id }}</td>
                                        <td class="py-2">{{ user.name }}</td>
                                        <td class="py-2">{{ user.email }}</td>
                                        <td class="py-2">
                                            <span
                                                v-if="user.expires_at && new Date(user.expires_at) < new Date()"
                                                class="rounded bg-red-100 px-2 py-1 text-xs text-red-800"
                                            >
                                                Expirado
                                            </span>
                                            <span v-else-if="user.active" class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">
                                                Activo
                                            </span>
                                            <span v-else class="rounded bg-red-100 px-2 py-1 text-xs text-red-800"> Inactivo </span>
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
                                            <span v-else class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-800"> Sin límite </span>
                                        </td>
                                        <td class="py-2">
                                            <span
                                                :class="user.require_2fa ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'"
                                                class="rounded px-2 py-1 text-xs"
                                            >
                                                {{ user.require_2fa ? 'Requerido' : 'Opcional' }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <span
                                                v-for="role in user.roles"
                                                :key="role.name"
                                                class="mr-1 inline-block rounded bg-blue-100 px-2 py-1 text-xs text-blue-800"
                                            >
                                                {{ role.name }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <div class="flex space-x-2">
                                                <Button size="sm" variant="outline" @click="openEditModal(user)"> Editar </Button>
                                                <Button size="sm" variant="destructive" @click="deleteUser(user.id)"> Eliminar </Button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="users.length === 0">
                                        <td colspan="8" class="py-4 text-center text-gray-500">No hay usuarios registrados</td>
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
                        <div>
                            <label class="text-sm font-medium">Fecha de caducidad (opcional)</label>
                            <Input v-model="editUser.expires_at" type="date" placeholder="Fecha de caducidad" />
                            <p class="mt-1 text-xs text-gray-500">Dejar vacío para cuenta sin caducidad</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <Checkbox v-model="editUser.require_2fa" id="editUser2FA" />
                            <label for="editUser2FA" class="text-sm font-medium">Requerir autenticación 2FA</label>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Rol</label>
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
                            <Button variant="outline" @click="cancelEdit">Cancelar</Button>
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
            <div
                v-if="!canManageUsers"
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
