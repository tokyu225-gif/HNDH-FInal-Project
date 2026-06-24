<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>wow very bad guy is here</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.theme')
    <style>
        #preview-map { height: 380px; width: 100%; border-radius: var(--radius-lg); border: 1px solid var(--color-border); }
        .leaflet-control-zoom { margin-top: 12px !important; margin-left: 12px !important; }
        .leaflet-control-zoom a {
            background: var(--color-surface) !important;
            border-color: var(--color-border) !important;
            color: var(--color-text-secondary) !important;
        }
        .leaflet-control-zoom a:hover {
            background: var(--color-surface-hover) !important;
            color: var(--color-accent) !important;
        }
        .leaflet-control-attribution {
            background: var(--color-surface) !important;
            color: var(--color-text-muted) !important;
            font-size: 0.6rem !important;
            padding: 1px 5px !important;
            border-radius: 3px !important;
            border: 1px solid var(--color-border) !important;
        }
        .leaflet-control-attribution a { color: var(--color-accent) !important; }
        .map-section-header {
            display: flex; align-items: center; gap: 0.5rem;
            font-size: 0.8125rem; font-weight: 600; color: var(--color-text-muted);
            text-transform: uppercase; letter-spacing: 0.06em;
            margin-bottom: 0.625rem;
        }
        .map-section-header svg { color: var(--color-accent); }
        .stat-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 1.25rem 1.5rem;
            text-align: center;
            transition: all 0.25s ease;
        }
        [data-theme="dark"] .stat-card:hover {
            border-color: rgba(6, 182, 212, 0.3);
            box-shadow: 0 4px 24px rgba(6, 182, 212, 0.08);
            transform: translateY(-1px);
        }
        [data-theme="light"] .stat-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            transform: translateY(-1px);
        }
        .btn-map {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.75rem;
            background: var(--color-accent);
            color: #ffffff;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-map:hover {
            background: var(--color-accent-hover);
            box-shadow: 0 4px 16px var(--color-shield-glow);
        }
        .nav-link {
            padding: 0.5rem 1rem;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s ease;
            text-decoration: none;
        }
        .nav-link-login {
            color: var(--color-text-secondary);
            border: 1px solid var(--color-border);
        }
        .nav-link-login:hover {
            background: var(--color-surface-hover);
            color: var(--color-text);
        }
        .nav-link-primary {
            background: var(--color-accent);
            color: #ffffff;
        }
        .nav-link-primary:hover {
            background: var(--color-accent-hover);
        }
    </style>
</head>
<body style="background: var(--color-bg); min-height: 100vh; display: flex; flex-direction: column;">

    {{-- Header / Nav --}}
    <header style="background: var(--color-header-bg); border-bottom: 1px solid var(--color-header-border); padding: 0.75rem 1.5rem; position: relative; z-index: 10;">
        <div style="max-width: 1152px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between;">
            <a href="{{ url('/') }}" style="display: flex; align-items: center; gap: 0.625rem; text-decoration: none;">
                <svg class="shield-icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-shield)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                <span style="font-weight: 700; font-size: 1.125rem; color: var(--color-text); letter-spacing: -0.01em;">wow very bad guy is here</span>
                <span style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; padding: 0.125rem 0.5rem; border-radius: 9999px; background: var(--color-accent-soft); color: var(--color-accent-text);">Keamanan</span>
            </a>
            <nav style="display: flex; align-items: center; gap: 0.5rem;">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" style="display:flex; align-items:center; gap:0.5rem; text-decoration:none; padding:0.25rem 0.5rem; border-radius:var(--radius-sm); transition:background 0.15s;" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='transparent'">
                            @include('partials.user-avatar', ['size' => 28])
                            <span style="font-size:0.8125rem; color:var(--color-text); font-weight:500;">{{ Auth::user()->name }}</span>
                        </a>
                        <a href="{{ url('/map') }}" class="nav-link nav-link-primary">Map</a>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="nav-link nav-link-login" style="cursor:pointer; background:none; font-size:0.75rem;">Log out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="nav-link nav-link-login">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="nav-link nav-link-primary">Register</a>
                        @endif
                    @endauth
                @endif
            </nav>
        </div>
    </header>

    {{-- Hero --}}
    <main style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem 1rem;">
        {{-- Shield emblem --}}
        <div style="margin-bottom: 1.5rem; width: 72px; height: 72px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: var(--color-accent-soft);">
            <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="var(--color-shield)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <path d="M9 12l2 2 4-4"/>
            </svg>
        </div>

        <h1 style="font-size: 2.25rem; font-weight: 800; color: var(--color-text); letter-spacing: -0.02em; margin-bottom: 0.25rem; text-align: center;">
            Keamanan Geo Dashboard
        </h1>
        <p style="color: var(--color-text-secondary); font-size: 1rem; margin-bottom: 2rem; text-align: center; max-width: 480px; line-height: 1.6;">
            Monitor, map, and manage geospatial security features across Lombok with real-time threat visualization.
        </p>

        {{-- Stat Cards --}}
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 2rem; max-width: 540px; width: 100%;">
            <div class="stat-card">
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--color-blue);">{{ $pointCount }}</div>
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Spots</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--color-green);">{{ $polylineCount }}</div>
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Routes</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--color-purple);">{{ $polygonCount }}</div>
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Areas</div>
            </div>
        </div>

        {{-- CTA --}}
        <a href="{{ url('/map') }}" class="btn-map">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            Open Security Map
        </a>
    </main>

    {{-- Map Preview --}}
    <section style="max-width: 1152px; margin: 0 auto 2.5rem; padding: 0 1.5rem; width: 100%;">
        <div class="map-section-header">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 00-8 8c0 5.4 8 12 8 12s8-6.6 8-12a8 8 0 00-8-8z"/></svg>
            Live Map Preview — Lombok
        </div>
        <div id="preview-map"></div>
    </section>

    {{-- Footer --}}
    <footer style="text-align: center; padding: 1.5rem; color: var(--color-text-muted); font-size: 0.75rem;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>&copy; {{ date('Y') }} wow very bad guy is here</span>
            @include('partials.version')
        </div>
        <div style="margin-top: 0.75rem; max-width: 36rem; margin-left: auto; margin-right: auto; font-size: 0.65rem; line-height: 1.5; position: relative; height: 2.5rem;">
            <p class="legal-fade" style="position: absolute; inset: 0; animation-delay: 0s;">
                This project is open source and created for campus/academic purposes only. Unauthorized reproduction, distribution, or submission of this work as your own constitutes plagiarism and may result in academic disciplinary action and/or legal proceedings under applicable intellectual property laws.<br><strong>I am not responsible for your data.</strong>
            </p>
            <p class="legal-fade" style="position: absolute; inset: 0; animation-delay: 5s;">
                Proyek ini bersifat open source dan dibuat untuk keperluan kampus/akademik saja. Reproduksi, distribusi, atau penyerahan karya ini sebagai milik sendiri tanpa izin merupakan plagiarisme dan dapat mengakibatkan tindakan disiplin akademik dan/atau proses hukum berdasarkan undang-undang kekayaan intelektual yang berlaku.<br><strong>Saya tidak bertanggung jawab atas data Anda.</strong>
            </p>
        </div>
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    <script>
        (function() {
            const map = L.map('preview-map', {
                center: [-8.5333, 116.5333],
                zoom: 11,
                zoomControl: true,
                scrollWheelZoom: false,
                dragging: true,
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
                maxZoom: 19
            }).addTo(map);

            // Load geo features
            const crimeColors = { 'Theft':'#ef4444','Assault':'#f97316','Vandalism':'#eab308','Burglary':'#a855f7','Robbery':'#ec4899','Suspicious Activity':'#3b82f6','Drug-related':'#22c55e','Fraud':'#6366f1','Harassment':'#14b8a6','Other':'#6b7280' };
            function crimeColor(t) { return crimeColors[t] || '#6b7280'; }
            fetch('/api/geo-features')
                .then(res => res.json())
                .then(data => {
                    (data.points || []).forEach(p => {
                        const c = crimeColor(p.crime_type);
                        const icon = L.divIcon({ className:'crime-marker', html:`<div style="background:${c};width:12px;height:12px;border-radius:50%;border:2px solid #fff;box-shadow:0 1px 3px rgba(0,0,0,0.3);"></div>`, iconSize:[12,12], iconAnchor:[6,6] });
                        L.marker([p.latitude, p.longitude], { icon })
                            .bindPopup(`<b>${p.name || 'Spot'}</b>${p.crime_type ? `<br><span style="font-size:11px;">● ${p.crime_type}</span>` : ''}`)
                            .addTo(map);
                    });
                    (data.polylines || []).forEach(p => {
                        const c = crimeColor(p.crime_type);
                        L.polyline(p.coordinates.map(c => [c.lat, c.lng]), { color: c, weight: 3, opacity: 0.85 })
                            .bindPopup(`<b>${p.name || 'Route'}</b>${p.crime_type ? `<br><span style="font-size:11px;">● ${p.crime_type}</span>` : ''}`)
                            .addTo(map);
                    });
                    (data.polygons || []).forEach(p => {
                        const c = crimeColor(p.crime_type);
                        L.polygon(p.coordinates.map(c => [c.lat, c.lng]), { color: c, fillColor: c, fillOpacity: 0.15, weight: 2 })
                            .bindPopup(`<b>${p.name || 'Area'}</b>${p.crime_type ? `<br><span style="font-size:11px;">● ${p.crime_type}</span>` : ''}`)
                            .addTo(map);
                    });
                })
                .catch(() => {});

            // Load heatmap
            fetch('/api/heatmap')
                .then(res => res.json())
                .then(points => {
                    const heatData = points.map(p => [p.latitude, p.longitude, 0.6]);
                    L.heatLayer(heatData, {
                        radius: 28,
                        blur: 20,
                        max: 0.8,
                        minOpacity: 0.25,
                        gradient: {
                            0.0: 'blue',
                            0.3: 'cyan',
                            0.5: 'lime',
                            0.7: 'yellow',
                            0.85: 'orange',
                            1.0: 'red'
                        }
                    }).addTo(map);
                })
                .catch(() => {});
        })();
    </script>

    <style>
        .legal-fade {
            animation: legalFade 10s infinite;
            opacity: 0;
        }
        @keyframes legalFade {
            0%, 40%   { opacity: 1; }
            45%, 95%  { opacity: 0; }
            100%      { opacity: 1; }
        }
    </style>
</body>
</html>
