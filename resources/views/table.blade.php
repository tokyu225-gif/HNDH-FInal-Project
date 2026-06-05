<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Table - Saved Features</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 m-0 p-0">
    <div class="max-w-5xl mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-semibold">Saved Geo Features</h1>
            <a href="{{ url('/map') }}" class="text-blue-600 hover:text-blue-800 text-sm">&larr; Back to Map</a>
        </div>

        {{-- Points --}}
        <div class="mb-8">
            <h2 class="text-sm font-semibold text-blue-600 uppercase mb-2">Points</h2>
            <div class="bg-white rounded shadow-sm overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-left">
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Latitude</th>
                            <th class="px-4 py-2">Longitude</th>
                            <th class="px-4 py-2">Description</th>
                            <th class="px-4 py-2">Image</th>
                            <th class="px-4 py-2">Created</th>
                        </tr>
                    </thead>
                    <tbody id="pointsBody">
                        <tr><td colspan="7" class="px-4 py-4 text-gray-400">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Polylines --}}
        <div class="mb-8">
            <h2 class="text-sm font-semibold text-green-600 uppercase mb-2">Polylines</h2>
            <div class="bg-white rounded shadow-sm overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-left">
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Vertices</th>
                            <th class="px-4 py-2">Coordinates</th>
                            <th class="px-4 py-2">Description</th>
                            <th class="px-4 py-2">Image</th>
                            <th class="px-4 py-2">Created</th>
                        </tr>
                    </thead>
                    <tbody id="polylinesBody">
                        <tr><td colspan="7" class="px-4 py-4 text-gray-400">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Polygons --}}
        <div class="mb-8">
            <h2 class="text-sm font-semibold text-purple-600 uppercase mb-2">Polygons</h2>
            <div class="bg-white rounded shadow-sm overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-left">
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Vertices</th>
                            <th class="px-4 py-2">Coordinates</th>
                            <th class="px-4 py-2">Description</th>
                            <th class="px-4 py-2">Image</th>
                            <th class="px-4 py-2">Created</th>
                        </tr>
                    </thead>
                    <tbody id="polygonsBody">
                        <tr><td colspan="7" class="px-4 py-4 text-gray-400">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        fetch('/api/geo-features')
            .then(res => res.json())
            .then(data => {
                // Points
                let html = '';
                if (data.points.length) {
                    data.points.forEach(p => {
                        html += `<tr>
                            <td class="px-4 py-2 text-gray-400">#${p.id}</td>
                            <td class="px-4 py-2 font-medium">${esc(p.name)}</td>
                            <td class="px-4 py-2">${p.latitude}</td>
                            <td class="px-4 py-2">${p.longitude}</td>
                            <td class="px-4 py-2 text-gray-500 max-w-xs truncate">${p.description || '-'}</td>
                            <td class="px-4 py-2">${p.image_path ? '<img src="/storage/'+p.image_path+'" class="w-16 h-10 object-cover rounded">' : '-'}</td>
                            <td class="px-4 py-2 text-gray-400">${new Date(p.created_at).toLocaleDateString()}</td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="7" class="px-4 py-4 text-gray-400">No points saved.</td></tr>';
                }
                document.getElementById('pointsBody').innerHTML = html;

                // Polylines
                html = '';
                if (data.polylines.length) {
                    data.polylines.forEach(p => {
                        const coordsStr = p.coordinates.map(c => `${c.lat.toFixed(4)},${c.lng.toFixed(4)}`).join('; ');
                        html += `<tr>
                            <td class="px-4 py-2 text-gray-400">#${p.id}</td>
                            <td class="px-4 py-2 font-medium">${esc(p.name)}</td>
                            <td class="px-4 py-2">${p.coordinates.length}</td>
                            <td class="px-4 py-2 text-gray-500 max-w-xs truncate text-xs">${coordsStr}</td>
                            <td class="px-4 py-2 text-gray-500 max-w-xs truncate">${p.description || '-'}</td>
                            <td class="px-4 py-2">${p.image_path ? '<img src="/storage/'+p.image_path+'" class="w-16 h-10 object-cover rounded">' : '-'}</td>
                            <td class="px-4 py-2 text-gray-400">${new Date(p.created_at).toLocaleDateString()}</td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="7" class="px-4 py-4 text-gray-400">No polylines saved.</td></tr>';
                }
                document.getElementById('polylinesBody').innerHTML = html;

                // Polygons
                html = '';
                if (data.polygons.length) {
                    data.polygons.forEach(p => {
                        const coordsStr = p.coordinates.map(c => `${c.lat.toFixed(4)},${c.lng.toFixed(4)}`).join('; ');
                        html += `<tr>
                            <td class="px-4 py-2 text-gray-400">#${p.id}</td>
                            <td class="px-4 py-2 font-medium">${esc(p.name)}</td>
                            <td class="px-4 py-2">${p.coordinates.length}</td>
                            <td class="px-4 py-2 text-gray-500 max-w-xs truncate text-xs">${coordsStr}</td>
                            <td class="px-4 py-2 text-gray-500 max-w-xs truncate">${p.description || '-'}</td>
                            <td class="px-4 py-2">${p.image_path ? '<img src="/storage/'+p.image_path+'" class="w-16 h-10 object-cover rounded">' : '-'}</td>
                            <td class="px-4 py-2 text-gray-400">${new Date(p.created_at).toLocaleDateString()}</td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="7" class="px-4 py-4 text-gray-400">No polygons saved.</td></tr>';
                }
                document.getElementById('polygonsBody').innerHTML = html;
            });

        function esc(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }
    </script>
</body>
</html>
