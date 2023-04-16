import * as turf from '@turf/turf';

export default class DrawHandler {
    constructor(map, statusText) {
        if (new.target === DrawHandler) {
            throw new TypeError("Cannot construct DrawHandler directly");
        }

        this.map = map;
        this.statusText = statusText;

        this.currentDrawingCollection = {
            'type': 'FeatureCollection',
            'features': []
        };

        this.map.value.addSource(new.target.name, {
            'type': 'geojson',
            'data': this.currentDrawingCollection
        });
    }

    refreshCurrentDrawing() {
        this.map.value.getSource('geojson').setData(this.currentDrawingCollection);
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
            axios.post('gardens', {name, geojson: feature, area: turf.area(feature, {units: "centimeters"})})
                .then(response => {
                    console.log(this.map.value.getSource('gardensCollection'));
                    /*
                    const gardensCollection = this.map.value.getSource('gardensCollection').getData();
                    gardensCollection.features.push(response.data.geojson);

                    this.map.value.getSource('gardensCollection').setData(gardensCollection);
                    */
                })
                .catch(err => console.error(err));
        }
    }
}
