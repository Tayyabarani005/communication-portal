<?php

namespace App\Providers;

use App\Models\Channel;
use App\Models\DirectMessage;
use App\Models\DmConversation;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Task;
use App\Models\Workspace;
use App\Models\WorkspaceJoinRequest;
use App\Policies\ChannelPolicy;
use App\Policies\DirectMessagePolicy;
use App\Policies\MessagePolicy;
use App\Policies\TaskPolicy;
use App\Policies\WorkspacePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Workspace::class, WorkspacePolicy::class);
        Gate::policy(Channel::class, ChannelPolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(DmConversation::class, DirectMessagePolicy::class);

        View::composer('layouts.app', function ($view): void {
            $user = auth()->user();

            if (!$user) {
                return;
            }

            $userId = $user->user_id;
            $layoutWorkspaces = $user->workspaces()
                ->select('workspace.workspace_id', 'workspace.name')
                ->get();

            $conversationIds = DmConversation::whereHas(
                'dmParticipants',
                fn ($query) => $query->where('user_id', $userId)
            )->pluck('conversation_id');

            $layoutTotalUnreadDms = $conversationIds->isEmpty()
                ? 0
                : DirectMessage::query()
                    ->leftJoin('dm_read_state as drs', function ($join) use ($userId): void {
                        $join->on('direct_message.conversation_id', '=', 'drs.conversation_id')
                            ->where('drs.user_id', '=', $userId);
                    })
                    ->whereIn('direct_message.conversation_id', $conversationIds)
                    ->where(function ($query): void {
                        $query->whereNull('drs.last_read_message_id')
                            ->orWhereColumn('direct_message.dm_message_id', '>', 'drs.last_read_message_id');
                    })
                    ->count();

            $layoutNotifCount = Notification::where('user_id', $userId)
                ->where('is_seen', false)
                ->count();

            $layoutUserNotifications = Notification::where('user_id', $userId)
                ->with([
                    'sender:user_id,username,avatar_url',
                    'workspace:workspace_id,name',
                    'channel:channel_id,channel_name',
                ])
                ->latest()
                ->limit(15)
                ->get();

            $joinRequestPairs = $layoutUserNotifications
                ->where('type', 'join_request')
                ->filter(fn (Notification $notification) => $notification->workspace_id && $notification->sender_id);

            $layoutJoinRequests = collect();
            if ($joinRequestPairs->isNotEmpty()) {
                $layoutJoinRequests = WorkspaceJoinRequest::where('status', 'pending')
                    ->where(function ($query) use ($joinRequestPairs): void {
                        foreach ($joinRequestPairs as $notification) {
                            $query->orWhere(function ($pairQuery) use ($notification): void {
                                $pairQuery->where('workspace_id', $notification->workspace_id)
                                    ->where('user_id', $notification->sender_id);
                            });
                        }
                    })
                    ->get()
                    ->keyBy(fn (WorkspaceJoinRequest $request): string => $request->workspace_id . ':' . $request->user_id);
            }

            $inviteWorkspaceIds = $layoutUserNotifications
                ->where('type', 'workspace_invite')
                ->pluck('workspace_id')
                ->filter()
                ->unique();

            $layoutInviteRequests = $inviteWorkspaceIds->isEmpty()
                ? collect()
                : WorkspaceJoinRequest::where('user_id', $userId)
                    ->where('status', 'pending')
                    ->whereIn('workspace_id', $inviteWorkspaceIds)
                    ->get()
                    ->keyBy('workspace_id');

            $view->with(compact(
                'layoutWorkspaces',
                'layoutTotalUnreadDms',
                'layoutNotifCount',
                'layoutUserNotifications',
                'layoutJoinRequests',
                'layoutInviteRequests',
            ));
        });
    }
}
