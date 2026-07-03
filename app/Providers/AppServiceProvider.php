<?php

namespace App\Providers;

use App\Models\Channel;
use App\Models\DirectMessage;
use App\Models\DmConversation;
use App\Models\Message;
use App\Models\Task;
use App\Models\Workspace;
use App\Policies\ChannelPolicy;
use App\Policies\DirectMessagePolicy;
use App\Policies\MessagePolicy;
use App\Policies\TaskPolicy;
use App\Policies\WorkspacePolicy;
use Illuminate\Support\Facades\Gate;
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
    }
}
