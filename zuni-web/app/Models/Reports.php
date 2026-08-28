<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reports extends Model
{

    protected $fillable = [
        'student_id',
        'author_id',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => 'string',
    ];
    
    public function student() : belongsTo
    {
        return $this->belongsTo(StudentSheets::class, 'student_id');
    }

    public function author() : belongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

}
