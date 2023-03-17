<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {Head} from '@inertiajs/vue3';
import {Map, NavigationControl} from 'maplibre-gl';
import * as turf from '@turf/turf';
import {markRaw, onMounted, onUnmounted, ref, shallowRef} from 'vue';
import 'maplibre-gl/dist/maplibre-gl.css';
import {Menu, MenuButton, MenuItem, MenuItems} from '@headlessui/vue'
import {ChevronDownIcon} from '@heroicons/vue/20/solid'

const GardenShapes = [
    'Rectangle',
    'Polygon',
    'Circle',
]

const DefaultStatusText = {
    'Rectangle': 'Click where the first corner of your garden starts',
    'Polygon': 'Finish by clicking on the first point again',
    'Circle': 'Not implemented yet',
}

var geojson = {
    'type': 'FeatureCollection',
    'features': []
};

var gardensCollection = {
    'type': 'FeatureCollection',
    'features': []
};

const props = defineProps({
    gardens: Array,
});

const mapContainer = shallowRef(null);
const map = shallowRef(null);
const editGarden = ref(false);
const gardenShape = ref('Rectangle');
const statusText = ref();

function startEditGarden(shape) {
    editGarden.value = true;
    gardenShape.value = shape;
    statusText.value = DefaultStatusText[shape];
    geojson.features = [];
    map.value.getSource('geojson').setData(geojson);
}

function createGarden(feature) {
    gardensCollection.features.push(feature);
    map.value.getSource('gardensCollection').setData(gardensCollection);
    geojson.features = [];

    editGarden.value = false;
    statusText.value = '';
}

onMounted(() => {
    const initialState = {lng: 11.8985, lat: 55.10555, zoom: 10};

    map.value = markRaw(new Map({
        container: mapContainer.value,
        style: 'https://maps-assets.geocod.io/styles/geocodio.json',
        center: [initialState.lng, initialState.lat],
        zoom: initialState.zoom
    }));
    map.value.addControl(new NavigationControl(), 'top-right');

    var linestring = {
        'type': 'Feature',
        'properties': {},
        'geometry': {
            'type': 'LineString',
            'coordinates': []
        }
    };

    map.value.on('load', function () {
        map.value.addSource('geojson', {
            'type': 'geojson',
            'data': geojson
        });

        map.value.addSource('gardensCollection', {
            'type': 'geojson',
            'data': gardensCollection
        });

        // Styles
        map.value.addLayer({
            id: 'measure-points',
            type: 'circle',
            source: 'geojson',
            paint: {
                'circle-radius': 5,
                'circle-color': '#000'
            },
            filter: ['in', '$type', 'Point']
        });
        map.value.addLayer({
            id: 'measure-lines',
            type: 'line',
            source: 'geojson',
            layout: {
                'line-cap': 'round',
                'line-join': 'round'
            },
            paint: {
                'line-color': '#000',
                'line-width': 2.5
            },
            filter: ['in', '$type', 'LineString']
        });

        map.value.addLayer({
            'id': 'measure-lines-labels',
            'type': 'symbol',
            'source': 'geojson',
            'layout': {
                'text-field': ['get', 'distance'],
                'text-font': ['Open Sans Regular'],
                'text-size': 8,
                'symbol-placement': 'line-center',
            },
            filter: ['in', '$type', 'LineString']
        });

        map.value.addLayer({
            id: 'garden-polygons',
            type: 'line',
            source: 'gardensCollection',
            layout: {
                'line-cap': 'round',
                'line-join': 'round'
            },
            paint: {
                'line-color': '#000',
                'line-width': 2.5
            },
            filter: ['in', '$type', 'Polygon']
        });

        map.value.addLayer({
            'id': 'garden-labels',
            'type': 'symbol',
            'source': 'gardensCollection',
            'layout': {
                'text-field': ['get', 'area'],
                'text-font': ['Open Sans Regular'],
                'text-size': 8
            }
        });

        map.value.on('click', function (e) {
            if (editGarden.value) {
                var selectedFeatures = map.value.queryRenderedFeatures(e.point, {
                    layers: ['measure-points']
                });

                if (gardenShape.value === 'Rectangle') {
                    if (selectedFeatures.length <= 0) {
                        if (geojson.features.length > 0) {
                            var line = turf.lineString([geojson.features[0].geometry.coordinates, [e.lngLat.lng, e.lngLat.lat]]);
                            var bbox = turf.bbox(line);
                            var bboxPolygon = turf.bboxPolygon(bbox);

                            createGarden(bboxPolygon);
                        } else {
                            var point = {
                                'type': 'Feature',
                                'geometry': {
                                    'type': 'Point',
                                    'coordinates': [e.lngLat.lng, e.lngLat.lat]
                                },
                                'properties': {
                                    'id': String(new Date().getTime())
                                }
                            };

                            geojson.features.push(point);
                        }
                    }
                } else if (gardenShape.value === 'Polygon') {
                    if (geojson.features.length > 1) {
                        geojson.features.pop();
                    }

                    if (selectedFeatures.length) {
                        // If the first feature was clicked
                        var id = selectedFeatures[0].properties.id;
                        if (id === geojson.features[0].properties.id) {
                            var points = geojson.features
                                .filter(feature => feature.geometry.type === 'Point')
                                .map(feature => feature.geometry.coordinates);

                            points.push(points[0]);

                            var line = turf.lineString(points);
                            var polygon = turf.lineToPolygon(line);
                            createGarden(polygon);
                        }
                    } else {
                        var point = {
                            'type': 'Feature',
                            'geometry': {
                                'type': 'Point',
                                'coordinates': [e.lngLat.lng, e.lngLat.lat]
                            },
                            'properties': {
                                'id': String(new Date().getTime())
                            }
                        };
                        geojson.features.push(point);
                    }

                    if (geojson.features.length > 1) {
                        // Create line through points
                        linestring.geometry.coordinates = geojson.features.map(
                            function (point) {
                                return point.geometry.coordinates;
                            }
                        );
                        geojson.features.push(linestring);
                    }
                } else if (gardenShape.value === 'Circle') {
                    alert('Not implemented yet');
                }

                map.value.getSource('geojson').setData(geojson);
            }
        });
    });

    map.value.on('mousemove', function (e) {
        if (editGarden.value) {
            var features = map.value.queryRenderedFeatures(e.point, {
                layers: ['measure-points']
            });

            // UI indicator for clicking/hovering a point on the map
            map.value.getCanvas().style.cursor = features.length
                ? 'pointer'
                : 'crosshair';

            if (gardenShape.value === 'Rectangle') {
                if (geojson.features.length > 0) {
                    const line = turf.lineString([geojson.features[0].geometry.coordinates, [e.lngLat.lng, e.lngLat.lat]]);
                    var bbox = turf.bbox(line);
                    const width = Math.round(turf.distance([bbox[0], bbox[1]], [bbox[2], bbox[3]], {units: "centimeters"}));
                    const height = Math.round(turf.distance([bbox[0], bbox[1]], [bbox[0], bbox[3]], {units: "centimeters"}));
                    statusText.value = `${width}cm x ${height}cm`
                }
            }
        } else {
            map.value.getCanvas().style.cursor = '';
        }
    });
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

        <div class="flex mb-8">
            <div>{{ statusText }}</div>
            <div class="ml-auto">
                <div v-if="!editGarden" class="inline-flex rounded-md shadow-sm">
                    <button type="button"
                            class="relative inline-flex items-center rounded-l-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                        Add garden
                    </button>
                    <Menu as="div" class="relative -ml-px block">
                        <MenuButton
                            class="relative inline-flex items-center rounded-r-md bg-white px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                            <span class="sr-only">Open options</span>
                            <ChevronDownIcon class="h-5 w-5" aria-hidden="true"/>
                        </MenuButton>
                        <transition enter-active-class="transition ease-out duration-100"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95">
                            <MenuItems
                                class="absolute right-0 z-10 mt-2 -mr-1 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                <div class="py-1">
                                    <MenuItem v-for="shape in GardenShapes" :key="shape" v-slot="{ active }">
                                        <button
                                            @click="startEditGarden(shape)"
                                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'w-full text-left block px-4 py-2 text-sm']">
                                            {{ shape }}
                                        </button>
                                    </MenuItem>
                                </div>
                            </MenuItems>
                        </transition>
                    </Menu>
                </div>

                <PrimaryButton v-if="editGarden" @click="editGarden = false">Cancel</PrimaryButton>
            </div>
        </div>

        <div class="w-full h-[500px] relative">
            <div class="w-full h-full" ref="mapContainer"></div>
        </div>
    </AuthenticatedLayout>
</template>
