<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Plant extends Model
{
    use HasFactory;

    protected $with = ['seed'];

    protected static function booted(): void
    {
        static::addGlobalScope('recently-planted', function (Builder $builder) {
            $builder->where('plants.created_at', '>', now()->subMonths(6));
        });
    }

    public function seed()
    {
        return $this->belongsTo(Seed::class);
    }

    public function tray()
    {
        return $this->belongsTo(SeedTray::class, 'seed_tray_id');
    }
}
