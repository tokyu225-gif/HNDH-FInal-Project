<?php

namespace App\Http\Controllers;

use App\Models\GeoPoint;
use App\Models\GeoPolyline;
use App\Models\GeoPolygon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeoFeatureController extends Controller
{
    /**
     * Ambil semua fitur geo (titik, rute, zona).
     * Jika demo mode OFF, sembunyikan data dari user demo@maiguard.id
     */
    public function index()
    {
        $settings = (new SettingsController)->load();
        $demoUid = \App\Models\User::where('email', 'demo@maiguard.id')->value('id');

        $points = GeoPoint::with('user')->when(!($settings['demo_mode'] ?? false) && $demoUid, fn($q) => $q->where('user_id', '!=', $demoUid))->get();
        $polylines = GeoPolyline::with('user')->when(!($settings['demo_mode'] ?? false) && $demoUid, fn($q) => $q->where('user_id', '!=', $demoUid))->get();
        $polygons = GeoPolygon::with('user')->when(!($settings['demo_mode'] ?? false) && $demoUid, fn($q) => $q->where('user_id', '!=', $demoUid))->get();

        return response()->json([
            'points' => $points,
            'polylines' => $polylines,
            'polygons' => $polygons,
        ]);
    }

    /**
     * Simpan fitur baru (titik/rute/zona) yang digambar user di peta.
     * Menerima geometry_type dan geometry_data dari Leaflet.draw.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'crime_type' => 'nullable|string|max:100',
            'geometry_type' => 'required|string|in:Point,Polyline,Polygon',
            'geometry_data' => 'required|json',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('geo-features', 'public');
        }

        $data = json_decode($validated['geometry_data'], true);

        $feature = match ($validated['geometry_type']) {
            'Point' => GeoPoint::create([
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'crime_type' => $validated['crime_type'] ?? null,
                'latitude' => $data['lat'],
                'longitude' => $data['lng'],
                'image_path' => $imagePath,
            ]),
            'Polyline' => GeoPolyline::create([
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'crime_type' => $validated['crime_type'] ?? null,
                'coordinates' => $data,
                'image_path' => $imagePath,
            ]),
            'Polygon' => GeoPolygon::create([
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'crime_type' => $validated['crime_type'] ?? null,
                'coordinates' => $data,
                'image_path' => $imagePath,
            ]),
        };

        return response()->json($feature, 201);
    }

    /**
     * Update posisi dan data titik (drag marker di peta).
     */
    public function updatePoint(Request $request, GeoPoint $point)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'crime_type' => 'nullable|string|max:100',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($point->image_path) Storage::disk('public')->delete($point->image_path);
            $point->image_path = $request->file('image')->store('geo-features', 'public');
        }

        $point->update($request->only(['name', 'description', 'crime_type', 'latitude', 'longitude']));

        return response()->json($point);
    }

    /**
     * Update koordinat rute (edit vertex di peta).
     */
    public function updatePolyline(Request $request, GeoPolyline $polyline)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'crime_type' => 'nullable|string|max:100',
            'coordinates' => 'sometimes|json',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($polyline->image_path) Storage::disk('public')->delete($polyline->image_path);
            $polyline->image_path = $request->file('image')->store('geo-features', 'public');
        }

        $data = $request->only(['name', 'description', 'crime_type']);
        if ($request->has('coordinates')) {
            $data['coordinates'] = json_decode($request->input('coordinates'), true);
        }
        $polyline->update($data);

        return response()->json($polyline);
    }

    public function updatePolygon(Request $request, GeoPolygon $polygon)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'crime_type' => 'nullable|string|max:100',
            'coordinates' => 'sometimes|json',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($polygon->image_path) Storage::disk('public')->delete($polygon->image_path);
            $polygon->image_path = $request->file('image')->store('geo-features', 'public');
        }

        $data = $request->only(['name', 'description', 'crime_type']);
        if ($request->has('coordinates')) {
            $data['coordinates'] = json_decode($request->input('coordinates'), true);
        }
        $polygon->update($data);

        return response()->json($polygon);
    }

    public function destroyPoint(GeoPoint $point)
    {
        if ($point->image_path) {
            Storage::disk('public')->delete($point->image_path);
        }
        $point->delete();
        return response()->json(['message' => 'Deleted'], 200);
    }

    public function destroyPolyline(GeoPolyline $polyline)
    {
        if ($polyline->image_path) {
            Storage::disk('public')->delete($polyline->image_path);
        }
        $polyline->delete();
        return response()->json(['message' => 'Deleted'], 200);
    }

    public function destroyPolygon(GeoPolygon $polygon)
    {
        if ($polygon->image_path) {
            Storage::disk('public')->delete($polygon->image_path);
        }
        $polygon->delete();
        return response()->json(['message' => 'Deleted'], 200);
    }
}
