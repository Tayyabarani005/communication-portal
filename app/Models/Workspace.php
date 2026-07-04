<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workspace extends Model
{
    use HasFactory;

    protected $table = 'workspace';
    protected $primaryKey = 'workspace_id';

    protected $fillable = [
        'name',
        'description',
        'avatar_url',
    ];

    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class, 'workspace_id', 'workspace_id');
    }

    public function workspaceMembers(): HasMany
    {
        return $this->hasMany(WorkspaceMember::class, 'workspace_id', 'workspace_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'workspace_members', 'workspace_id', 'user_id', 'workspace_id', 'user_id')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }
}
