<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'sender_id',
        'type',
        'workspace_id',
        'channel_id',
        'message_id',
        'text',
        'is_seen',
    ];

    protected static function booted(): void
    {
        static::created(function (Notification $notification) {
            app()->terminating(function () use ($notification): void {
                try {
                    broadcast(new \App\Events\NotificationCreated($notification));
                } catch (\Throwable $e) {
                    Log::warning('Notification broadcast failed.', [
                        'notification_id' => $notification->getKey(),
                        'user_id' => $notification->user_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            });
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id', 'user_id');
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'workspace_id', 'workspace_id');
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class, 'channel_id', 'channel_id');
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'message_id', 'message_id');
    }
}
