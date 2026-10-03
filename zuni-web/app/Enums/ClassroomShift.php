<?php

namespace App\Enums;

enum ClassroomShift: string
{
    case MORNING = 'morning';
    case AFTERNOON = 'afternoon';
    case FULLTIME = 'full_time';
    case EVENING = 'evening';

    public function label(): string
    {
        return match ($this) {
            self::MORNING => 'Manhã',
            self::AFTERNOON => 'Tarde',
            self::FULLTIME => 'Integral',
            self::EVENING => 'Noite',
        };
    }
}