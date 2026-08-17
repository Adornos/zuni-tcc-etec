<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{

    protected $fillable = [
        'student_id',
        'guardian_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];
    


}
