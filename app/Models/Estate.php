<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estate extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = 'estate';


    public function afdeling()
    {
        return $this->hasMany(Afdeling::class, 'estate', 'id');
    }

    public function dtracker_est()
    {
        return $this->hasMany(DeficiencyTracker::class, 'est', 'est');
    }
}
