<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSheet extends Model
{
    protected $fillable = [
        'student_id',
        'guardian_id',

        'name',
        'birth_date',
        'gender',
        'class',
        'age',

        'street',
        'number',
        'district',
        'city',
        'state',

        'neurodivergent',
        'allergy',
        'food_restriction',
        'special_care',

        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',

        'neurodivergent' => 'boolean',
        'allergy' => 'boolean',
        'food_restriction' => 'boolean',
        'special_care' => 'boolean',
    ];

    /**
     * Student belongs to a Guardian (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }
}