<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeficiencyTracker extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = 'deficiency_tracker';


    public function estatetracker()
    {
        return $this->belongsTo(Estate::class, 'est', 'est');
    }

    public function estate()
    {
        return $this->belongsTo(Estate::class, 'est', 'est');
    }

    public function afdeling()
    {
        return $this->belongsTo(Afdeling::class, 'afd', 'nama');
    }
}
