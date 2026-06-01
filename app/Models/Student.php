<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    //
    protected $fillable = [
        'name',
        'parent_id',
        'latlng',
        'sex',
        'bus_id',
    ];
}
