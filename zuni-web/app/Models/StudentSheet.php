<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentSheet extends Model
{
    protected $fillable = [
        'student_id',
        'guardian_id',

        'class',
        'age',

        'neurodivergent',
        'allergy',
        'food_restriction',
        'special_care',

        'notes',
    ];

    protected $casts = [
        'neurodivergent' => 'boolean',
        'allergy' => 'boolean',
        'food_restriction' => 'boolean',
        'special_care' => 'boolean',
    ];

    /**
     * Student User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Guardian responsible for the student
     */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }

    /**
     * Enrollment
     */
    public function enrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class, 'sheet_id');
    }

    /**
     * Reports
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'student_id');
    }
}