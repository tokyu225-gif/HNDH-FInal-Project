<?php

namespace App\Http\Controllers;

use App\Models\GeoPoint;
use App\Models\GeoPolyline;
use App\Models\GeoPolygon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeoFeatureController extends Controller
{
    public function index()
    {
        return response()->json([
            'points' => GeoPoint::all(),
            'polylines' => GeoPolyline::all(),
            'polygons' => GeoPolygon::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
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
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'latitude' => $data['lat'],
                'longitude' => $data['lng'],
                'image_path' => $imagePath,
            ]),
            'Polyline' => GeoPolyline::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'coordinates' => $data,
                'image_path' => $imagePath,
            ]),
            'Polygon' => GeoPolygon::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'coordinates' => $data,
                'image_path' => $imagePath,
            ]),
        };

        return response()->json($feature, 201);
    }

    public function updatePoint(Request $request, GeoPoint $point)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($point->image_path) Storage::disk('public')->delete($point->image_path);
            $point->image_path = $request->file('image')->store('geo-features', 'public');
        }

        $point->update($request->only(['name', 'description', 'latitude', 'longitude']));

        return response()->json($point);
    }

    public function updatePolyline(Request $request, GeoPolyline $polyline)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'coordinates' => 'sometimes|json',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($polyline->image_path) Storage::disk('public')->delete($polyline->image_path);
            $polyline->image_path = $request->file('image')->store('geo-features', 'public');
        }

        $data = $request->only(['name', 'description']);
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
            'coordinates' => 'sometimes|json',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($polygon->image_path) Storage::disk('public')->delete($polygon->image_path);
            $polygon->image_path = $request->file('image')->store('geo-features', 'public');
        }

        $data = $request->only(['name', 'description']);
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
