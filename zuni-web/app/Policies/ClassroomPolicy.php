<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;

class ClassroomPolicy
{
    /** Médias e evolução de proficiências da sala. */
    public function viewProficiencies(User $user, Classroom $classroom): bool
    {
        return match (true) {
            $user->isCoordinator(), $user->isDirector() => true,
            $user->isTeacher() => $user->classrooms()->where('classrooms.id', $classroom->id)->exists(),
            default => false,
        };
    }
}
