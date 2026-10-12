<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

/**
 * Regras de Reports. Separa três permissões distintas:
 *  - editar conteúdo/atribuições e excluir definitivamente: somente o autor;
 *  - alterar estado (ativo/inativo): autor ou coordenador;
 *  - visualizar: definido por Report::scopeVisibleTo().
 * Diretor e responsável nunca escrevem.
 */
class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isTeacher() || $user->isCoordinator() || $user->isDirector();
    }

    public function view(User $user, Report $report): bool
    {
        return Report::query()
            ->visibleTo($user)
            ->whereKey($report->getKey())
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->isTeacher() || $user->isCoordinator();
    }

    public function update(User $user, Report $report): bool
    {
        return $this->create($user) && $report->author_id === $user->id;
    }

    public function delete(User $user, Report $report): bool
    {
        return $this->update($user, $report);
    }

    public function toggleActive(User $user, Report $report): bool
    {
        return $user->isCoordinator() || $this->update($user, $report);
    }
}
