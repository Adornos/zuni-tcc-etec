<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    public const LINK_REPORT = 'report';

    public const LINK_STUDENT = 'student';

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
        'link_type',
        'link_id',
        'client_uuid',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function hasLink(): bool
    {
        return $this->link_type !== null && $this->link_id !== null;
    }
}
