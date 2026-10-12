<?php

namespace App\Enums;

enum ObservationStatus: string
{
    case VALID = 'valid';
    case VOIDED = 'voided';

    public function label(): string
    {
        return match ($this) {
            self::VALID => 'Válida',
            self::VOIDED => 'Anulada',
        };
    }
}
