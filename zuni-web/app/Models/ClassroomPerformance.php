<?php

namespace App\Models;

use App\Enums\ClassroomPerformancePeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassroomPerformance extends Model
{
    protected $fillable = [
        'classroom_id',
        'year',
        'period',
        'average_grade',
        'sociability',
        'autonomy',
        'engagement',
        'communication',
        'motor_development',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'period' => ClassroomPerformancePeriod::class,

            'average_grade' => 'decimal:2',

            'sociability' => 'decimal:2',
            'autonomy' => 'decimal:2',
            'engagement' => 'decimal:2',
            'communication' => 'decimal:2',
            'motor_development' => 'decimal:2',
        ];
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
}