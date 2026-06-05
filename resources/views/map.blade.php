<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Map - My Final Project</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #map { height: calc(100vh - 56px); width: 100%; }
        .leaflet-control-zoom { margin-top: 60px !important; }
    </style>
</head>
<body class="m-0 p-0">
    <div class="absolute top-0 left-0 right-0 z-[1000] bg-white/90 backdrop-blur shadow-sm px-4 py-2 flex items-center justify-between">
        <a href="{{ url('/') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">&larr; Back</a>
        <span id="coords" class="text-gray-500 text-xs">Click anywhere on the map</span>
        <button onclick="clearMarkers()" class="text-red-500 hover:text-red-700 text-sm font-medium">Clear</button>
        @include('partials.version')
    </div>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([51.505, -0.09], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);

        const markers = [];

        map.on('click', function(e) {
            const marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);
            marker.bindPopup(`<b>Marker</b><br>${e.latlng.lat.toFixed(5)}, ${e.latlng.lng.toFixed(5)}`).openPopup();
            markers.push(marker);
            document.getElementById('coords').textContent =
                `Lat: ${e.latlng.lat.toFixed(5)} | Lng: ${e.latlng.lng.toFixed(5)}`;
        });

        function clearMarkers() {
            markers.forEach(m => map.removeLayer(m));
            markers.length = 0;
            document.getElementById('coords').textContent = 'Click anywhere on the map';
        }
    </script>
</body>
</html>
