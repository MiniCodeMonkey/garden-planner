import * as turf from '@turf/turf';
import DrawHandler from './DrawHandler.js';

export default class PolygonDrawHandler extends DrawHandler {
    constructor(...args) {
        super(...args);
        this.layers = [
            {
                id: 'measure-points',
                type: 'circle',
                source: 'PolygonDrawHandler',
                paint: {
                    'circle-radius': 5,
                    'circle-color': '#000'
                },
                filter: ['in', '$type', 'Point']
            },
            {
                id: 'measure-lines',
                type: 'line',
                source: 'PolygonDrawHandler',
                layout: {
                    'line-cap': 'round',
                    'line-join': 'round'
                },
                paint: {
                    'line-color': '#000',
                    'line-width': 2.5
                },
                filter: ['in', '$type', 'LineString']
            },
            {
                id: 'polygon',
                type: 'line',
                source: 'PolygonDrawHandler',
                layout: {
                    'line-cap': 'round',
                    'line-join': 'round'
                },
                paint: {
                    'line-color': '#000',
                    'line-width': 2.5
                },
                filter: ['in', '$type', 'Polygon']
            },
            {
                id: 'measure-lines-labels',
                type: 'symbol',
                source: 'PolygonDrawHandler',
                layout: {
                    'text-field': ['get', 'distance'],
                    'text-font': ['Open Sans Regular'],
                    'text-size': 8,
                    'symbol-placement': 'line-center',
                },
                filter: ['in', '$type', 'LineString']
            },
        ];

        this.addLayers();
    }

    getDefaultStatusText() {
        return 'Finish by clicking on the first point again';
    }

    onClick(e) {
        var selectedFeatures = this.map.value.queryRenderedFeatures(e.point, {
            layers: ['measure-points']
        });

        if (this.currentDrawingCollection.features.length > 1) {
            this.currentDrawingCollection.features.pop();
        }

        if (selectedFeatures.length) {
            // If the first feature was clicked
            var id = selectedFeatures[0].properties.id;
            if (id === this.currentDrawingCollection.features[0].properties.id) {
                var points = this.currentDrawingCollection.features
                    .filter(feature => feature.geometry.type === 'Point')
                    .map(feature => feature.geometry.coordinates);

                points.push(points[0]);

                var line = turf.lineString(points);
                var polygon = turf.lineToPolygon(line);
                this.currentDrawingCollection.features = [polygon];
                this.refreshCurrentDrawing();
                window.setTimeout(() => this.createGarden(polygon), 100);
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
            this.currentDrawingCollection.features.push(point);
        }

        if (this.currentDrawingCollection.features.length > 1) {
            // Create line through points
            this.currentDrawingCollection.features.push(
                this.createFeature('LineString', this.currentDrawingCollection.features.map(point => point.geometry.coordinates))
            );
        }

        this.refreshCurrentDrawing();
    }

    onMouseMove(e) {
        var features = this.map.value.queryRenderedFeatures(e.point, {
            layers: ['measure-points']
        });

        this.map.value.getCanvas().style.cursor = features.length
            ? 'pointer'
            : 'crosshair';
    }
}
