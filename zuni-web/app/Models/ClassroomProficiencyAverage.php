<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Média ATUAL por sala e habilidade (null = sem alunos elegíveis).
 */
class ClassroomProficiencyAverage extends Model
{
    protected $fillable = ['classroom_id', 'proficiency_id', 'average', 'students_count', 'calculated_at'];

    protected function casts(): array
    {
        return [
            'average' => 'decimal:4',
            'students_count' => 'integer',
            'calculated_at' => 'datetime',
        ];
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function proficiency(): BelongsTo
    {
        return $this->belongsTo(Proficiency::class);
    }
}
