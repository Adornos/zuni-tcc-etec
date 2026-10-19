<?php

namespace App\Models;

use App\Enums\ObservationDirection;
use App\Enums\ObservationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Histórico auditável. Nunca é apagado fisicamente: correções são anulações
 * (status = voided) com autor, data e motivo.
 */
class ProficiencyObservation extends Model
{
    protected $fillable = [
        'student_sheet_id',
        'proficiency_id',
        'author_id',
        'direction',
        'intensity',
        'level',
        'reason',
        'observed_at',
        'weight',
        'requested_delta',
        'applied_delta',
        'value_before',
        'value_after',
        'status',
        'voided_by',
        'voided_at',
        'void_reason',
        'recalculated_at',
        'request_token',
    ];

    protected function casts(): array
    {
        return [
            'direction' => ObservationDirection::class,
            'status' => ObservationStatus::class,
            'intensity' => 'decimal:2',
            'level' => 'decimal:4',
            'weight' => 'decimal:6',
            'requested_delta' => 'decimal:4',
            'applied_delta' => 'decimal:4',
            'value_before' => 'decimal:4',
            'value_after' => 'decimal:4',
            'observed_at' => 'datetime',
            'voided_at' => 'datetime',
            'recalculated_at' => 'datetime',
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

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function voidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->where('status', ObservationStatus::VALID->value);
    }

    public function isVoided(): bool
    {
        return $this->status === ObservationStatus::VOIDED;
    }
}
