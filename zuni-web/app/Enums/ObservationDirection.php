<?php

namespace App\Enums;

enum ObservationDirection: string
{
    case POSITIVE = 'positive';
    case NEGATIVE = 'negative';

    public function sign(): int
    {
        return match ($this) {
            self::POSITIVE => 1,
            self::NEGATIVE => -1,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::POSITIVE => 'Positiva',
            self::NEGATIVE => 'Negativa',
        };
    }
}
