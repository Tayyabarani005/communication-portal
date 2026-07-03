<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DmReadState extends Model
{
    use HasFactory;

    protected $table = 'dm_read_state';
    protected $primaryKey = 'dm_read_state_id';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'last_read_message_id',
        'last_read_at',
    ];

    protected function casts(): array
    {
        return [
            'last_read_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(DmConversation::class, 'conversation_id', 'conversation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function lastReadMessage(): BelongsTo
    {
        return $this->belongsTo(DirectMessage::class, 'last_read_message_id', 'dm_message_id');
    }
}
