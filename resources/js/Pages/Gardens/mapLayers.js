export default [
    {
        id: 'garden-lines',
        type: 'line',
        source: 'gardens.geojson',
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
        source: 'gardens.geojson',
        paint: {
            'fill-color': 'rgba(255, 255, 255, 0.5)'
        },
        filter: ['in', '$type', 'Polygon']
    },
    {
        id: 'garden-labels',
        type: 'symbol',
        source: 'gardens.geojson',
        layout: {
            'text-field': ['get', 'area'],
            'text-font': ['Open Sans Regular'],
            'text-size': 8
        }
    }
];
