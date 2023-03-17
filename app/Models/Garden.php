<?php

namespace App\Models;

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
}
