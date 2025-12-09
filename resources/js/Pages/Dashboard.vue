<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import * as usersRoutes from '@/routes/users';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Activity, AlertCircle, Clock, FolderKanban, Shield, TrendingUp, Users } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import BarChart from '@/components/charts/BarChart.vue';
import DoughnutChart from '@/components/charts/DoughnutChart.vue';
import axios from 'axios';

interface Role {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    active: boolean;
    expires_at: string | null;
    require_2fa: boolean;
    deleted_at: string | null;
    created_at: string;
    roles: Role[];
}

interface Statistics {
    total: number;
    active: number;
    expired: number;
    with2FA: number;
    inactive: number;
    deleted: number;
}

interface RoleDistribution {
    name: string;
    count: number;
}

interface BancoProyectoStats {
    total: number;
    por_localidad: Array<{ localidad: string; total: number }>;
    por_estado: Array<{ estado: string; total: number }>;
    eliminados: number;
}

interface PreviabilizacionStats {
    total: number;
    por_priorizado: Array<{ priorizado_por: string; total: number }>;
    por_jac: Array<{ juntas_accion_comunal: string; total: number }>;
    por_mes: Array<{ mes: string; total: number }>;
    recientes: any[];
}

interface PageProps {
    canManageUsers: boolean;
    canManageBancoProyectos: boolean;
    canManagePreviabilizacion: boolean;
    availableRoles: Role[];
    statistics: Statistics;
    recentUsers: User[];
    expiringUsers: User[];
    roleDistribution: RoleDistribution[];
}

const props = defineProps<PageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// Computed properties
const statistics = computed(() => props.statistics || {});
const recentUsers = computed(() => props.recentUsers || []);
const expiringUsers = computed(() => props.expiringUsers || []);
const roleDistribution = computed(() => props.roleDistribution || []);

// Calculate percentages for role distribution
const totalUsersInRoles = computed(() =>
    roleDistribution.value.reduce((sum, role) => sum + role.count, 0)
);

const getRolePercentage = (count: number): number => {
    if (totalUsersInRoles.value === 0) return 0;
    return Math.round((count / totalUsersInRoles.value) * 100);
};

// Colors for role chart
const roleColors = ['#667eea', '#764ba2', '#f093fb', '#4facfe'];

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const getDaysUntilExpiration = (expiresAt: string): number => {
    const now = new Date();
    const expiration = new Date(expiresAt);
    const diffTime = expiration.getTime() - now.getTime();
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
};

// Banco de Proyectos Stats
const bancoProyectosStats = ref<BancoProyectoStats | null>(null);
const loadingBancoStats = ref(false);

const fetchBancoProyectosStats = async (): Promise<void> => {
    if (!props.canManageBancoProyectos) return;

    loadingBancoStats.value = true;
    try {
        const response = await axios.get<BancoProyectoStats>('/api/banco-proyectos/stats');
        bancoProyectosStats.value = response.data;
    } catch (error) {
        console.error('Error fetching banco proyectos stats:', error);
    } finally {
        loadingBancoStats.value = false;
    }
};

// Previabilización Social Stats
const previabilizacionStats = ref<PreviabilizacionStats | null>(null);
const loadingPreviabilizacionStats = ref(false);

const fetchPreviabilizacionStats = async (): Promise<void> => {
    if (!props.canManagePreviabilizacion) return;

    loadingPreviabilizacionStats.value = true;
    try {
        const response = await axios.get<PreviabilizacionStats>('/api/previabilizacion-social/stats');
        previabilizacionStats.value = response.data;
    } catch (error) {
        console.error('Error fetching previabilizacion stats:', error);
    } finally {
        loadingPreviabilizacionStats.value = false;
    }
};

const localidadesChartData = computed(() => {
    if (!bancoProyectosStats.value) return [];
    return bancoProyectosStats.value.por_localidad
        .map(item => ({
            label: item.localidad || 'Sin localidad',
            value: item.total
        }))
        .sort((a, b) => b.value - a.value)
        .slice(0, 10); // Top 10 localidades
});

const estadosChartData = computed(() => {
    if (!bancoProyectosStats.value) return [];
    return bancoProyectosStats.value.por_estado
        .map(item => ({
            label: item.estado || 'Sin estado',
            value: item.total
        }))
        .sort((a, b) => b.value - a.value);
});

const priorizadoChartData = computed(() => {
    if (!previabilizacionStats.value) return [];
    return previabilizacionStats.value.por_priorizado
        .map(item => ({
            label: item.priorizado_por || 'Sin priorizar',
            value: item.total
        }))
        .sort((a, b) => b.value - a.value)
        .slice(0, 10); // Top 10
});

const jacChartData = computed(() => {
    if (!previabilizacionStats.value) return [];
    return previabilizacionStats.value.por_jac
        .map(item => ({
            label: item.juntas_accion_comunal || 'Sin JAC',
            value: item.total
        }))
        .sort((a, b) => b.value - a.value);
});

onMounted(() => {
    if (props.canManageBancoProyectos) {
        fetchBancoProyectosStats();
    }
    if (props.canManagePreviabilizacion) {
        fetchPreviabilizacionStats();
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Dashboard para Superadmin -->
            <div v-if="canManageUsers" class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Dashboard</h2>
                        <p class="text-muted-foreground">Panel de administración y estadísticas del sistema</p>
                    </div>
                    <Button @click="router.visit(usersRoutes.index().url)">
                        <Users class="mr-2 h-4 w-4" />
                        Gestionar Usuarios
                    </Button>
                </div>

                <!-- KPI Cards -->
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Total Users -->
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Total Usuarios</CardTitle>
                            <Users class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ statistics.total || 0 }}</div>
                            <p class="text-xs text-muted-foreground">Registrados en el sistema</p>
                        </CardContent>
                    </Card>

                    <!-- Active Users -->
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Usuarios Activos</CardTitle>
                            <Activity class="h-4 w-4 text-green-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-green-600">{{ statistics.active || 0 }}</div>
                            <p class="text-xs text-muted-foreground">
                                {{ statistics.inactive || 0 }} inactivos
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Expired Users -->
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Cuentas Expiradas</CardTitle>
                            <AlertCircle class="h-4 w-4 text-red-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-red-600">{{ statistics.expired || 0 }}</div>
                            <p class="text-xs text-muted-foreground">Requieren atención</p>
                        </CardContent>
                    </Card>

                    <!-- 2FA Users -->
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Con 2FA</CardTitle>
                            <Shield class="h-4 w-4 text-blue-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-blue-600">{{ statistics.with2FA || 0 }}</div>
                            <p class="text-xs text-muted-foreground">Autenticación adicional</p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Main Content Grid -->
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
                    <!-- Role Distribution Chart -->
                    <Card class="col-span-4">
                        <CardHeader>
                            <CardTitle>Distribución por Roles</CardTitle>
                            <CardDescription>Usuarios asignados a cada rol</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div v-if="roleDistribution.length > 0" class="space-y-4">
                                <div v-for="(role, index) in roleDistribution" :key="role.name" class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="h-3 w-3 rounded-full"
                                                :style="{ backgroundColor: roleColors[index % roleColors.length] }"
                                            ></div>
                                            <span class="text-sm font-medium">{{ role.name }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-muted-foreground">{{ role.count }} usuarios</span>
                                            <span class="text-sm font-medium">{{ getRolePercentage(role.count) }}%</span>
                                        </div>
                                    </div>
                                    <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                        <div
                                            class="h-full rounded-full transition-all"
                                            :style="{
                                                width: `${getRolePercentage(role.count)}%`,
                                                backgroundColor: roleColors[index % roleColors.length],
                                            }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="flex h-32 items-center justify-center text-muted-foreground">
                                No hay datos de distribución
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Recent Activity -->
                    <Card class="col-span-3">
                        <CardHeader>
                            <CardTitle>Usuarios Recientes</CardTitle>
                            <CardDescription>Últimos 5 usuarios creados</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div
                                    v-for="user in recentUsers"
                                    :key="user.id"
                                    class="flex items-center justify-between border-b pb-3 last:border-0"
                                >
                                    <div class="space-y-1">
                                        <p class="text-sm font-medium leading-none">{{ user.name }}</p>
                                        <p class="text-xs text-muted-foreground">{{ user.email }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span
                                            v-if="user.roles.length > 0"
                                            class="rounded-full bg-blue-100 px-2 py-1 text-xs text-blue-800"
                                        >
                                            {{ user.roles[0].name }}
                                        </span>
                                        <TrendingUp class="h-4 w-4 text-muted-foreground" />
                                    </div>
                                </div>
                                <div
                                    v-if="recentUsers.length === 0"
                                    class="flex h-32 items-center justify-center text-muted-foreground"
                                >
                                    No hay usuarios recientes
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Expiring Users Alert -->
                <Card v-if="expiringUsers.length > 0" class="border-yellow-200 bg-yellow-50">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-yellow-800">
                            <Clock class="h-5 w-5" />
                            Cuentas Próximas a Expirar
                        </CardTitle>
                        <CardDescription class="text-yellow-700">
                            {{ expiringUsers.length }} cuenta(s) expirará(n) en los próximos 7 días
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div
                                v-for="user in expiringUsers"
                                :key="user.id"
                                class="flex items-center justify-between rounded-lg bg-white p-3"
                            >
                                <div class="space-y-1">
                                    <p class="text-sm font-medium">{{ user.name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ user.email }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-yellow-800">
                                        {{ getDaysUntilExpiration(user.expires_at!) }} días
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ formatDate(user.expires_at!) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Quick Actions -->
                <Card>
                    <CardHeader>
                        <CardTitle>Acciones Rápidas</CardTitle>
                        <CardDescription>Accesos directos a funciones principales</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            <Button variant="outline" class="h-auto flex-col gap-2 py-4" @click="router.visit(usersRoutes.index().url)">
                                <Users class="h-6 w-6" />
                                <span class="text-sm">Ver Todos los Usuarios</span>
                            </Button>
                            <Button variant="outline" class="h-auto flex-col gap-2 py-4" @click="router.visit('/activity-logs')">
                                <Activity class="h-6 w-6" />
                                <span class="text-sm">Logs del Sistema</span>
                            </Button>
                            <Button variant="outline" class="h-auto flex-col gap-2 py-4" disabled>
                                <Shield class="h-6 w-6" />
                                <span class="text-sm">Configuración 2FA</span>
                            </Button>
                            <Button variant="outline" class="h-auto flex-col gap-2 py-4" disabled>
                                <AlertCircle class="h-6 w-6" />
                                <span class="text-sm">Reportes</span>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Dashboard para usuarios con rol Banco de Proyectos -->
            <div v-else-if="!canManageUsers && canManageBancoProyectos" class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Banco de Proyectos</h2>
                        <p class="text-muted-foreground">Panel de estadísticas y gestión de proyectos</p>
                    </div>
                    <Button @click="router.visit('/banco-proyectos')">
                        <FolderKanban class="mr-2 h-4 w-4" />
                        Gestionar Proyectos
                    </Button>
                </div>

                <!-- KPI Cards -->
                <div v-if="bancoProyectosStats" class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Total Proyectos -->
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Total Proyectos</CardTitle>
                            <FolderKanban class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ bancoProyectosStats.total }}</div>
                            <p class="text-xs text-muted-foreground">Registrados en el banco</p>
                        </CardContent>
                    </Card>

                    <!-- Localidades -->
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Localidades</CardTitle>
                            <Activity class="h-4 w-4 text-blue-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-blue-600">{{ bancoProyectosStats.por_localidad.length }}</div>
                            <p class="text-xs text-muted-foreground">Diferentes ubicaciones</p>
                        </CardContent>
                    </Card>

                    <!-- Estados -->
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Estados</CardTitle>
                            <TrendingUp class="h-4 w-4 text-green-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-green-600">{{ bancoProyectosStats.por_estado.length }}</div>
                            <p class="text-xs text-muted-foreground">Diferentes estados</p>
                        </CardContent>
                    </Card>

                    <!-- Eliminados -->
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Eliminados</CardTitle>
                            <AlertCircle class="h-4 w-4 text-red-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-red-600">{{ bancoProyectosStats.eliminados }}</div>
                            <p class="text-xs text-muted-foreground">Proyectos archivados</p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Loading State -->
                <div v-else-if="loadingBancoStats" class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card v-for="i in 4" :key="i">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Cargando...</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="h-8 bg-gray-200 rounded animate-pulse"></div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Charts Section -->
                <div v-if="bancoProyectosStats && bancoProyectosStats.total > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
                    <!-- Proyectos por Localidad -->
                    <Card class="col-span-4">
                        <CardHeader>
                            <CardTitle>Proyectos por Localidad</CardTitle>
                            <CardDescription>Top 10 localidades con más proyectos</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="h-[300px]">
                                <BarChart
                                    v-if="localidadesChartData.length > 0"
                                    :data="localidadesChartData"
                                    color="#3b82f6"
                                />
                                <div v-else class="flex items-center justify-center h-full text-muted-foreground">
                                    No hay datos disponibles
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Distribución por Estado -->
                    <Card class="col-span-3">
                        <CardHeader>
                            <CardTitle>Distribución por Estado</CardTitle>
                            <CardDescription>Proyectos según su estado</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="h-[300px]">
                                <DoughnutChart
                                    v-if="estadosChartData.length > 0"
                                    :data="estadosChartData"
                                />
                                <div v-else class="flex items-center justify-center h-full text-muted-foreground">
                                    No hay datos disponibles
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Quick Actions -->
                <Card>
                    <CardHeader>
                        <CardTitle>Acciones Rápidas</CardTitle>
                        <CardDescription>Accesos directos a funciones principales</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            <Button variant="outline" class="h-auto flex-col gap-2 py-4" @click="router.visit('/banco-proyectos')">
                                <FolderKanban class="h-6 w-6" />
                                <span class="text-sm">Ver Todos los Proyectos</span>
                            </Button>
                            <Button variant="outline" class="h-auto flex-col gap-2 py-4" disabled>
                                <Activity class="h-6 w-6" />
                                <span class="text-sm">Reportes</span>
                            </Button>
                            <Button variant="outline" class="h-auto flex-col gap-2 py-4" disabled>
                                <TrendingUp class="h-6 w-6" />
                                <span class="text-sm">Estadísticas Avanzadas</span>
                            </Button>
                            <Button variant="outline" class="h-auto flex-col gap-2 py-4" disabled>
                                <AlertCircle class="h-6 w-6" />
                                <span class="text-sm">Proyectos Archivados</span>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Dashboard para usuarios con rol Previabilización Social -->
            <div v-else-if="!canManageUsers && !canManageBancoProyectos && canManagePreviabilizacion" class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Previabilización Social</h2>
                        <p class="text-muted-foreground">Panel de estadísticas y gestión de previabilizaciones</p>
                    </div>
                    <Button @click="router.visit('/banco-proyectos')">
                        <FolderKanban class="mr-2 h-4 w-4" />
                        Ver Banco de Proyectos
                    </Button>
                </div>

                <!-- KPI Cards -->
                <div v-if="previabilizacionStats" class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Total Previabilizaciones -->
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Total Previabilizaciones</CardTitle>
                            <ClipboardCheck class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ previabilizacionStats.total }}</div>
                            <p class="text-xs text-muted-foreground">Registradas en el sistema</p>
                        </CardContent>
                    </Card>

                    <!-- Priorizadores -->
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Priorizadores</CardTitle>
                            <Users class="h-4 w-4 text-blue-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-blue-600">{{ previabilizacionStats.por_priorizado.length }}</div>
                            <p class="text-xs text-muted-foreground">Diferentes priorizadores</p>
                        </CardContent>
                    </Card>

                    <!-- JACs -->
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Juntas de Acción Comunal</CardTitle>
                            <Activity class="h-4 w-4 text-green-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-green-600">{{ previabilizacionStats.por_jac.length }}</div>
                            <p class="text-xs text-muted-foreground">JACs registradas</p>
                        </CardContent>
                    </Card>

                    <!-- Recientes -->
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Últimos Meses</CardTitle>
                            <TrendingUp class="h-4 w-4 text-purple-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-purple-600">{{ previabilizacionStats.por_mes.length }}</div>
                            <p class="text-xs text-muted-foreground">Meses con actividad</p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Loading State -->
                <div v-else-if="loadingPreviabilizacionStats" class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card v-for="i in 4" :key="i">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Cargando...</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="h-8 bg-gray-200 rounded animate-pulse"></div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Charts Section -->
                <div v-if="previabilizacionStats && previabilizacionStats.total > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
                    <!-- Previabilizaciones por Priorizado -->
                    <Card class="col-span-4">
                        <CardHeader>
                            <CardTitle>Previabilizaciones por Priorizado</CardTitle>
                            <CardDescription>Top 10 priorizadores con más registros</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="h-[300px]">
                                <BarChart
                                    v-if="priorizadoChartData.length > 0"
                                    :data="priorizadoChartData"
                                    color="#8b5cf6"
                                />
                                <div v-else class="flex items-center justify-center h-full text-muted-foreground">
                                    No hay datos disponibles
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Distribución por JAC -->
                    <Card class="col-span-3">
                        <CardHeader>
                            <CardTitle>Distribución por JAC</CardTitle>
                            <CardDescription>Juntas de Acción Comunal</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="h-[300px]">
                                <DoughnutChart
                                    v-if="jacChartData.length > 0"
                                    :data="jacChartData"
                                />
                                <div v-else class="flex items-center justify-center h-full text-muted-foreground">
                                    No hay datos disponibles
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Quick Actions -->
                <Card>
                    <CardHeader>
                        <CardTitle>Acciones Rápidas</CardTitle>
                        <CardDescription>Accesos directos a funciones principales</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            <Button variant="outline" class="h-auto flex-col gap-2 py-4" @click="router.visit('/banco-proyectos')">
                                <FolderKanban class="h-6 w-6" />
                                <span class="text-sm">Ver Banco de Proyectos</span>
                            </Button>
                            <Button variant="outline" class="h-auto flex-col gap-2 py-4" disabled>
                                <ClipboardCheck class="h-6 w-6" />
                                <span class="text-sm">Reportes</span>
                            </Button>
                            <Button variant="outline" class="h-auto flex-col gap-2 py-4" disabled>
                                <TrendingUp class="h-6 w-6" />
                                <span class="text-sm">Estadísticas Avanzadas</span>
                            </Button>
                            <Button variant="outline" class="h-auto flex-col gap-2 py-4" disabled>
                                <Activity class="h-6 w-6" />
                                <span class="text-sm">Actividad Reciente</span>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Dashboard para usuarios regulares (placeholder) -->
            <div v-else-if="!canManageUsers && !canManageBancoProyectos && !canManagePreviabilizacion" class="space-y-4">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Dashboard</h2>
                    <p class="text-muted-foreground">Bienvenido a tu panel de usuario</p>
                </div>

                <div class="grid auto-rows-min gap-4 md:grid-cols-3">
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
                    class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
