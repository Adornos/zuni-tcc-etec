<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Foto histórica da média numa data de referência. Não é alterada quando as
 * proficiências atuais mudam.
 */
class ClassroomProficiencySnapshot extends Model
{
    protected $fillable = ['classroom_id', 'proficiency_id', 'reference_date', 'average', 'students_count', 'generated_at'];

    protected function casts(): array
    {
        return [
            'reference_date' => 'date',
            'average' => 'decimal:4',
            'students_count' => 'integer',
            'generated_at' => 'datetime',
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
