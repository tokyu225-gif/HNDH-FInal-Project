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
        <span id="statusMsg" class="text-gray-500 text-xs"></span>
        <div class="flex items-center gap-3">
            <a href="{{ url('/table') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Table</a>
            <a href="{{ url('/settings') }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">Settings</a>
            <button id="routeBtn" onclick="toggleRouting()" class="text-orange-600 hover:text-orange-800 text-sm font-medium">Route</button>
            <button onclick="clearAll()" class="text-red-500 hover:text-red-700 text-sm font-medium">Clear</button>
            <button onclick="openSaveModal()" class="text-green-600 hover:text-green-800 text-sm font-medium">Save to DB</button>
            @include('partials.version')
        </div>
    </div>
    <div id="map"></div>

    {{-- Bottom-left coords --}}
    <div id="coords" class="absolute bottom-1 left-2 z-[1000] bg-white/80 backdrop-blur px-2 py-0.5 rounded text-xs text-gray-600 font-mono shadow-sm">Move mouse over map</div>

    {{-- Dev: Beach Toggle --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-[1000]">
        <button id="beachToggle" onclick="toggleBeaches()"
                class="px-5 py-2.5 bg-white text-gray-700 border border-gray-300 rounded-full shadow-lg text-sm font-medium hover:bg-gray-50 transition flex items-center gap-2">
            <span id="toggleText">Load Beaches</span>
        </button>
    </div>

    {{-- Save / Edit Modal --}}
    <div id="saveModal" class="fixed inset-0 z-[2000] hidden bg-black/50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
            <h2 id="modalTitle" class="text-lg font-semibold mb-4">Save Geo Feature</h2>
            <form id="saveForm" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" id="editId">
                <input type="hidden" name="edit_type" id="editType">
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
                    <img id="featImagePreview" class="mt-2 hidden w-full h-24 object-cover rounded">
                </div>
                <div class="flex justify-end gap-3 mt-5">
                    <button type="button" onclick="closeSaveModal()"
                            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded">Cancel</button>
                    <button type="submit" id="modalSubmitBtn"
                            class="px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Move/Edit Shape Action Bar --}}
    <div id="moveBar" class="absolute top-14 left-1/2 -translate-x-1/2 z-[2000] hidden bg-white rounded-lg shadow-lg border px-4 py-2 flex items-center gap-3">
        <span class="text-sm text-gray-600">Drag vertices to reshape</span>
        <button onclick="cancelShapeEdit()" class="px-3 py-1 text-sm text-gray-600 border border-gray-300 rounded hover:bg-gray-50">Cancel</button>
        <button onclick="saveShapeEdit()" class="px-3 py-1 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">Save</button>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script>
        const map = L.map('map').setView([-8.5333, 116.5333], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);

        // Show mouse coordinates in real-time
        map.on('mousemove', function(e) {
            document.getElementById('coords').textContent =
                `Lat: ${e.latlng.lat.toFixed(5)} | Lng: ${e.latlng.lng.toFixed(5)}`;
        });

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
                edit: true,
            }
        });
        map.addControl(drawControl);

        map.on(L.Draw.Event.CREATED, function(e) {
            drawnItems.addLayer(e.layer);
            setCurrentGeometry(e.layer);
            openSaveModal();
        });

        map.on('draw:deleted', function() {
            document.getElementById('statusMsg').textContent = '';
        });

        drawnItems.on('dblclick', function(e) {
            L.DomEvent.stopPropagation(e);
            setCurrentGeometry(e.layer);
            openSaveModal();
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
                document.getElementById('statusMsg').textContent = `Point: ${ll.lat.toFixed(5)}, ${ll.lng.toFixed(5)}`;
            } else if (layer instanceof L.Polygon) {
                type = 'Polygon';
                const latlngs = layer.getLatLngs()[0].map(ll => ({ lat: ll.lat, lng: ll.lng }));
                coords = JSON.stringify(latlngs);
                document.getElementById('statusMsg').textContent = `Polygon: ${latlngs.length} vertices`;
            } else if (layer instanceof L.Polyline) {
                type = 'Polyline';
                const latlngs = layer.getLatLngs().map(ll => ({ lat: ll.lat, lng: ll.lng }));
                coords = JSON.stringify(latlngs);
                document.getElementById('statusMsg').textContent = `Polyline: ${latlngs.length} vertices`;
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
                        });
                } else {
                    beachMarkers.forEach(m => m.addTo(map));
                    beachesLoaded = true;
                    document.getElementById('toggleText').textContent = 'Hide Beaches';
                }
            }
        }

        // ---- Load saved geo features ----
        const geoFeatureLayers = [];

        function loadGeoFeatures() {
            fetch('/api/geo-features')
                .then(res => res.json())
                .then(data => {
                    // Points
                    (data.points || []).forEach(p => {
                        const layer = L.marker([p.latitude, p.longitude]);
                        addFeaturePopup(layer, 'Point', p);
                        layer.addTo(map);
                        geoFeatureLayers.push({ type: 'Point', id: p.id, layer, data: p });
                    });
                    // Polylines
                    (data.polylines || []).forEach(p => {
                        const latlngs = p.coordinates.map(c => [c.lat, c.lng]);
                        const layer = L.polyline(latlngs, { color: '#3388ff' });
                        addFeaturePopup(layer, 'Polyline', p);
                        layer.addTo(map);
                        geoFeatureLayers.push({ type: 'Polyline', id: p.id, layer, data: p });
                    });
                    // Polygons
                    (data.polygons || []).forEach(p => {
                        const latlngs = p.coordinates.map(c => [c.lat, c.lng]);
                        const layer = L.polygon(latlngs, { color: '#3388ff' });
                        addFeaturePopup(layer, 'Polygon', p);
                        layer.addTo(map);
                        geoFeatureLayers.push({ type: 'Polygon', id: p.id, layer, data: p });
                    });
                });
        }

        function addFeaturePopup(layer, type, f) {
            let popupHtml = `<b>${f.name}</b>`;
            if (f.description) popupHtml += `<p style="font-size:12px;color:#555">${f.description}</p>`;
            if (f.image_path) popupHtml += `<img src="/storage/${f.image_path}" style="width:100%;max-height:120px;object-fit:cover;border-radius:4px;margin-top:4px">`;
            popupHtml += `<br><small>`;
            popupHtml += `<span class="text-blue-500 cursor-pointer mr-2" onclick="editFeature('${type}', ${f.id})">Edit</span>`;
            popupHtml += `<span class="text-green-500 cursor-pointer mr-2" onclick="editShape('${type}', ${f.id})">Move</span>`;
            popupHtml += `<span class="text-red-500 cursor-pointer" onclick="deleteFeature('${type}', ${f.id})">Delete</span>`;
            popupHtml += `</small>`;
            layer.bindPopup(popupHtml);
            layer.on('dblclick', function(e) {
                L.DomEvent.stopPropagation(e);
                editFeature(type, f.id);
            });
        }

        function foundData(id, type) {
            return geoFeatureLayers.find(g => g.id === id && g.type === type);
        }

        let editingShape = null; // { type, id, layer }

        function editShape(type, id) {
            const found = foundData(id, type);
            if (!found) return;

            if (type === 'Point') {
                // Make point draggable, store original position
                found.layer.dragging.enable();
                const ll = found.layer.getLatLng();
                editingShape = { type, id, layer: found.layer, originalPos: { lat: ll.lat, lng: ll.lng } };
            } else {
                map.removeLayer(found.layer);
                drawnItems.addLayer(found.layer);
                editingShape = { type, id, layer: found.layer, originalCoords: null };
                if (found.layer instanceof L.Polygon) {
                    editingShape.originalCoords = found.layer.getLatLngs()[0].map(ll => ({ lat: ll.lat, lng: ll.lng }));
                } else if (found.layer instanceof L.Polyline) {
                    editingShape.originalCoords = found.layer.getLatLngs().map(ll => ({ lat: ll.lat, lng: ll.lng }));
                }
                try {
                    drawControl._toolbars.edit._modes.edit.handler.enable();
                } catch(e) {
                    if (editingShape.layer.editing) editingShape.layer.editing.enable();
                }
            }
            document.getElementById('moveBar').classList.remove('hidden');
            document.getElementById('statusMsg').textContent = 'Drag to move, then Save or Cancel.';
            map.closePopup();
        }

        function saveShapeEdit() {
            if (!editingShape) return;

            if (editingShape.type === 'Point') {
                const ll = editingShape.layer.getLatLng();
                fetch(`/api/points/${editingShape.id}`, {
                    method: 'POST',
                    body: JSON.stringify({ latitude: ll.lat, longitude: ll.lng }),
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(res => res.json())
                .then(updated => {
                    const found = foundData(editingShape.id, 'Point');
                    if (found) found.data = updated;
                    finishShapeEdit();
                    document.getElementById('statusMsg').textContent = 'Point moved.';
                });
                return;
            }

            const layer = editingShape.layer;
            let coords;
            if (layer instanceof L.Polygon) {
                coords = layer.getLatLngs()[0].map(ll => ({ lat: ll.lat, lng: ll.lng }));
            } else if (layer instanceof L.Polyline) {
                coords = layer.getLatLngs().map(ll => ({ lat: ll.lat, lng: ll.lng }));
            }
            if (!coords) { cancelShapeEdit(); return; }
            const url = editingShape.type === 'Polyline' ? `/api/polylines/${editingShape.id}`
                      : `/api/polygons/${editingShape.id}`;
            fetch(url, {
                method: 'POST',
                body: JSON.stringify({ coordinates: JSON.stringify(coords) }),
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(res => res.json())
            .then(updated => {
                const found = foundData(editingShape.id, editingShape.type);
                if (found) found.data = updated;
                finishShapeEdit();
                document.getElementById('statusMsg').textContent = 'Shape updated.';
            });
        }

        function cancelShapeEdit() {
            if (!editingShape) return;

            if (editingShape.type === 'Point') {
                // Revert to original position
                if (editingShape.originalPos) {
                    editingShape.layer.setLatLng([editingShape.originalPos.lat, editingShape.originalPos.lng]);
                }
                finishShapeEdit();
                document.getElementById('statusMsg').textContent = 'Edit cancelled.';
                return;
            }

            if (editingShape.originalCoords) {
                const latlngs = editingShape.originalCoords.map(c => [c.lat, c.lng]);
                if (editingShape.layer instanceof L.Polygon) {
                    editingShape.layer.setLatLngs([latlngs]);
                } else if (editingShape.layer instanceof L.Polyline) {
                    editingShape.layer.setLatLngs(latlngs);
                }
            }
            finishShapeEdit();
            document.getElementById('statusMsg').textContent = 'Edit cancelled.';
        }

        function finishShapeEdit() {
            if (editingShape.type === 'Point') {
                editingShape.layer.dragging.disable();
            } else {
                try {
                    drawControl._toolbars.edit._modes.edit.handler.disable();
                } catch(e) {}
                drawnItems.removeLayer(editingShape.layer);
                map.addLayer(editingShape.layer);
            }
            editingShape = null;
            document.getElementById('moveBar').classList.add('hidden');
        }

        function editFeature(type, id) {
            const found = geoFeatureLayers.find(g => g.id === id && g.type === type);
            if (!found) return;
            const f = found.data;
            document.getElementById('modalTitle').textContent = 'Edit Geo Feature';
            document.getElementById('modalSubmitBtn').textContent = 'Update';
            document.getElementById('editId').value = id;
            document.getElementById('editType').value = type;
            document.getElementById('featName').value = f.name || '';
            document.getElementById('featDesc').value = f.description || '';
            // For points, set geometry data
            if (type === 'Point') {
                document.getElementById('geomType').value = 'Point';
                document.getElementById('geomData').value = JSON.stringify({ lat: parseFloat(f.latitude), lng: parseFloat(f.longitude) });
            } else {
                document.getElementById('geomType').value = type;
                document.getElementById('geomData').value = JSON.stringify(f.coordinates || []);
            }
            // Image preview
            const preview = document.getElementById('featImagePreview');
            if (f.image_path) {
                preview.src = '/storage/' + f.image_path;
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
            document.getElementById('featImage').value = '';
            document.getElementById('saveModal').classList.remove('hidden');
            currentGeometry = null;
        }

        loadGeoFeatures();

        function deleteFeature(type, id) {
            if (!confirm('Delete this feature?')) return;
            const endpoint = type === 'Point' ? `/api/points/${id}`
                           : type === 'Polyline' ? `/api/polylines/${id}`
                           : `/api/polygons/${id}`;
            fetch(endpoint, { method: 'DELETE' })
                .then(res => res.json())
                .then(() => {
                    const idx = geoFeatureLayers.findIndex(g => g.id === id && g.type === type);
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
            // Reset to create mode
            document.getElementById('modalTitle').textContent = 'Save Geo Feature';
            document.getElementById('modalSubmitBtn').textContent = 'Save';
            document.getElementById('editId').value = '';
            document.getElementById('editType').value = '';
            document.getElementById('featName').value = '';
            document.getElementById('featDesc').value = '';
            document.getElementById('featImage').value = '';
            document.getElementById('featImagePreview').classList.add('hidden');
            document.getElementById('saveModal').classList.remove('hidden');
        }

        function closeSaveModal() {
            document.getElementById('saveModal').classList.add('hidden');
            document.getElementById('saveForm').reset();
            document.getElementById('featImagePreview').classList.add('hidden');
            document.getElementById('editId').value = '';
            document.getElementById('editType').value = '';
            // Remove the unsaved drawn feature from the map
            if (currentGeometry) {
                drawnItems.removeLayer(currentGeometry);
                currentGeometry = null;
                document.getElementById('statusMsg').textContent = '';
            }
        }

        document.getElementById('saveForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const editId = document.getElementById('editId').value;
            const editType = document.getElementById('editType').value;

            let url, method;
            if (editId && editType) {
                // Update existing
                method = 'POST';
                formData.delete('geometry_type');
                formData.delete('geometry_data');
                url = editType === 'Point' ? `/api/points/${editId}`
                    : editType === 'Polyline' ? `/api/polylines/${editId}`
                    : `/api/polygons/${editId}`;
            } else {
                // Create new
                method = 'POST';
                url = '/api/geo-features';
            }

            fetch(url, {
                method: method,
                body: formData,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(res => res.json())
            .then(() => {
                closeSaveModal();
                alert(editId ? 'Updated!' : 'Saved!');
                geoFeatureLayers.forEach(g => map.removeLayer(g.layer));
                geoFeatureLayers.length = 0;
                loadGeoFeatures();
                drawnItems.clearLayers();
                currentGeometry = null;
                document.getElementById('statusMsg').textContent = '';
            })
            .catch(err => {
                alert('Error. Check console.');
                console.error(err);
            });
        });

        // ---- Routing (A to B) ----
        let routingMode = false;
        let routePointA = null;
        let routePointB = null;
        let routeMarkerA = null;
        let routeMarkerB = null;
        let routeLine = null;

        function toggleRouting() {
            routingMode = !routingMode;
            const btn = document.getElementById('routeBtn');
            if (routingMode) {
                btn.textContent = 'Route (ON)';
                btn.className = 'text-white bg-orange-500 px-2 py-0.5 rounded text-sm font-medium';
                clearRoute();
                document.getElementById('statusMsg').textContent = 'Click map to set point A';
                map.getContainer().style.cursor = 'crosshair';
            } else {
                btn.textContent = 'Route';
                btn.className = 'text-orange-600 hover:text-orange-800 text-sm font-medium';
                map.getContainer().style.cursor = '';
                document.getElementById('statusMsg').textContent = '';
            }
        }

        function clearRoute() {
            if (routeMarkerA) { map.removeLayer(routeMarkerA); routeMarkerA = null; }
            if (routeMarkerB) { map.removeLayer(routeMarkerB); routeMarkerB = null; }
            if (routeLine) { map.removeLayer(routeLine); routeLine = null; }
            routePointA = null;
            routePointB = null;
        }

        map.on('click', function(e) {
            if (!routingMode) return;

            if (!routePointA) {
                routePointA = e.latlng;
                routeMarkerA = L.marker([routePointA.lat, routePointA.lng], {
                    icon: L.divIcon({ className: 'route-marker-a', html: '<div style="background:#22c55e;color:white;width:22px;height:22px;border-radius:50%;text-align:center;line-height:22px;font-weight:bold;font-size:13px;border:2px solid white;box-shadow:0 1px 4px rgba(0,0,0,0.3)">A</div>', iconSize: [22,22], iconAnchor: [11,11] })
                }).addTo(map);
                document.getElementById('statusMsg').textContent = 'Point A set. Click map to set point B';
            } else if (!routePointB) {
                routePointB = e.latlng;
                routeMarkerB = L.marker([routePointB.lat, routePointB.lng], {
                    icon: L.divIcon({ className: 'route-marker-b', html: '<div style="background:#ef4444;color:white;width:22px;height:22px;border-radius:50%;text-align:center;line-height:22px;font-weight:bold;font-size:13px;border:2px solid white;box-shadow:0 1px 4px rgba(0,0,0,0.3)">B</div>', iconSize: [22,22], iconAnchor: [11,11] })
                }).addTo(map);
                document.getElementById('statusMsg').textContent = 'Fetching route...';
                fetchRoute();
            }
        });

        function fetchRoute() {
            const url = `https://router.project-osrm.org/route/v1/driving/${routePointA.lng},${routePointA.lat};${routePointB.lng},${routePointB.lat}?overview=full&geometries=geojson`;
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data.code !== 'Ok') { alert('No route found.'); clearRoute(); return; }
                    const route = data.routes[0];
                    const distance = (route.distance / 1000).toFixed(1);
                    const duration = Math.round(route.duration / 60);
                    const coords = route.geometry.coordinates.map(c => [c[1], c[0]]);
                    routeLine = L.polyline(coords, { color: '#f97316', weight: 5, opacity: 0.7 }).addTo(map);
                    map.fitBounds(routeLine.getBounds().pad(0.1));
                    document.getElementById('statusMsg').textContent = `Route: ${distance} km, ~${duration} min`;
                })
                .catch(() => { alert('Routing failed.'); clearRoute(); });
        }

        function clearAll() {
            drawnItems.clearLayers();
            currentGeometry = null;
            clearRoute();
            document.getElementById('statusMsg').textContent = '';
        }

    </script>
</body>
</html>
