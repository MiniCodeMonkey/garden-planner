<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {Map, Marker, NavigationControl} from 'maplibre-gl';
import {markRaw, onMounted, onUnmounted, shallowRef} from 'vue';
import 'maplibre-gl/dist/maplibre-gl.css';

const props = defineProps({
    gardens: Array,
});

const mapContainer = shallowRef(null);
const map = shallowRef(null);

onMounted(() => {
    const initialState = {lng: 11.8985, lat: 55.18555, zoom: 10};

    map.value = markRaw(new Map({
        container: mapContainer.value,
        style: 'https://maps-assets.geocod.io/styles/geocodio.json',
        center: [initialState.lng, initialState.lat],
        zoom: initialState.zoom
    }));
    map.value.addControl(new NavigationControl(), 'top-right');
    new Marker({color: "#FF0000"})
        .setLngLat([11.8985, 55.18555])
        .addTo(map.value);
});

onUnmounted(() => {
    map.value?.remove();
});
</script>

<template>
    <Head title="Garden"/>

    <AuthenticatedLayout>
        <template #header>
            Garden
        </template>

        <div class="w-full h-[500px] relative">
            <div class="w-full h-full" ref="mapContainer"></div>
        </div>
    </AuthenticatedLayout>
</template>
