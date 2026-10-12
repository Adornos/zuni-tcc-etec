<?php

namespace App\Policies;

use App\Models\Proficiency;
use App\Models\User;

/**
 * Cadastro de habilidades: somente coordenação.
 */
class ProficiencyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isCoordinator();
    }

    public function view(User $user, Proficiency $proficiency): bool
    {
        return $user->isCoordinator();
    }

    public function create(User $user): bool
    {
        return $user->isCoordinator();
    }

    public function update(User $user, Proficiency $proficiency): bool
    {
        return $user->isCoordinator();
    }

    public function toggleActive(User $user, Proficiency $proficiency): bool
    {
        return $user->isCoordinator();
    }
}
