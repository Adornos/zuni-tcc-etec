<?php

namespace App\Policies;

use App\Models\ProficiencyObservation;
use App\Models\User;

/**
 * Anulação: coordenador (qualquer observação) ou o professor autor (a própria).
 */
class ProficiencyObservationPolicy
{
    public function void(User $user, ProficiencyObservation $observation): bool
    {
        if ($observation->isVoided()) {
            return false;
        }

        return $user->isCoordinator()
            || ($user->isTeacher() && $observation->author_id === $user->id);
    }
}
