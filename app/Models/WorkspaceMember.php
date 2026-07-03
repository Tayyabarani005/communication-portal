<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WorkspaceRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkspaceMember extends Model
{
    use HasFactory;

    protected $table = 'workspace_members';
    protected $primaryKey = 'member_id';

    protected $fillable = [
        'user_id',
        'workspace_id',
        'role',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'role' => WorkspaceRole::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'workspace_id', 'workspace_id');
    }

    /**
     * Helper to check admin role.
     */
    public function isAdmin(): bool
    {
        return $this->role === WorkspaceRole::ADMIN;
    }
}
