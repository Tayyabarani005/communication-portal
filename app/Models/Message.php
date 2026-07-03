<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Message extends Model
{
    use HasFactory;

    protected $table = 'message';
    protected $primaryKey = 'message_id';

    protected $fillable = [
        'channel_id',
        'sender_id',
        'parent_id',
        'msg_body',
        'sent_at',
        'msg_type',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class, 'channel_id', 'channel_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id', 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'parent_id', 'message_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'parent_id', 'message_id');
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
