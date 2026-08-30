<?php

namespace App\Enums;

enum StudentStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendente',
            self::ACTIVE => 'Ativo',
            self::INACTIVE => 'Inativo',
            self::SUSPENDED => 'Suspenso',
        };
    }

}