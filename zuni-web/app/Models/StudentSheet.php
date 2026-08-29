<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StudentSheet extends Model
{
    protected $fillable = [
        'student_id',
        'guardian_id',

        'status',

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

    public function enrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class, 'sheet_id');
    }
    public function report(): HasMany
    {
        return $this->hasMany(Report::class, 'student_id');
    }
}