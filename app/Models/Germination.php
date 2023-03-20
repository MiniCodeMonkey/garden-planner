<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Germination extends Model
{
    use HasFactory;

    protected $with = ['seed'];

    public function seed()
    {
        return $this->belongsTo(Seed::class);
    }
}
