<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    
    public function student() : belongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }


}
