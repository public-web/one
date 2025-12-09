# Uso de LocationPicker con Heatmap (Leaflet.heat)

## Instalación Completada

Se ha instalado e integrado `leaflet.heat` en el componente `LocationPicker.vue`.

## Nuevas Props del Componente

### `heatmapData` (opcional)
Array de puntos para mostrar en el mapa de calor.

```typescript
interface HeatPoint {
    latitude: number;
    longitude: number;
    intensity?: number; // Opcional: valor de intensidad (0-1)
}
```

### `showHeatmap` (opcional, default: false)
Boolean para activar/desactivar la visualización del mapa de calor.

## Ejemplos de Uso

### 1. Uso básico (sin heatmap) - Ya existente
```vue
<LocationPicker
    v-model="location"
    :address="form.direccion"
/>
```

### 2. Con mapa de calor
```vue
<script setup lang="ts">
import { ref } from 'vue';
import LocationPicker from '@/components/LocationPicker.vue';

// Datos de ejemplo para el heatmap
const heatmapPoints = ref([
    { latitude: 4.6097, longitude: -74.0817, intensity: 0.5 },
    { latitude: 4.6100, longitude: -74.0820, intensity: 0.8 },
    { latitude: 4.6120, longitude: -74.0830, intensity: 1.0 },
]);

const showHeat = ref(true);
const location = ref({ latitude: null, longitude: null });
</script>

<template>
    <LocationPicker
        v-model="location"
        :heatmap-data="heatmapPoints"
        :show-heatmap="showHeat"
    />
</template>
```

### 3. Ejemplo con Banco de Proyectos
```vue
<script setup lang="ts">
import { computed } from 'vue';

// Asumiendo que tienes un array de proyectos
const proyectos = ref<BancoProyecto[]>([]);

// Convertir proyectos a puntos de calor
const heatmapData = computed(() => {
    return proyectos.value
        .filter(p => p.latitude && p.longitude)
        .map(p => ({
            latitude: p.latitude!,
            longitude: p.longitude!,
            intensity: 1 // Puedes calcular intensidad basada en algún criterio
        }));
});
</script>

<template>
    <LocationPicker
        v-model="selectedLocation"
        :heatmap-data="heatmapData"
        :show-heatmap="true"
    />
</template>
```

## Configuración del Heatmap

El heatmap está configurado con los siguientes parámetros (puedes modificarlos en `LocationPicker.vue:121-132`):

- **radius**: 25 - Radio del punto de calor en píxeles
- **blur**: 15 - Cantidad de desenfoque
- **maxZoom**: 17 - Zoom máximo donde se muestra el heatmap
- **max**: 1.0 - Valor máximo de intensidad
- **gradient**: Colores del mapa de calor
  - 0.0 (mínimo): azul
  - 0.5: verde lima
  - 0.7: amarillo
  - 1.0 (máximo): rojo

## Toggle del Heatmap

Puedes añadir un botón para activar/desactivar el heatmap:

```vue
<script setup lang="ts">
const showHeatmap = ref(false);
</script>

<template>
    <div>
        <Button @click="showHeatmap = !showHeatmap">
            {{ showHeatmap ? 'Ocultar' : 'Mostrar' }} Mapa de Calor
        </Button>

        <LocationPicker
            v-model="location"
            :heatmap-data="heatmapData"
            :show-heatmap="showHeatmap"
        />
    </div>
</template>
```

## Notas

- El heatmap se actualiza automáticamente cuando cambian los datos o el estado de `showHeatmap`
- Los puntos con mayor intensidad se muestran en rojo (más caliente)
- Los puntos con menor intensidad se muestran en azul (más frío)
- Si no especificas `intensity`, se usa 1.0 por defecto
- El componente mantiene toda la funcionalidad existente (selección de ubicación, geocodificación, etc.)
