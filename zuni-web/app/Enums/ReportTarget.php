<?php

namespace App\Enums;

/**
 * Categoria de atribuição de um Report: alunos OU salas (nunca os dois).
 */
enum ReportTarget: string
{
    case STUDENTS = 'students';
    case CLASSROOMS = 'classrooms';

    public function label(): string
    {
        return match ($this) {
            self::STUDENTS => 'Alunos',
            self::CLASSROOMS => 'Salas',
        };
    }
}
