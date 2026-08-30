<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DirectorSheet extends Model
{
    protected $fillable = [
        'director_id',
        'birth_date',
        'gender',
        'formation',
        'street',
        'number',
        'district',
        'city',
        'state',
        'registration',
        'hire_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'hire_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}