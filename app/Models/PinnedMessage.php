<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PinnedMessage extends Model
{
    use HasFactory;

    protected $table = 'pinned_message';
    protected $primaryKey = 'pin_id';

    protected $fillable = [
        'pinnable_id',
        'pinnable_type',
        'pinned_by',
    ];

    public function pinnable(): MorphTo
    {
        return $this->morphTo();
    }

    public function pinnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pinned_by', 'user_id');
    }

    protected static function booted(): void
    {
        static::created(function (PinnedMessage $pin) {
            if ($pin->pinnable_type === Message::class) {
                $channelId = $pin->pinnable?->channel_id;
                if ($channelId) {
                    \Illuminate\Support\Facades\Cache::forget("channel-pin-count-{$channelId}");
                    \Illuminate\Support\Facades\Cache::forget("channel-pins-{$channelId}");
                }
            }
        });

        static::deleting(function (PinnedMessage $pin) {
            if ($pin->pinnable_type === Message::class) {
                $channelId = $pin->pinnable?->channel_id;
                if ($channelId) {
                    \Illuminate\Support\Facades\Cache::forget("channel-pin-count-{$channelId}");
                    \Illuminate\Support\Facades\Cache::forget("channel-pins-{$channelId}");
                }
            }
        });
    }
}
