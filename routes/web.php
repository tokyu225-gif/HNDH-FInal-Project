<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'pointCount' => \App\Models\GeoPoint::count(),
        'polylineCount' => \App\Models\GeoPolyline::count(),
        'polygonCount' => \App\Models\GeoPolygon::count(),
    ]);
});

// Serve storage files (bypasses broken Windows symlink) — public
Route::get('/files/{path}', function (string $path) {
    $file = storage_path('app/public/' . $path);
    if (!file_exists($file) || !is_file($file)) {
        abort(404);
    }
    return response()->file($file);
})->where('path', '.*');

// Public read-only map & data
Route::get('/map', function () {
    return view('map');
})->name('map');

Route::get('/api/geo-features', [\App\Http\Controllers\GeoFeatureController::class, 'index']);
Route::get('/api/heatmap', function () {
    return \App\Models\GeoPoint::select('latitude', 'longitude')->get();
});
Route::get('/api/beaches', function () {
    return \App\Models\Beach::all();
});

// All CRUD requires login
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/table', function () {
        return view('table');
    })->name('table');

    Route::get('/map/point/{point}', function (\App\Models\GeoPoint $point) {
        return view('map', ['focusFeature' => ['type' => 'Point', 'data' => $point]]);
    })->name('map.point.edit');

    Route::get('/map/polyline/{polyline}', function (\App\Models\GeoPolyline $polyline) {
        return view('map', ['focusFeature' => ['type' => 'Polyline', 'data' => $polyline]]);
    })->name('map.polyline.edit');

    Route::get('/map/polygon/{polygon}', function (\App\Models\GeoPolygon $polygon) {
        return view('map', ['focusFeature' => ['type' => 'Polygon', 'data' => $polygon]]);
    })->name('map.polygon.edit');

    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'edit'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    Route::post('/api/geo-features', [\App\Http\Controllers\GeoFeatureController::class, 'store']);
    Route::delete('/api/points/{point}', [\App\Http\Controllers\GeoFeatureController::class, 'destroyPoint']);
    Route::delete('/api/polylines/{polyline}', [\App\Http\Controllers\GeoFeatureController::class, 'destroyPolyline']);
    Route::delete('/api/polygons/{polygon}', [\App\Http\Controllers\GeoFeatureController::class, 'destroyPolygon']);
    Route::post('/api/points/{point}', [\App\Http\Controllers\GeoFeatureController::class, 'updatePoint']);
    Route::post('/api/polylines/{polyline}', [\App\Http\Controllers\GeoFeatureController::class, 'updatePolyline']);
    Route::post('/api/polygons/{polygon}', [\App\Http\Controllers\GeoFeatureController::class, 'updatePolygon']);

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
