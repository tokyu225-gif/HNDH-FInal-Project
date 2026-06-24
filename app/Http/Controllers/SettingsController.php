<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function edit()
    {
        $settings = $this->load();
        return view('settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = $request->validate([
            'default_lat' => 'nullable|numeric',
            'default_lng' => 'nullable|numeric',
            'default_zoom' => 'nullable|integer|min:1|max:19',
            'units' => 'nullable|string|in:km,mi',
            'theme' => 'nullable|string|in:light,dark',
            'show_beaches' => 'nullable|boolean',
            'demo_mode' => 'nullable|boolean',
        ]);

        $current = $this->load();
        $merged = array_merge($current, array_filter($settings, fn($v) => $v !== null));

        file_put_contents(storage_path('app/settings.json'), json_encode($merged, JSON_PRETTY_PRINT));

        return redirect()->route('settings')->with('status', 'Settings saved.');
    }

    public function load(): array
    {
        $defaults = [
            'default_lat' => -8.5333,
            'default_lng' => 116.5333,
            'default_zoom' => 11,
            'units' => 'km',
            'theme' => 'light',
            'show_beaches' => false,
            'demo_mode' => false,
        ];

        $path = storage_path('app/settings.json');
        if (file_exists($path)) {
            $saved = json_decode(file_get_contents($path), true);
            return array_merge($defaults, $saved ?? []);
        }

        return $defaults;
    }
}
