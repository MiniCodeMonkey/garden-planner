import * as turf from '@turf/turf';
import DrawHandler from './DrawHandler.js';

export default class RectangleDrawHandler extends DrawHandler {
    constructor(...args) {
        super(...args);
        this.startingPoint = null;
        this.layers = [
            {
                id: 'starting-point',
                type: 'circle',
                source: 'RectangleDrawHandler',
                paint: {
                    'circle-radius': 5,
                    'circle-color': '#000'
                },
                filter: ['in', '$type', 'Point']
            },
            {
                id: 'rectangle-lines',
                type: 'line',
                source: 'RectangleDrawHandler',
                layout: {
                    'line-cap': 'round',
                    'line-join': 'round'
                },
                paint: {
                    'line-color': '#333',
                    'line-width': 2.5,
                    'line-dasharray': [2, 2],
                },
                filter: ['in', '$type', 'Polygon']
            }
        ];

        this.addLayers();
    }

    getDefaultStatusText() {
        return 'Click where the first corner of your garden starts';
    }

    onClick(e) {
        if (this.startingPoint) {
            const line = turf.lineString([this.startingPoint, [e.lngLat.lng, e.lngLat.lat]]);
            const bbox = turf.bbox(line);
            const bboxPolygon = turf.bboxPolygon(bbox);

            this.createGarden(bboxPolygon);
        } else {
            this.startingPoint = [e.lngLat.lng, e.lngLat.lat];
            this.currentDrawingCollection.features.push(this.createFeature('Point', this.startingPoint));
            this.refreshCurrentDrawing();
        }
    }

    onMouseMove(e) {
        this.map.value.getCanvas().style.cursor = 'crosshair';

        if (this.startingPoint) {
            const line = turf.lineString([this.startingPoint, [e.lngLat.lng, e.lngLat.lat]]);
            const bbox = turf.bbox(line);
            const bboxPolygon = turf.bboxPolygon(bbox);

            const [minX, minY, maxX, maxY] = bbox;

            const width = Math.round(turf.distance([minX, minY], [maxX, minY], {units: "centimeters"}));
            const height = Math.round(turf.distance([minX, minY], [minX, maxY], {units: "centimeters"}));
            const area = Math.round(turf.area(bboxPolygon));

            this.statusText.value = `${width}cm x ${height}cm - ${area}m2`

            this.currentDrawingCollection.features = [
                bboxPolygon
            ];
            
            this.refreshCurrentDrawing();
        }
    }

}
