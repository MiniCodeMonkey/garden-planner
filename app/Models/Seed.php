<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seed extends Model
{
    use HasFactory;

    protected $casts = [
        'seeding_start' => 'date',
        'seeding_end' => 'date',
        'planting_start' => 'date',
        'planting_end' => 'date',
        'harvest_start' => 'date',
        'harvest_end' => 'date',
    ];

    public function inventory() {
        return $this->hasMany(SeedInventory::class);
    }

    public function publicImageUrl(): Attribute {
        return Attribute::make(
            get: fn() => $this->image_filename
                ? asset('storage/' . basename($this->image_filename))
                : 'plant.png'
        );
    }
}
