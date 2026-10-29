<?php

namespace App\Enums;

enum ConversationType: string
{
    case TEACHER_COORDINATOR = 'teacher_coordinator';
    case COORDINATOR_GUARDIAN = 'coordinator_guardian';

    public function label(): string
    {
        return match ($this) {
            self::TEACHER_COORDINATOR => 'Corpo docente',
            self::COORDINATOR_GUARDIAN => 'Responsáveis',
        };
    }

    /** Papel da outra ponta (não coordenador) da conversa. */
    public function counterpartRole(): UserRole
    {
        return match ($this) {
            self::TEACHER_COORDINATOR => UserRole::TEACHER,
            self::COORDINATOR_GUARDIAN => UserRole::GUARDIAN,
        };
    }
}
