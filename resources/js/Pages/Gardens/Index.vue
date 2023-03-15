<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {Head} from '@inertiajs/vue3';
import {Map, NavigationControl} from 'maplibre-gl';
import * as turf from '@turf/turf';
import {markRaw, onMounted, onUnmounted, ref, shallowRef, watch} from 'vue';
import 'maplibre-gl/dist/maplibre-gl.css';

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

watch(editGarden, (editGarden, prevEditGarden) => {
    if (editGarden && !prevEditGarden) {
        geojson.features = [];
        map.value.getSource('geojson').setData(geojson);
    }
})

onMounted(() => {
    const initialState = {lng: 11.8985, lat: 55.10555, zoom: 10};

    map.value = markRaw(new Map({
        container: mapContainer.value,
        style: 'https://maps-assets.geocod.io/styles/geocodio.json',
        center: [initialState.lng, initialState.lat],
        zoom: initialState.zoom
    }));
    map.value.addControl(new NavigationControl(), 'top-right');

    // Used to draw a line between points
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

        // Add styles to the map
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
                var features = map.value.queryRenderedFeatures(e.point, {
                    layers: ['measure-points']
                });

                // Remove the linestring from the group
                // So we can redraw it based on the points collection
                if (geojson.features.length > 1) geojson.features.pop();

                // If a feature was clicked, remove it from the map
                if (features.length) {
                    var id = features[0].properties.id;

                    if (id === geojson.features[0].properties.id) {
                        var points = geojson.features
                            .filter(feature => feature.geometry.type === 'Point')
                            .map(feature => feature.geometry.coordinates);
                        points.push(points[0]);

                        var line = turf.lineString(points);
                        var polygon = turf.lineToPolygon(line);

                        var area = turf.area(polygon);
                        console.log(area);

                        polygon.properties.area = Math.round(area).toLocaleString() + ' m2';

                        gardensCollection.features.push(polygon);
                        map.value.getSource('gardensCollection').setData(gardensCollection);
                        geojson.features = [];
                        editGarden.value = false;
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
                    linestring.geometry.coordinates = geojson.features.map(
                        function (point) {
                            return point.geometry.coordinates;
                        }
                    );

                    linestring.properties.distance = Math.round((turf.length(linestring) * 1000)).toLocaleString() + ' meters';
                    geojson.features.push(linestring);

                    console.log(geojson.features);
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

        <div class="mb-8">
            <PrimaryButton @click="editGarden = !editGarden" v-if="!editGarden">Add garden</PrimaryButton>
            <PrimaryButton @click="editGarden = !editGarden" v-if="editGarden">Cancel add garden</PrimaryButton>
        </div>

        <div class="w-full h-[500px] relative">
            <div class="w-full h-full" ref="mapContainer"></div>
        </div>
    </AuthenticatedLayout>
</template>
