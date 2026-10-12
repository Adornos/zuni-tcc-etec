<?php

namespace App\Policies;

use App\Enums\UserStatus;
use App\Models\StudentSheet;
use App\Models\User;

/**
 * Permissões de proficiência sobre uma ficha de aluno.
 *  - observe: somente professor responsável pela sala do aluno (aluno com matrícula ativa).
 *  - viewProficiencies: valores atuais (inclui o responsável do próprio aluno).
 *  - viewProficiencyHistory: histórico de observações (somente equipe).
 * Coordenador consulta e administra, mas não registra observações.
 */
class StudentSheetPolicy
{
    public function viewProficiencies(User $user, StudentSheet $sheet): bool
    {
        return match (true) {
            $user->isCoordinator(), $user->isDirector() => true,
            $user->isTeacher() => $this->teachesSheet($user, $sheet),
            $user->isGuardian() => $sheet->guardian_id === $user->id,
            default => false,
        };
    }

    public function viewProficiencyHistory(User $user, StudentSheet $sheet): bool
    {
        return match (true) {
            $user->isCoordinator(), $user->isDirector() => true,
            $user->isTeacher() => $this->teachesSheet($user, $sheet),
            default => false,
        };
    }

    public function observe(User $user, StudentSheet $sheet): bool
    {
        if (! $user->isTeacher() || ! $this->teachesSheet($user, $sheet)) {
            return false;
        }

        $sheet->loadMissing('user');

        return $sheet->user?->status === UserStatus::ACTIVE;
    }

    private function teachesSheet(User $user, StudentSheet $sheet): bool
    {
        return $sheet->classroom_id !== null
            && $user->classrooms()->where('classrooms.id', $sheet->classroom_id)->exists();
    }
}
