<script setup lang="ts">
import { ref, watch, onMounted, computed, nextTick } from 'vue';
import { LMap, LTileLayer, LMarker, LPopup } from '@vue-leaflet/vue-leaflet';
import 'leaflet/dist/leaflet.css';
import { Icon, type Map as LeafletMap } from 'leaflet';
import L from 'leaflet';
import 'leaflet.heat';
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';

// Fix for default marker icon in Leaflet
import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import iconUrl from 'leaflet/dist/images/marker-icon.png';
import shadowUrl from 'leaflet/dist/images/marker-shadow.png';

delete (Icon.Default.prototype as any)._getIconUrl;
Icon.Default.mergeOptions({
    iconRetinaUrl,
    iconUrl,
    shadowUrl,
});

interface HeatPoint {
    latitude: number;
    longitude: number;
    intensity?: number; // Optional intensity value (0-1)
    label?: string; // Optional label for marker popup
    id?: number | string; // Optional ID
}

interface Props {
    modelValue?: { latitude: number | null; longitude: number | null };
    address?: string;
    heatmapData?: HeatPoint[]; // Array of points for heatmap
    showHeatmap?: boolean; // Toggle heatmap visibility
    showMarkers?: boolean; // Toggle individual markers visibility
    mapCenter?: [number, number]; // External map center control
    mapZoom?: number; // External map zoom control
    mapLocked?: boolean; // Lock map interaction
}

const props = withDefaults(defineProps<Props>(), {
    showHeatmap: false,
    showMarkers: false,
    heatmapData: () => [],
    mapLocked: false,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: { latitude: number | null; longitude: number | null }): void;
}>();

const zoom = ref(props.mapZoom ?? 13);
const center = ref<[number, number]>(props.mapCenter ?? [4.60971, -74.07765]); // Default: Bogotá
const markerPosition = ref<[number, number] | null>(null);
const geocoding = ref(false);
const mapKey = ref(0);
const map = ref<any>(null);
const heatLayer = ref<any>(null);
const markerClusterGroup = ref<any>(null);

// Watch for external map center/zoom changes (combined)
watch(() => [props.mapCenter, props.mapZoom], ([newCenter, newZoom]) => {
    if (map.value?.leafletObject) {
        if (newCenter) {
            center.value = newCenter;
        }
        if (newZoom !== undefined) {
            zoom.value = newZoom;
        }

        // Apply both center and zoom together
        const targetCenter = newCenter || center.value;
        const targetZoom = newZoom ?? zoom.value;
        map.value.leafletObject.setView(targetCenter, targetZoom, { animate: true });
    }
}, { deep: true });

// Watch for map lock changes and force re-render
watch(() => props.mapLocked, () => {
    mapKey.value++; // Force re-render to apply new options
});

// Initialize on mount
onMounted(() => {
    if (props.modelValue?.latitude != null && props.modelValue?.longitude != null &&
        !isNaN(Number(props.modelValue.latitude)) && !isNaN(Number(props.modelValue.longitude))) {
        const lat = Number(props.modelValue.latitude);
        const lng = Number(props.modelValue.longitude);
        markerPosition.value = [lat, lng];
        center.value = [lat, lng];
    }
});

const handleMapClick = (event: any) => {
    const { lat, lng } = event.latlng;
    markerPosition.value = [lat, lng];
    emit('update:modelValue', { latitude: lat, longitude: lng });
};

const geocodeAddress = async () => {
    if (!props.address) return;

    geocoding.value = true;
    try {
        const response = await fetch(
            `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(props.address)},Colombia&format=json&limit=1`
        );
        const data = await response.json();

        if (data && data.length > 0) {
            const lat = parseFloat(data[0].lat);
            const lng = parseFloat(data[0].lon);
            markerPosition.value = [lat, lng];
            center.value = [lat, lng];
            emit('update:modelValue', { latitude: lat, longitude: lng });
        }
    } catch (error) {
        console.error('Error geocoding address:', error);
    } finally {
        geocoding.value = false;
    }
};

const clearLocation = () => {
    markerPosition.value = null;
    emit('update:modelValue', { latitude: null, longitude: null });
};

// Heatmap functions
const onMapReady = async () => {
    await nextTick();
    if (map.value?.leafletObject) {
        updateHeatmap();
        updateMarkers();
    }
};

const updateHeatmap = () => {
    if (!map.value?.leafletObject) return;

    // Remove existing heat layer
    if (heatLayer.value) {
        map.value.leafletObject.removeLayer(heatLayer.value);
        heatLayer.value = null;
    }

    // Add new heat layer if enabled and has data
    if (props.showHeatmap && props.heatmapData && props.heatmapData.length > 0) {
        const heatPoints = props.heatmapData.map(point => {
            const intensity = point.intensity ?? 1;
            return [point.latitude, point.longitude, intensity];
        });

        heatLayer.value = (L as any).heatLayer(heatPoints, {
            radius: 25,
            blur: 15,
            maxZoom: 17,
            max: 1.0,
            gradient: {
                0.0: 'blue',
                0.5: 'lime',
                0.7: 'yellow',
                1.0: 'red'
            }
        }).addTo(map.value.leafletObject);
    }
};

const updateMarkers = () => {
    console.log('=== updateMarkers called ===');
    console.log('showMarkers:', props.showMarkers);
    console.log('heatmapData length:', props.heatmapData?.length);
    console.log('First 3 data points:', props.heatmapData?.slice(0, 3));

    try {
        if (!map.value?.leafletObject) {
            console.log('❌ No map available');
            return;
        }

        // Remove existing marker cluster group
        if (markerClusterGroup.value) {
            console.log('🗑️ Removing existing cluster group');
            map.value.leafletObject.removeLayer(markerClusterGroup.value);
            markerClusterGroup.value = null;
        }

        // Add marker cluster if enabled and has data
        if (props.showMarkers && props.heatmapData && props.heatmapData.length > 0) {
            console.log('✅ Creating new marker cluster group');
            markerClusterGroup.value = (L as any).markerClusterGroup({
                chunkedLoading: true,
                maxClusterRadius: 80,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: true,
                zoomToBoundsOnClick: true,
                iconCreateFunction: function(cluster: any) {
                    const count = cluster.getChildCount();
                    let size = 'small';
                    if (count > 100) size = 'large';
                    else if (count > 10) size = 'medium';

                    return L.divIcon({
                        html: `<div><span>${count}</span></div>`,
                        className: `marker-cluster marker-cluster-${size}`,
                        iconSize: L.point(40, 40)
                    });
                }
            });

            // Filter valid points and create markers
            const validPoints = props.heatmapData.filter(point => {
                if (!point) return false;

                // Check for null/undefined BEFORE converting
                if (point.latitude === null || point.latitude === undefined ||
                    point.longitude === null || point.longitude === undefined) {
                    return false;
                }

                const lat = typeof point.latitude === 'number' ? point.latitude : parseFloat(String(point.latitude));
                const lng = typeof point.longitude === 'number' ? point.longitude : parseFloat(String(point.longitude));

                return !isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0;
            }).map(point => ({
                ...point,
                latitude: typeof point.latitude === 'number' ? point.latitude : parseFloat(String(point.latitude)),
                longitude: typeof point.longitude === 'number' ? point.longitude : parseFloat(String(point.longitude)),
            }));

            console.log('📍 Valid points after filtering:', validPoints.length);
            console.log('First valid point:', validPoints[0]);

            let markersCreated = 0;
            validPoints.forEach(point => {
                try {
                    const lat = point.latitude;
                    const lng = point.longitude;

                    const marker = L.marker([lat, lng]);
                    if (point.label) {
                        marker.bindPopup(`<div class="text-sm font-semibold">${point.label}</div>`);
                    }
                    markerClusterGroup.value.addLayer(marker);
                    markersCreated++;
                } catch (error) {
                    console.error('❌ Error creating marker:', error, point);
                }
            });

            console.log('✅ Markers created and added to cluster:', markersCreated);

            map.value.leafletObject.addLayer(markerClusterGroup.value);
            console.log('✅ Cluster group added to map');

            // Auto-zoom to fit all markers
            if (validPoints.length > 0 && markerClusterGroup.value.getLayers().length > 0) {
                try {
                    const bounds = markerClusterGroup.value.getBounds();
                    if (bounds && bounds.isValid && bounds.isValid()) {
                        map.value.leafletObject.fitBounds(bounds, { padding: [50, 50] });
                        console.log('✅ Map zoomed to fit bounds');
                    } else {
                        console.log('⚠️ Bounds not valid, skipping auto-zoom');
                    }
                } catch (error) {
                    console.warn('⚠️ Could not auto-zoom to bounds:', error);
                }
            } else {
                console.log('⚠️ No markers to zoom to');
            }
        } else {
            console.log('❌ Not showing markers because:');
            console.log('  - showMarkers:', props.showMarkers);
            console.log('  - has data:', !!props.heatmapData);
            console.log('  - data length:', props.heatmapData?.length);
        }
    } catch (error) {
        console.error('❌ Error in updateMarkers:', error);
    }
    console.log('=== updateMarkers end ===');
};

// Watch for heatmap data changes
watch(() => [props.showHeatmap, props.heatmapData], () => {
    updateHeatmap();
}, { deep: true });

// Watch for markers visibility changes
watch(() => props.showMarkers, () => {
    updateMarkers();
});

// Watch for heatmap data changes (affects markers too)
watch(() => props.heatmapData, () => {
    if (props.showMarkers) {
        updateMarkers();
    }
}, { deep: true });

watch(() => props.modelValue, (newVal) => {
    if (newVal?.latitude != null && newVal?.longitude != null &&
        !isNaN(Number(newVal.latitude)) && !isNaN(Number(newVal.longitude))) {
        const lat = Number(newVal.latitude);
        const lng = Number(newVal.longitude);

        // Check if position actually changed before updating
        const isDifferent = markerPosition.value === null ||
            Math.abs(markerPosition.value[0] - lat) > 0.000001 ||
            Math.abs(markerPosition.value[1] - lng) > 0.000001;

        if (isDifferent) {
            markerPosition.value = [lat, lng];
            center.value = [lat, lng];
            // Zoom in more when coordinates are manually set
            if (zoom.value < 15) {
                zoom.value = 16;
            }
            mapKey.value++; // Force map re-render to update center
        }
    } else if ((newVal?.latitude === null || newVal?.longitude === null) && markerPosition.value !== null) {
        // Clear marker only if it was previously set
        markerPosition.value = null;
        center.value = [4.60971, -74.07765];
        zoom.value = 13;
        mapKey.value++; // Force map re-render
    }
}, { deep: true, immediate: true });
</script>

<template>
    <div class="flex flex-col h-full space-y-2">
        <div class="flex gap-2">
            <button
                v-if="address"
                type="button"
                @click="geocodeAddress"
                :disabled="geocoding"
                class="flex h-9 items-center justify-center rounded-md bg-blue-600 px-3 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
            >
                {{ geocoding ? 'Geocodificando...' : 'Buscar en Mapa' }}
            </button>
            <button
                v-if="markerPosition"
                type="button"
                @click="clearLocation"
                class="flex h-9 items-center justify-center rounded-md bg-red-600 px-3 text-sm font-medium text-white hover:bg-red-700"
            >
                Limpiar Ubicación
            </button>
        </div>

        <div v-if="markerPosition && markerPosition[0] != null && markerPosition[1] != null" class="text-sm text-muted-foreground">
            <p><span class="font-semibold">Latitud:</span> {{ Number(markerPosition[0]).toFixed(6) }}</p>
            <p><span class="font-semibold">Longitud:</span> {{ Number(markerPosition[1]).toFixed(6) }}</p>
        </div>

        <div class="rounded-lg overflow-hidden border flex-1" style="min-height: 300px;">
            <LMap
                :key="mapKey"
                ref="map"
                v-model:zoom="zoom"
                v-model:center="center"
                :use-global-leaflet="false"
                @click="handleMapClick"
                @ready="onMapReady"
                style="height: 100%; width: 100%;"
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
                    attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                />

                <!-- Marcador individual para selección (solo cuando no hay heatmap) -->
                <LMarker
                    v-if="markerPosition && !showHeatmap"
                    :lat-lng="markerPosition"
                />
            </LMap>
        </div>

        <p class="text-xs text-muted-foreground">
            Haz clic en el mapa para seleccionar la ubicación exacta del proyecto
        </p>
    </div>
</template>
