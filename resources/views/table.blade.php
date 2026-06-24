<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MaiGuard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.theme')
    <style>
        body { background: var(--color-bg); margin: 0; padding: 0; }
        .page-container { max-width: 1152px; margin: 0 auto; padding: 1.5rem 1rem; }
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .page-title {
            font-size: 1.25rem; font-weight: 700; color: var(--color-text);
            display: flex; align-items: center; gap: 0.5rem;
        }
        .section-header {
            font-size: 0.75rem; font-weight: 700; letter-spacing: 0.06em;
            text-transform: uppercase; margin-bottom: 0.5rem;
            display: flex; align-items: center; gap: 0.375rem;
        }
        .section-header-points { color: var(--color-blue); }
        .section-header-polylines { color: var(--color-green); }
        .section-header-polygons { color: var(--color-purple); }

        .table-wrap {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            overflow-x: auto;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }
        [data-theme="dark"] .table-wrap {
            box-shadow: 0 2px 12px rgba(0,0,0,0.3);
        }
        table { width: 100%; font-size: 0.8125rem; border-collapse: collapse; }
        thead th {
            text-align: left; padding: 0.625rem 1rem;
            font-size: 0.6875rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.04em;
            color: var(--color-text-muted);
            background: var(--color-table-header-bg);
            border-bottom: 1px solid var(--color-border);
        }
        tbody td {
            padding: 0.625rem 1rem;
            color: var(--color-text-secondary);
            border-bottom: 1px solid var(--color-border-light);
            white-space: nowrap;
        }
        tbody tr:hover { background: var(--color-table-row-hover); }
        tbody tr:last-child td { border-bottom: none; }
        .cell-name { font-weight: 500; color: var(--color-text); max-width: 160px; overflow: hidden; text-overflow: ellipsis; }
        .cell-id { color: var(--color-text-muted); font-size: 0.75rem; }
        .cell-desc { max-width: 200px; overflow: hidden; text-overflow: ellipsis; color: var(--color-text-muted); }
        .cell-coords { max-width: 280px; overflow: hidden; text-overflow: ellipsis; font-size: 0.6875rem; font-family: monospace; }
        .cell-image img { width: 48px; height: 32px; object-fit: cover; border-radius: 4px; }
        .badge-empty { font-size: 0.75rem; color: var(--color-text-muted); padding: 1rem; text-align: center; }

        .nav-back {
            color: var(--color-accent); font-size: 0.8125rem; font-weight: 500;
            text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;
            transition: color 0.15s;
        }
        .nav-back:hover { color: var(--color-accent-hover); }

        .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
        .dot-blue { background: var(--color-blue); }
        .dot-green { background: var(--color-green); }
        .dot-purple { background: var(--color-purple); }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="page-header">
            <div style="display:flex; align-items:center; gap:0.5rem;">
                @include('partials.user-avatar', ['size' => 36])
                <div>
                    <h1 class="page-title" style="margin-bottom:0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-shield)" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Saved Geo Features
                    </h1>
                    <p style="font-size:0.75rem; color:var(--color-text-muted);">{{ Auth::user()->name }} &middot; {{ Auth::user()->email }}</p>
                </div>
            </div>
            <a href="{{ url('/map') }}" class="nav-back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Back to Map
            </a>
        </div>

        {{-- Points --}}
        <div style="margin-bottom: 2rem;">
            <h2 class="section-header section-header-points">
                <span class="dot dot-blue"></span> Points
            </h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody id="pointsBody">
                        <tr><td colspan="7"><span class="badge-empty">Loading...</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Polylines --}}
        <div style="margin-bottom: 2rem;">
            <h2 class="section-header section-header-polylines">
                <span class="dot dot-green"></span> Polylines
            </h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Vertices</th>
                            <th>Coordinates</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody id="polylinesBody">
                        <tr><td colspan="7"><span class="badge-empty">Loading...</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Polygons --}}
        <div>
            <h2 class="section-header section-header-polygons">
                <span class="dot dot-purple"></span> Polygons
            </h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Vertices</th>
                            <th>Coordinates</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody id="polygonsBody">
                        <tr><td colspan="7"><span class="badge-empty">Loading...</span></td></tr>
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
                            <td class="cell-id">#${p.id}</td>
                            <td class="cell-name">${esc(p.name)}</td>
                            <td>${p.latitude}</td>
                            <td>${p.longitude}</td>
                            <td class="cell-desc">${p.description || '-'}</td>
                            <td class="cell-image">${p.image_path ? '<img src="/files/'+p.image_path+'">' : '-'}</td>
                            <td class="cell-id">${new Date(p.created_at).toLocaleDateString()}</td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="7"><span class="badge-empty">No points saved.</span></td></tr>';
                }
                document.getElementById('pointsBody').innerHTML = html;

                // Polylines
                html = '';
                if (data.polylines.length) {
                    data.polylines.forEach(p => {
                        const coordsStr = p.coordinates.map(c => `${c.lat.toFixed(4)},${c.lng.toFixed(4)}`).join('; ');
                        html += `<tr>
                            <td class="cell-id">#${p.id}</td>
                            <td class="cell-name">${esc(p.name)}</td>
                            <td>${p.coordinates.length}</td>
                            <td class="cell-coords">${coordsStr}</td>
                            <td class="cell-desc">${p.description || '-'}</td>
                            <td class="cell-image">${p.image_path ? '<img src="/files/'+p.image_path+'">' : '-'}</td>
                            <td class="cell-id">${new Date(p.created_at).toLocaleDateString()}</td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="7"><span class="badge-empty">No polylines saved.</span></td></tr>';
                }
                document.getElementById('polylinesBody').innerHTML = html;

                // Polygons
                html = '';
                if (data.polygons.length) {
                    data.polygons.forEach(p => {
                        const coordsStr = p.coordinates.map(c => `${c.lat.toFixed(4)},${c.lng.toFixed(4)}`).join('; ');
                        html += `<tr>
                            <td class="cell-id">#${p.id}</td>
                            <td class="cell-name">${esc(p.name)}</td>
                            <td>${p.coordinates.length}</td>
                            <td class="cell-coords">${coordsStr}</td>
                            <td class="cell-desc">${p.description || '-'}</td>
                            <td class="cell-image">${p.image_path ? '<img src="/files/'+p.image_path+'">' : '-'}</td>
                            <td class="cell-id">${new Date(p.created_at).toLocaleDateString()}</td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="7"><span class="badge-empty">No polygons saved.</span></td></tr>';
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
