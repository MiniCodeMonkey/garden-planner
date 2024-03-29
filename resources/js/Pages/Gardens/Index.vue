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
import GardenDetails from './Partials/GardenDetails.vue';
import mapLayers from './mapLayers.js'
import RectangleDrawHandler from './RectangleDrawHandler';
import PolygonDrawHandler from './PolygonDrawHandler';
import CircleDrawHandler from './CircleDrawHandler';

const props = defineProps({
    gardens: Array,
    plants: Array,
});

const mapContainer = shallowRef(null);
const map = shallowRef(null);

const selectedGarden = ref(null)
const selectedPlant = ref(null)

const editGarden = ref(false);
const gardenShape = ref('Rectangle');
const statusText = ref();

let drawHandler;
let plantFeatures = {
    'type': 'geojson',
    'data': {
        'type': 'FeatureCollection',
        'features': []
    }
};

function startEditGarden(shape) {
    if (shape === 'Rectangle') {
        drawHandler = new RectangleDrawHandler(map, statusText)
    } else if (shape === 'Polygon') {
        drawHandler = new PolygonDrawHandler(map, statusText)
    } else if (shape === 'Circle') {
        drawHandler = new CircleDrawHandler(map, statusText)
    } else {
        drawHandler = null;
    }

    if (drawHandler) {
        editGarden.value = true;
        gardenShape.value = shape;
        statusText.value = drawHandler.getDefaultStatusText();
    }
}

function selectPlant(plant) {
    selectedPlant.value = plant
    const seedDistance = plant.seed?.seed_distance_min || 40;
    updateGrid(seedDistance);
}

function updateGrid(gridSize) {
    const bbox = turf.bbox(selectedGarden.value);

    const options = {units: 'centimeters'};
    const grid = turf.squareGrid(bbox, gridSize, options);
    map.value.getSource('grid').setData(grid);
}

function deleteGarden(gardenId) {
    if (gardenId) {
        axios.delete('gardens/' + gardenId)
            .then(response => {
                map.value.getSource('gardens.geojson').setData(response.data);
                map.value.flyTo({zoom: map.value.getZoom() - 3});
                selectedGarden.value = null;
            })
            .catch(err => console.error(err));
    }
}

function getInitialState() {
    let center = null;
    if (props.gardens && props.gardens.length > 0) {
        center = turf.center(props.gardens[0].geojson)
    }

    return {
        lng: center?.geometry.coordinates[0] || -34,
        lat: center?.geometry.coordinates[1] || 42,
        zoom: center ? 18 : 2
    };
}

onMounted(() => {
    const initialState = getInitialState();

    map.value = markRaw(new Map({
        container: mapContainer.value,
        style: 'https://maps-assets.geocod.io/styles/geocodio.json',
        center: [initialState.lng, initialState.lat],
        zoom: initialState.zoom
    }));
    map.value.addControl(new NavigationControl(), 'top-right');

    map.value.on('load', function () {
        map.value.loadImage('https://cdn.shopify.com/s/files/1/0276/3902/1652/products/Salat_Valmaine_illustration_1080x.jpg?v=1605905545', function (error, image) {
            if (error) {
                throw error;
            }

            map.value.addImage('plant', image);
            map.value.addSource('plants', plantFeatures);
            map.value.addLayer({
                'id': 'plant-image',
                'type': 'symbol',
                'source': 'plants',
                'layout': {
                    'icon-image': 'plant',
                    'icon-size': 0.25
                }
            });
        });

        map.value.addSource('gardens.geojson', {
            'type': 'geojson',
            'data': '/gardens.geojson'
        });

        map.value.addSource('grid', {
            'type': 'geojson',
            'data': {
                'type': 'FeatureCollection',
                'features': []
            }
        });

        for (const layer in mapLayers) {
            map.value.addLayer(mapLayers[layer]);
        }

        map.value.on('click', function (e) {
            if (editGarden.value) {
                drawHandler.onClick(e);
            } else if (selectedPlant.value) {
                const point = [e.lngLat.lng, e.lngLat.lat];
                plantFeatures.features.push({
                    'type': 'Feature',
                    'properties': {},
                    'geometry': {
                        'type': 'Point',
                        'coordinates': point
                    }
                });
                this.map.value.getSource('plants').setData(plantFeatures);
            } else {
                var selectedFeatures = map.value.queryRenderedFeatures(e.point, {
                    layers: ['garden-fill']
                });

                if (selectedFeatures.length > 0) {
                    selectedGarden.value = selectedFeatures[0];

                    const bbox = turf.bbox(selectedFeatures[0]);
                    const bounds = [[bbox[0], bbox[1]], [bbox[2], bbox[3]]];
                    map.value.fitBounds(bounds);

                    updateGrid(40);
                }
            }
        });

        map.value.on('mousemove', function (e) {
            if (editGarden.value) {
                drawHandler.onMouseMove(e);
            } else {
                var features = map.value.queryRenderedFeatures(e.point, {
                    layers: ['garden-fill']
                });

                map.value.getCanvas().style.cursor = features.length
                    ? 'context-menu'
                    : 'pointer';
            }
        });
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

        <GardenDetails :garden="selectedGarden" :plants="plants" :selectedPlant="selectedPlant"
                       v-on:close="selectedGarden = null"
                       v-on:plantSelected="plant => selectPlant(plant)"
                       v-on:deleteGarden="() => deleteGarden(selectedGarden.properties.id)"/>

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
                                    <MenuItem v-for="shape in ['Rectangle', 'Polygon', 'Circle']" :key="shape"
                                              v-slot="{ active }">
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
