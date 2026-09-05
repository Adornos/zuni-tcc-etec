<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'guardian_id',
        'classroom_id',
        'registration_number',

        'sociability',
        'autonomy',
        'engagement',
        'communication',
        'motor_development',

        'neurodivergent',
        'allergy',
        'food_restriction',
        'special_care',

        'notes',
    ];

    protected function casts(): array
    {
        return [
            'sociability' => 'decimal:2',
            'autonomy' => 'decimal:2',
            'engagement' => 'decimal:2',
            'communication' => 'decimal:2',
            'motor_development' => 'decimal:2',

            'neurodivergent' => 'boolean',
            'allergy' => 'boolean',
            'food_restriction' => 'boolean',
            'special_care' => 'boolean',
        ];
    }

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
     * Classroom
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    /**
     * Reports
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'student_id');
    }
}