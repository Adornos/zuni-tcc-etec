<?php

namespace App\Enums;

enum ReportType: string
{
    case NOTICE = 'notice';
    case OBSERVATION = 'observation';

    public function label(): string
    {
        return match ($this) {
            self::NOTICE => 'Aviso',
            self::OBSERVATION => 'Observação',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::NOTICE => 'badge-warning',
            self::OBSERVATION => 'badge-info',
        };
    }
}
