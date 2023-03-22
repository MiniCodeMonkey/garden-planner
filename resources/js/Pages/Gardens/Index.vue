<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {Head} from '@inertiajs/vue3';
import {Map, NavigationControl} from 'maplibre-gl';
import * as turf from '@turf/turf';
import {markRaw, onMounted, onUnmounted, ref, shallowRef} from 'vue';
import 'maplibre-gl/dist/maplibre-gl.css';
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    TransitionChild,
    TransitionRoot
} from '@headlessui/vue'
import {ChevronDownIcon} from '@heroicons/vue/20/solid'
import {XMarkIcon} from '@heroicons/vue/24/outline'

const open = ref(false)

const props = defineProps({
    gardens: Array,
    plants: Array,
});

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
    'features': props.gardens.map(garden => garden.geojson)
};

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
    geojson.features = [];

    editGarden.value = false;
    statusText.value = '';

    const name = prompt('What should we call this lovely garden?');

    axios.post('gardens', {name, geojson: feature, area: turf.area(feature, {units: "centimeters"})})
        .then(response => {
            gardensCollection.features.push(response.data.geojson);
            map.value.getSource('gardensCollection').setData(gardensCollection);
        })
        .catch(err => console.error(err));
}

onMounted(() => {
    let center = null;
    if (props.gardens && props.gardens.length > 0) {
        center = turf.center(props.gardens[0].geojson)
    }

    const initialState = {
        lng: center?.geometry.coordinates[0] || -34,
        lat: center?.geometry.coordinates[1] || 42,
        zoom: center ? 18 : 2
    };

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
            id: 'garden-lines',
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
            id: 'garden-fill',
            type: 'fill',
            source: 'gardensCollection',
            paint: {
                'fill-color': 'rgba(255, 255, 255, 0.5)'
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

        map.value.getSource('gardensCollection').setData(gardensCollection);

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
            } else {
                var selectedFeatures = map.value.queryRenderedFeatures(e.point, {
                    layers: ['garden-fill']
                });

                if (selectedFeatures.length > 0) {
                    open.value = true;
                    //map.flyTo({center: [0, 0], zoom: 9});

                    const bbox = turf.bbox(selectedFeatures[0]);
                    console.log(bbox);
                    const bounds = [[bbox[0], bbox[1]], [bbox[2], bbox[3]]];
                    map.value.fitBounds(bounds);
                }
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
            var features = map.value.queryRenderedFeatures(e.point, {
                layers: ['garden-fill']
            });

            // UI indicator for clicking/hovering a point on the map
            map.value.getCanvas().style.cursor = features.length
                ? 'context-menu'
                : 'pointer';
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

        <TransitionRoot as="template" :show="open">
            <Dialog as="div" class="relative z-10" @close="open = false">
                <div class="fixed inset-0"/>

                <div class="fixed inset-0 overflow-hidden">
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                            <TransitionChild as="template"
                                             enter="transform transition ease-in-out duration-500 sm:duration-700"
                                             enter-from="translate-x-full" enter-to="translate-x-0"
                                             leave="transform transition ease-in-out duration-500 sm:duration-700"
                                             leave-from="translate-x-0" leave-to="translate-x-full">
                                <DialogPanel class="pointer-events-auto w-screen max-w-2xl">
                                    <div class="flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl">
                                        <div class="px-4 sm:px-6">
                                            <div class="flex items-start justify-between">
                                                <DialogTitle class="text-base font-semibold leading-6 text-gray-900">
                                                    Select plants
                                                </DialogTitle>
                                                <div class="ml-3 flex h-7 items-center">
                                                    <button type="button"
                                                            class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                            @click="open = false">
                                                        <span class="sr-only">Close panel</span>
                                                        <XMarkIcon class="h-6 w-6" aria-hidden="true"/>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative mt-6 flex-1 px-4 sm:px-6">
                                            <ul role="list"
                                                class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                                                <li v-for="plant in plants" :key="plant.id"
                                                    class="col-span-1 flex flex-col divide-y divide-gray-200 rounded-lg bg-white text-center shadow transition ease-in-out hover:scale-110 hover:rotate-6 duration-300 cursor-pointer">
                                                    <div class="flex flex-1 flex-col p-2">
                                                        <img class="mx-auto flex-shrink-0 rounded-full"
                                                             :src="plant.seed.public_image_url" alt=""/>
                                                        <h3 class="mt-6 text-sm font-medium text-gray-900">{{
                                                                plant.seed.variety
                                                            }}</h3>
                                                        <dl class="mt-1 flex flex-grow flex-col justify-between">
                                                            <dt class="sr-only">Title</dt>
                                                            <dd class="text-sm text-gray-500">{{
                                                                    plant.seed.name
                                                                }}
                                                            </dd>
                                                            <dt class="sr-only">Role</dt>
                                                            <dd class="mt-3">
                                                                <span
                                                                    class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">{{
                                                                        plant.quantity
                                                                    }}</span>
                                                            </dd>
                                                        </dl>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>

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
