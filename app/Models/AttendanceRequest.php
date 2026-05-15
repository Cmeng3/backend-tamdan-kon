<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    //
    protected $fillable = [
        'student_id',
        'reason',
        'status',
    ];
}
