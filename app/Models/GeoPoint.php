<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoPoint extends Model
{
    protected $table = 'points';

    protected $fillable = ['name', 'description', 'latitude', 'longitude', 'image_path'];
}
