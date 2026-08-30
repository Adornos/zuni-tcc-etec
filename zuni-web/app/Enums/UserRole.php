<?php

namespace App\Enums;

enum UserRole: string
{
    case GUARDIAN = 'guardian';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
    case COORDINATOR = 'coordinator';
    case DIRECTOR = 'director';
    case ADMIN = 'admin';


    public function label(): string
    {
        return match ($this) {
            self::TEACHER => 'Professor',
            self::COORDINATOR => 'Coordenador',
            self::DIRECTOR => 'Diretor',
            self::STUDENT => 'Aluno',
            self::GUARDIAN => 'Responsável',
        };
    }

}