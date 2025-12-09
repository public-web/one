<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import LocationPicker from '@/components/LocationPicker.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import axios from 'axios';

interface Props {
    canManageBancoProyectos: boolean;
}

const props = defineProps<Props>();

interface BancoProyecto {
    id: number;
    tipo_elemento_civ_rupi: string | null;
    codigo_elemento: string;
    uso: string | null;
    area_elemento: string | null;
    localidad: string | null;
    upl: string | null;
    barrio: string | null;
    tramo_direccion: string | null;
    eje: string | null;
    inicio: string | null;
    fin: string | null;
    reserva: string | null;
    estado: string | null;
    id_contrato: string | null;
    latitude: number | null;
    longitude: number | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

interface PaginatedResponse {
    data: BancoProyecto[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface ProyectoFormData {
    tipo_elemento_civ_rupi: string;
    codigo_elemento: string;
    uso: string;
    area_elemento: string;
    localidad: string;
    upl: string;
    barrio: string;
    tramo_direccion: string;
    eje: string;
    inicio: string;
    fin: string;
    reserva: string;
    estado: string;
    id_contrato: string;
    latitude: number | null;
    longitude: number | null;
}

interface Stats {
    total: number;
    por_localidad: Array<{ localidad: string; total: number }>;
    por_estado: Array<{ estado: string; total: number }>;
    por_contrato: Array<{ id_contrato: string; total: number }>;
    eliminados: number;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Banco de Proyectos',
        href: '/banco-proyectos',
    },
];

const proyectos = ref<BancoProyecto[]>([]);
const proyectosParaHeatmap = ref<BancoProyecto[]>([]);
const stats = ref<Stats | null>(null);
const loading = ref(false);
const loadingHeatmap = ref(false);
const showHeatmap = ref(false);
const showMarkers = ref(false); // Start with markers hidden
const mapLocked = ref(false);
const mapZoom = ref(11);
const mapCenter = ref<[number, number]>([4.60971, -74.07765]);
const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isImportModalOpen = ref(false);
const editingProyecto = ref<BancoProyecto | null>(null);
const formErrors = ref<Record<string, string>>({});
const importFile = ref<File | null>(null);
const importLoading = ref(false);
const importResult = ref<any>(null);

// Pagination
const currentPage = ref(1);
const lastPage = ref(1);
const perPage = ref(500);
const total = ref(0);

// Filters
const searchQuery = ref('');
const filterEstado = ref('');
const filterLocalidad = ref('');
const filterContrato = ref('');

const getDefaultForm = (): ProyectoFormData => ({
    tipo_elemento_civ_rupi: '',
    codigo_elemento: '',
    uso: '',
    area_elemento: '',
    localidad: '',
    upl: '',
    barrio: '',
    tramo_direccion: '',
    eje: '',
    inicio: '',
    fin: '',
    reserva: '',
    estado: '',
    id_contrato: '',
    latitude: null,
    longitude: null,
});

const newProyecto = ref<ProyectoFormData>(getDefaultForm());
const editProyecto = ref<ProyectoFormData>(getDefaultForm());

const resetForm = (formRef: typeof newProyecto | typeof editProyecto): void => {
    Object.assign(formRef.value, getDefaultForm());
};

const fetchProyectos = async (): Promise<void> => {
    loading.value = true;
    try {
        const params = new URLSearchParams({
            page: currentPage.value.toString(),
            per_page: perPage.value.toString(),
        });

        if (searchQuery.value) params.append('search', searchQuery.value);
        if (filterEstado.value) params.append('estado', filterEstado.value);
        if (filterLocalidad.value) params.append('localidad', filterLocalidad.value);
        if (filterContrato.value) params.append('id_contrato', filterContrato.value);

        const response = await axios.get<PaginatedResponse>(`/api/banco-proyectos?${params.toString()}`);
        proyectos.value = response.data.data;
        currentPage.value = response.data.current_page;
        lastPage.value = response.data.last_page;
        total.value = response.data.total;
    } catch (error) {
        console.error('Error fetching proyectos:', error);
    } finally {
        loading.value = false;
    }
};

const fetchStats = async (): Promise<void> => {
    try {
        const response = await axios.get<Stats>('/api/banco-proyectos/stats');
        stats.value = response.data;
    } catch (error) {
        console.error('Error fetching stats:', error);
    }
};

const fetchProyectosParaHeatmap = async (): Promise<void> => {
    console.log('🔄 Fetching proyectos para heatmap...');
    loadingHeatmap.value = true;
    try {
        // Obtener todos los proyectos (o los primeros 5000) para el heatmap
        const response = await axios.get<PaginatedResponse>('/api/banco-proyectos?per_page=5000');
        proyectosParaHeatmap.value = response.data.data;
        console.log('✅ Proyectos fetched:', proyectosParaHeatmap.value.length);
        console.log('First proyecto:', proyectosParaHeatmap.value[0]);

        const withCoords = proyectosParaHeatmap.value.filter(p => p.latitude !== null && p.longitude !== null);
        console.log('📍 Proyectos with coordinates:', withCoords.length);
        console.log('First proyecto with coords:', withCoords[0]);
    } catch (error) {
        console.error('Error fetching proyectos para heatmap:', error);
    } finally {
        loadingHeatmap.value = false;
    }
};

const debouncedFetch = useDebounceFn(fetchProyectos, 300);

const createProyecto = async (): Promise<void> => {
    formErrors.value = {};
    try {
        await axios.post('/api/banco-proyectos', newProyecto.value);
        isCreateModalOpen.value = false;
        resetForm(newProyecto);
        fetchProyectos();
        fetchStats();
    } catch (error: any) {
        if (error.response?.data?.errors) {
            formErrors.value = error.response.data.errors;
        }
    }
};

const updateProyecto = async (): Promise<void> => {
    if (!editingProyecto.value) return;
    formErrors.value = {};
    try {
        await axios.put(`/api/banco-proyectos/${editingProyecto.value.id}`, editProyecto.value);
        isEditModalOpen.value = false;
        editingProyecto.value = null;
        resetForm(editProyecto);
        fetchProyectos();
        fetchStats();
    } catch (error: any) {
        if (error.response?.data?.errors) {
            formErrors.value = error.response.data.errors;
        }
    }
};

const deleteProyecto = async (id: number): Promise<void> => {
    if (!confirm('¿Está seguro de eliminar este proyecto?')) return;
    try {
        await axios.delete(`/api/banco-proyectos/${id}`);
        fetchProyectos();
        fetchStats();
    } catch (error) {
        console.error('Error deleting proyecto:', error);
    }
};

const openEditModal = (proyecto: BancoProyecto): void => {
    editingProyecto.value = proyecto;
    editProyecto.value = {
        tipo_elemento_civ_rupi: proyecto.tipo_elemento_civ_rupi || '',
        codigo_elemento: proyecto.codigo_elemento,
        uso: proyecto.uso || '',
        area_elemento: proyecto.area_elemento || '',
        localidad: proyecto.localidad || '',
        upl: proyecto.upl || '',
        barrio: proyecto.barrio || '',
        tramo_direccion: proyecto.tramo_direccion || '',
        eje: proyecto.eje || '',
        inicio: proyecto.inicio || '',
        fin: proyecto.fin || '',
        reserva: proyecto.reserva || '',
        estado: proyecto.estado || '',
        id_contrato: proyecto.id_contrato || '',
        latitude: proyecto.latitude || null,
        longitude: proyecto.longitude || null,
    };
    isEditModalOpen.value = true;
};

const localidades = computed(() => {
    if (!stats.value) return [];
    return stats.value.por_localidad.map(l => l.localidad).filter(Boolean);
});

const estados = computed(() => {
    if (!stats.value) return [];
    return stats.value.por_estado.map(e => e.estado).filter(Boolean);
});

const contratos = computed(() => {
    if (!stats.value) return [];
    return stats.value.por_contrato.map(c => c.id_contrato).filter(Boolean);
});

const newProyectoAddress = computed(() => {
    const parts = [
        newProyecto.value.tramo_direccion,
        newProyecto.value.barrio,
        newProyecto.value.localidad,
    ].filter(Boolean);
    return parts.join(', ');
});

const editProyectoAddress = computed(() => {
    const parts = [
        editProyecto.value.tramo_direccion,
        editProyecto.value.barrio,
        editProyecto.value.localidad,
    ].filter(Boolean);
    return parts.join(', ');
});

// Heatmap data
const heatmapData = computed(() => {
    return proyectosParaHeatmap.value
        .filter(p => p.latitude !== null && p.longitude !== null)
        .map(p => ({
            id: p.id,
            latitude: p.latitude!,
            longitude: p.longitude!,
            intensity: 1,
            label: p.codigo_elemento
        }));
});

const handleFileSelect = (event: Event): void => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        importFile.value = target.files[0];
    }
};

const downloadTemplate = async (): Promise<void> => {
    try {
        const response = await axios.get('/api/banco-proyectos/template', {
            responseType: 'blob'
        });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'plantilla_banco_proyectos.xlsx');
        document.body.appendChild(link);
        link.click();
        link.remove();
    } catch (error) {
        console.error('Error downloading template:', error);
    }
};

const importProyectos = async (): Promise<void> => {
    if (!importFile.value) return;

    importLoading.value = true;
    importResult.value = null;

    try {
        const formData = new FormData();
        formData.append('file', importFile.value);

        const response = await axios.post('/api/banco-proyectos/import', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        importResult.value = response.data;
        fetchProyectos();
        fetchStats();

        if (response.data.errors_count === 0) {
            setTimeout(() => {
                isImportModalOpen.value = false;
                importFile.value = null;
                importResult.value = null;
            }, 2000);
        }
    } catch (error: any) {
        importResult.value = error.response?.data || { message: 'Error al importar' };
    } finally {
        importLoading.value = false;
    }
};

const exportProyectos = async (format: 'xlsx' | 'csv'): Promise<void> => {
    try {
        const params = new URLSearchParams({ format });
        if (searchQuery.value) params.append('search', searchQuery.value);
        if (filterEstado.value) params.append('estado', filterEstado.value);
        if (filterLocalidad.value) params.append('localidad', filterLocalidad.value);
        if (filterContrato.value) params.append('id_contrato', filterContrato.value);

        const response = await axios.get(`/api/banco-proyectos/export?${params.toString()}`, {
            responseType: 'blob'
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        const fileName = `banco_proyectos_${new Date().toISOString().split('T')[0]}.${format}`;
        link.setAttribute('download', fileName);
        document.body.appendChild(link);
        link.click();
        link.remove();
    } catch (error) {
        console.error('Error exporting proyectos:', error);
    }
};

// Generate page numbers for pagination
const pageNumbers = computed(() => {
    const pages: (number | string)[] = [];
    const maxVisible = 5; // Maximum number of page buttons to show

    if (lastPage.value <= maxVisible + 2) {
        // Show all pages if total is small
        for (let i = 1; i <= lastPage.value; i++) {
            pages.push(i);
        }
    } else {
        // Always show first page
        pages.push(1);

        if (currentPage.value > 3) {
            pages.push('...');
        }

        // Show pages around current page
        const start = Math.max(2, currentPage.value - 1);
        const end = Math.min(lastPage.value - 1, currentPage.value + 1);

        for (let i = start; i <= end; i++) {
            pages.push(i);
        }

        if (currentPage.value < lastPage.value - 2) {
            pages.push('...');
        }

        // Always show last page
        pages.push(lastPage.value);
    }

    return pages;
});

const goToPage = (page: number | string): void => {
    if (typeof page === 'number') {
        currentPage.value = page;
        fetchProyectos();
    }
};

const toggleHeatmap = async (): Promise<void> => {
    showHeatmap.value = !showHeatmap.value;
    console.log('🗺️ Heatmap toggled:', showHeatmap.value);
    if (showHeatmap.value && proyectosParaHeatmap.value.length === 0) {
        await fetchProyectosParaHeatmap();
    }
    console.log('📊 heatmapData length:', heatmapData.value.length);
};

const toggleMapLock = (): void => {
    mapLocked.value = !mapLocked.value;
};

const focusOnBogota = (): void => {
    mapCenter.value = [4.60971, -74.07765];
    mapZoom.value = 11;
};

// Debug watcher for showMarkers
watch(() => showMarkers.value, (newVal) => {
    console.log('🔔 showMarkers changed to:', newVal);
    console.log('📊 heatmapData at this time:', heatmapData.value.length, 'points');
});

onMounted(() => {
    fetchProyectos();
    fetchStats();
});
</script>

<template>
    <Head title="Banco de Proyectos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <!-- Heatmap Section -->
            <Card v-if="showHeatmap || !showHeatmap">
                <CardHeader>
                    <div class="flex justify-between items-center">
                        <CardTitle>Mapa de Calor de Proyectos</CardTitle>
                        <div class="flex gap-2">
                            <Button
                                v-if="showHeatmap"
                                @click="focusOnBogota"
                                variant="outline"
                                size="sm"
                                class="bg-blue-50 hover:bg-blue-100"
                                title="Focalizar en Bogotá"
                            >
                                <span class="flex items-center gap-2">
                                    🎯 Focalizar
                                </span>
                            </Button>
                            <Button
                                v-if="showHeatmap"
                                @click="toggleMapLock"
                                variant="outline"
                                size="sm"
                                :class="mapLocked ? 'bg-red-50 hover:bg-red-100' : 'bg-green-50 hover:bg-green-100'"
                            >
                                <span v-if="mapLocked" class="flex items-center gap-2">
                                    🔒 Mapa Fijado
                                </span>
                                <span v-else class="flex items-center gap-2">
                                    🔓 Mapa Libre
                                </span>
                            </Button>
                            <Button v-if="showHeatmap" @click="showMarkers = !showMarkers" variant="outline" size="sm">
                                <span class="flex items-center gap-2">
                                    📍 {{ showMarkers ? 'Ocultar Marcadores' : 'Mostrar Marcadores' }}
                                </span>
                            </Button>
                            <Button @click="toggleHeatmap" variant="outline" size="sm">
                                {{ showHeatmap ? 'Ocultar Mapa de Calor' : 'Mostrar Mapa de Calor' }}
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent v-if="showHeatmap">
                    <div v-if="loadingHeatmap" class="flex justify-center items-center py-12">
                        <div class="text-muted-foreground">Cargando datos del mapa de calor...</div>
                    </div>
                    <div v-else-if="heatmapData.length === 0" class="text-center py-8 text-muted-foreground">
                        No hay proyectos con coordenadas para mostrar en el mapa de calor
                    </div>
                    <div v-else class="space-y-2">
                        <div class="text-sm text-muted-foreground">
                            Mostrando {{ heatmapData.length }} proyectos con ubicación geográfica
                        </div>
                        <div style="height: 85vh; min-height: 600px;">
                            <LocationPicker
                                :model-value="{ latitude: 4.60971, longitude: -74.07765 }"
                                :heatmap-data="heatmapData"
                                :show-heatmap="true"
                                :show-markers="showMarkers"
                                :map-center="mapCenter"
                                :map-zoom="mapZoom"
                                :map-locked="mapLocked"
                            />
                        </div>
                        <div class="text-xs text-muted-foreground mt-2">
                            <p><strong>Leyenda:</strong></p>
                            <div class="flex items-center gap-4 mt-1">
                                <span class="flex items-center gap-1">
                                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                                    Baja densidad
                                </span>
                                <span class="flex items-center gap-1">
                                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                    Media-baja
                                </span>
                                <span class="flex items-center gap-1">
                                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                                    Media-alta
                                </span>
                                <span class="flex items-center gap-1">
                                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                                    Alta densidad
                                </span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Main Content Card -->
            <Card>
                <CardHeader>
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <CardTitle>Banco de Proyectos</CardTitle>
                        <div class="flex flex-wrap gap-2">
                            <!-- Map Button -->
                            <a href="/banco-proyectos/mapa">
                                <Button variant="default" size="sm">
                                    Ver Mapa
                                </Button>
                            </a>
                            <!-- Import/Export Buttons - Only for users with banco-proyectos role -->
                            <Button v-if="props.canManageBancoProyectos" variant="outline" size="sm" @click="downloadTemplate">
                                Descargar Plantilla
                            </Button>
                            <Dialog v-if="props.canManageBancoProyectos" v-model:open="isImportModalOpen">
                                <DialogTrigger as-child>
                                    <Button variant="outline" size="sm">Importar</Button>
                                </DialogTrigger>
                                <DialogContent>
                                    <DialogHeader>
                                        <DialogTitle>Importar Proyectos desde Excel</DialogTitle>
                                    </DialogHeader>
                                    <div class="space-y-4 py-4">
                                        <div class="space-y-2">
                                            <Label for="import-file">Seleccionar archivo Excel (.xlsx, .xls, .csv)</Label>
                                            <input
                                                id="import-file"
                                                type="file"
                                                accept=".xlsx,.xls,.csv"
                                                @change="handleFileSelect"
                                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                            />
                                        </div>
                                        <div v-if="importResult" class="rounded-md p-4" :class="importResult.errors_count > 0 ? 'bg-yellow-50 text-yellow-800' : 'bg-green-50 text-green-800'">
                                            <p class="font-medium">{{ importResult.message }}</p>
                                            <p v-if="importResult.success_count > 0" class="text-sm mt-1">
                                                Importados: {{ importResult.success_count }}
                                            </p>
                                            <p v-if="importResult.errors_count > 0" class="text-sm mt-1">
                                                Errores: {{ importResult.errors_count }}
                                            </p>
                                            <div v-if="importResult.failures && importResult.failures.length > 0" class="mt-2 max-h-40 overflow-y-auto">
                                                <p class="text-sm font-medium">Detalles de errores:</p>
                                                <ul class="text-xs mt-1 space-y-1">
                                                    <li v-for="(failure, idx) in importResult.failures.slice(0, 5)" :key="idx">
                                                        Fila {{ failure.row }}: {{ failure.errors ? failure.errors.join(', ') : failure.error }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2">
                                        <Button variant="outline" @click="isImportModalOpen = false">Cancelar</Button>
                                        <Button @click="importProyectos" :disabled="!importFile || importLoading">
                                            {{ importLoading ? 'Importando...' : 'Importar' }}
                                        </Button>
                                    </div>
                                </DialogContent>
                            </Dialog>
                            <Button v-if="props.canManageBancoProyectos" variant="outline" size="sm" @click="exportProyectos('xlsx')">
                                Exportar Excel
                            </Button>
                            <Button v-if="props.canManageBancoProyectos" variant="outline" size="sm" @click="exportProyectos('csv')">
                                Exportar CSV
                            </Button>
                            <Dialog v-if="props.canManageBancoProyectos" v-model:open="isCreateModalOpen">
                                <DialogTrigger as-child>
                                    <Button>Crear Proyecto</Button>
                                </DialogTrigger>
                            <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
                                <DialogHeader>
                                    <DialogTitle>Nuevo Proyecto</DialogTitle>
                                </DialogHeader>
                                <div class="grid grid-cols-2 gap-4 py-4">
                                    <div class="space-y-2">
                                        <Label for="tipo">Tipo Elemento CIV/RUPI</Label>
                                        <select
                                            id="tipo"
                                            v-model="newProyecto.tipo_elemento_civ_rupi"
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                        >
                                            <option value="">Seleccione un tipo</option>
                                            <option value="CIV">CIV</option>
                                            <option value="RUPI">RUPI</option>
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="codigo">Código Elemento *</Label>
                                        <Input id="codigo" v-model="newProyecto.codigo_elemento" />
                                        <span v-if="formErrors.codigo_elemento" class="text-sm text-red-500">{{ formErrors.codigo_elemento }}</span>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="uso">Uso</Label>
                                        <Input id="uso" v-model="newProyecto.uso" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="area">Área Elemento</Label>
                                        <Input id="area" v-model="newProyecto.area_elemento" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="localidad">Localidad</Label>
                                        <Input id="localidad" v-model="newProyecto.localidad" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="upl">UPL</Label>
                                        <Input id="upl" v-model="newProyecto.upl" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="barrio">Barrio</Label>
                                        <Input id="barrio" v-model="newProyecto.barrio" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="eje">Eje</Label>
                                        <Input id="eje" v-model="newProyecto.eje" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="inicio">Inicio</Label>
                                        <Input id="inicio" v-model="newProyecto.inicio" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="fin">Fin</Label>
                                        <Input id="fin" v-model="newProyecto.fin" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="reserva">Reserva</Label>
                                        <Input id="reserva" v-model="newProyecto.reserva" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="estado">Estado</Label>
                                        <Input id="estado" v-model="newProyecto.estado" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="id_contrato">ID Contrato</Label>
                                        <Input id="id_contrato" v-model="newProyecto.id_contrato" />
                                    </div>
                                    <div class="space-y-2 col-span-2">
                                        <Label for="tramo">Tramo/Dirección</Label>
                                        <textarea
                                            id="tramo"
                                            v-model="newProyecto.tramo_direccion"
                                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                        ></textarea>
                                    </div>
                                    <div class="space-y-2 col-span-2">
                                        <Label>Ubicación en el Mapa</Label>
                                        <p class="text-xs text-muted-foreground mb-2">
                                            Ingrese las coordenadas manualmente o haga clic en el mapa para seleccionar la ubicación
                                        </p>
                                        <LocationPicker
                                            :model-value="{ latitude: newProyecto.latitude, longitude: newProyecto.longitude }"
                                            :address="newProyectoAddress"
                                            @update:modelValue="(val) => { newProyecto.latitude = val.latitude; newProyecto.longitude = val.longitude; }"
                                        />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="latitude">Latitud</Label>
                                        <Input
                                            id="latitude"
                                            v-model.number="newProyecto.latitude"
                                            type="number"
                                            step="any"
                                            placeholder="Ej: 4.60971"
                                        />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="longitude">Longitud</Label>
                                        <Input
                                            id="longitude"
                                            v-model.number="newProyecto.longitude"
                                            type="number"
                                            step="any"
                                            placeholder="Ej: -74.07765"
                                        />
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <Button variant="outline" @click="isCreateModalOpen = false">Cancelar</Button>
                                    <Button @click="createProyecto">Crear</Button>
                                </div>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>
                </CardHeader>
                <CardContent>
                    <!-- Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <Input v-model="searchQuery" placeholder="Buscar..." @input="debouncedFetch" />
                        <select
                            v-model="filterLocalidad"
                            @change="fetchProyectos"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        >
                            <option value="">Todas las Localidades</option>
                            <option v-for="loc in localidades" :key="loc" :value="loc">{{ loc }}</option>
                        </select>
                        <select
                            v-model="filterEstado"
                            @change="fetchProyectos"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        >
                            <option value="">Todos los Estados</option>
                            <option v-for="est in estados" :key="est" :value="est">{{ est }}</option>
                        </select>
                        <select
                            v-model="filterContrato"
                            @change="fetchProyectos"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        >
                            <option value="">Todos los Contratos</option>
                            <option v-for="contrato in contratos" :key="contrato" :value="contrato">{{ contrato }}</option>
                        </select>
                    </div>

                    <!-- Table -->
                    <div class="rounded-md border overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-muted/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Tipo</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Código</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Localidad</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">UPL</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Barrio</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Tramo/Dirección</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Eje</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Inicio</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Fin</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Reserva</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Estado</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">ID Contrato</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Latitud</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Longitud</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="loading">
                                    <td colspan="15" class="px-4 py-8 text-center text-sm text-muted-foreground">
                                        Cargando...
                                    </td>
                                </tr>
                                <tr v-else-if="proyectos.length === 0">
                                    <td colspan="15" class="px-4 py-8 text-center text-sm text-muted-foreground">
                                        No hay proyectos para mostrar
                                    </td>
                                </tr>
                                <tr v-for="proyecto in proyectos" :key="proyecto.id" class="border-t hover:bg-muted/50">
                                    <td class="px-4 py-3 text-sm">{{ proyecto.tipo_elemento_civ_rupi || '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ proyecto.codigo_elemento }}</td>
                                    <td class="px-4 py-3 text-sm">{{ proyecto.localidad || '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ proyecto.upl || '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ proyecto.barrio || '-' }}</td>
                                    <td class="px-4 py-3 text-sm max-w-xs truncate" :title="proyecto.tramo_direccion || ''">{{ proyecto.tramo_direccion || '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ proyecto.eje || '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ proyecto.inicio || '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ proyecto.fin || '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ proyecto.reserva || '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ proyecto.estado || '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ proyecto.id_contrato || '-' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span v-if="proyecto.latitude" class="text-xs font-mono">{{ proyecto.latitude }}</span>
                                        <span v-else class="text-muted-foreground">-</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span v-if="proyecto.longitude" class="text-xs font-mono">{{ proyecto.longitude }}</span>
                                        <span v-else class="text-muted-foreground">-</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex gap-2">
                                            <a :href="`/banco-proyectos/${proyecto.id}`">
                                                <Button variant="default" size="sm">Ver detalle</Button>
                                            </a>
                                            <Button v-if="props.canManageBancoProyectos" variant="outline" size="sm" @click="openEditModal(proyecto)">Editar</Button>
                                            <Button v-if="props.canManageBancoProyectos" variant="destructive" size="sm" @click="deleteProyecto(proyecto.id)">Eliminar</Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center mt-4">
                        <div class="text-sm text-muted-foreground">
                            Mostrando del {{ ((currentPage - 1) * perPage) + 1 }} al {{ Math.min(currentPage * perPage, total) }} de {{ total }} proyectos
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-muted-foreground">Por página:</span>
                                <select
                                    v-model="perPage"
                                    @change="currentPage = 1; fetchProyectos()"
                                    class="h-8 rounded-md border border-input bg-background px-3 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                >
                                    <option :value="15">15</option>
                                    <option :value="25">25</option>
                                    <option :value="50">50</option>
                                    <option :value="100">100</option>
                                    <option :value="500">500</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    :disabled="currentPage === 1"
                                    @click="currentPage--; fetchProyectos()"
                                >
                                    Anterior
                                </Button>

                                <!-- Page Numbers -->
                                <template v-for="(page, index) in pageNumbers" :key="index">
                                    <button
                                        v-if="page === '...'"
                                        disabled
                                        class="flex h-8 w-8 items-center justify-center text-sm text-muted-foreground"
                                    >
                                        ...
                                    </button>
                                    <button
                                        v-else
                                        @click="goToPage(page)"
                                        :class="[
                                            'flex h-8 w-8 items-center justify-center rounded-md text-sm transition-colors',
                                            currentPage === page
                                                ? 'bg-primary text-primary-foreground'
                                                : 'hover:bg-muted'
                                        ]"
                                    >
                                        {{ page }}
                                    </button>
                                </template>

                                <Button
                                    variant="outline"
                                    size="sm"
                                    :disabled="currentPage === lastPage"
                                    @click="currentPage++; fetchProyectos()"
                                >
                                    Siguiente
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Edit Modal -->
            <Dialog v-model:open="isEditModalOpen">
                <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>Editar Proyecto</DialogTitle>
                    </DialogHeader>
                    <div class="grid grid-cols-2 gap-4 py-4">
                        <div class="space-y-2">
                            <Label for="edit_tipo">Tipo Elemento CIV/RUPI</Label>
                            <select
                                id="edit_tipo"
                                v-model="editProyecto.tipo_elemento_civ_rupi"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            >
                                <option value="">Seleccione un tipo</option>
                                <option value="CIV">CIV</option>
                                <option value="RUPI">RUPI</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_codigo">Código Elemento *</Label>
                            <Input id="edit_codigo" v-model="editProyecto.codigo_elemento" />
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_uso">Uso</Label>
                            <Input id="edit_uso" v-model="editProyecto.uso" />
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_area">Área Elemento</Label>
                            <Input id="edit_area" v-model="editProyecto.area_elemento" />
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_localidad">Localidad</Label>
                            <Input id="edit_localidad" v-model="editProyecto.localidad" />
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_upl">UPL</Label>
                            <Input id="edit_upl" v-model="editProyecto.upl" />
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_barrio">Barrio</Label>
                            <Input id="edit_barrio" v-model="editProyecto.barrio" />
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_eje">Eje</Label>
                            <Input id="edit_eje" v-model="editProyecto.eje" />
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_inicio">Inicio</Label>
                            <Input id="edit_inicio" v-model="editProyecto.inicio" />
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_fin">Fin</Label>
                            <Input id="edit_fin" v-model="editProyecto.fin" />
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_reserva">Reserva</Label>
                            <Input id="edit_reserva" v-model="editProyecto.reserva" />
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_estado">Estado</Label>
                            <Input id="edit_estado" v-model="editProyecto.estado" />
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_id_contrato">ID Contrato</Label>
                            <Input id="edit_id_contrato" v-model="editProyecto.id_contrato" />
                        </div>
                        <div class="space-y-2 col-span-2">
                            <Label for="edit_tramo">Tramo/Dirección</Label>
                            <textarea
                                id="edit_tramo"
                                v-model="editProyecto.tramo_direccion"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            ></textarea>
                        </div>
                        <div class="space-y-2 col-span-2">
                            <Label>Ubicación en el Mapa</Label>
                            <p class="text-xs text-muted-foreground mb-2">
                                Ingrese las coordenadas manualmente o haga clic en el mapa para seleccionar la ubicación
                            </p>
                            <LocationPicker
                                :model-value="{ latitude: editProyecto.latitude, longitude: editProyecto.longitude }"
                                :address="editProyectoAddress"
                                @update:modelValue="(val) => { editProyecto.latitude = val.latitude; editProyecto.longitude = val.longitude; }"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_latitude">Latitud</Label>
                            <Input
                                id="edit_latitude"
                                :model-value="editProyecto.latitude ?? undefined"
                                @update:model-value="(val) => editProyecto.latitude = val ? Number(val) : null"
                                type="number"
                                step="any"
                                placeholder="Ej: 4.60971"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_longitude">Longitud</Label>
                            <Input
                                id="edit_longitude"
                                :model-value="editProyecto.longitude ?? undefined"
                                @update:model-value="(val) => editProyecto.longitude = val ? Number(val) : null"
                                type="number"
                                step="any"
                                placeholder="Ej: -74.07765"
                            />
                        </div>
                    </div>
                    <div class="flex justify-end gap-2">
                        <Button variant="outline" @click="isEditModalOpen = false">Cancelar</Button>
                        <Button @click="updateProyecto">Actualizar</Button>
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
