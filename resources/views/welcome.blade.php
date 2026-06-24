<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MaiGuard</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.theme')
    <style>
        /* ---- Map Preview ---- */
        #preview-map { height: 360px; width: 100%; border-radius: var(--radius-lg); border: 1px solid var(--color-border); }
        .leaflet-control-zoom { margin-top: 12px !important; margin-left: 12px !important; }
        .leaflet-control-zoom a { background: var(--color-surface) !important; border-color: var(--color-border) !important; color: var(--color-text-secondary) !important; }
        .leaflet-control-zoom a:hover { background: var(--color-surface-hover) !important; color: var(--color-accent) !important; }
        .leaflet-control-attribution { background: var(--color-surface) !important; color: var(--color-text-muted) !important; font-size: 0.6rem !important; padding: 1px 5px !important; border-radius: 3px !important; border: 1px solid var(--color-border) !important; }
        .leaflet-control-attribution a { color: var(--color-accent) !important; }

        /* ---- Hero ---- */
        .hero-gradient {
            background: radial-gradient(ellipse 80% 60% at 50% 0%, var(--color-accent-soft) 0%, transparent 60%);
        }
        [data-theme="dark"] .hero-gradient {
            background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(225,29,72,0.06) 0%, transparent 60%),
                        radial-gradient(ellipse 40% 40% at 80% 80%, rgba(225,29,72,0.03) 0%, transparent 50%);
        }
        .shield-glow-pulse {
            animation: shieldPulse 3s ease-in-out infinite;
        }
        @keyframes shieldPulse {
            0%, 100% { filter: drop-shadow(0 0 8px var(--color-shield-glow)); }
            50% { filter: drop-shadow(0 0 20px var(--color-shield-glow)); }
        }
        .hero-icon-ring {
            width: 80px; height: 80px; border-radius: 50%;
            background: linear-gradient(135deg, var(--color-accent-soft), var(--color-purple-soft));
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 32px var(--color-shield-glow);
        }

        /* ---- Glass Stat Cards ---- */
        .stat-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 1.25rem 1rem 1rem;
            text-align: center; transition: all 0.3s ease;
            position: relative; overflow: hidden;
        }
        .stat-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
            transition: height 0.3s ease;
        }
        .stat-card-spots::before { background: var(--color-blue); }
        .stat-card-routes::before { background: var(--color-orange); }
        .stat-card-areas::before { background: var(--color-purple); }
        .stat-card:hover { transform: translateY(-2px); }
        [data-theme="dark"] .stat-card:hover {
            border-color: rgba(225,29,72,0.15);
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        }
        [data-theme="light"] .stat-card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        .stat-card:hover::before { height: 3px; }
        .stat-number {
            font-size: 2rem; font-weight: 800; line-height: 1.1;
        }
        .stat-icon { font-size: 1.25rem; margin-bottom: 0.25rem; }

        /* ---- CTA Button ---- */
        .btn-cta {
            display: inline-flex; align-items: center; gap: 0.625rem;
            padding: 0.875rem 2rem;
            background: linear-gradient(135deg, var(--color-accent), var(--color-purple));
            color: #fff; border-radius: var(--radius-md);
            font-weight: 600; font-size: 0.9375rem;
            text-decoration: none; transition: all 0.3s ease;
            box-shadow: 0 2px 16px var(--color-shield-glow);
        }
        .btn-cta:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 24px var(--color-shield-glow);
        }

        /* ---- Section ---- */
        .section-divider {
            max-width: 1152px; margin: 0 auto; padding: 0 1.5rem; width: 100%;
        }
        .section-divider hr { border: none; border-top: 1px solid var(--color-border); }
        .map-frame {
            background: linear-gradient(180deg, var(--color-surface), var(--color-bg));
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 0.5rem;
            box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        }
        [data-theme="dark"] .map-frame { box-shadow: 0 4px 24px rgba(0,0,0,0.3); }
        .map-frame-header {
            display: flex; align-items: center; gap: 0.375rem;
            padding: 0.375rem 0.5rem 0.5rem;
            font-size: 0.6875rem; color: var(--color-text-muted);
            font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em;
        }
        .map-frame-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
        .map-frame-dot.r { background: #ef4444; } .map-frame-dot.y { background: #eab308; } .map-frame-dot.g { background: #22c55e; }

        /* ---- Nav ---- */
        .nav-link {
            padding: 0.5rem 1rem; border-radius: var(--radius-sm);
            font-size: 0.875rem; font-weight: 500; transition: all 0.15s ease; text-decoration: none;
        }
        .nav-link-ghost { color: var(--color-text-secondary); }
        .nav-link-ghost:hover { background: var(--color-surface-hover); color: var(--color-text); }
        .nav-link-accent { background: var(--color-accent); color: #fff; }
        .nav-link-accent:hover { background: var(--color-accent-hover); }
    </style>
</head>
<body style="background: var(--color-bg); min-height: 100vh; display: flex; flex-direction: column;">

    {{-- Header --}}
    <header style="background: var(--color-header-bg); border-bottom: 1px solid var(--color-header-border); padding: 0.75rem 1.5rem; position: sticky; top:0; z-index:10; backdrop-filter:blur(12px);">
        <div style="max-width:1152px; margin:0 auto; display:flex; align-items:center; justify-content:space-between;">
            <a href="{{ url('/') }}" style="display:flex; align-items:center; gap:0.5rem; text-decoration:none;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--color-shield)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shield-glow-pulse"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span style="font-weight:700; font-size:1rem; color:var(--color-text);">MaiGuard</span>
                <span style="font-size:0.625rem; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; padding:0.125rem 0.5rem; border-radius:9999px; background:var(--color-accent-soft); color:var(--color-accent-text);">Keamanan</span>
            </a>
            <nav style="display:flex; align-items:center; gap:0.5rem;">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" style="display:flex; align-items:center; gap:0.5rem; text-decoration:none; padding:0.25rem 0.5rem; border-radius:var(--radius-sm); transition:background 0.15s;" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='transparent'">
                            @include('partials.user-avatar', ['size' => 28])
                            <span style="font-size:0.8125rem; color:var(--color-text); font-weight:500;">{{ Auth::user()->name }}</span>
                        </a>
                        <a href="{{ url('/map') }}" class="nav-link nav-link-accent">Map</a>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="nav-link nav-link-ghost" style="cursor:pointer; background:none; border:1px solid var(--color-border); font-size:0.75rem;">Log out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="nav-link nav-link-ghost" style="border:1px solid var(--color-border);">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="nav-link nav-link-accent">Register</a>
                        @endif
                    @endauth
                @endif
            </nav>
        </div>
    </header>

    {{-- Hero --}}
    <main class="hero-gradient" style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:3rem 1rem 2rem; background-image:url('/images/hero-bg.jpg'); background-size:cover; background-position:center; background-repeat:no-repeat; position:relative;">
        {{-- Dark overlay --}}
        <div style="position:absolute; inset:0; background:linear-gradient(180deg, rgba(15,10,10,0.85) 0%, rgba(15,10,10,0.7) 50%, rgba(15,10,10,0.9) 100%);"></div>
        <div style="position:relative; z-index:1; display:flex; flex-direction:column; align-items:center;">
        <div class="hero-icon-ring" style="margin-bottom:1.5rem;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--color-shield)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>
            </svg>
        </div>

        <h1 style="font-size:2.5rem; font-weight:800; color:var(--color-text); letter-spacing:-0.03em; margin-bottom:0.5rem; text-align:center; line-height:1.15;">
            Aktivitas Kriminal<br><span style="background:linear-gradient(135deg,var(--color-accent),var(--color-purple)); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">& Mencurigakan</span>
        </h1>
        <p style="color:var(--color-text-secondary); font-size:0.9375rem; margin-bottom:2.5rem; text-align:center; max-width:440px; line-height:1.6;">
            Pemantauan keamanan geospasial di Lombok Nusra — visualisasikan ancaman, lacak insiden, tetap waspada.
        </p>

        {{-- Stats --}}
        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:0.75rem; margin-bottom:2rem; max-width:480px; width:100%;">
            <div class="stat-card stat-card-spots">
                <div class="stat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-blue)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
                <div class="stat-number" style="color:var(--color-blue);" id="countSpots">{{ $pointCount }}</div>
                <div style="font-size:0.6875rem; color:var(--color-text-muted); margin-top:0.125rem; text-transform:uppercase; letter-spacing:0.06em; font-weight:600;">Spots</div>
            </div>
            <div class="stat-card stat-card-routes">
                <div class="stat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-orange)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 2 15 10 8 6 2 14"/></svg></div>
                <div class="stat-number" style="color:var(--color-orange);" id="countRoutes">{{ $polylineCount }}</div>
                <div style="font-size:0.6875rem; color:var(--color-text-muted); margin-top:0.125rem; text-transform:uppercase; letter-spacing:0.06em; font-weight:600;">Routes</div>
            </div>
            <div class="stat-card stat-card-areas">
                <div class="stat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-purple)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg></div>
                <div class="stat-number" style="color:var(--color-purple);" id="countAreas">{{ $polygonCount }}</div>
                <div style="font-size:0.6875rem; color:var(--color-text-muted); margin-top:0.125rem; text-transform:uppercase; letter-spacing:0.06em; font-weight:600;">Areas</div>
            </div>
        </div>

        <a href="{{ url('/map') }}" class="btn-cta">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            Open Security Map
        </a>
        </div>
    </main>

    {{-- Map Preview --}}
    <section style="max-width:1152px; margin:0 auto 2.5rem; padding:0 1.5rem; width:100%;">
        <div class="map-frame">
            <div class="map-frame-header">
                <span class="map-frame-dot r"></span><span class="map-frame-dot y"></span><span class="map-frame-dot g"></span>
                <span style="margin-left:0.5rem;">Live Preview — Lombok</span>
            </div>
            <div id="preview-map"></div>
        </div>
    </section>

    {{-- Footer --}}
    <footer style="text-align:center; padding:1.5rem; color:var(--color-text-muted); font-size:0.6875rem; border-top:1px solid var(--color-border);">
        <div style="display:flex; align-items:center; justify-content:center; gap:0.5rem; margin-bottom:0.375rem;">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>&copy; {{ date('Y') }} MaiGuard</span>
            @include('partials.version')
        </div>
        <div style="max-width:36rem; margin:0.5rem auto 0; font-size:0.6rem; line-height:1.5; position:relative; height:2rem;">
            <p class="legal-fade" style="position:absolute; inset:0; animation-delay:0s;">
                This project is open source for academic purposes only. I am not responsible for your data.
            </p>
            <p class="legal-fade" style="position:absolute; inset:0; animation-delay:4s;">
                Proyek ini open source untuk keperluan akademik. Saya tidak bertanggung jawab atas data Anda.
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
                        const coords = typeof p.coordinates === 'string' ? JSON.parse(p.coordinates) : p.coordinates;
                        L.polyline(coords.map(c => [c.lat, c.lng]), { color: c, weight: 3, opacity: 0.85 })
                            .bindPopup(`<b>${p.name || 'Route'}</b>${p.crime_type ? `<br><span style="font-size:11px;">● ${p.crime_type}</span>` : ''}`)
                            .addTo(map);
                    });
                    (data.polygons || []).forEach(p => {
                        const c = crimeColor(p.crime_type);
                        const coords = typeof p.coordinates === 'string' ? JSON.parse(p.coordinates) : p.coordinates;
                        L.polygon(coords.map(c => [c.lat, c.lng]), { color: c, fillColor: c, fillOpacity: 0.15, weight: 2 })
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
