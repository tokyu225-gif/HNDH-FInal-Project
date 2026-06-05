<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoPolygon extends Model
{
    protected $table = 'polygons';

    protected $fillable = ['name', 'description', 'coordinates', 'image_path'];

    protected $casts = ['coordinates' => 'array'];
}
