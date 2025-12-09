<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { LMap, LTileLayer, LMarker, LPopup } from '@vue-leaflet/vue-leaflet';
import 'leaflet/dist/leaflet.css';
import { Icon } from 'leaflet';

// Fix for default marker icon
import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import iconUrl from 'leaflet/dist/images/marker-icon.png';
import shadowUrl from 'leaflet/dist/images/marker-shadow.png';

delete (Icon.Default.prototype as any)._getIconUrl;
Icon.Default.mergeOptions({
    iconRetinaUrl,
    iconUrl,
    shadowUrl,
});

interface DetalleDocumento {
    id: number;
    detalle_banco_proyecto_id: number;
    archivo_path: string;
    archivo_nombre: string;
    archivo_tipo: string | null;
    archivo_tamanio: number | null;
    archivo_url: string;
    created_at: string;
    updated_at: string;
}

interface DetalleBancoProyecto {
    id: number;
    codigo: string;
    numero_radicado_entrada: string | null;
    fecha_entrada: string | null;
    peticionario: string | null;
    asunto: string | null;
    numero_radicado_salida: string | null;
    fecha_salida: string | null;
    observacion: string | null;
    documentos: DetalleDocumento[];
    created_at: string;
    updated_at: string;
}

interface PreviabilizacionSocial {
    id: number;
    codigo: string;
    fecha: string | null;
    priorizado_por: string | null;
    juntas_accion_comunal: string | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    proyecto: {
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
        detalles: DetalleBancoProyecto[];
        previabilizaciones: PreviabilizacionSocial[];
    };
    canManageDetalles: boolean;
    canManagePreviabilizaciones: boolean;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Banco de Proyectos', href: '/banco-proyectos' },
    { title: props.proyecto.codigo_elemento, href: `/banco-proyectos/${props.proyecto.id}` },
];

const zoom = ref(15);
const mapLocked = ref(false);
const mapKey = ref(0);
const mapRef = ref<any>(null);

// Load map lock state from localStorage
const loadMapLockState = () => {
    const savedState = localStorage.getItem(`mapLocked_${props.proyecto.id}`);
    if (savedState !== null) {
        mapLocked.value = savedState === 'true';
    }
};

// Initialize map lock state when component mounts
loadMapLockState();

const center = computed<[number, number]>(() => {
    if (props.proyecto.latitude && props.proyecto.longitude) {
        return [props.proyecto.latitude, props.proyecto.longitude];
    }
    return [4.60971, -74.07765]; // Bogotá default
});

const hasCoordinates = computed(() => {
    return props.proyecto.latitude !== null && props.proyecto.longitude !== null;
});

const toggleMapLock = () => {
    mapLocked.value = !mapLocked.value;
    // Save state to localStorage
    localStorage.setItem(`mapLocked_${props.proyecto.id}`, mapLocked.value.toString());
    mapKey.value++; // Force map re-render with new options
};

const focusOnLocation = () => {
    if (hasCoordinates.value) {
        // Reset zoom and center to project coordinates
        zoom.value = 18;
        mapKey.value++; // Force map re-render to apply new center and zoom
    }
};

const estadoColor = computed(() => {
    const colors: Record<string, string> = {
        'Activo': 'bg-green-100 text-green-800',
        'En proceso': 'bg-blue-100 text-blue-800',
        'Completado': 'bg-purple-100 text-purple-800',
        'Pendiente': 'bg-yellow-100 text-yellow-800',
        'Cancelado': 'bg-red-100 text-red-800',
        'CVP': 'bg-orange-100 text-orange-800',
        'En estabilidad sostenibilidad': 'bg-cyan-100 text-cyan-800',
    };
    return props.proyecto.estado ? colors[props.proyecto.estado] || 'bg-gray-100 text-gray-800' : 'bg-gray-100 text-gray-800';
});

const goBack = () => {
    router.visit('/banco-proyectos');
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatDateShort = (dateString: string | null) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
    });
};

// Form state for detalles
const showDetalleForm = ref(false);
const editingDetalle = ref<DetalleBancoProyecto | null>(null);
const detalleForm = ref({
    numero_radicado_entrada: '',
    fecha_entrada: '',
    peticionario: '',
    asunto: '',
    numero_radicado_salida: '',
    fecha_salida: '',
    observacion: '',
    documentos: [] as File[],
});

const resetDetalleForm = () => {
    detalleForm.value = {
        numero_radicado_entrada: '',
        fecha_entrada: '',
        peticionario: '',
        asunto: '',
        numero_radicado_salida: '',
        fecha_salida: '',
        observacion: '',
        documentos: [],
    };
    editingDetalle.value = null;
};

const openCreateForm = () => {
    resetDetalleForm();
    showDetalleForm.value = true;
};

const openEditForm = (detalle: DetalleBancoProyecto) => {
    editingDetalle.value = detalle;
    detalleForm.value = {
        numero_radicado_entrada: detalle.numero_radicado_entrada || '',
        fecha_entrada: detalle.fecha_entrada || '',
        peticionario: detalle.peticionario || '',
        asunto: detalle.asunto || '',
        numero_radicado_salida: detalle.numero_radicado_salida || '',
        fecha_salida: detalle.fecha_salida || '',
        observacion: detalle.observacion || '',
        documentos: [],
    };
    showDetalleForm.value = true;
};

const cancelForm = () => {
    showDetalleForm.value = false;
    resetDetalleForm();
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        detalleForm.value.documentos = Array.from(target.files);
    }
};

const deleteDocumento = (detalleId: number, documentoId: number) => {
    if (confirm('¿Está seguro de eliminar este documento?')) {
        router.delete(`/banco-proyectos/${props.proyecto.id}/detalles/${detalleId}/documentos/${documentoId}`);
    }
};

const submitDetalle = () => {
    const url = editingDetalle.value
        ? `/banco-proyectos/${props.proyecto.id}/detalles/${editingDetalle.value.id}`
        : `/banco-proyectos/${props.proyecto.id}/detalles`;

    // Prepare form data
    const formData = { ...detalleForm.value };

    // Add _method for PUT requests when editing (Laravel method spoofing)
    if (editingDetalle.value) {
        formData._method = 'PUT';
    }

    // Always use POST for file uploads (Inertia requirement)
    router.post(url, formData, {
        onSuccess: () => {
            showDetalleForm.value = false;
            resetDetalleForm();
        },
        forceFormData: true,
    });
};

const deleteDetalle = (detalleId: number) => {
    if (confirm('¿Está seguro de eliminar este detalle?')) {
        router.delete(`/banco-proyectos/${props.proyecto.id}/detalles/${detalleId}/delete`);
    }
};

// Form state for previabilizaciones
const showPreviabilizacionForm = ref(false);
const editingPreviabilizacion = ref<PreviabilizacionSocial | null>(null);
const previabilizacionForm = ref({
    fecha: '',
    priorizado_por: '',
    juntas_accion_comunal: '',
});

const resetPreviabilizacionForm = () => {
    previabilizacionForm.value = {
        fecha: '',
        priorizado_por: '',
        juntas_accion_comunal: '',
    };
    editingPreviabilizacion.value = null;
};

const openCreatePreviabilizacionForm = () => {
    resetPreviabilizacionForm();
    showPreviabilizacionForm.value = true;
};

const openEditPreviabilizacionForm = (previabilizacion: PreviabilizacionSocial) => {
    editingPreviabilizacion.value = previabilizacion;
    previabilizacionForm.value = {
        fecha: previabilizacion.fecha || '',
        priorizado_por: previabilizacion.priorizado_por || '',
        juntas_accion_comunal: previabilizacion.juntas_accion_comunal || '',
    };
    showPreviabilizacionForm.value = true;
};

const cancelPreviabilizacionForm = () => {
    showPreviabilizacionForm.value = false;
    resetPreviabilizacionForm();
};

const submitPreviabilizacion = () => {
    const url = editingPreviabilizacion.value
        ? `/banco-proyectos/${props.proyecto.id}/previabilizaciones/${editingPreviabilizacion.value.id}`
        : `/banco-proyectos/${props.proyecto.id}/previabilizaciones`;

    const formData: any = { ...previabilizacionForm.value };

    if (editingPreviabilizacion.value) {
        formData._method = 'PUT';
    }

    router.post(url, formData, {
        onSuccess: () => {
            showPreviabilizacionForm.value = false;
            resetPreviabilizacionForm();
        },
    });
};

const deletePreviabilizacion = (previabilizacionId: number) => {
    if (confirm('¿Está seguro de eliminar esta previabilización?')) {
        router.delete(`/banco-proyectos/${props.proyecto.id}/previabilizaciones/${previabilizacionId}/delete`);
    }
};
</script>

<template>
    <Head :title="`Proyecto ${proyecto.codigo_elemento}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <!-- Header Card -->
            <Card>
                <CardHeader>
                    <div class="flex items-start justify-between">
                        <div>
                            <CardTitle class="text-3xl">{{ proyecto.codigo_elemento }}</CardTitle>
                            <p class="text-sm text-muted-foreground mt-2">
                                {{ proyecto.tipo_elemento_civ_rupi || 'Sin tipo especificado' }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <Button @click="goBack" variant="outline" size="sm">
                                ← Volver
                            </Button>
                            <a :href="`/banco-proyectos/mapa`">
                                <Button variant="outline" size="sm">
                                    Ver en Mapa General
                                </Button>
                            </a>
                        </div>
                    </div>
                </CardHeader>
            </Card>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Mapa Principal -->
                <div class="lg:col-span-2">
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle>Ubicación en el Mapa</CardTitle>
                                <div v-if="hasCoordinates" class="flex gap-2">
                                    <Button
                                        @click="focusOnLocation"
                                        variant="outline"
                                        size="sm"
                                        class="bg-blue-50 hover:bg-blue-100"
                                        title="Focalizar en el mapa"
                                    >
                                        <span class="flex items-center gap-2">
                                            🎯 Focalizar
                                        </span>
                                    </Button>
                                    <Button
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
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div v-if="hasCoordinates" class="space-y-4">
                                <div class="rounded-lg overflow-hidden border" style="height: 75vh; min-height: 650px;">
                                    <LMap
                                        :key="mapKey"
                                        v-model:zoom="zoom"
                                        :center="center"
                                        :use-global-leaflet="false"
                                        :options="{
                                            dragging: !mapLocked,
                                            scrollWheelZoom: !mapLocked,
                                            doubleClickZoom: !mapLocked,
                                            touchZoom: !mapLocked,
                                            boxZoom: !mapLocked,
                                            keyboard: !mapLocked,
                                            zoomControl: !mapLocked,
                                        }"
                                    >
                                        <LTileLayer
                                            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                                            attribution='&copy; OpenStreetMap contributors'
                                        />
                                        <LMarker :lat-lng="center">
                                            <LPopup>
                                                <div class="font-semibold">{{ proyecto.codigo_elemento }}</div>
                                                <div class="text-sm">{{ proyecto.localidad }}</div>
                                            </LPopup>
                                        </LMarker>
                                    </LMap>
                                </div>
                                <div class="flex items-center justify-between text-sm text-muted-foreground bg-blue-50 p-3 rounded-lg">
                                    <div>
                                        <p class="font-semibold text-blue-900">📍 Coordenadas GPS</p>
                                        <p class="mt-1"><span class="text-blue-700 font-medium">Latitud:</span> {{ proyecto.latitude }} | <span class="text-blue-700 font-medium">Longitud:</span> {{ proyecto.longitude }}</p>
                                    </div>
                                    <a
                                        :href="`https://www.google.com/maps?q=${proyecto.latitude},${proyecto.longitude}`"
                                        target="_blank"
                                        class="text-blue-600 hover:underline font-medium"
                                    >
                                        Abrir en Google Maps →
                                    </a>
                                </div>
                            </div>
                            <div v-else class="text-center text-muted-foreground flex flex-col items-center justify-center" style="height: 75vh; min-height: 650px;">
                                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                <p class="mt-4 text-lg font-medium">No hay coordenadas GPS disponibles</p>
                                <p class="text-sm mt-2">Edita el proyecto para agregar la ubicación</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Información Complementaria -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="sticky top-6 space-y-6">
                    <!-- Información Básica -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Información Básica</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-muted-foreground">Código Elemento</p>
                                    <p class="text-base">{{ proyecto.codigo_elemento }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-muted-foreground">Tipo</p>
                                    <p class="text-base">{{ proyecto.tipo_elemento_civ_rupi || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-muted-foreground">Uso</p>
                                    <p class="text-base">{{ proyecto.uso || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-muted-foreground">Área Elemento</p>
                                    <p class="text-base">{{ proyecto.area_elemento || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-muted-foreground">Estado</p>
                                    <span v-if="proyecto.estado" :class="estadoColor" class="inline-block px-3 py-1 rounded-full text-sm font-medium">
                                        {{ proyecto.estado }}
                                    </span>
                                    <p v-else class="text-base">-</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-muted-foreground">ID Contrato</p>
                                    <p class="text-base">{{ proyecto.id_contrato || '-' }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Ubicación -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Ubicación</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-muted-foreground">Localidad</p>
                                    <p class="text-base">{{ proyecto.localidad || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-muted-foreground">UPL</p>
                                    <p class="text-base">{{ proyecto.upl || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-muted-foreground">Barrio</p>
                                    <p class="text-base">{{ proyecto.barrio || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-muted-foreground">Eje</p>
                                    <p class="text-base">{{ proyecto.eje || '-' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-sm font-semibold text-muted-foreground">Tramo/Dirección</p>
                                    <p class="text-base">{{ proyecto.tramo_direccion || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-muted-foreground">Inicio</p>
                                    <p class="text-base">{{ proyecto.inicio || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-muted-foreground">Fin</p>
                                    <p class="text-base">{{ proyecto.fin || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-muted-foreground">Reserva</p>
                                    <p class="text-base">{{ proyecto.reserva || '-' }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Metadatos -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Información del Registro</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="font-semibold text-muted-foreground">Creado</p>
                                    <p>{{ formatDate(proyecto.created_at) }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold text-muted-foreground">Última actualización</p>
                                    <p>{{ formatDate(proyecto.updated_at) }}</p>
                                </div>
                                <div v-if="proyecto.deleted_at">
                                    <p class="font-semibold text-muted-foreground">Eliminado</p>
                                    <p>{{ formatDate(proyecto.deleted_at) }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                    </div>
                </div>
            </div>

            <!-- Sección de Detalles del Proyecto -->
            <Card v-if="canManageDetalles">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Detalles del Proyecto</CardTitle>
                        <Button @click="openCreateForm" size="sm">
                            + Agregar Detalle
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <!-- Formulario de Creación/Edición -->
                    <div v-if="showDetalleForm" class="mb-6 p-6 bg-gray-50 rounded-lg border">
                        <h3 class="text-lg font-semibold mb-4">
                            {{ editingDetalle ? 'Editar Detalle' : 'Nuevo Detalle' }}
                        </h3>
                        <form @submit.prevent="submitDetalle" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="numero_radicado_entrada">Número Radicado Entrada</Label>
                                <Input
                                    id="numero_radicado_entrada"
                                    v-model="detalleForm.numero_radicado_entrada"
                                    type="text"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="fecha_entrada">Fecha Entrada</Label>
                                <Input
                                    id="fecha_entrada"
                                    v-model="detalleForm.fecha_entrada"
                                    type="date"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="peticionario">Peticionario</Label>
                                <Input
                                    id="peticionario"
                                    v-model="detalleForm.peticionario"
                                    type="text"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="numero_radicado_salida">Número Radicado Salida</Label>
                                <Input
                                    id="numero_radicado_salida"
                                    v-model="detalleForm.numero_radicado_salida"
                                    type="text"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="fecha_salida">Fecha Salida</Label>
                                <Input
                                    id="fecha_salida"
                                    v-model="detalleForm.fecha_salida"
                                    type="date"
                                />
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <Label for="asunto">Asunto</Label>
                                <Textarea
                                    id="asunto"
                                    v-model="detalleForm.asunto"
                                    rows="3"
                                />
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <Label for="observacion">Observación</Label>
                                <Textarea
                                    id="observacion"
                                    v-model="detalleForm.observacion"
                                    rows="3"
                                />
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <Label for="documentos">Documentos Adjuntos</Label>
                                <Input
                                    id="documentos"
                                    type="file"
                                    @change="handleFileChange"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                    multiple
                                />
                                <p class="text-xs text-muted-foreground">
                                    Puedes seleccionar múltiples archivos. Formatos: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Máx. 10MB c/u)
                                </p>
                                <p v-if="detalleForm.documentos.length > 0" class="text-sm text-green-600">
                                    {{ detalleForm.documentos.length }} archivo(s) seleccionado(s)
                                </p>
                                <div v-if="editingDetalle && editingDetalle.documentos && editingDetalle.documentos.length > 0" class="mt-2">
                                    <p class="text-sm font-semibold text-gray-700 mb-2">Documentos existentes:</p>
                                    <div class="space-y-1">
                                        <div v-for="doc in editingDetalle.documentos" :key="doc.id" class="flex items-center justify-between text-sm bg-blue-50 p-2 rounded">
                                            <span class="text-blue-700">📎 {{ doc.archivo_nombre }}</span>
                                            <button
                                                type="button"
                                                @click="deleteDocumento(editingDetalle.id, doc.id)"
                                                class="text-red-600 hover:text-red-800 text-xs"
                                            >
                                                Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2 flex gap-2 justify-end">
                                <Button type="button" @click="cancelForm" variant="outline">
                                    Cancelar
                                </Button>
                                <Button type="submit">
                                    {{ editingDetalle ? 'Actualizar' : 'Guardar' }}
                                </Button>
                            </div>
                        </form>
                    </div>

                    <!-- Tabla de Detalles -->
                    <div v-if="proyecto.detalles && proyecto.detalles.length > 0" class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Radicado Entrada</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha Entrada</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Peticionario</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Asunto</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Radicado Salida</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha Salida</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Documento</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="detalle in proyecto.detalles"
                                    :key="detalle.id"
                                    class="border-b hover:bg-gray-50"
                                >
                                    <td class="px-4 py-3 text-sm">{{ detalle.numero_radicado_entrada || '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ formatDateShort(detalle.fecha_entrada) }}</td>
                                    <td class="px-4 py-3 text-sm">{{ detalle.peticionario || '-' }}</td>
                                    <td class="px-4 py-3 text-sm max-w-xs truncate" :title="detalle.asunto || ''">
                                        {{ detalle.asunto || '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ detalle.numero_radicado_salida || '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ formatDateShort(detalle.fecha_salida) }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <div v-if="detalle.documentos && detalle.documentos.length > 0" class="space-y-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-lg">
                                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <span class="text-blue-600 font-medium">{{ detalle.documentos.length }} archivo(s)</span>
                                            </div>
                                            <div class="pl-2 space-y-1">
                                                <a
                                                    v-for="doc in detalle.documentos"
                                                    :key="doc.id"
                                                    :href="doc.archivo_url"
                                                    target="_blank"
                                                    class="block text-blue-600 hover:text-blue-800 hover:underline text-xs"
                                                    :title="doc.archivo_nombre"
                                                >
                                                    📄 {{ doc.archivo_nombre.substring(0, 30) }}{{ doc.archivo_nombre.length > 30 ? '...' : '' }}
                                                </a>
                                            </div>
                                        </div>
                                        <div v-else class="flex items-center gap-2">
                                            <div class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-lg">
                                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="text-gray-400 text-sm">Sin documentos</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex gap-2">
                                            <Button
                                                @click="openEditForm(detalle)"
                                                size="sm"
                                                variant="outline"
                                            >
                                                Editar
                                            </Button>
                                            <Button
                                                @click="deleteDetalle(detalle.id)"
                                                size="sm"
                                                variant="destructive"
                                            >
                                                Eliminar
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mensaje cuando no hay detalles -->
                    <div v-else class="text-center py-12 text-muted-foreground">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-lg font-medium">No hay detalles registrados</p>
                        <p class="text-sm mt-2">Haz clic en "Agregar Detalle" para crear el primer registro</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Sección de Previabilización Social (Solo para rol previabilizacion-social) -->
            <Card v-if="canManagePreviabilizaciones">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Previabilización Social</CardTitle>
                        <Button @click="openCreatePreviabilizacionForm" size="sm">
                            + Agregar Previabilización
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <!-- Formulario de Creación/Edición -->
                    <div v-if="showPreviabilizacionForm" class="mb-6 p-6 bg-gray-50 rounded-lg border">
                        <h3 class="text-lg font-semibold mb-4">
                            {{ editingPreviabilizacion ? 'Editar Previabilización' : 'Nueva Previabilización' }}
                        </h3>
                        <form @submit.prevent="submitPreviabilizacion" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-2">
                                <Label for="fecha">Fecha</Label>
                                <Input
                                    id="fecha"
                                    v-model="previabilizacionForm.fecha"
                                    type="date"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="priorizado_por">Priorizado Por</Label>
                                <Input
                                    id="priorizado_por"
                                    v-model="previabilizacionForm.priorizado_por"
                                    type="text"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="juntas_accion_comunal">Juntas Acción Comunal</Label>
                                <Input
                                    id="juntas_accion_comunal"
                                    v-model="previabilizacionForm.juntas_accion_comunal"
                                    type="text"
                                />
                            </div>

                            <div class="md:col-span-3 flex gap-2 justify-end">
                                <Button type="button" @click="cancelPreviabilizacionForm" variant="outline">
                                    Cancelar
                                </Button>
                                <Button type="submit">
                                    {{ editingPreviabilizacion ? 'Actualizar' : 'Guardar' }}
                                </Button>
                            </div>
                        </form>
                    </div>

                    <!-- Tabla de Previabilizaciones -->
                    <div v-if="proyecto.previabilizaciones && proyecto.previabilizaciones.length > 0" class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Priorizado Por</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Juntas Acción Comunal</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="previabilizacion in proyecto.previabilizaciones"
                                    :key="previabilizacion.id"
                                    class="border-b hover:bg-gray-50"
                                >
                                    <td class="px-4 py-3 text-sm">{{ formatDateShort(previabilizacion.fecha) }}</td>
                                    <td class="px-4 py-3 text-sm">{{ previabilizacion.priorizado_por || '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ previabilizacion.juntas_accion_comunal || '-' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex gap-2">
                                            <Button
                                                @click="openEditPreviabilizacionForm(previabilizacion)"
                                                size="sm"
                                                variant="outline"
                                            >
                                                Editar
                                            </Button>
                                            <Button
                                                @click="deletePreviabilizacion(previabilizacion.id)"
                                                size="sm"
                                                variant="destructive"
                                            >
                                                Eliminar
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mensaje cuando no hay previabilizaciones -->
                    <div v-else class="text-center py-12 text-muted-foreground">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-lg font-medium">No hay previabilizaciones registradas</p>
                        <p class="text-sm mt-2">Haz clic en "Agregar Previabilización" para crear el primer registro</p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<style scoped>
.whitespace-pre-line {
    white-space: pre-line;
}
</style>
