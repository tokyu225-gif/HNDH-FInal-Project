<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoPolyline extends Model
{
    protected $table = 'polylines';

    protected $fillable = ['user_id', 'name', 'description', 'crime_type', 'coordinates', 'image_path'];

    protected $casts = ['coordinates' => 'array'];

    public function user() { return $this->belongsTo(User::class); }
}
