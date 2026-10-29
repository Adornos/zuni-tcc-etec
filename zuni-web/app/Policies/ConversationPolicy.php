<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;
use App\Services\Chat\ChatPermissions;

/**
 * Regras de acesso a uma conversa. Participar é condição para tudo;
 * enviar exige ainda conversa aberta e o par ainda autorizado a conversar.
 */
class ConversationPolicy
{
    public function __construct(private ChatPermissions $permissions)
    {
    }

    public function view(User $user, Conversation $conversation): bool
    {
        return $this->permissions->participatesInChat($user)
            && $conversation->participants()->where('user_id', $user->id)->exists();
    }

    public function send(User $user, Conversation $conversation): bool
    {
        if (! $this->view($user, $conversation) || $conversation->isClosed()) {
            return false;
        }

        $other = $conversation->otherParticipant($user)?->user;

        return $other !== null && $this->permissions->canChat($user, $other);
    }

    public function archive(User $user, Conversation $conversation): bool
    {
        return $this->view($user, $conversation);
    }

    /** Somente coordenador encerra e reabre. */
    public function close(User $user, Conversation $conversation): bool
    {
        return $user->isCoordinator() && $this->view($user, $conversation);
    }
}
