<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Map - My Final Project</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
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
        <div class="flex items-center gap-3">
            <button onclick="clearAll()" class="text-red-500 hover:text-red-700 text-sm font-medium">Clear</button>
            <button onclick="openSaveModal()" class="text-green-600 hover:text-green-800 text-sm font-medium">💾 Save to DB</button>
            @include('partials.version')
        </div>
    </div>
    <div id="map"></div>

    {{-- Dev: Beach Toggle --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-[1000]">
        <button id="beachToggle" onclick="toggleBeaches()"
                class="px-5 py-2.5 bg-white text-gray-700 border border-gray-300 rounded-full shadow-lg text-sm font-medium hover:bg-gray-50 transition flex items-center gap-2">
            <span id="toggleIcon">🏖️</span>
            <span id="toggleText">Load Beaches</span>
        </button>
    </div>

    {{-- Save Modal --}}
    <div id="saveModal" class="fixed inset-0 z-[2000] hidden bg-black/50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-semibold mb-4">Save Geo Feature</h2>
            <form id="saveForm" enctype="multipart/form-data">
                <input type="hidden" name="geometry_type" id="geomType">
                <input type="hidden" name="geometry_data" id="geomData">

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" id="featName" required
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="featDesc" rows="3"
                              class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image (optional)</label>
                    <input type="file" name="image" id="featImage" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div class="flex justify-end gap-3 mt-5">
                    <button type="button" onclick="closeSaveModal()"
                            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded">Cancel</button>
                    <button type="submit"
                            class="px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script>
        const map = L.map('map').setView([-8.5333, 116.5333], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);

        // ---- Drawing layer ----
        const drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        const drawControl = new L.Control.Draw({
            draw: {
                marker: { icon: new L.Icon.Default() },
                polygon: true,
                polyline: true,
                rectangle: false,
                circle: false,
                circlemarker: false,
            },
            edit: {
                featureGroup: drawnItems,
                edit: false,
            }
        });
        map.addControl(drawControl);

        map.on(L.Draw.Event.CREATED, function(e) {
            drawnItems.addLayer(e.layer);
            setCurrentGeometry(e.layer);
        });

        map.on('draw:deleted', function() {
            document.getElementById('coords').textContent = 'Draw something on the map';
        });

        let currentGeometry = null;

        function setCurrentGeometry(layer) {
            currentGeometry = layer;
            let type = '';
            let coords = '';
            if (layer instanceof L.Marker) {
                type = 'Point';
                const ll = layer.getLatLng();
                coords = JSON.stringify({ lat: ll.lat, lng: ll.lng });
                document.getElementById('coords').textContent = `Point: ${ll.lat.toFixed(5)}, ${ll.lng.toFixed(5)}`;
            } else if (layer instanceof L.Polygon) {
                type = 'Polygon';
                const latlngs = layer.getLatLngs()[0].map(ll => ({ lat: ll.lat, lng: ll.lng }));
                coords = JSON.stringify(latlngs);
                document.getElementById('coords').textContent = `Polygon: ${latlngs.length} vertices`;
            } else if (layer instanceof L.Polyline) {
                type = 'Polyline';
                const latlngs = layer.getLatLngs().map(ll => ({ lat: ll.lat, lng: ll.lng }));
                coords = JSON.stringify(latlngs);
                document.getElementById('coords').textContent = `Polyline: ${latlngs.length} vertices`;
            }
            document.getElementById('geomType').value = type;
            document.getElementById('geomData').value = coords;
        }

        // ---- Dev: Beach Toggle ----
        const beachMarkers = [];
        let beachesLoaded = false;

        function toggleBeaches() {
            if (beachesLoaded) {
                beachMarkers.forEach(m => map.removeLayer(m));
                beachMarkers.length = 0;
                beachesLoaded = false;
                document.getElementById('toggleText').textContent = 'Load Beaches';
                document.getElementById('toggleIcon').textContent = '🏖️';
            } else {
                if (beachMarkers.length === 0) {
                    fetch('/api/beaches')
                        .then(res => res.json())
                        .then(beaches => {
                            beaches.forEach(beach => {
                                const marker = L.marker([beach.latitude, beach.longitude]).addTo(map);
                                marker.bindPopup(`
                                    <div style="max-width:200px">
                                        <img src="${beach.image_url}" alt="${beach.name}" style="width:100%;height:100px;object-fit:cover;border-radius:4px;margin-bottom:4px">
                                        <b>${beach.name}</b>
                                        <p style="margin:4px 0;font-size:12px;color:#555">${beach.description.substring(0,120)}...</p>
                                    </div>
                                `);
                                beachMarkers.push(marker);
                            });
                            beachesLoaded = true;
                            document.getElementById('toggleText').textContent = 'Hide Beaches';
                            document.getElementById('toggleIcon').textContent = '🗺️';
                        });
                } else {
                    beachMarkers.forEach(m => m.addTo(map));
                    beachesLoaded = true;
                    document.getElementById('toggleText').textContent = 'Hide Beaches';
                    document.getElementById('toggleIcon').textContent = '🗺️';
                }
            }
        }

        // ---- Load saved geo features ----
        const geoFeatureLayers = [];

        function loadGeoFeatures() {
            fetch('/api/geo-features')
                .then(res => res.json())
                .then(features => {
                    features.forEach(f => {
                        const data = f.geometry_data;
                        let layer;
                        if (f.geometry_type === 'Point') {
                            layer = L.marker([data.lat, data.lng]);
                        } else if (f.geometry_type === 'Polygon') {
                            const latlngs = data.map(p => [p.lat, p.lng]);
                            layer = L.polygon(latlngs, { color: '#3388ff' });
                        } else if (f.geometry_type === 'Polyline') {
                            const latlngs = data.map(p => [p.lat, p.lng]);
                            layer = L.polyline(latlngs, { color: '#3388ff' });
                        }
                        if (layer) {
                            let popupHtml = `<b>${f.name}</b>`;
                            if (f.description) popupHtml += `<p style="font-size:12px;color:#555">${f.description}</p>`;
                            if (f.image_path) popupHtml += `<img src="/storage/${f.image_path}" style="width:100%;max-height:120px;object-fit:cover;border-radius:4px;margin-top:4px">`;
                            popupHtml += `<br><small class="text-red-500 cursor-pointer" onclick="deleteFeature(${f.id})">🗑 Delete</small>`;
                            layer.bindPopup(popupHtml);
                            layer.addTo(map);
                            geoFeatureLayers.push({ id: f.id, layer });
                        }
                    });
                });
        }

        loadGeoFeatures();

        function deleteFeature(id) {
            if (!confirm('Delete this feature?')) return;
            fetch(`/api/geo-features/${id}`, { method: 'DELETE' })
                .then(res => res.json())
                .then(() => {
                    const idx = geoFeatureLayers.findIndex(g => g.id === id);
                    if (idx !== -1) {
                        map.removeLayer(geoFeatureLayers[idx].layer);
                        geoFeatureLayers.splice(idx, 1);
                    }
                });
        }

        // ---- Save Modal ----
        function openSaveModal() {
            if (!currentGeometry) {
                alert('Draw something on the map first (use the toolbar on the left).');
                return;
            }
            document.getElementById('saveModal').classList.remove('hidden');
        }

        function closeSaveModal() {
            document.getElementById('saveModal').classList.add('hidden');
            document.getElementById('saveForm').reset();
        }

        document.getElementById('saveForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('/api/geo-features', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(res => res.json())
            .then(feature => {
                closeSaveModal();
                alert('Saved!');
                // Reload geo features
                geoFeatureLayers.forEach(g => map.removeLayer(g.layer));
                geoFeatureLayers.length = 0;
                loadGeoFeatures();
                drawnItems.clearLayers();
                currentGeometry = null;
                document.getElementById('coords').textContent = 'Draw something on the map';
            })
            .catch(err => {
                alert('Error saving. Check console.');
                console.error(err);
            });
        });

        function clearAll() {
            drawnItems.clearLayers();
            currentGeometry = null;
            document.getElementById('coords').textContent = 'Draw something on the map';
        }
    </script>
</body>
</html>
