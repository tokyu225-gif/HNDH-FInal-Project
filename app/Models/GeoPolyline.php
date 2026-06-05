<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoPolyline extends Model
{
    protected $table = 'polylines';

    protected $fillable = ['name', 'description', 'coordinates', 'image_path'];

    protected $casts = ['coordinates' => 'array'];
}
