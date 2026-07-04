<x-app-layout>
    <x-slot name="title">{{ $workspace->name }}</x-slot>

    {{-- Left: Channel sidebar --}}
    <div class="flex flex-col w-60 flex-shrink-0 border-r sidebar-scroll overflow-y-auto" style="background: var(--color-sidebar-bg); border-color: var(--color-sidebar-border);">
        <div class="p-4 border-b" style="border-color: var(--color-sidebar-border);">
            <h2 class="font-semibold text-sm" style="color: var(--color-sidebar-text-active);">{{ $workspace->name }}</h2>
            <p class="text-xs mt-0.5" style="color: var(--color-sidebar-text-muted);">{{ $members->count() }} members</p>
        </div>

        @if($isMember)
        {{-- Channels list --}}
        <div class="flex-1 p-2">
            <div class="flex items-center justify-between px-2 py-1.5 mb-1">
                <span class="section-label">Channels</span>
                @can('update', $workspace)
                <a href="{{ route('workspaces.channels.create', $workspace) }}"
                   class="w-5 h-5 rounded flex items-center justify-center text-xs transition-colors"
                   style="color: var(--color-sidebar-text-muted);"
                   onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.color='white'"
                   onmouseout="this.style.background='transparent'; this.style.color='var(--color-sidebar-text-muted)'">+</a>
                @endcan
            </div>

            @foreach($channels as $channel)
            @php $inChannel = $channel->users->isNotEmpty(); @endphp
            <div class="flex items-center group">
                <a href="{{ $inChannel ? route('channels.show', $channel) : '#' }}"
                   class="nav-item flex-1 {{ !$inChannel ? 'opacity-50' : '' }}">
                    <span class="text-xs">{{ $channel->is_private ? '🔒' : '#' }}</span>
                    <span class="truncate">{{ $channel->channel_name }}</span>
                </a>
                @if(!$inChannel && !$channel->is_private)
                <form method="POST" action="{{ route('channels.join', [$workspace, $channel]) }}">
                    @csrf
                    <button type="submit" class="text-xs px-2 hidden group-hover:block transition-all"
                            style="color: var(--color-accent-400); background: none; border: none; cursor: pointer;">Join</button>
                </form>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Members --}}
        <div class="p-3 border-t" style="border-color: var(--color-sidebar-border);">
            <div class="flex items-center justify-between mb-2">
                <p class="section-label">Members ({{ $members->count() }})</p>
                @if($isAdmin)
                <button onclick="document.getElementById('add-member-modal').classList.remove('hidden')"
                        class="w-5 h-5 rounded flex items-center justify-center text-xs transition-colors"
                        style="color: var(--color-sidebar-text-muted); background: none; border: none; cursor: pointer;"
                        onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.color='white'"
                        onmouseout="this.style.background='transparent'; this.style.color='var(--color-sidebar-text-muted)'"
                        title="Add Member">+</button>
                @endif
            </div>
            @foreach($members->take(8) as $member)
            <div class="flex items-center gap-2 py-1">
                <div class="avatar-initials w-6 h-6 text-xs flex-shrink-0" style="font-size: 0.65rem;">
                    @if($member->user->avatar_url)
                        <img src="{{ $member->user->avatar_url }}" alt="{{ $member->user->username }}" class="avatar w-6 h-6">
                    @else
                        {{ strtoupper(substr($member->user->username, 0, 1)) }}
                    @endif
                </div>
                <span class="text-xs truncate flex-1" style="color: var(--color-sidebar-text);">{{ $member->user->username }}</span>
                @if($member->role->value === 'admin')
                <span class="text-xs px-1.5 py-0.5 rounded font-medium flex-shrink-0"
                      style="background: rgba(59,130,246,0.15); color: var(--color-accent-300); font-size: 0.6rem;">Admin</span>
                @endif
            </div>
            @endforeach
        </div>
        @else
        {{-- Non-member sidebar state --}}
        <div class="flex-1 flex flex-col items-center justify-center p-4 text-center">
            <p class="text-xs mb-3" style="color: var(--color-sidebar-text-muted);">Join this workspace to see channels and members.</p>
        </div>
        @endif
    </div>

    {{-- Main area: workspace overview --}}
    <div class="flex-1 flex flex-col overflow-hidden" style="background: var(--color-bg-main);">
        <div class="page-header">
            <div>
                <h1>{{ $workspace->name }}</h1>
                @if($workspace->description)
                <p>{{ $workspace->description }}</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if($isAdmin)
                <button onclick="document.getElementById('add-member-modal').classList.remove('hidden')"
                        class="btn btn-secondary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                    </svg>
                    Add Member
                </button>
                <a href="{{ route('workspaces.edit', $workspace) }}" class="btn btn-secondary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>
                @endif
                @if(!$isMember)
                @if($myRequest && $myRequest->status === 'pending')
                <span class="btn btn-sm" style="background: var(--color-warning-bg); color: var(--color-warning-text); border: 1px solid var(--color-warning-border); cursor: default;">
                    ⏳ Request Pending
                </span>
                @else
                <form method="POST" action="{{ route('workspaces.join', $workspace) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">Join Workspace</button>
                </form>
                @endif
                @endif
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6">
            {{-- Flash messages --}}
            @if(session('success'))
            <div class="alert-success mb-5 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="alert-error mb-5">{{ session('error') }}</div>
            @endif

            @if($isMember)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Pending Join Requests (Admin only) --}}
                @if($isAdmin && $joinRequests->isNotEmpty())
                <div class="md:col-span-2">
                    <h2 class="text-xs font-semibold uppercase tracking-widest mb-3" style="color: var(--color-text-secondary);">
                        Pending Join Requests
                        <span class="ml-2 badge">{{ $joinRequests->count() }}</span>
                    </h2>
                    <div class="card overflow-hidden">
                        @foreach($joinRequests as $req)
                        <div class="flex items-center gap-4 px-5 py-4 border-b last:border-b-0" style="border-color: var(--color-border);">
                            <div class="avatar-initials w-9 h-9 text-sm flex-shrink-0">{{ strtoupper(substr($req->user->username, 0, 1)) }}</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold" style="color: var(--color-text-primary);">{{ $req->user->username }}</p>
                                <p class="text-xs" style="color: var(--color-text-muted);">{{ $req->user->email }} · {{ $req->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex gap-2 flex-shrink-0">
                                <form method="POST" action="{{ route('workspaces.join-requests.approve', [$workspace, $req]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">Accept</button>
                                </form>
                                <form method="POST" action="{{ route('workspaces.join-requests.reject', [$workspace, $req]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Decline</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Channels grid --}}
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xs font-semibold uppercase tracking-widest" style="color: var(--color-text-secondary);">Channels</h2>
                        @can('update', $workspace)
                        <a href="{{ route('workspaces.channels.create', $workspace) }}" class="btn btn-secondary btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            New Channel
                        </a>
                        @endcan
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @forelse($channels as $channel)
                        @php $inChannel = $channel->users->isNotEmpty(); @endphp
                        <div class="card p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm" style="color: var(--color-text-muted);">{{ $channel->is_private ? '🔒' : '#' }}</span>
                                    <h3 class="font-semibold text-sm" style="color: var(--color-text-primary);">{{ $channel->channel_name }}</h3>
                                </div>
                                @if($inChannel)
                                <a href="{{ route('channels.show', $channel) }}" class="btn btn-primary btn-sm">Open</a>
                                @elseif(!$channel->is_private)
                                <form method="POST" action="{{ route('channels.join', [$workspace, $channel]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm">Join</button>
                                </form>
                                @else
                                <span class="text-xs px-2 py-1 rounded" style="background: var(--color-primary-100); color: var(--color-text-muted);">Private</span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="card p-8 text-center col-span-2">
                            <p class="text-sm" style="color: var(--color-text-secondary);">No channels yet.</p>
                            @can('update', $workspace)
                            <a href="{{ route('workspaces.channels.create', $workspace) }}" class="btn btn-primary btn-sm mt-3 inline-flex">Create First Channel</a>
                            @endcan
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Members panel --}}
                <div class="card p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-semibold" style="color: var(--color-text-primary);">Members ({{ $members->count() }})</h2>
                        @if($isAdmin)
                        <button onclick="document.getElementById('add-member-modal').classList.remove('hidden')" class="btn btn-secondary btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" /></svg>
                            Add Member
                        </button>
                        @endif
                    </div>
                    <div class="space-y-3">
                        @foreach($members as $member)
                        <div class="flex items-center gap-3">
                            <div class="relative flex-shrink-0">
                                <div class="avatar-initials w-8 h-8 text-sm flex items-center justify-center">
                                    @if($member->user->avatar_url)
                                        <img src="{{ $member->user->avatar_url }}" alt="{{ $member->user->username }}" class="avatar w-8 h-8 rounded-full">
                                    @else
                                        {{ strtoupper(substr($member->user->username, 0, 1)) }}
                                    @endif
                                </div>
                                @if($member->user->isOnline())
                                    <span class="absolute bottom-0 right-0 block h-2 w-2 rounded-full ring-1 ring-white" style="background-color: #22c55e;"></span>
                                @else
                                    <span class="absolute bottom-0 right-0 block h-2 w-2 rounded-full ring-1 ring-white" style="background-color: #9ca3af;"></span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate" style="color: var(--color-text-primary);">{{ $member->user->username }}</p>
                                @if($member->user->name)
                                <p class="text-xs truncate" style="color: var(--color-text-muted);">{{ $member->user->name }}</p>
                                @endif
                            </div>
                            @if($member->role->value === 'admin')
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                  style="background: var(--color-accent-50); color: var(--color-accent-700);">Admin</span>
                            @elseif($isAdmin)
                            <form method="POST" action="{{ route('workspaces.members.remove', [$workspace, $member->member_id]) }}" onsubmit="return confirm('Are you sure you want to remove this member?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold hover:underline" style="color: #ef4444; background: none; border: none; cursor: pointer; padding: 0;">
                                    Remove
                                </button>
                            </form>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>

            @else
            {{-- Non-member state --}}
            <div class="flex flex-col items-center justify-center h-full text-center py-20">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4" style="background: var(--color-primary-100);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8" style="color: var(--color-text-muted);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold mb-2" style="color: var(--color-text-primary);">You're not a member</h2>
                <p class="text-sm mb-6" style="color: var(--color-text-secondary);">Join this workspace to access channels and collaborate with the team.</p>
                @if($myRequest && $myRequest->status === 'pending')
                <div class="alert-info px-6 py-3 text-sm">
                    ⏳ Your join request is pending admin approval.
                </div>
                @elseif($myRequest && $myRequest->status === 'rejected')
                <div class="alert-error px-6 py-3 text-sm">
                    Your previous request was declined.
                </div>
                @else
                <form method="POST" action="{{ route('workspaces.join', $workspace) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                        Request to Join
                    </button>
                </form>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- Add Member Modal --}}
    @if($isAdmin)
    <div id="add-member-modal"
         class="hidden fixed inset-0 flex items-center justify-center z-50"
         style="background: rgba(15,23,42,0.5); backdrop-filter: blur(2px);"
         onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="modal-box">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-base" style="color: var(--color-text-primary);">Add Member</h3>
                <button onclick="document.getElementById('add-member-modal').classList.add('hidden')"
                        style="background: none; border: none; cursor: pointer; color: var(--color-text-muted); font-size: 1.25rem; line-height: 1;">✕</button>
            </div>
            <p class="text-sm mb-4" style="color: var(--color-text-secondary);">Enter the email address of the person you want to invite.</p>
            <form method="POST" action="{{ route('workspaces.invite', $workspace) }}">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email"
                           placeholder="colleague@company.com"
                           required
                           class="form-input">
                    @error('email')
                    <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button"
                            onclick="document.getElementById('add-member-modal').classList.add('hidden')"
                            class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                        Send Invitation
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</x-app-layout>
