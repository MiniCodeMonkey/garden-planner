<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    use HasFactory;

    protected $with = ['seed'];

    public function seed()
    {
        return $this->belongsTo(Seed::class);
    }

    public function tray()
    {
        return $this->belongsTo(SeedTray::class, 'seed_tray_id');
    }
}
