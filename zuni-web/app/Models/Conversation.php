<?php

namespace App\Models;

use App\Enums\ConversationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'pair_key',
        'initiated_by',
        'closed_at',
        'closed_by',
        'last_message_id',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => ConversationType::class,
            'closed_at' => 'datetime',
            'last_message_at' => 'datetime',
        ];
    }

    public static function pairKey(ConversationType $type, int $userA, int $userB): string
    {
        return $type->value.':'.min($userA, $userB).':'.max($userA, $userB);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    public function isClosed(): bool
    {
        return $this->closed_at !== null;
    }

    public function participantFor(User $user): ?ConversationParticipant
    {
        return $this->participants->firstWhere('user_id', $user->id)
            ?? $this->participants()->where('user_id', $user->id)->first();
    }

    public function otherParticipant(User $user): ?ConversationParticipant
    {
        return $this->participants()->where('user_id', '!=', $user->id)->with('user')->first();
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->whereHas(
            'participants',
            fn (Builder $q) => $q->where('conversation_participants.user_id', $user->id)
        );
    }
}
