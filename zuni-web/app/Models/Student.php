<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $fillable = [
        'user_id',
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
        return $this->belongsTo(User::class);
    }
}