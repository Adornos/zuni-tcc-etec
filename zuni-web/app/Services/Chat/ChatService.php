<?php

namespace App\Services\Chat;

use App\Enums\ConversationType;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Ciclo de vida das conversas e mensagens. O banco é a fonte do histórico;
 * nenhum método aqui apaga mensagens.
 *
 * Regras documentadas:
 *  - Unicidade: uma conversa por (tipo, par de usuários); reaproveitada ao reiniciar.
 *  - Nova mensagem desarquiva a conversa para os dois lados.
 *  - Somente coordenador encerra/reabre; encerrada bloqueia novos envios.
 *  - Entregue: o cliente do destinatário buscou a mensagem (markDelivered).
 *  - Lida: o destinatário abriu a conversa (markRead).
 */
class ChatService
{
    public function __construct(
        private ChatPermissions $permissions,
        private ChatLinkService $links,
    ) {
    }

    /**
     * Inicia (ou reaproveita) a conversa entre dois usuários.
     *
     * @throws AuthorizationException
     */
    public function start(User $initiator, User $other): Conversation
    {
        $type = $this->permissions->typeBetween($initiator, $other);

        if ($type === null || ! $this->permissions->canChat($initiator, $other)) {
            throw new AuthorizationException('Você não pode conversar com este usuário.');
        }

        $key = Conversation::pairKey($type, $initiator->id, $other->id);
        $conversation = Conversation::query()->where('pair_key', $key)->first();

        if ($conversation === null) {
            try {
                $conversation = DB::transaction(function () use ($type, $key, $initiator, $other) {
                    $conversation = Conversation::create([
                        'type' => $type,
                        'pair_key' => $key,
                        'initiated_by' => $initiator->id,
                    ]);

                    $conversation->participants()->createMany([
                        ['user_id' => $initiator->id, 'joined_at' => now()],
                        ['user_id' => $other->id, 'joined_at' => now()],
                    ]);

                    return $conversation;
                });

                Log::info('chat.conversation.started', [
                    'conversation_id' => $conversation->id,
                    'initiated_by' => $initiator->id,
                    'type' => $type->value,
                ]);
            } catch (UniqueConstraintViolationException) {
                $conversation = Conversation::query()->where('pair_key', $key)->firstOrFail();
            }
        }

        $conversation->participants()
            ->where('user_id', $initiator->id)
            ->update(['archived_at' => null]);

        return $conversation;
    }

    /**
     * Envia uma mensagem. Reenvios com o mesmo $clientUuid retornam a mensagem já salva.
     *
     * @param  array{type:string,id:int}|null  $link
     *
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function send(User $sender, Conversation $conversation, string $body, ?string $clientUuid = null, ?array $link = null): Message
    {
        Gate::forUser($sender)->authorize('view', $conversation);

        $body = $this->normalizeBody($body);

        $max = (int) config('chat.max_length');
        $length = mb_strlen($body);

        if ($length < 1) {
            throw ValidationException::withMessages(['body' => 'Digite uma mensagem.']);
        }

        if ($length > $max) {
            throw ValidationException::withMessages(['body' => "A mensagem pode ter no máximo {$max} caracteres."]);
        }

        if ($conversation->isClosed()) {
            throw ValidationException::withMessages(['body' => 'Esta conversa foi encerrada.']);
        }

        Gate::forUser($sender)->authorize('send', $conversation);

        $recipient = $conversation->otherParticipant($sender)->user;

        if ($link !== null) {
            $this->links->assertShareable($sender, $recipient, (string) ($link['type'] ?? ''), (int) ($link['id'] ?? 0));
        }

        if ($clientUuid !== null && ($existing = $this->findByUuid($conversation, $sender, $clientUuid))) {
            return $existing;
        }

        try {
            $message = DB::transaction(function () use ($sender, $recipient, $conversation, $body, $clientUuid, $link) {
                $message = $conversation->messages()->create([
                    'sender_id' => $sender->id,
                    'body' => $body,
                    'link_type' => $link['type'] ?? null,
                    'link_id' => isset($link['id']) ? (int) $link['id'] : null,
                    'client_uuid' => $clientUuid,
                ]);

                $conversation->forceFill([
                    'last_message_id' => $message->id,
                    'last_message_at' => $message->created_at,
                ])->save();

                // Quem envia já "leu" até aqui; nova mensagem desarquiva para os dois lados.
                $conversation->participants()->where('user_id', $sender->id)->update([
                    'last_read_message_id' => $message->id,
                    'last_read_at' => now(),
                    'archived_at' => null,
                ]);
                $conversation->participants()->where('user_id', $recipient->id)->update(['archived_at' => null]);

                return $message;
            });
        } catch (UniqueConstraintViolationException) {
            return $this->findByUuid($conversation, $sender, (string) $clientUuid)
                ?? throw ValidationException::withMessages(['body' => 'Não foi possível enviar. Tente novamente.']);
        }

        Log::info('chat.message.sent', [
            'conversation_id' => $conversation->id,
            'message_id' => $message->id,
            'sender_id' => $sender->id,
        ]);

        return $message;
    }

    /**
     * Marca como entregues, ao usuário, as mensagens de outros em todas as suas conversas.
     * Uma única UPDATE, monotônica e idempotente.
     */
    public function markDelivered(User $user): void
    {
        $latestFromOthers = '(select max(m.id) from messages m where m.conversation_id = conversation_participants.conversation_id and m.sender_id <> '.(int) $user->id.')';

        ConversationParticipant::query()
            ->where('user_id', $user->id)
            ->whereRaw("{$latestFromOthers} > coalesce(last_delivered_message_id, 0)")
            ->update(['last_delivered_message_id' => DB::raw($latestFromOthers)]);
    }

    /**
     * Marca como lidas as mensagens de outros até $upToMessageId (ou a última). Idempotente.
     */
    public function markRead(User $user, Conversation $conversation, ?int $upToMessageId = null): void
    {
        Gate::forUser($user)->authorize('view', $conversation);

        $latest = $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->when($upToMessageId !== null, fn ($q) => $q->where('id', '<=', $upToMessageId))
            ->max('id');

        if ($latest === null) {
            return;
        }

        $latest = (int) $latest;

        ConversationParticipant::query()
            ->where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->whereRaw('coalesce(last_read_message_id, 0) < ?', [$latest])
            ->update([
                'last_read_message_id' => $latest,
                'last_delivered_message_id' => DB::raw(
                    "case when coalesce(last_delivered_message_id, 0) < {$latest} then {$latest} else last_delivered_message_id end"
                ),
                'last_read_at' => now(),
            ]);
    }

    public function archive(User $user, Conversation $conversation): void
    {
        Gate::forUser($user)->authorize('archive', $conversation);

        $conversation->participants()->where('user_id', $user->id)->whereNull('archived_at')
            ->update(['archived_at' => now()]);
    }

    public function unarchive(User $user, Conversation $conversation): void
    {
        Gate::forUser($user)->authorize('archive', $conversation);

        $conversation->participants()->where('user_id', $user->id)->update(['archived_at' => null]);
    }

    public function close(User $user, Conversation $conversation): void
    {
        Gate::forUser($user)->authorize('close', $conversation);

        if (! $conversation->isClosed()) {
            $conversation->forceFill(['closed_at' => now(), 'closed_by' => $user->id])->save();
            Log::info('chat.conversation.closed', ['conversation_id' => $conversation->id, 'by' => $user->id]);
        }
    }

    public function reopen(User $user, Conversation $conversation): void
    {
        Gate::forUser($user)->authorize('close', $conversation);

        if ($conversation->isClosed()) {
            $conversation->forceFill(['closed_at' => null, 'closed_by' => null])->save();
            Log::info('chat.conversation.reopened', ['conversation_id' => $conversation->id, 'by' => $user->id]);
        }
    }

    /**
     * Caixa de entrada do usuário (somente conversas em que participa), com contagem de não lidas.
     *
     * @return Builder<Conversation>
     */
    public function inbox(User $user, ?ConversationType $type = null, string $search = '', bool $archived = false): Builder
    {
        $uid = $user->id;

        return Conversation::query()
            ->join('conversation_participants as me', function ($join) use ($uid) {
                $join->on('me.conversation_id', '=', 'conversations.id')->where('me.user_id', '=', $uid);
            })
            ->select('conversations.*')
            ->addSelect('me.archived_at as my_archived_at')
            ->selectSub(
                Message::query()
                    ->selectRaw('count(*)')
                    ->whereColumn('messages.conversation_id', 'conversations.id')
                    ->where('messages.sender_id', '<>', $uid)
                    ->whereRaw('messages.id > coalesce(me.last_read_message_id, 0)'),
                'unread_count'
            )
            ->when($archived, fn ($q) => $q->whereNotNull('me.archived_at'), fn ($q) => $q->whereNull('me.archived_at'))
            ->when($type !== null, fn ($q) => $q->where('conversations.type', $type->value))
            ->when($search !== '', function ($q) use ($search, $uid) {
                $like = '%'.addcslashes($search, '%_\\').'%';

                $q->where(function ($q) use ($like, $uid) {
                    // Nome do participante, alunos (responsável) ou salas (professor).
                    $q->whereHas('participants', function ($p) use ($like, $uid) {
                        $p->where('user_id', '<>', $uid)->whereHas('user', function ($u) use ($like) {
                            $u->where('name', 'like', $like)
                                ->orWhereHas('students', fn ($s) => $s
                                    ->whereHas('user', fn ($su) => $su->where('name', 'like', $like))
                                    ->orWhereHas('classroom', fn ($c) => $c->where('name', 'like', $like)))
                                ->orWhereHas('classrooms', fn ($c) => $c->where('classrooms.name', 'like', $like));
                        });
                    })->orWhereHas('lastMessage', fn ($m) => $m->where('body', 'like', $like));
                });
            })
            ->orderByDesc(DB::raw('coalesce(conversations.last_message_at, conversations.created_at)'))
            ->orderByDesc('conversations.id');
    }

    /**
     * Pesquisa de mensagens nas conversas do usuário (inclui arquivadas e encerradas,
     * pois o histórico é preservado e continua sendo dele).
     */
    public function searchMessages(User $user, string $term, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $like = '%'.addcslashes($term, '%_\\').'%';

        return Message::query()
            ->whereHas('conversation.participants', fn ($p) => $p->where('user_id', $user->id))
            ->where('body', 'like', $like)
            ->with(['sender:id,name', 'conversation.participants.user:id,name'])
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', max(1, $page));
    }

    private function findByUuid(Conversation $conversation, User $sender, string $uuid): ?Message
    {
        return $conversation->messages()
            ->where('sender_id', $sender->id)
            ->where('client_uuid', $uuid)
            ->first();
    }

    /** Quebras de linha preservadas; caracteres de controle removidos; sem HTML interpretado (escape na saída). */
    private function normalizeBody(string $body): string
    {
        $body = str_replace(["\r\n", "\r"], "\n", $body);
        $body = (string) preg_replace('/[^\P{C}\n\t]+/u', '', $body);

        return trim($body);
    }
}
