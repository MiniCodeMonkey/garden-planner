<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Garden extends Model
{
    protected $casts = [
        'geojson' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function geojson(): Attribute
    {
        return Attribute::make(
            get: function ($geojson) {
                $geojson = json_decode($geojson);
                $geojson->properties['id'] = $this->id;
                $geojson->properties['name'] = $this->name;
                $geojson->properties['area'] = round($this->area) . 'm2';
                return $geojson;
            }
        );
    }
}
