export default [
    {
        id: 'measure-points',
        type: 'circle',
        source: 'geojson',
        paint: {
            'circle-radius': 5,
            'circle-color': '#000'
        },
        filter: ['in', '$type', 'Point']
    },
    {
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
    },
    {
        id: 'measure-lines-labels',
        type: 'symbol',
        source: 'geojson',
        layout: {
            'text-field': ['get', 'distance'],
            'text-font': ['Open Sans Regular'],
            'text-size': 8,
            'symbol-placement': 'line-center',
        },
        filter: ['in', '$type', 'LineString']
    },
    {
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
    },
    {
        id: 'garden-fill',
        type: 'fill',
        source: 'gardensCollection',
        paint: {
            'fill-color': 'rgba(255, 255, 255, 0.5)'
        },
        filter: ['in', '$type', 'Polygon']
    },
    {
        id: 'garden-labels',
        type: 'symbol',
        source: 'gardensCollection',
        layout: {
            'text-field': ['get', 'area'],
            'text-font': ['Open Sans Regular'],
            'text-size': 8
        }
    }
];
