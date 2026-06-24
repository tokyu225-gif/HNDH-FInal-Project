<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoPolygon extends Model
{
    protected $table = 'polygons';

    protected $fillable = ['user_id', 'name', 'description', 'crime_type', 'coordinates', 'image_path'];

    protected $casts = ['coordinates' => 'array'];

    public function user() { return $this->belongsTo(User::class); }
}
