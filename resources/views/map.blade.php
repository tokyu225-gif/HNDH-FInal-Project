<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MaiGuard</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.theme')
    <style>
        #map { height: calc(100vh - 56px); width: 100%; }
        body { margin: 0; padding: 0; background: var(--color-bg); }
        .leaflet-control-zoom { margin-top: 60px !important; }

        [data-theme="dark"] .leaflet-control-zoom {
            background: var(--color-surface) !important;
            border: 1px solid var(--color-border) !important;
            border-radius: var(--radius-md) !important;
            box-shadow: 0 2px 12px rgba(0,0,0,0.4) !important; overflow: hidden;
        }
        [data-theme="dark"] .leaflet-control-zoom a {
            background: var(--color-surface) !important; border-bottom: 1px solid var(--color-border) !important;
            color: var(--color-text-secondary) !important; width: 32px !important; height: 32px !important;
            line-height: 32px !important; font-size: 16px !important; transition: all 0.15s ease !important;
        }
        [data-theme="dark"] .leaflet-control-zoom a:last-child { border-bottom: none !important; }
        [data-theme="dark"] .leaflet-control-zoom a:hover { background: var(--color-surface-hover) !important; color: var(--color-accent) !important; }
        [data-theme="dark"] .leaflet-draw-actions {
            background: var(--color-surface) !important; border: 1px solid var(--color-border) !important;
            border-radius: var(--radius-md) !important; box-shadow: 0 4px 20px rgba(0,0,0,0.5) !important;
        }
        [data-theme="dark"] .leaflet-draw-actions a {
            background: transparent !important; background-image: none !important; filter: none !important;
            color: var(--color-text-secondary) !important; font-size: 0.75rem !important; font-weight: 500 !important;
            text-decoration: none !important; padding: 5px 12px !important; border-radius: var(--radius-sm) !important;
            display: block !important; width: auto !important; height: auto !important; transition: all 0.15s ease !important;
        }
        [data-theme="dark"] .leaflet-draw-actions a:hover { background: var(--color-surface-hover) !important; color: var(--color-text) !important; }
        .leaflet-control-attribution { font-size: 0.5625rem !important; padding: 1px 5px !important; line-height: 1.3 !important; }
        [data-theme="dark"] .leaflet-control-attribution { background: rgba(0,0,0,0.45) !important; color: rgba(255,255,255,0.4) !important; border-radius: 2px !important; border: none !important; }
        [data-theme="dark"] .leaflet-control-attribution a { color: rgba(255,255,255,0.5) !important; }
        .map-toolbar {
            position: absolute; top: 0; left: 0; right: 0; z-index: 1000;
            background: var(--color-header-bg); backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--color-header-border);
            padding: 0.5rem 1rem; display: flex; align-items: center; justify-content: space-between;
        }
        [data-theme="dark"] .map-toolbar { box-shadow: 0 1px 8px rgba(225,29,72,0.06); }
        [data-theme="light"] .map-toolbar { box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        .toolbar-divider { width: 1px; height: 20px; background: var(--color-border); margin: 0 0.125rem; }
        .status-text { color: var(--color-text-muted); font-size: 0.75rem; }
        .icon-btn {
            display: flex; align-items: center; justify-content: center;
            width: 34px; height: 34px; border-radius: var(--radius-sm);
            border: none; background: transparent; cursor: pointer;
            color: var(--color-text-secondary); transition: all 0.15s ease;
            text-decoration: none; flex-shrink: 0;
        }
        .icon-btn:hover { color: var(--color-text); background: var(--color-surface-hover); }
        .icon-btn svg { width: 18px; height: 18px; flex-shrink: 0; }
        .icon-btn-save { color: var(--color-success); }
        .icon-btn-save:hover { background: var(--color-success-soft); color: var(--color-success); }
        .icon-btn-danger { color: var(--color-danger); }
        .icon-btn-danger:hover { background: var(--color-danger-soft); color: var(--color-danger); }
        .icon-btn-warning { color: var(--color-warning); }
        .icon-btn-warning:hover { background: var(--color-warning-soft); color: var(--color-warning); }
        .icon-btn-accent { color: var(--color-accent); }
        .icon-btn-accent:hover { background: var(--color-accent-soft); color: var(--color-accent); }
        .icon-btn-heat { color: #ef4444; background: #fef2f2; border: 1px solid #fecaca; }
        .icon-btn-heat:hover { background: #fee2e2; }
        .icon-btn-heat.active { color: #fff; background: #ef4444; border-color: #dc2626; animation: pulse-glow 1.5s ease-in-out infinite; }
        @keyframes pulse-glow { 0%,100% { box-shadow: 0 0 4px rgba(239,68,68,0.4); } 50% { box-shadow: 0 0 16px rgba(239,68,68,0.8); } }
        .icon-btn-route-active { color: #fff !important; background: var(--color-warning) !important; box-shadow: 0 0 14px rgba(245,158,11,0.6),0 0 6px rgba(245,158,11,0.3); }
        .icon-btn-route-active:hover { background: #d97706 !important; color: #fff !important; box-shadow: 0 0 18px rgba(245,158,11,0.7),0 0 8px rgba(245,158,11,0.4); }
        #drawMarkerBtn:hover { box-shadow: 0 0 12px rgba(37,99,235,0.5), 0 0 4px rgba(37,99,235,0.3); }
        #drawPolylineBtn:hover { box-shadow: 0 0 12px rgba(249,115,22,0.5), 0 0 4px rgba(249,115,22,0.3); }
        #drawPolygonBtn:hover { box-shadow: 0 0 12px rgba(34,197,94,0.5), 0 0 4px rgba(34,197,94,0.3); }
        #editLayersBtn:hover { box-shadow: 0 0 12px rgba(139,92,246,0.5), 0 0 4px rgba(139,92,246,0.3); }
        #deleteLayersBtn:hover { box-shadow: 0 0 12px rgba(239,68,68,0.5), 0 0 4px rgba(239,68,68,0.3); }
        #routeBtn:hover { box-shadow: 0 0 12px rgba(245,158,11,0.5), 0 0 4px rgba(245,158,11,0.3); }
        #heatmapBtn:hover { box-shadow: 0 0 12px rgba(239,68,68,0.5), 0 0 4px rgba(239,68,68,0.3); }
        .draw-active { color: var(--color-accent) !important; background: var(--color-accent-soft) !important; box-shadow: 0 0 14px rgba(225,29,72,0.5),0 0 6px rgba(225,29,72,0.25) !important; }
        .draw-panel {
            position: absolute; top: 9rem; left: 12px; z-index: 1000;
            background: var(--color-surface); border: 1px solid var(--color-border);
            border-radius: var(--radius-lg); box-shadow: var(--shadow-md);
            padding: 0.375rem; display: flex; flex-direction: column; gap: 0.125rem;
        }
        [data-theme="dark"] .draw-panel { box-shadow: 0 4px 20px rgba(0,0,0,0.4); }
        .draw-panel-btn {
            display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.625rem;
            border: none; background: transparent; border-radius: var(--radius-sm);
            cursor: pointer; color: var(--color-text-secondary);
            font-size: 0.75rem; font-weight: 500; font-family: inherit;
            transition: all 0.15s ease; white-space: nowrap;
        }
        .draw-panel-btn:hover { background: var(--color-surface-hover); color: var(--color-text); }
        .draw-panel-btn.draw-active { background: var(--color-accent-soft); color: var(--color-accent); }
        .draw-panel-btn svg { width: 18px; height: 18px; flex-shrink: 0; }
        .draw-panel-label { font-size: 0.625rem; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.06em; padding: 0.25rem 0.625rem 0.125rem; }
        .draw-panel-divider { height: 1px; background: var(--color-border); margin: 0.125rem 0.375rem; }
        #coords { position: absolute; bottom: 4px; left: 8px; z-index: 1000; background: var(--color-surface); color: var(--color-text-secondary); padding: 2px 8px; border-radius: 4px; font-size: 0.6875rem; font-family: monospace; border: 1px solid var(--color-border); }
        #saveModal { position: fixed; inset: 0; z-index: 2000; background: var(--color-modal-overlay); display: flex; align-items: center; justify-content: center; }
        #saveModal.hidden { display: none; }
        .modal-content { background: var(--color-modal-bg); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); width: 100%; max-width: 28rem; margin: 0 1rem; padding: 1.5rem; }
        .modal-content h2 { color: var(--color-text); font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; }
        .modal-content label { display: block; font-size: 0.8125rem; font-weight: 500; color: var(--color-text-secondary); margin-bottom: 0.25rem; }
        .modal-input { width: 100%; border: 1px solid var(--color-input-border); background: var(--color-input-bg); color: var(--color-input-text); border-radius: var(--radius-sm); padding: 0.5rem 0.75rem; font-size: 0.8125rem; margin-bottom: 0.75rem; outline: none; transition: border-color 0.15s ease; }
        .modal-input:focus { border-color: var(--color-input-focus-ring); box-shadow: 0 0 0 3px var(--color-shield-glow); }
        .modal-input::placeholder { color: var(--color-input-placeholder); }
        .btn-cancel { padding: 0.5rem 1rem; font-size: 0.8125rem; color: var(--color-text-secondary); border: 1px solid var(--color-border); background: transparent; border-radius: var(--radius-sm); cursor: pointer; }
        .btn-cancel:hover { background: var(--color-surface-hover); }
        .btn-submit { padding: 0.5rem 1rem; font-size: 0.8125rem; background: var(--color-accent); color: #fff; border: none; border-radius: var(--radius-sm); cursor: pointer; font-weight: 500; }
        .btn-submit:hover { background: var(--color-accent-hover); }
        .move-bar { position: absolute; top: 3.5rem; left: 50%; transform: translateX(-50%); z-index: 2000; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-md); box-shadow: var(--shadow-md); padding: 0.5rem 1rem; display: flex; align-items: center; gap: 0.75rem; }
        .move-bar.hidden { display: none; }
        .leaflet-popup-content-wrapper { border-radius: 14px !important; box-shadow: 0 10px 40px rgba(0,0,0,0.22) !important; padding: 0 !important; overflow: hidden; }
        .leaflet-popup-content { margin: 0 !important; min-width: 260px; max-width: 310px; }
        .leaflet-popup-close-button { top: 10px !important; right: 10px !important; width: 26px !important; height: 26px !important; background: rgba(0,0,0,0.35) !important; border-radius: 50% !important; font-size: 16px !important; line-height: 26px !important; color: #fff !important; z-index: 10; }
        .leaflet-popup-close-button:hover { background: rgba(0,0,0,0.55) !important; }
        .leaflet-popup-tip { box-shadow: 0 4px 10px rgba(0,0,0,0.12) !important; }
        .geo-popup { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .geo-popup .popup-image { width: 100%; height: 150px; object-fit: cover; display: block; }
        .geo-popup .popup-image-placeholder { width: 100%; height: 90px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%); display: flex; align-items: center; justify-content: center; gap: 8px; }
        .geo-popup .popup-image-placeholder span { color: rgba(255,255,255,0.85); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; }
        .geo-popup .popup-body { padding: 14px 16px 16px; background: #fff; }
        .geo-popup .popup-badges { display: flex; gap: 6px; margin-bottom: 10px; flex-wrap: wrap; }
        .geo-popup .popup-title { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 4px; line-height: 1.3; }
        .geo-popup .popup-type-badge { display: inline-flex; align-items: center; gap: 3px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; padding: 3px 10px; border-radius: 9999px; }
        .geo-popup .popup-type-badge.Point { background: #dbeafe; color: #1d4ed8; }
        .geo-popup .popup-type-badge.Polyline { background: #fef3c7; color: #b45309; }
        .geo-popup .popup-type-badge.Polygon { background: #d1fae5; color: #047857; }
        .geo-popup .popup-desc { font-size: 12px; color: #6b7280; line-height: 1.55; margin-top: 6px; margin-bottom: 14px; }
        .geo-popup .popup-actions { display: flex; gap: 8px; }
        .geo-popup .popup-btn { flex: 1; padding: 8px 0; border: none; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; text-align: center; transition: all 0.2s ease; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 4px; }
        .geo-popup .popup-btn-edit { background: #eff6ff; color: #2563eb; }
        .geo-popup .popup-btn-edit:hover { background: #2563eb; color: #fff; }
        .geo-popup .popup-btn-move { background: #f0fdf4; color: #16a34a; }
        .geo-popup .popup-btn-move:hover { background: #16a34a; color: #fff; }
        .geo-popup .popup-btn-delete { background: #fef2f2; color: #dc2626; }
        .geo-popup .popup-btn-delete:hover { background: #dc2626; color: #fff; }
        .popup-crime-badge { display: inline-flex; align-items: center; gap: 3px; background: #fef2f2; color: #dc2626; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.4px; }
        .popup-author { font-size: 11px; color: #9ca3af; font-weight: 500; margin-bottom: 2px; }

        /* ---- Bottom Bar ---- */
        #bottomBar { box-shadow: 0 -1px 6px rgba(0,0,0,0.08); }
        [data-theme="dark"] #bottomBar { box-shadow: 0 -1px 8px rgba(225,29,72,0.05); }
        .legend-dot {
            color: var(--color-text-muted); padding: 1px 4px; border-radius: 3px;
            cursor: pointer; transition: all 0.15s ease;
        }
        .legend-dot:hover { background: var(--color-surface-hover); color: var(--color-text); }
        .legend-dot.active { background: var(--color-accent-soft); color: var(--color-accent-text); }

        /* ---- Toast ---- */
        #toast {
            position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%);
            z-index: 3000; padding: 0.625rem 1.25rem;
            border-radius: var(--radius-md); font-size: 0.8125rem; font-weight: 500;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            opacity: 0; transition: opacity 0.25s ease, transform 0.25s ease;
            transform: translateX(-50%) translateY(10px);
            pointer-events: none;
        }
        #toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
        #toast.success { background: #065f46; color: #d1fae5; }
        #toast.error { background: #7f1d1d; color: #fee2e2; }
        #toast.info { background: #1e3a5f; color: #dbeafe; }
    </style>
</head>
<body>
    {{-- Toolbar --}}
    <div class="map-toolbar">
        <div style="display: flex; align-items: center; gap: 0.375rem;">
            {{-- Home / Site Name --}}
            <a href="{{ url('/') }}" class="icon-btn icon-btn-accent" style="text-decoration:none; width:auto; padding:0 0.625rem; gap:0.375rem; font-weight:700; font-size:0.8125rem; color:var(--color-text);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>MaiGuard</span>
            </a>
            <span id="statusMsg" class="status-text" style="margin-left:0.25rem;"></span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.125rem;">
            @auth
            <a href="{{ route('profile.edit') }}" style="display:flex; align-items:center; gap:0.375rem; font-size:0.75rem; color:var(--color-text-secondary); margin-right:0.375rem; text-decoration:none; padding:0.125rem 0.375rem; border-radius:var(--radius-sm); transition:all 0.15s ease;" onmouseover="this.style.background='var(--color-surface-hover)'; this.style.color='var(--color-text)';" onmouseout="this.style.background='transparent'; this.style.color='var(--color-text-secondary)';">
                @include('partials.user-avatar', ['size' => 26])
                <span style="font-weight:500;">{{ Auth::user()->name }}</span>
            </a>
            <span class="toolbar-divider"></span>
            {{-- Table --}}
            <a href="{{ url('/table') }}" class="icon-btn" style="text-decoration:none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
            </a>
            {{-- Route --}}
            <button id="routeBtn" onclick="toggleRouting()" class="icon-btn icon-btn-warning">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="5" r="2"/><circle cx="19" cy="19" r="2"/><path d="M5 7v5a3 3 0 003 3h8a3 3 0 013 3v1"/></svg>
            </button>
            @endauth
            {{-- Basemap toggle --}}
            <button id="basemapBtn" onclick="toggleBasemap()" class="icon-btn" title="Switch basemap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
            </button>
            {{-- Heatmap (public) --}}
            <button id="heatmapBtn" onclick="toggleHeatmap()" class="icon-btn icon-btn-heat">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2c-4 4-8 7-8 12a8 8 0 0016 0c0-5-4-8-8-12z"/><path d="M12 6v6"/><circle cx="12" cy="16" r="1" fill="currentColor" stroke="none"/></svg>
            </button>
            @auth
            {{-- Settings --}}
            <a href="{{ url('/settings') }}" class="icon-btn" style="text-decoration:none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
            </a>
            @endauth
        </div>
    </div>

    {{-- Floating Draw Panel --}}
    @auth
    <div class="draw-panel">
        <span class="draw-panel-label">Draw</span>
        <button id="drawMarkerBtn" class="draw-panel-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Marker
        </button>
        <button id="drawPolylineBtn" class="draw-panel-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 2 15 10 8 6 2 14"/></svg>
            Polyline
        </button>
        <button id="drawPolygonBtn" class="draw-panel-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            Polygon
        </button>
        <span class="draw-panel-divider"></span>
        <button id="editLayersBtn" class="draw-panel-btn" style="display:none;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Shape
        </button>
        <button id="deleteLayersBtn" class="draw-panel-btn icon-btn-danger" style="display:none; color:var(--color-danger);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
            Delete
        </button>
    </div>
    @endauth

    <div id="map"></div>

    {{-- Bottom Bar --}}
    <div id="bottomBar" style="position:absolute; bottom:0; left:0; right:0; z-index:1000; background:var(--color-header-bg); border-top:1px solid var(--color-header-border); padding:0.375rem 0.75rem; display:flex; align-items:center; justify-content:space-between; font-size:0.6875rem; backdrop-filter:blur(8px);">
        {{-- Legend (left) --}}
        <div id="legendBar" style="display:flex; align-items:center; gap:0.5rem; overflow-x:auto; white-space:nowrap; scrollbar-width:none;">
            <span style="color:var(--color-text-muted); font-weight:600; text-transform:uppercase; letter-spacing:0.04em; flex-shrink:0;">Legend</span>
            <span class="legend-dot" data-type="Theft" style="display:inline-flex;align-items:center;gap:3px;"><span style="width:7px;height:7px;border-radius:50%;background:#ef4444;flex-shrink:0;"></span>Theft</span>
            <span class="legend-dot" data-type="Assault" style="display:inline-flex;align-items:center;gap:3px;"><span style="width:7px;height:7px;border-radius:50%;background:#f97316;flex-shrink:0;"></span>Assault</span>
            <span class="legend-dot" data-type="Vandalism" style="display:inline-flex;align-items:center;gap:3px;"><span style="width:7px;height:7px;border-radius:50%;background:#eab308;flex-shrink:0;"></span>Vandalism</span>
            <span class="legend-dot" data-type="Burglary" style="display:inline-flex;align-items:center;gap:3px;"><span style="width:7px;height:7px;border-radius:50%;background:#a855f7;flex-shrink:0;"></span>Burglary</span>
            <span class="legend-dot" data-type="Robbery" style="display:inline-flex;align-items:center;gap:3px;"><span style="width:7px;height:7px;border-radius:50%;background:#ec4899;flex-shrink:0;"></span>Robbery</span>
            <span class="legend-dot" data-type="Suspicious Activity" style="display:inline-flex;align-items:center;gap:3px;"><span style="width:7px;height:7px;border-radius:50%;background:#3b82f6;flex-shrink:0;"></span>Suspicious</span>
            <span class="legend-dot" data-type="Drug-related" style="display:inline-flex;align-items:center;gap:3px;"><span style="width:7px;height:7px;border-radius:50%;background:#22c55e;flex-shrink:0;"></span>Drug</span>
            <span class="legend-dot" data-type="Other" style="display:inline-flex;align-items:center;gap:3px;"><span style="width:7px;height:7px;border-radius:50%;background:#6b7280;flex-shrink:0;"></span>Other</span>
        </div>

        {{-- Recent feed (right) --}}
        <div style="display:flex;align-items:center;gap:0.625rem;flex-shrink:0;">
            <div id="recentFeed" style="color:var(--color-text-secondary); font-size:0.6875rem; max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                Loading features...
            </div>
            <span style="color:var(--color-text-muted);">|</span>
            <span id="coords" style="color:var(--color-text-muted); font-family:monospace; font-size:0.625rem; margin:0; padding:0; border:none; background:none; position:static;">Move mouse over map</span>
            @include('partials.version')
        </div>
    </div>

    {{-- Old coords (hidden, referenced by JS) --}}
    <span id="coordsLegacy" style="display:none;"></span>

    {{-- Toast --}}
    <div id="toast"></div>

    {{-- Save / Edit Modal --}}
    <div id="saveModal" class="hidden">
        <div class="modal-content">
            <h2 id="modalTitle">Save Geo Feature</h2>
            <form id="saveForm" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" id="editId">
                <input type="hidden" name="edit_type" id="editType">
                <input type="hidden" name="geometry_type" id="geomType">
                <input type="hidden" name="geometry_data" id="geomData">

                <label>Name</label>
                <input type="text" name="name" id="featName" required class="modal-input" placeholder="Feature name">

                <label>Description</label>
                <textarea name="description" id="featDesc" rows="3" class="modal-input" placeholder="Describe this feature"></textarea>

                <label>Crime Type</label>
                <select name="crime_type" id="featCrimeType" class="modal-input">
                    <option value="">-- Select crime type --</option>
                    <option value="Theft">Theft</option>
                    <option value="Assault">Assault</option>
                    <option value="Vandalism">Vandalism</option>
                    <option value="Burglary">Burglary</option>
                    <option value="Robbery">Robbery</option>
                    <option value="Suspicious Activity">Suspicious Activity</option>
                    <option value="Drug-related">Drug-related</option>
                    <option value="Fraud">Fraud</option>
                    <option value="Harassment">Harassment</option>
                    <option value="Other">Other</option>
                </select>

                <label>Image (optional)</label>
                <input type="file" name="image" id="featImage" accept="image/*" class="modal-input" style="padding:0.375rem;" onchange="previewImage(this)">
                <img id="featImagePreview" style="display:none; margin-top:0.5rem; width:100%; height:6rem; object-fit:cover; border-radius:var(--radius-sm);">

                <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-top:1.25rem;">
                    <button type="button" onclick="closeSaveModal()" class="btn-cancel">Cancel</button>
                    <button type="submit" id="modalSubmitBtn" class="btn-submit">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Move/Edit Shape Action Bar --}}
    <div id="moveBar" class="move-bar hidden">
        <span style="font-size:0.8125rem; color: var(--color-text-secondary);">Drag vertices to reshape</span>
        <button onclick="cancelShapeEdit()" class="btn-cancel">Cancel</button>
        <button onclick="saveShapeEdit()" class="btn-submit">Save</button>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    <script>
        const isAuthenticated = @json(auth()->check());

        // ======================== INISIALISASI PETA ========================
        const map = L.map('map').setView([-8.5333, 116.5333], 11);

        // Basemap: OpenStreetMap (default) dan Esri Satellite
        const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);

        const esriLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
            maxZoom: 19
        });

        let basemapSatellite = false;
        function toggleBasemap() {
            basemapSatellite = !basemapSatellite;
            const btn = document.getElementById('basemapBtn');
            if (basemapSatellite) {
                map.removeLayer(osmLayer);
                map.addLayer(esriLayer);
                btn.style.color = 'var(--color-accent)';
                btn.style.background = 'var(--color-accent-soft)';
                btn.title = 'Switch to Street Map';
            } else {
                map.removeLayer(esriLayer);
                map.addLayer(osmLayer);
                btn.style.color = 'var(--color-text-secondary)';
                btn.style.background = 'transparent';
                btn.title = 'Switch to Satellite';
            }
        }

        // ======================== KOORDINAT MOUSE ========================
        // Tampilkan koordinat real-time saat mouse bergerak di atas peta
        map.on('mousemove', function(e) {
            document.getElementById('coords').textContent =
                `Lat: ${e.latlng.lat.toFixed(5)} | Lng: ${e.latlng.lng.toFixed(5)}`;
        });

        // ======================== LAYER GAMBAR (DRAW) ========================
        // Layer untuk fitur yang sedang digambar (belum disimpan)
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
        // Sembunyikan toolbar default Leaflet.draw, kita pakai custom UI
        map.addControl(drawControl);
        drawControl._container.style.display = 'none';

        // ---- Tombol Gambar Custom di Draw Panel ----
        let activeDraw = null; // handler gambar yang sedang aktif

        // Batalkan mode gambar yang sedang aktif
        function cancelActiveDraw() {
            if (activeDraw) { activeDraw.disable(); activeDraw = null; }
            document.querySelectorAll('#drawMarkerBtn, #drawPolylineBtn, #drawPolygonBtn').forEach(b => b.classList.remove('draw-active'));
        }

        // Mulai mode gambar (Marker, Polyline, atau Polygon)
        function startDraw(type) {
            cancelActiveDraw();
            const opts = drawControl.options.draw;
            let handler;
            if (type === 'marker') handler = new L.Draw.Marker(map, opts.marker);
            else if (type === 'polyline') handler = new L.Draw.Polyline(map, opts.polyline);
            else if (type === 'polygon') handler = new L.Draw.Polygon(map, opts.polygon);
            if (!handler) return;
            handler.enable();
            activeDraw = handler;
            document.getElementById(type === 'marker' ? 'drawMarkerBtn' : type === 'polyline' ? 'drawPolylineBtn' : 'drawPolygonBtn').classList.add('draw-active');
        }

        document.getElementById('drawMarkerBtn')?.addEventListener('click', () => startDraw('marker'));
        document.getElementById('drawPolylineBtn')?.addEventListener('click', () => startDraw('polyline'));
        document.getElementById('drawPolygonBtn')?.addEventListener('click', () => startDraw('polygon'));

        // Show/hide edit buttons when layers change
        function updateEditButtons() {
            const has = drawnItems.getLayers().length > 0;
            document.getElementById('editLayersBtn').style.display = has ? '' : 'none';
            document.getElementById('deleteLayersBtn').style.display = has ? '' : 'none';
        }
        drawnItems.on('layeradd', updateEditButtons);
        drawnItems.on('layerremove', updateEditButtons);
        map.on('draw:deleted', updateEditButtons);

        document.getElementById('editLayersBtn')?.addEventListener('click', () => {
            if (drawnItems.getLayers().length === 0) return;
            drawControl._toolbars.edit._modes.edit.handler.enable();
        });
        document.getElementById('deleteLayersBtn')?.addEventListener('click', () => {
            if (drawnItems.getLayers().length === 0) return;
            drawControl._toolbars.edit._modes.remove.handler.enable();
        });

        // Event listener: saat user selesai menggambar, buka modal simpan
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

        // ---- Beaches (auto-load from settings) ----
        @if($appSettings['show_beaches'] ?? false)
        (function loadBeaches() {
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
                    });
                });
        })();
        @endif

        // ---- Load saved geo features ----
        const geoFeatureLayers = [];
        let isFocusMode = false;

        @if(isset($focusFeature))
        // Focus on a single feature for editing/moving
        isFocusMode = true;
        (function focusOnFeature() {
            const f = @json($focusFeature);
            let layer;
            if (f.type === 'Point') {
                layer = L.marker([f.data.latitude, f.data.longitude], { draggable: true });
            } else if (f.type === 'Polyline') {
                const coords = typeof f.data.coordinates === 'string' ? JSON.parse(f.data.coordinates) : f.data.coordinates;
                layer = L.polyline(coords.map(c => [c.lat, c.lng]), { color: '#3388ff' });
            } else if (f.type === 'Polygon') {
                const coords = typeof f.data.coordinates === 'string' ? JSON.parse(f.data.coordinates) : f.data.coordinates;
                layer = L.polygon(coords.map(c => [c.lat, c.lng]), { color: '#3388ff' });
            }
            if (layer) {
                layer.addTo(map);
                if (f.type === 'Point') {
                    map.setView([f.data.latitude, f.data.longitude], 16);
                } else {
                    map.fitBounds(layer.getBounds(), { padding: [60, 60], maxZoom: 15 });
                }
                geoFeatureLayers.push({ type: f.type, id: f.data.id, layer, data: f.data });
                // Auto-enter move/edit mode
                setTimeout(() => editShape(f.type, f.data.id), 800);
            }
        })();
        @endif

        // ======================== WARNA BERDASARKAN JENIS KRIMINAL ========================
        // Peta warna sesuai legend di bottom bar
        const crimeColors = {
            'Theft': '#ef4444', 'Assault': '#f97316', 'Vandalism': '#eab308',
            'Burglary': '#a855f7', 'Robbery': '#ec4899', 'Suspicious Activity': '#3b82f6',
            'Drug-related': '#22c55e', 'Fraud': '#6366f1', 'Harassment': '#14b8a6',
            'Other': '#6b7280'
        };
        function getCrimeColor(crimeType) { return crimeColors[crimeType] || '#6b7280'; }

        // ======================== LOAD FITUR DARI SERVER ========================
        // Ambil semua titik, rute, dan zona dari API dan tampilkan di peta
        function loadGeoFeatures() {
            fetch('/api/geo-features')
                .then(res => res.json())
                .then(data => {
                    // Points
                    (data.points || []).forEach(p => {
                        const color = getCrimeColor(p.crime_type);
                        const icon = L.divIcon({
                            className: 'crime-marker',
                            html: `<div style="background:${color};width:14px;height:14px;border-radius:50%;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,0.3);"></div>`,
                            iconSize: [14, 14], iconAnchor: [7, 7]
                        });
                        const layer = L.marker([p.latitude, p.longitude], { icon, draggable: true });
                        addFeaturePopup(layer, 'Point', p);
                        layer.addTo(map);
                        geoFeatureLayers.push({ type: 'Point', id: p.id, layer, data: p });
                    });
                    // Polylines
                    (data.polylines || []).forEach(p => {
                        const coords = typeof p.coordinates === 'string' ? JSON.parse(p.coordinates) : p.coordinates;
                        const latlngs = coords.map(c => [c.lat, c.lng]);
                        const color = getCrimeColor(p.crime_type);
                        const layer = L.polyline(latlngs, { color, weight: 4, opacity: 0.85 });
                        addFeaturePopup(layer, 'Polyline', p);
                        layer.addTo(map);
                        geoFeatureLayers.push({ type: 'Polyline', id: p.id, layer, data: p });
                    });
                    // Polygons
                    (data.polygons || []).forEach(p => {
                        const coords = typeof p.coordinates === 'string' ? JSON.parse(p.coordinates) : p.coordinates;
                        const latlngs = coords.map(c => [c.lat, c.lng]);
                        const color = getCrimeColor(p.crime_type);
                        const layer = L.polygon(latlngs, { color, fillColor: color, fillOpacity: 0.2, weight: 2 });
                        addFeaturePopup(layer, 'Polygon', p);
                        layer.addTo(map);
                        geoFeatureLayers.push({ type: 'Polygon', id: p.id, layer, data: p });
                    });

                    updateFeatureFeed(data);
                });
        }

        // ======================== UPDATE FEED TERBARU ========================
        // Tampilkan fitur paling baru di bottom bar
        function updateFeatureFeed(data) {
            const allFeatures = [
                ...(data.points || []).map(p => ({ ...p, _type: 'Point' })),
                ...(data.polylines || []).map(p => ({ ...p, _type: 'Polyline' })),
                ...(data.polygons || []).map(p => ({ ...p, _type: 'Polygon' })),
            ];
            allFeatures.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            const latest = allFeatures[0];
            const icons = { Point: '📍', Polyline: '📈', Polygon: '🔷' };
            const feed = document.getElementById('recentFeed');
            if (latest) {
                const icon = icons[latest._type] || '📍';
                const name = latest.name || 'Unnamed';
                const author = latest.user ? `by ${latest.user.name}` : '';
                const crime = latest.crime_type ? ` · ${latest.crime_type}` : '';
                feed.textContent = `${icon} ${name} ${author}${crime}`;
            } else {
                feed.textContent = 'No features yet — draw one!';
            }
        }

        // ======================== POPUP UNTUK SETIAP FITUR ========================
        // Tambahkan popup informasi saat fitur diklik
        function addFeaturePopup(layer, type, f) {
            const escapedDesc = f.description ? f.description.replace(/'/g, "\\'").replace(/\n/g, '<br>') : '';
            let popupHtml = `<div class="geo-popup">`;

            // Image or placeholder
            if (f.image_path) {
                popupHtml += `<img src="/files/${f.image_path}" class="popup-image" alt="${f.name}">`;
            } else {
                const icons = { Point: '📍', Polyline: '📐', Polygon: '🔷' };
                popupHtml += `<div class="popup-image-placeholder"><span>${icons[type] || '📍'} ${type}</span></div>`;
            }

            // Body
            popupHtml += `<div class="popup-body">`;
            popupHtml += `<div class="popup-badges">`;
            popupHtml += `<span class="popup-type-badge ${type}">${type}</span>`;
            if (f.crime_type) {
                popupHtml += `<span class="popup-crime-badge">🔴 ${f.crime_type}</span>`;
            }
            popupHtml += `</div>`;
            popupHtml += `<div class="popup-title">${f.name}</div>`;
            if (f.user) {
                popupHtml += `<div class="popup-author">👤 ${f.user.name}</div>`;
            }
            if (escapedDesc) {
                popupHtml += `<p class="popup-desc">${escapedDesc}</p>`;
            }

            // Actions
            if (isAuthenticated) {
                const slug = { Point: 'point', Polyline: 'polyline', Polygon: 'polygon' };
                const editUrl = `/map/${slug[type]}/${f.id}`;
                popupHtml += `<div class="popup-actions">`;
                popupHtml += `<a href="${editUrl}" class="popup-btn popup-btn-edit" style="text-decoration:none;display:inline-flex;align-items:center;gap:4px;">✎ Edit</a>`;
                popupHtml += `<a href="${editUrl}" class="popup-btn popup-btn-move" style="text-decoration:none;display:inline-flex;align-items:center;gap:4px;">⇲ Move</a>`;
                popupHtml += `<button class="popup-btn popup-btn-delete" onclick="confirmDelete(event, '${type}', ${f.id})">✕ Delete</button>`;
                popupHtml += `</div>`;
            }
            popupHtml += `</div></div>`;

            layer.bindPopup(popupHtml, { maxWidth: 300, className: 'geo-popup-wrapper' });
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
                // Enable vertex editing
                try {
                    if (editingShape.layer.editing) {
                        editingShape.layer.editing.enable();
                    } else if (drawControl._toolbars.edit._modes.edit.handler) {
                        drawControl._toolbars.edit._modes.edit.handler.enable();
                    }
                } catch(e) {
                    console.warn('Edit handler unavailable, layer may not be editable');
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
            if (isFocusMode) { window.location.href = '/map'; }
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
            document.getElementById('featCrimeType').value = f.crime_type || '';
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
                preview.src = '/files/' + f.image_path;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
            document.getElementById('featImage').value = '';
            document.getElementById('saveModal').classList.remove('hidden');
            currentGeometry = null;
        }

        @if(!isset($focusFeature))
        loadGeoFeatures();
        @endif

        // ======================== DELETE FITUR ========================
        // Konfirmasi dua-klik: pertama "Sure?", kedua hapus
        let deleteConfirmTimer = null;

        function confirmDelete(e, type, id) {
            const btn = e.target;
            if (btn.dataset.confirming === 'true') {
                // Second click — actually delete
                clearTimeout(deleteConfirmTimer);
                btn.textContent = 'Deleting...';
                btn.disabled = true;
                deleteFeature(type, id);
                return;
            }
            // First click — ask for confirmation
            clearTimeout(deleteConfirmTimer);
            btn.dataset.confirming = 'true';
            btn.textContent = 'Sure?';
            btn.style.background = '#dc2626';
            btn.style.color = '#fff';
            deleteConfirmTimer = setTimeout(() => {
                btn.dataset.confirming = 'false';
                btn.textContent = '✕ Delete';
                btn.style.background = '#fef2f2';
                btn.style.color = '#dc2626';
            }, 3000);
        }

        // Kirim request DELETE ke server dan hapus dari peta
        function deleteFeature(type, id) {
            const endpoint = type === 'Point' ? `/api/points/${id}`
                           : type === 'Polyline' ? `/api/polylines/${id}`
                           : `/api/polygons/${id}`;
            fetch(endpoint, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
                .then(async res => {
                    if (!res.ok) {
                        const data = await res.json();
                        throw new Error(data.message || 'Delete failed');
                    }
                    return res.json();
                })
                .then(() => {
                    const idx = geoFeatureLayers.findIndex(g => g.id === id && g.type === type);
                    if (idx !== -1) {
                        map.removeLayer(geoFeatureLayers[idx].layer);
                        geoFeatureLayers.splice(idx, 1);
                    }
                    showToast('Deleted!');
                })
                .catch(err => {
                    showToast('Delete failed: ' + err.message, 'error');
                });
        }

        // ======================== TOAST NOTIFIKASI ========================
        // Tampilkan toast di bagian bawah (success/error/info)
        let toastTimer;
        function showToast(msg, type = 'success') {
            const el = document.getElementById('toast');
            clearTimeout(toastTimer);
            el.textContent = msg;
            el.className = type;
            el.id = 'toast';
            requestAnimationFrame(() => el.classList.add('show'));
            toastTimer = setTimeout(() => el.classList.remove('show'), 2500);
        }

        // ======================== MODAL SIMPAN / EDIT ========================
        // Buka modal untuk menyimpan fitur yang baru digambar
        function openSaveModal() {
            if (!currentGeometry) {
                showToast('Draw something on the map first', 'info');
                return;
            }
            // Reset to create mode
            document.getElementById('modalTitle').textContent = 'Save Geo Feature';
            document.getElementById('modalSubmitBtn').textContent = 'Save';
            document.getElementById('editId').value = '';
            document.getElementById('editType').value = '';
            document.getElementById('featName').value = '';
            document.getElementById('featDesc').value = '';
            document.getElementById('featCrimeType').value = '';
            document.getElementById('featImage').value = '';
            document.getElementById('featImagePreview').style.display = 'none';
            document.getElementById('saveModal').classList.remove('hidden');
        }

        // Tutup modal dan hapus fitur yang belum disimpan dari peta
        function closeSaveModal() {
            document.getElementById('saveModal').classList.add('hidden');
            document.getElementById('saveForm').reset();
            document.getElementById('featImagePreview').style.display = 'none';
            document.getElementById('editId').value = '';
            document.getElementById('editType').value = '';
            // Remove the unsaved drawn feature from the map
            if (currentGeometry) {
                drawnItems.removeLayer(currentGeometry);
                currentGeometry = null;
                document.getElementById('statusMsg').textContent = '';
            }
        }

        // Image preview on file select
        function previewImage(input) {
            const preview = document.getElementById('featImagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '';
                preview.style.display = 'none';
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
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    const msg = data.message || data.error || JSON.stringify(data);
                    throw new Error(msg);
                }
                return data;
            })
            .then(() => {
                closeSaveModal();
                showToast(editId ? 'Updated!' : 'Saved!');
                geoFeatureLayers.forEach(g => map.removeLayer(g.layer));
                geoFeatureLayers.length = 0;
                loadGeoFeatures();
                drawnItems.clearLayers();
                currentGeometry = null;
                document.getElementById('statusMsg').textContent = '';
            })
            .catch(err => {
                showToast('Save failed: ' + err.message, 'error');
                console.error(err);
            });
        });

        // ======================== ROUTING (OSRM) ========================
        // Mode routing: klik titik A, klik titik B, gambar rute via OSRM
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
                btn.className = 'icon-btn icon-btn-route-active';
                btn.title = 'Route (ON)';
                document.getElementById('statusMsg').textContent = 'Click map to set point A';
                map.getContainer().style.cursor = 'crosshair';
            } else {
                btn.className = 'icon-btn icon-btn-warning';
                btn.title = 'Route';
                map.getContainer().style.cursor = '';
                document.getElementById('statusMsg').textContent = '';
                clearRoute();
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

        // Ambil rute dari OSRM API (OpenStreetMap Routing Machine)
        function fetchRoute() {
            const url = `https://router.project-osrm.org/route/v1/driving/${routePointA.lng},${routePointA.lat};${routePointB.lng},${routePointB.lat}?overview=full&geometries=geojson`;
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data.code !== 'Ok') { showToast('No route found', 'info'); clearRoute(); return; }
                    // Remove old route line before drawing new one
                    if (routeLine) { map.removeLayer(routeLine); routeLine = null; }
                    const route = data.routes[0];
                    const distance = (route.distance / 1000).toFixed(1);
                    const duration = Math.round(route.duration / 60);
                    const coords = route.geometry.coordinates.map(c => [c[1], c[0]]);
                    routeLine = L.polyline(coords, { color: '#f97316', weight: 5, opacity: 0.7 }).addTo(map);
                    map.fitBounds(routeLine.getBounds().pad(0.1));
                    document.getElementById('statusMsg').textContent = `Route: ${distance} km, ~${duration} min`;
                })
                .catch(() => { showToast('Routing failed', 'error'); clearRoute(); });
        }

        function clearAll() {
            cancelActiveDraw();
            drawnItems.clearLayers();
            currentGeometry = null;
            clearRoute();
            if (heatmapLayer) { map.removeLayer(heatmapLayer); heatmapLayer = null; }
            heatmapRawData = [];
            if (heatmapOn) {
                heatmapOn = false;
                document.getElementById('heatmapBtn').classList.remove('active');
                document.getElementById('heatmapBtn').title = 'Toggle heatmap';
            }
            document.getElementById('statusMsg').textContent = '';
        }

        // ======================== HEATMAP ========================
        // Heatmap menggunakan Kernel Density Estimation (KDE)
        let heatmapLayer = null;
        let heatmapOn = false;
        let heatmapRawData = [];

        // Sesuaikan radius heatmap berdasarkan level zoom
        function getHeatRadius() {
            const z = map.getZoom();
            // Scale radius so it represents ~200m at all zoom levels
            // At z=10: ~30px, z=14: ~120px, z=18: ~500px (capped)
            return Math.min(Math.round(8 * Math.pow(2, (z - 6) / 2)), 400);
        }

        function updateHeatmapAppearance() {
            if (!heatmapLayer || !heatmapOn) return;
            // Re-render heatmap saat zoom/move agar tidak ada artifak kotak
            map.removeLayer(heatmapLayer);
            heatmapLayer = null;
            renderHeatmap();
        }

        // Pakai moveend (di-render ulang di renderHeatmap) — hapus listener lama
        // map.on('zoomend', updateHeatmapAppearance); // diganti moveend di renderHeatmap

        function toggleHeatmap() {
            const btn = document.getElementById('heatmapBtn');
            if (heatmapOn) {
                if (heatmapLayer) { map.removeLayer(heatmapLayer); heatmapLayer = null; }
                btn.classList.remove('active');
                btn.title = 'Toggle heatmap';
                heatmapOn = false;
            } else {
                btn.classList.add('active');
                btn.title = 'Heatmap (ON)';
                heatmapOn = true;
                if (heatmapLayer) { map.removeLayer(heatmapLayer); }

                if (heatmapRawData.length > 0) {
                    renderHeatmap();
                } else {
                    fetch('/api/heatmap')
                        .then(res => res.json())
                        .then(points => {
                            heatmapRawData = points.map(p => [p.latitude, p.longitude, 1.0]);
                            renderHeatmap();
                        });
                }
            }
        }

        // Render ulang heatmap dengan data yang sudah di-fetch
        function renderHeatmap() {
            if (heatmapLayer) { map.removeLayer(heatmapLayer); }
            const r = getHeatRadius();
            heatmapLayer = L.heatLayer(heatmapRawData, {
                radius: r,
                blur: r * 0.8,
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

            // Perbaiki artifak kotak — re-render saat peta digeser/di-zoom
            map.off('moveend', updateHeatmapAppearance);
            map.on('moveend', updateHeatmapAppearance);
        }

    </script>
</body>
</html>
