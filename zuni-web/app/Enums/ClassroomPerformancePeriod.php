<?php

namespace App\Enums;

enum ClassroomPerformancePeriod: int
{
    case FIRST = 1;
    case SECOND = 2;
    case THIRD = 3;
    case FOURTH = 4;

    public function label(): string
    {
        return match ($this) {
            self::FIRST => '1º Período',
            self::SECOND => '2º Período',
            self::THIRD => '3º Período',
            self::FOURTH => '4º Período',
        };
    }
}