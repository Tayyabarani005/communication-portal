<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'password_hash',
        'is_active',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the password name for authentication.
     */
    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    /**
     * Get the password hash for authentication.
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password_hash' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Boot the model and register cascade deletes on deleting event.
     */
    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            $user->workspaceMembers()->delete();
            $user->channels()->detach();
            $user->messages()->delete();
            $user->directMessages()->delete();
            $user->createdTasks()->delete();
            $user->assignedTasks()->update(['assigned_to' => null]);
            
            \DB::table('channel_read_state')->where('user_id', $user->user_id)->delete();
            \DB::table('dm_read_state')->where('user_id', $user->user_id)->delete();
            \DB::table('pinned_message')->where('pinned_by', $user->user_id)->delete();
            \DB::table('workspace_join_requests')->where('user_id', $user->user_id)->delete();
            \DB::table('notifications')
                ->where('user_id', $user->user_id)
                ->orWhere('sender_id', $user->user_id)
                ->delete();
        });
    }

    /**
     * Get the user's password (compatibility alias for password_hash).
     */
    public function getPasswordAttribute(): ?string
    {
        return $this->password_hash;
    }

    /**
     * Set the user's password (compatibility alias for password_hash).
     */
    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password_hash'] = $value;
    }

    /**
     * Get the user's ID (compatibility alias for user_id).
     */
    public function getIdAttribute(): ?int
    {
        return $this->user_id;
    }

    /* Relationships */

    public function workspaceMembers(): HasMany
    {
        return $this->hasMany(WorkspaceMember::class, 'user_id', 'user_id');
    }

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'workspace_members', 'user_id', 'workspace_id', 'user_id', 'workspace_id')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function channels(): BelongsToMany
    {
        return $this->belongsToMany(Channel::class, 'channel_user', 'user_id', 'channel_id', 'user_id', 'channel_id')
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id', 'user_id');
    }

    public function directMessages(): HasMany
    {
        return $this->hasMany(DirectMessage::class, 'sender_id', 'user_id');
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by', 'user_id');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to', 'user_id');
    }

    /**
     * Check if user is online.
     */
    public function isOnline(): bool
    {
        return \Illuminate\Support\Facades\Cache::has('user-online-' . $this->user_id);
    }
}
