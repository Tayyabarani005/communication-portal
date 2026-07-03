<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DirectMessage extends Model
{
    use HasFactory;

    protected $table = 'direct_message';
    protected $primaryKey = 'dm_message_id';

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'parent_id',
        'msg_body',
        'msg_type',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(DmConversation::class, 'conversation_id', 'conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id', 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DirectMessage::class, 'parent_id', 'dm_message_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(DirectMessage::class, 'parent_id', 'dm_message_id');
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'attachable', 'attachable_type', 'attachable_id', 'file_id');
    }

    public function pins(): MorphMany
    {
        return $this->morphMany(PinnedMessage::class, 'pinnable', 'pinnable_type', 'pinnable_id', 'pin_id');
    }

    public function isPinned(): bool
    {
        return $this->pins()->exists();
    }
}
