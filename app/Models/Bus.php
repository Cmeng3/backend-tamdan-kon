<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $fillable = [
        'driver_id',
        'bus_name',
        'direction',
        'polyline',
        'is_active',
    ];
}
