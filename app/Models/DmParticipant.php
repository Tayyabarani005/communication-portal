<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DmParticipant extends Model
{
    use HasFactory;

    protected $table = 'dm_participant';
    protected $primaryKey = 'dm_participant_id';

    protected $fillable = [
        'conversation_id',
        'user_id',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(DmConversation::class, 'conversation_id', 'conversation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
