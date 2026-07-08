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
            $layoutWorkspaces = \Illuminate\Support\Facades\Cache::remember("user-workspaces-{$userId}", 5, function () use ($user) {
                return $user->workspaces()
                    ->select('workspace.workspace_id', 'workspace.name')
                    ->get();
            });

            $layoutTotalUnreadDms = \Illuminate\Support\Facades\Cache::remember("user-unread-dms-{$userId}", 5, function () use ($userId) {
                return DirectMessage::query()
                    ->join('dm_participant as dp', 'direct_message.conversation_id', '=', 'dp.conversation_id')
                    ->leftJoin('dm_read_state as drs', function ($join) use ($userId): void {
                        $join->on('direct_message.conversation_id', '=', 'drs.conversation_id')
                            ->where('drs.user_id', '=', $userId);
                    })
                    ->where('dp.user_id', $userId)
                    ->where(function ($query): void {
                        $query->whereNull('drs.last_read_message_id')
                            ->orWhereColumn('direct_message.dm_message_id', '>', 'drs.last_read_message_id');
                    })
                    ->count('direct_message.dm_message_id');
            });

            $layoutNotifCount = \Illuminate\Support\Facades\Cache::remember("user-notif-count-{$userId}", 5, function () use ($userId) {
                return Notification::where('user_id', $userId)
                    ->where('is_seen', false)
                    ->count();
            });

            $layoutUserNotifications = \Illuminate\Support\Facades\Cache::remember("user-notifs-{$userId}", 5, function () use ($userId) {
                return Notification::query()
                    ->leftJoin('users as sender', 'notifications.sender_id', '=', 'sender.user_id')
                    ->leftJoin('workspace as notif_workspace', 'notifications.workspace_id', '=', 'notif_workspace.workspace_id')
                    ->where('notifications.user_id', $userId)
                    ->select([
                        'notifications.id',
                        'notifications.user_id',
                        'notifications.sender_id',
                        'notifications.type',
                        'notifications.workspace_id',
                        'notifications.channel_id',
                        'notifications.message_id',
                        'notifications.text',
                        'notifications.is_seen',
                        'notifications.created_at',
                        'sender.username as sender_username',
                        'notif_workspace.name as workspace_name',
                    ])
                    ->latest('notifications.created_at')
                    ->limit(15)
                    ->get();
            });

            $joinRequestPairs = $layoutUserNotifications
                ->where('type', 'join_request')
                ->filter(fn (Notification $notification) => $notification->workspace_id && $notification->sender_id);

            $layoutJoinRequests = collect();
            if ($joinRequestPairs->isNotEmpty()) {
                $joinWorkspaceIds = $joinRequestPairs->pluck('workspace_id')->unique();
                $joinSenderIds = $joinRequestPairs->pluck('sender_id')->unique();

                $layoutJoinRequests = WorkspaceJoinRequest::where('status', 'pending')
                    ->whereIn('workspace_id', $joinWorkspaceIds)
                    ->whereIn('user_id', $joinSenderIds)
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
