<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoordinatorSheet extends Model
{
    protected $fillable = [
        'coordinator_id',
        'formation',
        'specialization',
        'registration',
        'hire_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }
}