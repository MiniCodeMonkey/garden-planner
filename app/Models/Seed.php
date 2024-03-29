<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Seed extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'green_house' => 'bool',
        'seeding_start' => 'date',
        'seeding_end' => 'date',
        'planting_start' => 'date',
        'planting_end' => 'date',
        'harvest_start' => 'date',
        'harvest_end' => 'date',
    ];

    protected $appends = [
        'public_image_url',
        'quantity'
    ];

    public function inventory()
    {
        return $this->hasMany(SeedInventory::class);
    }

    public function plants()
    {
        return $this->hasMany(Plant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function publicImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->image_filename
                ? Storage::url($this->image_filename)
                : asset('plant.png')
        );
    }

    public function quantity(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->inventory()->sum('quantity')
        );
    }
}
