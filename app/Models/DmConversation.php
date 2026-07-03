<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DmConversation extends Model
{
    use HasFactory;

    protected $table = 'dm_conversation';
    protected $primaryKey = 'conversation_id';

    protected $fillable = [];

    public function directMessages(): HasMany
    {
        return $this->hasMany(DirectMessage::class, 'conversation_id', 'conversation_id');
    }

    public function dmParticipants(): HasMany
    {
        return $this->hasMany(DmParticipant::class, 'conversation_id', 'conversation_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'dm_participant', 'conversation_id', 'user_id', 'conversation_id', 'user_id')
            ->withTimestamps();
    }
}
