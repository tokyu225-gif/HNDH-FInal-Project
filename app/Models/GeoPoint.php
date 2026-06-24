<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoPoint extends Model
{
    protected $table = 'points';

    protected $fillable = ['user_id', 'name', 'description', 'crime_type', 'latitude', 'longitude', 'image_path'];

    public function user() { return $this->belongsTo(User::class); }
}
