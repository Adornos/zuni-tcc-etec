<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Valores de uma observação ANTES e DEPOIS de um reprocessamento (por anulação).
 */
class ProficiencyRecalculationLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'proficiency_observation_id',
        'cause_observation_id',
        'actor_id',
        'old_weight', 'old_requested_delta', 'old_applied_delta', 'old_value_before', 'old_value_after',
        'new_weight', 'new_requested_delta', 'new_applied_delta', 'new_value_before', 'new_value_after',
    ];
}
