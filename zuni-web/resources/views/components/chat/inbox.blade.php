<?php

use App\Enums\ConversationType;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use App\Services\Chat\ChatLinkService;
use App\Services\Chat\ChatPermissions;
use App\Services\Chat\ChatService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    /** Tamperável pelo cliente: sempre reautorizado em activeConversation(). */
    #[Url(as: 'c')]
    public ?int $conversationId = null;

    public string $tab = '';
    public string $search = '';
    public string $searchMode = 'conversations';
    public bool $showArchived = false;
    public int $listLimit = 0;
    public int $limit = 0;
    public ?int $focusMessageId = null;
    public int $messagePage = 1;

    public bool $starting = false;
    public string $contactSearch = '';

    public bool $pickingLink = false;
    public string $linkType = Message::LINK_REPORT;
    public string $linkSearch = '';
    public ?string $attachedType = null;
    public ?int $attachedId = null;

    public function mount(): void
    {
        $user = Auth::user();
        $permissions = app(ChatPermissions::class);

        abort_unless($permissions->participatesInChat($user), 403);

        $this->tab = $permissions->typesFor($user)[0]->value;
        $this->listLimit = (int) config('chat.list_size');
        $this->limit = (int) config('chat.page_size');

        if ($this->conversationId !== null) {
            $conversation = $this->activeConversation;

            if ($conversation === null) {
                $this->conversationId = null;
            } else {
                if (in_array($conversation->type, $this->types, true)) {
                    $this->tab = $conversation->type->value;
                }
                app(ChatService::class)->markRead($user, $conversation);
            }
        }

        app(ChatService::class)->markDelivered($user);
    }

    /* ------------------------------------------------------------------ */
    /* Dados                                                               */
    /* ------------------------------------------------------------------ */

    /** @return array<int, ConversationType> */
    #[Computed]
    public function types(): array
    {
        return app(ChatPermissions::class)->typesFor(Auth::user());
    }

    #[Computed]
    public function currentType(): ConversationType
    {
        $type = ConversationType::tryFrom($this->tab);

        return ($type !== null && in_array($type, $this->types, true)) ? $type : $this->types[0];
    }

    #[Computed]
    public function conversations(): array
    {
        $rows = app(ChatService::class)
            ->inbox(Auth::user(), $this->currentType, trim($this->search), $this->showArchived)
            ->with(['lastMessage', 'participants.user:id,name,role'])
            ->limit($this->listLimit + 1)
            ->get();

        return ['items' => $rows->take($this->listLimit), 'hasMore' => $rows->count() > $this->listLimit];
    }

    /** @return array<string, int> */
    #[Computed]
    public function unreadByType(): array
    {
        $uid = Auth::id();

        return Conversation::query()
            ->join('conversation_participants as me', function ($join) use ($uid) {
                $join->on('me.conversation_id', '=', 'conversations.id')->where('me.user_id', '=', $uid);
            })
            ->join('messages', 'messages.conversation_id', '=', 'conversations.id')
            ->whereNull('me.archived_at')
            ->where('messages.sender_id', '<>', $uid)
            ->whereRaw('messages.id > coalesce(me.last_read_message_id, 0)')
            ->selectRaw('conversations.type as type, count(*) as total')
            ->groupBy('conversations.type')
            ->pluck('total', 'type')
            ->map(fn ($n) => (int) $n)
            ->all();
    }

    #[Computed]
    public function activeConversation(): ?Conversation
    {
        if ($this->conversationId === null) {
            return null;
        }

        $conversation = Conversation::query()->with('participants.user')->find($this->conversationId);

        if ($conversation === null || Gate::denies('view', $conversation)) {
            return null;
        }

        return $conversation;
    }

    #[Computed]
    public function other(): ?ConversationParticipant
    {
        return $this->activeConversation?->participants->firstWhere('user_id', '!=', Auth::id());
    }

    #[Computed]
    public function canSend(): bool
    {
        $conversation = $this->activeConversation;

        return $conversation !== null && Gate::allows('send', $conversation);
    }

    /** @return array{messages: Collection<int, Message>, hasMore: bool} */
    #[Computed]
    public function history(): array
    {
        $conversation = $this->activeConversation;

        if ($conversation === null) {
            return ['messages' => collect(), 'hasMore' => false];
        }

        $rows = $conversation->messages()
            ->orderByDesc('id')
            ->limit($this->limit + 1)
            ->get();

        return [
            'messages' => $rows->take($this->limit)->reverse()->values(),
            'hasMore' => $rows->count() > $this->limit,
        ];
    }

    #[Computed]
    public function contacts(): Collection
    {
        if (! $this->starting) {
            return collect();
        }

        return app(ChatPermissions::class)
            ->contactsFor(Auth::user(), $this->currentType, trim($this->contactSearch))
            ->with(['students.user:id,name', 'classrooms:id,name'])
            ->limit(15)
            ->get();
    }

    #[Computed]
    public function messageResults()
    {
        return app(ChatService::class)->searchMessages(
            Auth::user(),
            trim($this->search),
            intdiv((int) config('chat.list_size'), 2),
            $this->messagePage
        );
    }

    #[Computed]
    public function linkCandidates(): Collection
    {
        $other = $this->other?->user;

        if (! $this->pickingLink || $other === null) {
            return collect();
        }

        return app(ChatLinkService::class)->candidates(Auth::user(), $other, $this->linkType, trim($this->linkSearch));
    }

    /** Helpers de exibição (somente leitura). */
    public function statusFor(Message $message): string
    {
        $other = $this->other;

        if ($other === null) {
            return 'sent';
        }

        if ((int) $other->last_read_message_id >= $message->id) {
            return 'read';
        }

        return (int) $other->last_delivered_message_id >= $message->id ? 'delivered' : 'sent';
    }

    public function linkPreview(Message $message): ?array
    {
        return app(ChatLinkService::class)->resolveFor(Auth::user(), $message->link_type, $message->link_id);
    }

    public function attachedPreview(): ?array
    {
        return app(ChatLinkService::class)->resolveFor(Auth::user(), $this->attachedType, $this->attachedId);
    }

    /** Contexto escolar do outro participante (alunos do responsável, salas do professor). */
    public function contextFor(User $user): string
    {
        if ($user->isGuardian()) {
            $names = $user->students()->with('user:id,name')->get()->pluck('user.name')->filter()->take(4);

            return $names->isEmpty() ? '' : 'Alunos: '.$names->implode(', ');
        }

        if ($user->isTeacher()) {
            $names = $user->classrooms()->pluck('classrooms.name')->take(4);

            return $names->isEmpty() ? '' : 'Salas: '.$names->implode(', ');
        }

        return 'Coordenação';
    }

    /* ------------------------------------------------------------------ */
    /* Ações                                                               */
    /* ------------------------------------------------------------------ */

    public function setTab(string $value): void
    {
        $type = ConversationType::tryFrom($value);

        if ($type === null || ! in_array($type, $this->types, true)) {
            return;
        }

        $this->tab = $type->value;
        $this->conversationId = null;
        $this->starting = false;
        $this->resetSelection();
        $this->refresh();
    }

    public function updatedSearch(): void
    {
        $this->messagePage = 1;
        $this->searchMode = 'conversations';
        $this->listLimit = (int) config('chat.list_size');
        $this->refresh();
    }

    public function updatedShowArchived(): void
    {
        $this->listLimit = (int) config('chat.list_size');
        $this->refresh();
    }

    public function updatedContactSearch(): void
    {
        unset($this->contacts);
    }

    public function updatedLinkSearch(): void
    {
        unset($this->linkCandidates);
    }

    public function selectConversation(int $id): void
    {
        $this->conversationId = $id;
        unset($this->activeConversation);

        $conversation = $this->activeConversation;

        if ($conversation === null) {
            $this->conversationId = null;

            return;
        }

        $this->resetSelection();
        $this->starting = false;
        app(ChatService::class)->markRead(Auth::user(), $conversation);
        $this->refresh();
    }

    /** Abre a conversa de um resultado de pesquisa já posicionada na mensagem. */
    public function openMessage(int $conversationId, int $messageId): void
    {
        $this->selectConversation($conversationId);

        $conversation = $this->activeConversation;

        if ($conversation === null) {
            return;
        }

        if (in_array($conversation->type, $this->types, true)) {
            $this->tab = $conversation->type->value;
        }

        $newer = $conversation->messages()->where('id', '>=', $messageId)->count();
        $this->limit = min(500, max((int) config('chat.page_size'), $newer + 10));
        $this->focusMessageId = $messageId;
        $this->refresh();
    }

    public function closeConversation(): void
    {
        $this->conversationId = null;
        $this->resetSelection();
        $this->refresh();
    }

    public function toggleStarting(): void
    {
        $this->starting = ! $this->starting;
        $this->contactSearch = '';
        unset($this->contacts);
    }

    public function startWith(int $userId): void
    {
        $user = Auth::user();

        // Revalida contra a lista de contatos permitidos (nunca confia no id do cliente).
        $contact = app(ChatPermissions::class)
            ->contactsFor($user, $this->currentType)
            ->whereKey($userId)
            ->first();

        if ($contact === null) {
            $this->addError('start', 'Este usuário não está disponível para conversa.');

            return;
        }

        try {
            $conversation = app(ChatService::class)->start($user, $contact);
        } catch (AuthorizationException) {
            $this->addError('start', 'Você não pode conversar com este usuário.');

            return;
        }

        $this->starting = false;
        $this->showArchived = false;
        $this->search = '';
        $this->selectConversation($conversation->id);
    }

    /**
     * @return bool true quando a mensagem foi persistida (ou já existia, em reenvio).
     */
    public function send(string $uuid, string $text): bool
    {
        $conversation = $this->activeConversation;

        if ($conversation === null) {
            $this->addError('body', 'Conversa indisponível.');

            return false;
        }

        $this->resetErrorBag();

        $link = $this->attachedType !== null && $this->attachedId !== null
            ? ['type' => $this->attachedType, 'id' => $this->attachedId]
            : null;

        try {
            app(ChatService::class)->send(
                Auth::user(),
                $conversation,
                $text,
                Str::isUuid($uuid) ? $uuid : null,
                $link
            );
        } catch (ValidationException $e) {
            $this->setErrorBag($e->errors());

            return false;
        } catch (AuthorizationException) {
            $this->addError('body', 'Você não pode enviar mensagens nesta conversa.');

            return false;
        }

        $this->attachedType = null;
        $this->attachedId = null;
        $this->pickingLink = false;
        $this->refresh();

        return true;
    }

    public function loadOlder(): void
    {
        $this->limit += (int) config('chat.page_size');
        unset($this->history);
    }

    public function loadMoreConversations(): void
    {
        $this->listLimit += (int) config('chat.list_size');
        unset($this->conversations);
    }

    public function archive(): void
    {
        $this->withActive(fn (Conversation $c) => app(ChatService::class)->archive(Auth::user(), $c));
        $this->conversationId = null;
        $this->refresh();
    }

    public function unarchive(): void
    {
        $this->withActive(fn (Conversation $c) => app(ChatService::class)->unarchive(Auth::user(), $c));
        $this->showArchived = false;
        $this->refresh();
    }

    public function closeThread(): void
    {
        $this->withActive(fn (Conversation $c) => app(ChatService::class)->close(Auth::user(), $c));
        $this->refresh();
    }

    public function reopenThread(): void
    {
        $this->withActive(fn (Conversation $c) => app(ChatService::class)->reopen(Auth::user(), $c));
        $this->refresh();
    }

    public function toggleLinkPicker(): void
    {
        $this->pickingLink = ! $this->pickingLink;
        $this->linkSearch = '';
        unset($this->linkCandidates);
    }

    public function setLinkType(string $type): void
    {
        if (in_array($type, ChatLinkService::TYPES, true)) {
            $this->linkType = $type;
            $this->linkSearch = '';
            unset($this->linkCandidates);
        }
    }

    public function pickLink(string $type, int $id): void
    {
        $other = $this->other?->user;

        if ($other === null) {
            return;
        }

        try {
            app(ChatLinkService::class)->assertShareable(Auth::user(), $other, $type, $id);
        } catch (ValidationException $e) {
            $this->setErrorBag($e->errors());

            return;
        }

        $this->resetErrorBag();
        $this->attachedType = $type;
        $this->attachedId = $id;
        $this->pickingLink = false;
    }

    public function clearLink(): void
    {
        $this->attachedType = null;
        $this->attachedId = null;
    }

    public function messagePageTo(int $page): void
    {
        $this->messagePage = max(1, $page);
        unset($this->messageResults);
    }

    public function setSearchMode(string $mode): void
    {
        $this->searchMode = $mode === 'messages' ? 'messages' : 'conversations';
        $this->messagePage = 1;
    }

    /** Chamado pelo polling do cliente (aba visível): entrega e leitura. */
    public function poll(): void
    {
        $user = Auth::user();
        $service = app(ChatService::class);

        $service->markDelivered($user);

        $conversation = $this->activeConversation;

        if ($conversation !== null) {
            $service->markRead($user, $conversation);
        }

        $this->refresh();
    }

    protected function withActive(Closure $callback): void
    {
        $conversation = $this->activeConversation;

        if ($conversation === null) {
            return;
        }

        try {
            $callback($conversation);
        } catch (AuthorizationException) {
            $this->addError('body', 'Ação não permitida.');
        }
    }

    protected function resetSelection(): void
    {
        $this->limit = (int) config('chat.page_size');
        $this->focusMessageId = null;
        $this->pickingLink = false;
        $this->attachedType = null;
        $this->attachedId = null;
        $this->resetErrorBag();
    }

    protected function refresh(): void
    {
        unset($this->conversations, $this->activeConversation, $this->other, $this->canSend, $this->history, $this->unreadByType);
    }
};
?>

<div
    x-data="{
        timer: null,
        init() {
            this.timer = setInterval(() => {
                if (!document.hidden && this.$wire) {
                    this.$wire.poll();
                }
            }, {{ (int) config('chat.poll_seconds') * 1000 }});
        },
        destroy() {
            clearInterval(this.timer);
        }
    }"
    class="col-span-4 row-span-4"
>
    @php
        $me = auth()->user();
        $conv = $this->activeConversation;
        $list = $this->conversations;
        $searching = trim($search) !== '';
    @endphp

    <div class="flex h-[72dvh] min-h-[32rem] overflow-hidden rounded-xl border border-base-300 bg-white shadow-xl">

        {{-- ============ BARRA LATERAL ============ --}}
        <aside
            class="{{ $conv ? 'hidden lg:flex' : 'flex' }} w-full shrink-0 flex-col border-r border-base-300 bg-white lg:w-[22rem]"
            aria-label="Conversas"
        >
            <div class="space-y-3 border-b border-base-300 p-4">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-lg font-bold text-Cprimary">Mensagens</h2>
                    <button type="button" wire:click="toggleStarting" class="btn btn-sm bg-Csecondary text-white" aria-expanded="{{ $starting ? 'true' : 'false' }}">
                        {{ $starting ? 'Cancelar' : 'Nova conversa' }}
                    </button>
                </div>

                {{-- Categorias do coordenador (nunca misturadas) --}}
                @if (count($this->types) > 1)
                    <div role="tablist" aria-label="Categorias" class="tabs tabs-box">
                        @foreach ($this->types as $type)
                            @php $unread = $this->unreadByType[$type->value] ?? 0; @endphp
                            <button
                                type="button"
                                role="tab"
                                wire:click="setTab('{{ $type->value }}')"
                                wire:key="tab-{{ $type->value }}"
                                aria-selected="{{ $this->currentType === $type ? 'true' : 'false' }}"
                                class="tab flex-1 gap-2 {{ $this->currentType === $type ? 'tab-active' : '' }}"
                            >
                                {{ $type->label() }}
                                @if ($unread > 0)
                                    <span class="badge badge-sm border-0 bg-Csecondary text-white">{{ $unread }}</span>
                                    <span class="sr-only">mensagens não lidas</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label for="chat-search" class="sr-only">Pesquisar</label>
                    <input
                        id="chat-search"
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Pesquisar conversas e mensagens"
                        class="input input-bordered input-sm w-full rounded-full"
                        autocomplete="off"
                    >
                </div>

                @if ($searching)
                    <div class="join w-full" role="group" aria-label="Tipo de pesquisa">
                        <button type="button" wire:click="setSearchMode('conversations')" class="btn btn-xs join-item flex-1 {{ $searchMode === 'conversations' ? 'btn-neutral' : '' }}">Conversas</button>
                        <button type="button" wire:click="setSearchMode('messages')" class="btn btn-xs join-item flex-1 {{ $searchMode === 'messages' ? 'btn-neutral' : '' }}">Mensagens</button>
                    </div>
                @else
                    <div class="join w-full" role="group" aria-label="Filtro">
                        <button type="button" wire:click="$set('showArchived', false)" class="btn btn-xs join-item flex-1 {{ ! $showArchived ? 'btn-neutral' : '' }}">Ativas</button>
                        <button type="button" wire:click="$set('showArchived', true)" class="btn btn-xs join-item flex-1 {{ $showArchived ? 'btn-neutral' : '' }}">Arquivadas</button>
                    </div>
                @endif
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto">

                @if ($starting)
                    {{-- Novos contatos permitidos --}}
                    <div class="space-y-2 p-3">
                        <label for="chat-contact-search" class="sr-only">Buscar pessoa</label>
                        <input id="chat-contact-search" type="search" wire:model.live.debounce.300ms="contactSearch" placeholder="Buscar pessoa" class="input input-bordered input-sm w-full">
                        @error('start') <p role="alert" class="text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                    @forelse ($this->contacts as $contact)
                        <button type="button" wire:click="startWith({{ $contact->id }})" wire:key="contact-{{ $contact->id }}" class="flex w-full items-center gap-3 border-b border-base-200 px-4 py-3 text-left hover:bg-Ccard focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-Cprimary">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-Cprimary text-sm font-semibold text-white" aria-hidden="true">{{ \Illuminate\Support\Str::of($contact->name)->explode(' ')->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}</span>
                            <span class="min-w-0">
                                <span class="block truncate font-semibold">{{ $contact->name }}</span>
                                <span class="block truncate text-xs text-base-content/60">{{ $this->contextFor($contact) }}</span>
                            </span>
                        </button>
                    @empty
                        <p class="p-4 text-sm text-base-content/60">Nenhuma pessoa disponível.</p>
                    @endforelse

                @elseif ($searching && $searchMode === 'messages')
                    {{-- Resultados de mensagens --}}
                    @php $results = $this->messageResults; @endphp
                    @forelse ($results as $hit)
                        @php $who = $hit->conversation->participants->firstWhere('user_id', '!=', $me->id)?->user?->name ?? 'Conversa'; @endphp
                        <button type="button" wire:click="openMessage({{ $hit->conversation_id }}, {{ $hit->id }})" wire:key="hit-{{ $hit->id }}" class="block w-full border-b border-base-200 px-4 py-3 text-left hover:bg-Ccard focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-Cprimary">
                            <span class="flex justify-between gap-2">
                                <span class="truncate font-semibold">{{ $who }}</span>
                                <time class="shrink-0 text-xs text-base-content/50" datetime="{{ $hit->created_at->toIso8601String() }}">{{ $hit->created_at->format('d/m/Y H:i') }}</time>
                            </span>
                            <span class="line-clamp-2 break-words text-sm text-base-content/70">{{ $hit->sender_id === $me->id ? 'Você: ' : '' }}{{ $hit->body }}</span>
                        </button>
                    @empty
                        <p class="p-4 text-sm text-base-content/60">Nenhuma mensagem encontrada.</p>
                    @endforelse
                    @if ($results->hasPages())
                        <div class="flex items-center justify-between gap-2 p-3">
                            <button type="button" wire:click="messagePageTo({{ $results->currentPage() - 1 }})" class="btn btn-xs" @disabled($results->onFirstPage())>Anterior</button>
                            <span class="text-xs">{{ $results->currentPage() }} / {{ $results->lastPage() }}</span>
                            <button type="button" wire:click="messagePageTo({{ $results->currentPage() + 1 }})" class="btn btn-xs" @disabled(! $results->hasMorePages())>Próxima</button>
                        </div>
                    @endif

                @else
                    {{-- Lista de conversas --}}
                    @forelse ($list['items'] as $c)
                        @php
                            $peer = $c->participants->firstWhere('user_id', '!=', $me->id)?->user;
                            $name = $peer?->name ?? 'Usuário removido';
                            $last = $c->lastMessage;
                        @endphp
                        <button
                            type="button"
                            wire:click="selectConversation({{ $c->id }})"
                            wire:key="c-{{ $c->id }}"
                            @if ($c->id === $conversationId) aria-current="true" @endif
                            class="flex w-full gap-3 border-b border-base-200 px-4 py-3 text-left hover:bg-Ccard focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-Cprimary {{ $c->id === $conversationId ? 'bg-Cprimary-light' : '' }}"
                        >
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-Cprimary text-sm font-semibold text-white" aria-hidden="true">{{ \Illuminate\Support\Str::of($name)->explode(' ')->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}</span>
                            <span class="min-w-0 flex-1">
                                <span class="flex items-baseline justify-between gap-2">
                                    <span class="truncate font-semibold">{{ $name }}</span>
                                    @if ($c->last_message_at)
                                        <time class="shrink-0 text-xs {{ $c->unread_count > 0 ? 'font-semibold text-Csecondary' : 'text-base-content/50' }}" datetime="{{ $c->last_message_at->toIso8601String() }}">
                                            {{ $c->last_message_at->isToday() ? $c->last_message_at->format('H:i') : $c->last_message_at->format('d/m') }}
                                        </time>
                                    @endif
                                </span>
                                <span class="flex items-center justify-between gap-2">
                                    <span class="truncate text-sm {{ $c->unread_count > 0 ? 'font-medium text-Ctext' : 'text-base-content/60' }}">
                                        @if ($last)
                                            {{ $last->sender_id === $me->id ? 'Você: ' : '' }}{{ \Illuminate\Support\Str::limit(str_replace("\n", ' ', $last->body), 60) }}
                                        @else
                                            Sem mensagens
                                        @endif
                                    </span>
                                    @if ($c->unread_count > 0)
                                        <span class="badge badge-sm shrink-0 border-0 bg-Csecondary text-white">{{ $c->unread_count }}</span>
                                        <span class="sr-only">mensagens não lidas</span>
                                    @endif
                                </span>
                                @if ($c->isClosed() || $c->my_archived_at)
                                    <span class="mt-1 flex gap-1">
                                        @if ($c->isClosed()) <span class="badge badge-ghost badge-xs">Encerrada</span> @endif
                                        @if ($c->my_archived_at) <span class="badge badge-ghost badge-xs">Arquivada</span> @endif
                                    </span>
                                @endif
                            </span>
                        </button>
                    @empty
                        <p class="p-6 text-center text-sm text-base-content/60">
                            {{ $searching ? 'Nenhuma conversa encontrada.' : ($showArchived ? 'Nenhuma conversa arquivada.' : 'Nenhuma conversa ainda. Use “Nova conversa” para começar.') }}
                        </p>
                    @endforelse

                    @if ($list['hasMore'])
                        <div class="p-3 text-center">
                            <button type="button" wire:click="loadMoreConversations" class="btn btn-sm btn-ghost">Carregar mais</button>
                        </div>
                    @endif
                @endif
            </div>
        </aside>

        {{-- ============ ÁREA PRINCIPAL ============ --}}
        <section class="{{ $conv ? 'flex' : 'hidden lg:flex' }} min-w-0 flex-1 flex-col bg-Ccard" aria-label="Conversa">
            @if ($conv)
                @php
                    $other = $this->other;
                    $peer = $other?->user;
                    $history = $this->history;
                    $canSend = $this->canSend;
                    $mineArchived = (bool) $conv->participants->firstWhere('user_id', $me->id)?->archived_at;
                    $attached = $attachedType ? $this->attachedPreview() : null;
                @endphp

                <div
                    wire:key="panel-{{ $conv->id }}-{{ $focusMessageId ?? 0 }}"
                    class="flex min-h-0 flex-1 flex-col"
                    data-focus="{{ $focusMessageId }}"
                    x-data="{
                        text: '',
                        pending: [],
                        stick: true,
                        loadingOlder: false,
                        newUuid() {
                            if (window.crypto && crypto.randomUUID) { return crypto.randomUUID(); }
                            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
                                const r = Math.random() * 16 | 0;
                                return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
                            });
                        },
                        onScroll() {
                            const s = this.$refs.scroller;
                            this.stick = s.scrollHeight - s.scrollTop - s.clientHeight < 80;
                        },
                        toBottom() {
                            const s = this.$refs.scroller;
                            if (s) { s.scrollTop = s.scrollHeight; }
                        },
                        submit() {
                            const body = this.text.trim();
                            if (! body) { return; }
                            this.pending.push({ uuid: this.newUuid(), text: body, failed: false });
                            this.text = '';
                            this.stick = true;
                            this.deliver(this.pending[this.pending.length - 1]);
                        },
                        deliver(item) {
                            item.failed = false;
                            this.$wire.send(item.uuid, item.text)
                                .then((ok) => {
                                    if (ok === true) { this.pending = this.pending.filter((p) => p.uuid !== item.uuid); }
                                    else { item.failed = true; }
                                })
                                .catch(() => { item.failed = true; });
                        },
                        discard(item) { this.pending = this.pending.filter((p) => p.uuid !== item.uuid); },
                        older() {
                            const s = this.$refs.scroller;
                            const height = s.scrollHeight;
                            const top = s.scrollTop;
                            this.loadingOlder = true;
                            this.$wire.loadOlder().then(() => {
                                this.$nextTick(() => {
                                    s.scrollTop = top + (s.scrollHeight - height);
                                    this.loadingOlder = false;
                                });
                            });
                        }
                    }"
                    x-init="
                        $nextTick(() => {
                            const focus = $el.dataset.focus;
                            const target = focus ? document.getElementById('msg-' + focus) : null;
                            if (target) { target.scrollIntoView({ block: 'center' }); stick = false; } else { toBottom(); }
                            new MutationObserver(() => { if (stick && ! loadingOlder) { toBottom(); } })
                                .observe($refs.list, { childList: true, subtree: true });
                        })
                    "
                >
                    {{-- Cabeçalho --}}
                    <header class="flex items-center gap-3 border-b border-base-300 bg-white px-3 py-3 sm:px-4">
                        <button type="button" wire:click="closeConversation" class="btn btn-ghost btn-sm btn-circle lg:hidden" aria-label="Voltar para as conversas">‹</button>
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-Cprimary text-sm font-semibold text-white" aria-hidden="true">{{ \Illuminate\Support\Str::of($peer?->name ?? '?')->explode(' ')->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}</span>
                        <div class="min-w-0 flex-1">
                            <h3 class="truncate font-semibold">{{ $peer?->name ?? 'Usuário removido' }}</h3>
                            <p class="truncate text-xs text-base-content/60">
                                {{ $peer?->role->label() }}@if ($peer && $this->contextFor($peer)) — {{ $this->contextFor($peer) }}@endif
                            </p>
                        </div>
                        <div class="flex shrink-0 gap-1">
                            @if ($mineArchived)
                                <button type="button" wire:click="unarchive" class="btn btn-ghost btn-xs sm:btn-sm">Desarquivar</button>
                            @else
                                <button type="button" wire:click="archive" class="btn btn-ghost btn-xs sm:btn-sm">Arquivar</button>
                            @endif
                            @can('close', $conv)
                                @if ($conv->isClosed())
                                    <button type="button" wire:click="reopenThread" class="btn btn-ghost btn-xs sm:btn-sm">Reabrir</button>
                                @else
                                    <button type="button" wire:click="closeThread" wire:confirm="Encerrar esta conversa? Ninguém poderá enviar novas mensagens até que seja reaberta." class="btn btn-ghost btn-xs sm:btn-sm">Encerrar</button>
                                @endif
                            @endcan
                        </div>
                    </header>

                    {{-- Histórico --}}
                    <div x-ref="scroller" @scroll.passive="onScroll()" role="log" aria-live="polite" aria-label="Histórico de mensagens" tabindex="0" class="min-h-0 flex-1 overflow-y-auto px-3 py-4 sm:px-6">
                        <div x-ref="list" class="mx-auto flex max-w-3xl flex-col gap-1.5">

                            @if ($history['hasMore'])
                                <div class="text-center">
                                    <button type="button" @click="older()" class="btn btn-xs btn-ghost" wire:loading.attr="disabled" wire:target="loadOlder">Carregar mensagens anteriores</button>
                                </div>
                            @endif

                            @php $lastDay = null; @endphp
                            @forelse ($history['messages'] as $m)
                                @php
                                    $mine = $m->sender_id === $me->id;
                                    $day = $m->created_at->format('Y-m-d');
                                    $status = $mine ? $this->statusFor($m) : null;
                                @endphp

                                @if ($day !== $lastDay)
                                    <div class="my-2 text-center" wire:key="day-{{ $conv->id }}-{{ $day }}">
                                        <span class="rounded-full bg-white px-3 py-1 text-xs text-base-content/60 shadow-sm">{{ $m->created_at->isToday() ? 'Hoje' : $m->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    @php $lastDay = $day; @endphp
                                @endif

                                <div id="msg-{{ $m->id }}" wire:key="m-{{ $m->id }}" class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-[85%] rounded-2xl px-3 py-2 shadow-sm sm:max-w-[70%] {{ $mine ? 'rounded-br-sm bg-Cprimary text-white' : 'rounded-bl-sm bg-white text-Ctext' }} {{ $focusMessageId === $m->id ? 'ring-2 ring-Csecondary' : '' }}">
                                        <p class="whitespace-pre-wrap break-words text-sm">{{ $m->body }}</p>

                                        @if ($m->hasLink())
                                            @php $preview = $this->linkPreview($m); @endphp
                                            @if ($preview)
                                                <a href="{{ route('chat.link', $m) }}" class="mt-2 block rounded-lg border px-3 py-2 text-sm focus-visible:outline focus-visible:outline-2 {{ $mine ? 'border-white/30 bg-white/10 hover:bg-white/20' : 'border-base-300 bg-Ccard hover:bg-Cprimary-light' }}">
                                                    <span class="block truncate font-semibold">{{ $preview['title'] }}</span>
                                                    <span class="block truncate text-xs opacity-80">{{ $preview['subtitle'] }}</span>
                                                </a>
                                            @else
                                                <p class="mt-2 text-xs italic opacity-70">Recurso indisponível para você.</p>
                                            @endif
                                        @endif

                                        <p class="mt-1 flex items-center justify-end gap-1 text-[11px] opacity-70">
                                            <time datetime="{{ $m->created_at->toIso8601String() }}">{{ $m->created_at->format('H:i') }}</time>
                                            @if ($mine)
                                                <span class="{{ $status === 'read' ? 'font-bold text-sky-300 opacity-100' : '' }}" aria-hidden="true">{{ $status === 'sent' ? '✓' : '✓✓' }}</span>
                                                <span class="sr-only">{{ ['sent' => 'Enviada', 'delivered' => 'Entregue', 'read' => 'Lida'][$status] }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="py-10 text-center text-sm text-base-content/60">Nenhuma mensagem ainda. Diga olá!</p>
                            @endforelse

                            {{-- Estado temporário "Enviando" (somente interface) --}}
                            <div wire:ignore class="flex flex-col gap-1.5">
                                <template x-for="p in pending" :key="p.uuid">
                                    <div class="flex justify-end">
                                        <div class="max-w-[85%] rounded-2xl rounded-br-sm bg-Cprimary/80 px-3 py-2 text-white shadow-sm sm:max-w-[70%]">
                                            <p class="whitespace-pre-wrap break-words text-sm" x-text="p.text"></p>
                                            <p class="mt-1 flex items-center justify-end gap-2 text-[11px]">
                                                <template x-if="! p.failed"><span>Enviando…</span></template>
                                                <template x-if="p.failed">
                                                    <span class="flex items-center gap-2">
                                                        <span role="alert">Falha no envio.</span>
                                                        <button type="button" class="underline" @click="deliver(p)">Tentar de novo</button>
                                                        <button type="button" class="underline" @click="discard(p)">Descartar</button>
                                                    </span>
                                                </template>
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Composição --}}
                    @if ($canSend)
                        <div class="border-t border-base-300 bg-white p-3">

                            @if ($pickingLink)
                                <div class="mb-3 space-y-2 rounded-lg border border-base-300 p-3">
                                    <div class="join">
                                        <button type="button" wire:click="setLinkType('report')" class="btn btn-xs join-item {{ $linkType === 'report' ? 'btn-neutral' : '' }}">Relatórios</button>
                                        <button type="button" wire:click="setLinkType('student')" class="btn btn-xs join-item {{ $linkType === 'student' ? 'btn-neutral' : '' }}">Alunos</button>
                                    </div>
                                    <label for="chat-link-search" class="sr-only">Buscar recurso</label>
                                    <input id="chat-link-search" type="search" wire:model.live.debounce.300ms="linkSearch" placeholder="Buscar" class="input input-bordered input-sm w-full">
                                    <ul class="max-h-40 divide-y divide-base-200 overflow-y-auto">
                                        @forelse ($this->linkCandidates as $item)
                                            <li wire:key="cand-{{ $item['type'] }}-{{ $item['id'] }}">
                                                <button type="button" wire:click="pickLink('{{ $item['type'] }}', {{ $item['id'] }})" class="block w-full px-2 py-2 text-left hover:bg-Ccard">
                                                    <span class="block truncate text-sm font-semibold">{{ $item['title'] }}</span>
                                                    <span class="block truncate text-xs text-base-content/60">{{ $item['subtitle'] }}</span>
                                                </button>
                                            </li>
                                        @empty
                                            <li class="px-2 py-2 text-sm text-base-content/60">Nada disponível para os dois participantes.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            @endif

                            @if ($attached)
                                <div class="mb-2 flex items-center justify-between gap-2 rounded-lg bg-Cprimary-light px-3 py-2 text-sm">
                                    <span class="min-w-0"><span class="block truncate font-semibold">{{ $attached['title'] }}</span><span class="block truncate text-xs">{{ $attached['subtitle'] }}</span></span>
                                    <button type="button" wire:click="clearLink" class="btn btn-ghost btn-xs" aria-label="Remover link">✕</button>
                                </div>
                            @endif

                            @error('body') <p role="alert" class="mb-2 text-sm text-error">{{ $message }}</p> @enderror
                            @error('link') <p role="alert" class="mb-2 text-sm text-error">{{ $message }}</p> @enderror

                            <form @submit.prevent="submit()" class="flex items-end gap-2">
                                <button type="button" wire:click="toggleLinkPicker" class="btn btn-ghost btn-circle" aria-label="Inserir link de relatório ou aluno" aria-expanded="{{ $pickingLink ? 'true' : 'false' }}">🔗</button>

                                <div wire:ignore class="min-w-0 flex-1">
                                    <label for="chat-body" class="sr-only">Mensagem</label>
                                    <textarea
                                        id="chat-body"
                                        x-model="text"
                                        rows="1"
                                        maxlength="{{ (int) config('chat.max_length') }}"
                                        placeholder="Digite uma mensagem"
                                        class="textarea textarea-bordered max-h-32 min-h-10 w-full resize-none"
                                        @keydown.enter="if (! $event.shiftKey && ! $event.isComposing) { $event.preventDefault(); submit(); }"
                                    ></textarea>
                                </div>

                                <button type="submit" class="btn bg-Csecondary text-white" x-bind:disabled="! text.trim()">Enviar</button>
                            </form>
                        </div>
                    @else
                        <p class="border-t border-base-300 bg-white p-4 text-center text-sm text-base-content/70" role="status">
                            {{ $conv->isClosed() ? 'Esta conversa foi encerrada. Não é possível enviar novas mensagens.' : 'Não é possível enviar mensagens nesta conversa.' }}
                        </p>
                    @endif
                </div>
            @else
                <div class="flex flex-1 flex-col items-center justify-center gap-2 p-8 text-center text-base-content/60">
                    <p class="text-lg font-semibold text-Cprimary">Suas mensagens</p>
                    <p class="text-sm">Selecione uma conversa ou inicie uma nova.</p>
                </div>
            @endif
        </section>
    </div>
</div>
