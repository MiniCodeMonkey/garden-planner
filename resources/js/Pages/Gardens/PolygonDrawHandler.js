import * as turf from '@turf/turf';
import DrawHandler from './DrawHandler.js';

export default class PolygonDrawHandler extends DrawHandler {
    constructor(...args) {
        super(...args);

        this.linestring = {
            'type': 'Feature',
            'properties': {},
            'geometry': {
                'type': 'LineString',
                'coordinates': []
            }
        };
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
                this.createGarden(polygon);
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
            this.linestring.geometry.coordinates = this.currentDrawingCollection.features.map(
                function (point) {
                    return point.geometry.coordinates;
                }
            );
            this.currentDrawingCollection.features.push(this.linestring);
        }
    }
}
