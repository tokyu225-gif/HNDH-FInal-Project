<?php

namespace App\Http\Controllers;

use App\Models\GeoFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeoFeatureController extends Controller
{
    public function index()
    {
        return GeoFeature::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'geometry_type' => 'required|string|in:Point,Polygon,Polyline',
            'geometry_data' => 'required|json',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('geo-features', 'public');
        }

        $feature = GeoFeature::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'geometry_type' => $validated['geometry_type'],
            'geometry_data' => json_decode($validated['geometry_data'], true),
            'image_path' => $imagePath,
        ]);

        return response()->json($feature, 201);
    }

    public function destroy(GeoFeature $geoFeature)
    {
        if ($geoFeature->image_path) {
            Storage::disk('public')->delete($geoFeature->image_path);
        }
        $geoFeature->delete();
        return response()->json(['message' => 'Deleted'], 200);
    }
}
