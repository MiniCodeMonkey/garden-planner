import * as turf from '@turf/turf';
import DrawHandler from './DrawHandler.js';

export default class RectangleDrawHandler extends DrawHandler {
    getDefaultStatusText() {
        return 'Click where the first corner of your garden starts';
    }

    onClick(e) {
        var selectedFeatures = this.map.value.queryRenderedFeatures(e.point, {
            layers: ['measure-points']
        });

        if (selectedFeatures.length <= 0) {
            if (this.currentDrawingCollection.features.length > 0) {
                var line = turf.lineString([this.currentDrawingCollection.features[0].geometry.coordinates, [e.lngLat.lng, e.lngLat.lat]]);
                var bbox = turf.bbox(line);
                var bboxPolygon = turf.bboxPolygon(bbox);

                this.createGarden(bboxPolygon);
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
        }
    }

    onMouseMove(e) {
        if (this.currentDrawingCollection.features.length > 0) {
            const line = turf.lineString([this.currentDrawingCollection.features[0].geometry.coordinates, [e.lngLat.lng, e.lngLat.lat]]);
            var bbox = turf.bbox(line);
            const width = Math.round(turf.distance([bbox[0], bbox[1]], [bbox[2], bbox[3]], {units: "centimeters"}));
            const height = Math.round(turf.distance([bbox[0], bbox[1]], [bbox[0], bbox[3]], {units: "centimeters"}));

            this.statusText.value = `${width}cm x ${height}cm`
        }
    }
}
