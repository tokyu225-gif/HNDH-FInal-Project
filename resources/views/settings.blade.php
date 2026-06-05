<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 m-0 p-0">
    <div class="max-w-lg mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-semibold">Settings</h1>
            <a href="{{ url('/map') }}" class="text-blue-600 hover:text-blue-800 text-sm">&larr; Back to Map</a>
        </div>

        @if (session('status'))
            <div class="mb-4 px-4 py-2 bg-green-100 text-green-700 rounded text-sm">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('settings.update') }}" class="bg-white rounded shadow-sm p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Default Map Center (Lat, Lng)</label>
                <div class="flex gap-2">
                    <input type="number" step="any" name="default_lat" value="{{ $settings['default_lat'] }}"
                           class="w-1/2 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Latitude">
                    <input type="number" step="any" name="default_lng" value="{{ $settings['default_lng'] }}"
                           class="w-1/2 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Longitude">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Default Zoom (1-19)</label>
                <input type="number" name="default_zoom" value="{{ $settings['default_zoom'] }}" min="1" max="19"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Distance Units</label>
                <select name="units"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="km" {{ $settings['units'] === 'km' ? 'selected' : '' }}>Kilometers (km)</option>
                    <option value="mi" {{ $settings['units'] === 'mi' ? 'selected' : '' }}>Miles (mi)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Theme</label>
                <select name="theme"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="light" {{ $settings['theme'] === 'light' ? 'selected' : '' }}>Light</option>
                    <option value="dark" {{ $settings['theme'] === 'dark' ? 'selected' : '' }}>Dark</option>
                </select>
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="w-full px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">Save Settings</button>
            </div>
        </form>
    </div>
</body>
</html>
