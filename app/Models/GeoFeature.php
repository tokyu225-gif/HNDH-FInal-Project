<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoFeature extends Model
{
    protected $fillable = [
        'name',
        'description',
        'geometry_type',
        'geometry_data',
        'image_path',
    ];

    protected $casts = [
        'geometry_data' => 'array',
    ];
}
