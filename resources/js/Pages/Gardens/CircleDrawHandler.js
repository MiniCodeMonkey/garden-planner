import * as turf from '@turf/turf';
import DrawHandler from './DrawHandler.js';

export default class CircleDrawHandler extends DrawHandler {
    constructor(...args) {
        super(...args);
        this.startingPoint = null;
        this.layers = [
            {
                id: 'starting-point',
                type: 'circle',
                source: 'CircleDrawHandler',
                paint: {
                    'circle-radius': 5,
                    'circle-color': '#000'
                },
                filter: ['in', '$type', 'Point']
            },
            {
                id: 'rectangle-lines',
                type: 'line',
                source: 'CircleDrawHandler',
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
        return 'Click where the center of your garden is located';
    }

    onClick(e) {
        if (this.startingPoint) {
            const from = turf.point(this.startingPoint);
            const to = turf.point([e.lngLat.lng, e.lngLat.lat]);
            const distance = turf.distance(from, to, {units: 'meters'});

            const radius = distance / 2;
            const circle = turf.circle(this.startingPoint, radius, {steps: 25, units: 'meters'});

            this.createGarden(circle);
        } else {
            this.startingPoint = [e.lngLat.lng, e.lngLat.lat];
            this.currentDrawingCollection.features.push(this.createFeature('Point', this.startingPoint));
            this.refreshCurrentDrawing();
        }
    }

    onMouseMove(e) {
        this.map.value.getCanvas().style.cursor = 'crosshair';

        if (this.startingPoint) {
            const from = turf.point(this.startingPoint);
            const to = turf.point([e.lngLat.lng, e.lngLat.lat]);
            const distance = turf.distance(from, to, {units: 'meters'});

            const radius = distance / 2;
            const circle = turf.circle(this.startingPoint, radius, {steps: 25, units: 'meters'});

            const area = Math.round(turf.area(circle));

            this.statusText.value = `r = ${Math.round(radius * 10) / 10}m - ${area}m2`

            this.currentDrawingCollection.features = [circle];
            this.refreshCurrentDrawing();
        }
    }

}
