import * as turf from '@turf/turf';

export default class DrawHandler {
    constructor(map, statusText) {
        if (new.target === DrawHandler) {
            throw new TypeError("Cannot construct DrawHandler directly");
        }

        this.name = new.target.name;

        this.map = map;
        this.statusText = statusText;

        this.currentDrawingCollection = {
            'type': 'FeatureCollection',
            'features': []
        };

        this.map.value.addSource(this.name, {
            'type': 'geojson',
            'data': this.currentDrawingCollection
        });
    }

    destruct() {
        this.removeLayers();
        this.map.value.removeSource(this.name);
    }

    refreshCurrentDrawing() {
        this.map.value.getSource(this.name).setData(this.currentDrawingCollection);
    }

    onMouseMove(e) {

    }

    onClick(e) {

    }

    createGarden(feature) {
        this.currentDrawingCollection.features = [];

        //editGarden.value = false;
        this.statusText.value = '';

        const name = prompt('What should we call this lovely garden?');

        if (name) {
            this.destruct();

            axios.post('gardens', {name, geojson: feature, area: turf.area(feature)})
                .then(response => {
                    this.map.value.getSource('gardens.geojson').setData(response.data);
                })
                .catch(err => console.error(err));
        }
    }

    addLayers() {
        for (const layer in this.layers) {
            this.map.value.addLayer(this.layers[layer]);
        }
    }

    removeLayers() {
        for (const layer in this.layers) {
            this.map.value.removeLayer(this.layers[layer].id);
        }
    }

    createFeature(type, coordinates) {
        return {
            'type': 'Feature',
            'properties': {},
            'geometry': {
                'type': type,
                'coordinates': coordinates
            }
        };
    }
}
