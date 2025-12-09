<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref, nextTick } from 'vue';
import { LMap, LTileLayer, LMarker, LPopup, LControl } from '@vue-leaflet/vue-leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import axios from 'axios';
import L from 'leaflet';
import 'leaflet.markercluster';
import html2canvas from 'html2canvas';

// Fix for default marker icon
import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import iconUrl from 'leaflet/dist/images/marker-icon.png';
import shadowUrl from 'leaflet/dist/images/marker-shadow.png';

delete (L.Icon.Default.prototype as any)._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl,
    iconUrl,
    shadowUrl,
});

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
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Banco de Proyectos', href: '/banco-proyectos' },
    { title: 'Mapa', href: '/banco-proyectos/mapa' },
];

const proyectos = ref<BancoProyecto[]>([]);
const loading = ref(false);
const searchQuery = ref('');
const filterLocalidad = ref('');
const filterEstado = ref('');
const filterTipo = ref('');
const mapRef = ref<any>(null);
const markerClusterGroup = ref<any>(null);
const drawnItems = ref<any>(null);
const drawControl = ref<any>(null);

const zoom = ref(6);
const center = ref<[number, number]>([4.570868, -74.297333]);

// Colores por estado
const estadoColors: Record<string, string> = {
    'Activo': '#10b981',
    'En proceso': '#3b82f6',
    'Completado': '#8b5cf6',
    'Pendiente': '#eab308',
    'Cancelado': '#ef4444',
    'CVP': '#f97316',
    'En estabilidad sostenibilidad': '#06b6d4',
};

const fetchProyectos = async (): Promise<void> => {
    loading.value = true;
    try {
        const params = new URLSearchParams();
        if (searchQuery.value) params.append('search', searchQuery.value);
        if (filterLocalidad.value) params.append('localidad', filterLocalidad.value);
        if (filterEstado.value) params.append('estado', filterEstado.value);
        if (filterTipo.value) params.append('tipo', filterTipo.value);
        params.append('per_page', '1000');

        const response = await axios.get<{data: BancoProyecto[]}>(`/api/banco-proyectos?${params.toString()}`);
        proyectos.value = response.data.data;

        await nextTick();
        updateMarkers();
    } catch (error) {
        console.error('Error fetching proyectos:', error);
    } finally {
        loading.value = false;
    }
};

const proyectosConCoordenadas = computed(() => {
    return proyectos.value.filter(p => p.latitude !== null && p.longitude !== null);
});

const proyectosSinCoordenadas = computed(() => {
    return proyectos.value.filter(p => p.latitude === null || p.longitude === null);
});

const localidades = computed<string[]>(() => {
    const locs = new Set(proyectos.value.map(p => p.localidad).filter(Boolean) as string[]);
    return Array.from(locs).sort();
});

const estados = computed<string[]>(() => {
    const ests = new Set(proyectos.value.map(p => p.estado).filter(Boolean) as string[]);
    return Array.from(ests).sort();
});

const getMarkerColor = (estado: string | null): string => {
    if (!estado) return '#6b7280';
    return estadoColors[estado] || '#6b7280';
};

const createColoredIcon = (color: string) => {
    return L.divIcon({
        className: 'custom-marker',
        html: `<div style="
            background-color: ${color};
            width: 25px;
            height: 25px;
            border-radius: 50% 50% 50% 0;
            border: 3px solid #fff;
            transform: rotate(-45deg);
            box-shadow: 0 3px 8px rgba(0,0,0,0.4);
        "></div>`,
        iconSize: [25, 25],
        iconAnchor: [12, 24],
        popupAnchor: [0, -24],
    });
};

const updateMarkers = () => {
    if (!mapRef.value?.leafletObject) return;

    const map = mapRef.value.leafletObject;

    // Limpiar cluster group anterior
    if (markerClusterGroup.value) {
        map.removeLayer(markerClusterGroup.value);
    }

    // Crear nuevo cluster group
    markerClusterGroup.value = L.markerClusterGroup({
        maxClusterRadius: 80,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        zoomToBoundsOnClick: true,
        iconCreateFunction: (cluster: any) => {
            const count = cluster.getChildCount();
            let size = 'small';
            if (count > 10) size = 'medium';
            if (count > 50) size = 'large';

            return L.divIcon({
                html: `<div class="cluster-icon cluster-${size}"><span>${count}</span></div>`,
                className: 'marker-cluster',
                iconSize: L.point(40, 40),
            });
        },
    });

    // Agregar marcadores
    proyectosConCoordenadas.value.forEach(proyecto => {
        const marker = L.marker(
            [proyecto.latitude!, proyecto.longitude!],
            { icon: createColoredIcon(getMarkerColor(proyecto.estado)) }
        );

        const popupContent = `
            <div class="popup-content">
                <h3 style="font-weight: bold; font-size: 1.125rem; margin-bottom: 0.5rem;">${proyecto.codigo_elemento}</h3>
                <div style="font-size: 0.875rem; line-height: 1.5;">
                    ${proyecto.tipo_elemento_civ_rupi ? `<p><strong>Tipo:</strong> ${proyecto.tipo_elemento_civ_rupi}</p>` : ''}
                    ${proyecto.localidad ? `<p><strong>Localidad:</strong> ${proyecto.localidad}</p>` : ''}
                    ${proyecto.barrio ? `<p><strong>Barrio:</strong> ${proyecto.barrio}</p>` : ''}
                    ${proyecto.tramo_direccion ? `<p><strong>Dirección:</strong> ${proyecto.tramo_direccion}</p>` : ''}
                    ${proyecto.estado ? `<p><strong>Estado:</strong> <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; background-color: ${getMarkerColor(proyecto.estado)}; color: white; font-size: 0.75rem;">${proyecto.estado}</span></p>` : ''}
                </div>
                <a href="/banco-proyectos/${proyecto.id}" style="display: inline-block; margin-top: 0.5rem; color: #3b82f6; text-decoration: none; font-size: 0.875rem;">Ver detalles →</a>
            </div>
        `;

        marker.bindPopup(popupContent, { maxWidth: 300 });
        markerClusterGroup.value.addLayer(marker);
    });

    map.addLayer(markerClusterGroup.value);
};

const centerMap = () => {
    if (proyectosConCoordenadas.value.length === 0 || !mapRef.value?.leafletObject) return;

    const map = mapRef.value.leafletObject;
    const lats = proyectosConCoordenadas.value.map(p => p.latitude!);
    const lngs = proyectosConCoordenadas.value.map(p => p.longitude!);

    const bounds = L.latLngBounds(
        [Math.min(...lats), Math.min(...lngs)],
        [Math.max(...lats), Math.max(...lngs)]
    );

    map.fitBounds(bounds, { padding: [50, 50] });
};

const clearFilters = () => {
    searchQuery.value = '';
    filterLocalidad.value = '';
    filterEstado.value = '';
    filterTipo.value = '';
    fetchProyectos();
};

const enableDrawing = () => {
    if (!mapRef.value?.leafletObject) return;

    const map = mapRef.value.leafletObject;

    if (!drawnItems.value) {
        drawnItems.value = new L.FeatureGroup();
        map.addLayer(drawnItems.value);
    }

    if (!drawControl.value) {
        drawControl.value = new L.Control.Draw({
            draw: {
                polygon: true,
                rectangle: true,
                circle: false,
                marker: false,
                polyline: false,
                circlemarker: false,
            },
            edit: {
                featureGroup: drawnItems.value,
                remove: true,
            },
        });
        map.addControl(drawControl.value);

        map.on(L.Draw.Event.CREATED, (event: any) => {
            const layer = event.layer;
            drawnItems.value.clearLayers();
            drawnItems.value.addLayer(layer);
            filterByDrawnArea(layer);
        });

        map.on(L.Draw.Event.DELETED, () => {
            fetchProyectos();
        });
    }
};

const filterByDrawnArea = (layer: any) => {
    const bounds = layer.getBounds();
    const filtered = proyectosConCoordenadas.value.filter(p => {
        const latlng = L.latLng(p.latitude!, p.longitude!);
        return bounds.contains(latlng);
    });

    // Actualizar marcadores solo con proyectos filtrados
    if (markerClusterGroup.value && mapRef.value?.leafletObject) {
        const map = mapRef.value.leafletObject;
        map.removeLayer(markerClusterGroup.value);

        markerClusterGroup.value = L.markerClusterGroup({
            maxClusterRadius: 80,
            iconCreateFunction: (cluster: any) => {
                return L.divIcon({
                    html: `<div class="cluster-icon"><span>${cluster.getChildCount()}</span></div>`,
                    className: 'marker-cluster',
                    iconSize: L.point(40, 40),
                });
            },
        });

        filtered.forEach(proyecto => {
            const marker = L.marker(
                [proyecto.latitude!, proyecto.longitude!],
                { icon: createColoredIcon(getMarkerColor(proyecto.estado)) }
            );

            const popupContent = `
                <div class="popup-content">
                    <h3 style="font-weight: bold; font-size: 1.125rem;">${proyecto.codigo_elemento}</h3>
                    <p><strong>Estado:</strong> ${proyecto.estado || 'N/A'}</p>
                    <a href="/banco-proyectos/${proyecto.id}" style="display: inline-block; margin-top: 0.5rem; color: #3b82f6; text-decoration: none; font-size: 0.875rem;">Ver detalles →</a>
                </div>
            `;

            marker.bindPopup(popupContent);
            markerClusterGroup.value.addLayer(marker);
        });

        map.addLayer(markerClusterGroup.value);
    }
};

const exportMapAsImage = async () => {
    const mapElement = document.querySelector('.map-container') as HTMLElement;
    if (!mapElement) return;

    try {
        const canvas = await html2canvas(mapElement, {
            useCORS: true,
            allowTaint: false,
            logging: false,
        });

        const link = document.createElement('a');
        link.download = `mapa-proyectos-${new Date().toISOString().split('T')[0]}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    } catch (error) {
        console.error('Error exporting map:', error);
        alert('Error al exportar el mapa. Por favor, intenta de nuevo.');
    }
};

onMounted(async () => {
    await fetchProyectos();
    await nextTick();

    setTimeout(() => {
        enableDrawing();
        centerMap();
    }, 1000);
});
</script>

<template>
    <Head title="Mapa de Proyectos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Mapa Interactivo de Proyectos</CardTitle>
                </CardHeader>
                <CardContent>
                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ proyectosConCoordenadas.length }}</div>
                            <div class="text-sm text-muted-foreground">Con coordenadas</div>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">{{ proyectosSinCoordenadas.length }}</div>
                            <div class="text-sm text-muted-foreground">Sin coordenadas</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ proyectos.length }}</div>
                            <div class="text-sm text-muted-foreground">Total</div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <Input v-model="searchQuery" placeholder="Buscar..." @input="fetchProyectos" />

                        <select v-model="filterTipo" @change="fetchProyectos" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                            <option value="">Todos los Tipos</option>
                            <option value="CIV">CIV</option>
                            <option value="RUPI">RUPI</option>
                        </select>

                        <select v-model="filterLocalidad" @change="fetchProyectos" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                            <option value="">Todas las Localidades</option>
                            <option v-for="loc in localidades" :key="loc" :value="loc">{{ loc }}</option>
                        </select>

                        <select v-model="filterEstado" @change="fetchProyectos" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                            <option value="">Todos los Estados</option>
                            <option v-for="est in estados" :key="est" :value="est">{{ est }}</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2 mb-4 flex-wrap">
                        <Button @click="centerMap" variant="outline" size="sm">Centrar Mapa</Button>
                        <Button @click="clearFilters" variant="outline" size="sm">Limpiar Filtros</Button>
                        <Button @click="exportMapAsImage" variant="outline" size="sm">Exportar como Imagen</Button>
                    </div>

                    <!-- Legend -->
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="text-sm font-semibold mb-2">Leyenda por Estado:</div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            <div v-for="(color, estado) in estadoColors" :key="estado" class="flex items-center gap-2 text-sm">
                                <div class="w-4 h-4 rounded-full border-2 border-white shadow" :style="{ backgroundColor: color }"></div>
                                <span>{{ estado }}</span>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-muted-foreground">
                            💡 Usa las herramientas de dibujo en el mapa para filtrar proyectos por área geográfica
                        </div>
                    </div>

                    <!-- Map -->
                    <div class="map-container rounded-lg overflow-hidden border" style="height: 600px;">
                        <LMap
                            ref="mapRef"
                            v-model:zoom="zoom"
                            v-model:center="center"
                            :use-global-leaflet="false"
                        >
                            <LTileLayer
                                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                                attribution='&copy; OpenStreetMap contributors'
                            />
                        </LMap>
                    </div>

                    <div v-if="loading" class="mt-4 text-center text-sm text-muted-foreground">
                        Cargando proyectos...
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<style>
.custom-marker {
    background: transparent !important;
    border: none !important;
}

.marker-cluster {
    background: transparent !important;
    border: none !important;
}

.cluster-icon {
    background-color: #3b82f6;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    border: 3px solid #fff;
    box-shadow: 0 3px 8px rgba(0,0,0,0.3);
    width: 100%;
    height: 100%;
}

.cluster-icon.cluster-medium {
    background-color: #f97316;
}

.cluster-icon.cluster-large {
    background-color: #ef4444;
}

.leaflet-draw-toolbar {
    margin-top: 12px !important;
}

.popup-content p {
    margin: 0.25rem 0;
}
</style>
