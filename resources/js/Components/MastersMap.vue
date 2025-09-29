<template>
    <div
        class="relative h-[600px] w-full overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800"
        :style="{ height }"
    >
        <div ref="mapContainer" class="h-full w-full"></div>

        <!-- Legend / Summary -->
        <div
            class="pointer-events-none absolute left-3 top-3 z-[500] rounded-md bg-white/90 p-2 text-sm text-gray-700 shadow dark:bg-gray-900/90 dark:text-gray-200"
        >
            <div class="font-medium">{{ $t('masters.map.title') }}</div>
            <div>{{ $t('masters.map.count') }}: {{ masters.length }}</div>
        </div>
    </div>
</template>

<script setup lang="ts">
import L from 'leaflet';
import type { Ref } from 'vue';
import { onMounted, onUnmounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

interface MasterMarker {
    id: number;
    name: string;
    slug: string;
    latitude?: number | null;
    longitude?: number | null;
    address?: string;
    rating?: number;
}

interface Props {
    masters: MasterMarker[];
    height?: string;
}

const props = defineProps<Props>();

const { t } = useI18n();

const map: Ref<L.Map | null> = ref(null);
const mapContainer = ref<HTMLDivElement | null>(null);
let markersLayer: L.LayerGroup | null = null;

const height = props.height ?? '600px';

const icon = L.icon({
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
    // Add alt text for accessibility
    className: 'map-marker-icon',
});

function initMap() {
    if (map.value || !mapContainer.value) return;

    map.value = L.map(mapContainer.value, {
        center: [49.0, 31.0],
        zoom: 6,
        zoomControl: true,
        attributionControl: true,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map.value);

    markersLayer = L.layerGroup().addTo(map.value);

    renderMarkers();
}

function renderMarkers() {
    if (!map.value || !markersLayer) return;

    markersLayer.clearLayers();

    const bounds = L.latLngBounds([]);
    let visibleCount = 0;

    props.masters.forEach((m) => {
        if (m.latitude == null || m.longitude == null) return;
        const latLng = L.latLng(m.latitude, m.longitude);
        const marker = L.marker(latLng, { icon });
        marker.bindPopup(
            `<div class="min-w-[200px]">
                <div class="font-semibold">${m.name}</div>
                ${m.address ? `<div class="text-xs text-gray-600">${m.address}</div>` : ''}
                ${m.rating ? `<div class="mt-1 text-yellow-600">★ ${m.rating.toFixed(1)}</div>` : ''}
                <div class="mt-2">
                    <a href="/masters/${m.slug}" class="text-blue-600 hover:underline">${t('masters.map.view_profile')}</a>
                </div>
            </div>`,
        );
        marker.addTo(markersLayer as L.LayerGroup);
        bounds.extend(latLng);
        visibleCount += 1;
    });

    // Fit bounds if we have at least one marker
    if (visibleCount > 0) {
        if (visibleCount === 1) {
            map.value.setView(bounds.getCenter(), 13);
        } else {
            map.value.fitBounds(bounds.pad(0.1));
        }
    }
}

onMounted(() => {
    // Ensure Leaflet CSS loaded (vite can also import via css, but keep fallback here)
    const id = 'leaflet-css';
    if (!document.getElementById(id)) {
        const link = document.createElement('link');
        link.id = id;
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(link);
    }

    initMap();
});

onUnmounted(() => {
    if (map.value) {
        map.value.remove();
        map.value = null;
    }
    markersLayer = null;
});

watch(
    () => props.masters,
    () => {
        // Re-render markers when list changes
        renderMarkers();
    },
    { deep: true },
);
</script>
