<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/table', function () {
    return view('table');
})->name('table');

Route::get('/map', function () {
    return view('map');
})->name('map');

Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'edit'])->name('settings');
Route::post('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

Route::get('/api/beaches', function () {
    return \App\Models\Beach::all();
});

Route::get('/api/geo-features', [\App\Http\Controllers\GeoFeatureController::class, 'index']);
Route::post('/api/geo-features', [\App\Http\Controllers\GeoFeatureController::class, 'store']);
Route::delete('/api/points/{point}', [\App\Http\Controllers\GeoFeatureController::class, 'destroyPoint']);
Route::delete('/api/polylines/{polyline}', [\App\Http\Controllers\GeoFeatureController::class, 'destroyPolyline']);
Route::delete('/api/polygons/{polygon}', [\App\Http\Controllers\GeoFeatureController::class, 'destroyPolygon']);
Route::post('/api/points/{point}', [\App\Http\Controllers\GeoFeatureController::class, 'updatePoint']);
Route::post('/api/polylines/{polyline}', [\App\Http\Controllers\GeoFeatureController::class, 'updatePolyline']);
Route::post('/api/polygons/{polygon}', [\App\Http\Controllers\GeoFeatureController::class, 'updatePolygon']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
