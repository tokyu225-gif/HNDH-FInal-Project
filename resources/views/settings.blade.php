<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>wow very bad guy is here</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.theme')
    <style>
        body { background: var(--color-bg); margin: 0; padding: 0; }
        .page-container { max-width: 512px; margin: 0 auto; padding: 1.5rem 1rem; }
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .page-title {
            font-size: 1.25rem; font-weight: 700; color: var(--color-text);
            display: flex; align-items: center; gap: 0.5rem;
        }
        .nav-back {
            color: var(--color-accent); font-size: 0.8125rem; font-weight: 500;
            text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;
            transition: color 0.15s;
        }
        .nav-back:hover { color: var(--color-accent-hover); }

        .settings-form {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
        }
        [data-theme="dark"] .settings-form {
            box-shadow: 0 2px 12px rgba(0,0,0,0.3);
        }
        .form-group { margin-bottom: 1rem; }
        .form-label {
            display: block; font-size: 0.8125rem; font-weight: 500;
            color: var(--color-text-secondary); margin-bottom: 0.25rem;
        }
        .form-input {
            width: 100%;
            border: 1px solid var(--color-input-border);
            background: var(--color-input-bg);
            color: var(--color-input-text);
            border-radius: var(--radius-sm);
            padding: 0.5rem 0.75rem;
            font-size: 0.8125rem;
            outline: none; box-sizing: border-box;
            transition: border-color 0.15s ease;
        }
        .form-input:focus {
            border-color: var(--color-input-focus-ring);
            box-shadow: 0 0 0 3px var(--color-shield-glow);
        }
        .form-input::placeholder { color: var(--color-input-placeholder); }
        .form-select {
            width: 100%;
            border: 1px solid var(--color-input-border);
            background: var(--color-input-bg);
            color: var(--color-input-text);
            border-radius: var(--radius-sm);
            padding: 0.5rem 0.75rem;
            font-size: 0.8125rem;
            outline: none; box-sizing: border-box;
            cursor: pointer;
            transition: border-color 0.15s ease;
        }
        .form-select:focus {
            border-color: var(--color-input-focus-ring);
            box-shadow: 0 0 0 3px var(--color-shield-glow);
        }
        .form-row { display: flex; gap: 0.5rem; }
        .form-row .form-input { flex: 1; }
        .btn-save {
            width: 100%; padding: 0.625rem;
            background: var(--color-accent);
            color: #ffffff; border: none;
            border-radius: var(--radius-sm);
            font-size: 0.875rem; font-weight: 500;
            cursor: pointer;
            transition: background 0.15s ease;
            margin-top: 0.5rem;
        }
        .btn-save:hover { background: var(--color-accent-hover); }
        .alert-success {
            margin-bottom: 1rem; padding: 0.625rem 1rem;
            background: var(--color-success-soft);
            color: var(--color-success);
            border-radius: var(--radius-sm);
            font-size: 0.8125rem;
        }

        /* ---- Toggle Switch ---- */
        .toggle-switch {
            position: relative; display: inline-block;
            width: 40px; height: 22px; flex-shrink: 0;
        }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute; inset: 0; cursor: pointer;
            background: var(--color-border);
            border-radius: 22px;
            transition: all 0.2s ease;
        }
        .toggle-slider::before {
            content: ""; position: absolute;
            width: 16px; height: 16px; left: 3px; bottom: 3px;
            background: #fff; border-radius: 50%;
            transition: all 0.2s ease;
        }
        .toggle-switch input:checked + .toggle-slider {
            background: var(--color-accent);
        }
        .toggle-switch input:checked + .toggle-slider::before {
            transform: translateX(18px);
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="page-header">
            <div style="display:flex; align-items:center; gap:0.5rem;">
                @include('partials.user-avatar', ['size' => 36])
                <div>
                    <h1 class="page-title" style="margin-bottom:0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-shield)" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        Settings
                    </h1>
                    <p style="font-size:0.75rem; color:var(--color-text-muted);">{{ Auth::user()->name }} &middot; {{ Auth::user()->email }}</p>
                </div>
            </div>
            <a href="{{ url('/map') }}" class="nav-back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Back to Map
            </a>
        </div>

        @if (session('status'))
            <div class="alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('settings.update') }}" class="settings-form">
            @csrf

            <div class="form-group">
                <label class="form-label">Default Map Center (Lat, Lng)</label>
                <div class="form-row">
                    <input type="number" step="any" name="default_lat" value="{{ $settings['default_lat'] }}"
                           class="form-input" placeholder="Latitude">
                    <input type="number" step="any" name="default_lng" value="{{ $settings['default_lng'] }}"
                           class="form-input" placeholder="Longitude">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Default Zoom (1-19)</label>
                <input type="number" name="default_zoom" value="{{ $settings['default_zoom'] }}" min="1" max="19"
                       class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Distance Units</label>
                <select name="units" class="form-select">
                    <option value="km" {{ $settings['units'] === 'km' ? 'selected' : '' }}>Kilometers (km)</option>
                    <option value="mi" {{ $settings['units'] === 'mi' ? 'selected' : '' }}>Miles (mi)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Theme</label>
                <select name="theme" class="form-select">
                    <option value="light" {{ $settings['theme'] === 'light' ? 'selected' : '' }}>☀️ Light</option>
                    <option value="dark" {{ $settings['theme'] === 'dark' ? 'selected' : '' }}>🔒 Dark (Keamanan)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" style="display:flex;align-items:center;justify-content:space-between;">
                    <span>Show Beaches on Map</span>
                    <label class="toggle-switch">
                        <input type="hidden" name="show_beaches" value="0">
                        <input type="checkbox" name="show_beaches" value="1" {{ ($settings['show_beaches'] ?? false) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </label>
                <p style="font-size:0.6875rem;color:var(--color-text-muted);margin-top:0.25rem;">Display beach markers as a overlay on the map.</p>
            </div>

            <button type="submit" class="btn-save">Save Settings</button>
        </form>
    </div>
</body>
</html>
