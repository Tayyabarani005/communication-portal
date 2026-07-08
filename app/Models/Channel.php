<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Channel extends Model
{
    use HasFactory;

    protected $table = 'channel';
    protected $primaryKey = 'channel_id';

    protected $fillable = [
        'workspace_id',
        'channel_name',
        'is_private',
    ];

    protected static function booted(): void
    {
        static::created(function (Channel $channel) {
            $userIds = WorkspaceMember::where('workspace_id', $channel->workspace_id)->pluck('user_id');
            foreach ($userIds as $uId) {
                \Illuminate\Support\Facades\Cache::forget("user-workspace-channels-{$uId}-{$channel->workspace_id}");
            }
        });

        static::updated(function (Channel $channel) {
            $userIds = WorkspaceMember::where('workspace_id', $channel->workspace_id)->pluck('user_id');
            foreach ($userIds as $uId) {
                \Illuminate\Support\Facades\Cache::forget("user-workspace-channels-{$uId}-{$channel->workspace_id}");
            }
        });

        static::deleted(function (Channel $channel) {
            $userIds = WorkspaceMember::where('workspace_id', $channel->workspace_id)->pluck('user_id');
            foreach ($userIds as $uId) {
                \Illuminate\Support\Facades\Cache::forget("user-workspace-channels-{$uId}-{$channel->workspace_id}");
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'workspace_id', 'workspace_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'channel_id', 'channel_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'channel_user', 'channel_id', 'user_id', 'channel_id', 'user_id')
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'channel_id', 'channel_id');
    }
}
