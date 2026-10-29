<?php

namespace App\Services\Chat;

use App\Enums\ConversationType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Fonte única da regra "quem pode conversar com quem".
 *
 *  - Professor ↔ Coordenador
 *  - Coordenador ↔ Responsável (que tenha ao menos um aluno vinculado)
 *  - Professor ↔ Responsável: nunca.
 *
 * O projeto não possui vínculo coordenador↔turma/aluno; o coordenador tem escopo
 * escolar inteiro (mesma regra de Report::scopeVisibleTo e StudentSheetPolicy).
 * Se esse vínculo for criado, restringir aqui (typeFor/canChat/contactsFor).
 */
class ChatPermissions
{
    public function participatesInChat(User $user): bool
    {
        return in_array($user->role, [UserRole::TEACHER, UserRole::COORDINATOR, UserRole::GUARDIAN], true);
    }

    public function isActive(User $user): bool
    {
        return $user->status === UserStatus::ACTIVE;
    }

    /**
     * Tipos de conversa disponíveis para o papel do usuário.
     *
     * @return array<int, ConversationType>
     */
    public function typesFor(User $user): array
    {
        return match ($user->role) {
            UserRole::TEACHER => [ConversationType::TEACHER_COORDINATOR],
            UserRole::COORDINATOR => [ConversationType::TEACHER_COORDINATOR, ConversationType::COORDINATOR_GUARDIAN],
            UserRole::GUARDIAN => [ConversationType::COORDINATOR_GUARDIAN],
            default => [],
        };
    }

    /** Tipo de conversa possível entre dois papéis; null quando a combinação é proibida. */
    public function typeBetween(User $a, User $b): ?ConversationType
    {
        if ($a->id === $b->id) {
            return null;
        }

        $roles = [$a->role, $b->role];

        if (in_array(UserRole::COORDINATOR, $roles, true) && in_array(UserRole::TEACHER, $roles, true)) {
            return ConversationType::TEACHER_COORDINATOR;
        }

        if (in_array(UserRole::COORDINATOR, $roles, true) && in_array(UserRole::GUARDIAN, $roles, true)) {
            return ConversationType::COORDINATOR_GUARDIAN;
        }

        return null;
    }

    /** Papéis compatíveis, usuários ativos e (para responsável) vínculo com aluno. */
    public function canChat(User $a, User $b): bool
    {
        if ($this->typeBetween($a, $b) === null) {
            return false;
        }

        foreach ([$a, $b] as $user) {
            if (! $this->isActive($user)) {
                return false;
            }

            if ($user->isGuardian() && ! $user->students()->exists()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Usuários com quem $user pode iniciar uma conversa do tipo dado.
     *
     * @return Builder<User>
     */
    public function contactsFor(User $user, ConversationType $type, string $search = ''): Builder
    {
        $query = User::query()->whereRaw('1 = 0');

        if (in_array($type, $this->typesFor($user), true) && $this->isActive($user)) {
            $counterpart = $user->isCoordinator() ? $type->counterpartRole() : UserRole::COORDINATOR;

            $query = User::query()
                ->where('users.role', $counterpart->value)
                ->where('users.status', UserStatus::ACTIVE->value)
                ->where('users.id', '!=', $user->id)
                ->when(
                    $counterpart === UserRole::GUARDIAN,
                    fn (Builder $q) => $q->whereHas('students')
                );
        }

        if ($search !== '') {
            $query->where('users.name', 'like', '%'.addcslashes($search, '%_\\').'%');
        }

        return $query->orderBy('users.name');
    }
}
