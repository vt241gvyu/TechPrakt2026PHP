<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $fillable = ['name', 'location', 'start_date', 'max_teams'];

    protected $casts = [
        'start_date' => 'date',
    ];
}
