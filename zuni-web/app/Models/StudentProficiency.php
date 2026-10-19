<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Estado atual de uma habilidade para um aluno (único por aluno + habilidade).
 */
class StudentProficiency extends Model
{
    protected $fillable = [
        'student_sheet_id',
        'proficiency_id',
        'base_value',
        'value',
        'effective_count',
        'observations_count',
        'last_observed_at',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'base_value' => 'decimal:4',
            'value' => 'decimal:4',
            'effective_count' => 'decimal:4',
            'observations_count' => 'integer',
            'last_observed_at' => 'datetime',
            'calculated_at' => 'datetime',
        ];
    }

    public function studentSheet(): BelongsTo
    {
        return $this->belongsTo(StudentSheet::class);
    }

    public function proficiency(): BelongsTo
    {
        return $this->belongsTo(Proficiency::class);
    }
}
