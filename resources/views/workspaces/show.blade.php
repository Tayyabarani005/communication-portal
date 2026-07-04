<x-app-layout>
    <x-slot name="title">{{ $workspace->name }}</x-slot>

    {{-- Left: Channel sidebar --}}
    <div class="flex flex-col w-60 flex-shrink-0 border-r overflow-y-auto" style="background: var(--color-sidebar-bg); border-color: var(--color-primary-800);">
        <div class="p-4 border-b" style="border-color: var(--color-primary-800);">
            <h2 class="font-semibold text-sm" style="color: white;">{{ $workspace->name }}</h2>
            <p class="text-xs mt-0.5" style="color: var(--color-primary-500);">{{ $members->count() }} members</p>
        </div>

        @if($isMember)
        {{-- Channels list --}}
        <div class="flex-1 p-2">
            <div class="flex items-center justify-between px-2 py-1.5 mb-1">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-primary-500);">Channels</span>
                @can('update', $workspace)
                <a href="{{ route('workspaces.channels.create', $workspace) }}"
                   class="text-xs hover:text-white transition-colors" style="color: var(--color-primary-500);">+</a>
                @endcan
            </div>

            @foreach($channels as $channel)
            @php $inChannel = $channel->users->isNotEmpty(); @endphp
            <div class="flex items-center group">
                <a href="{{ $inChannel ? route('channels.show', $channel) : '#' }}"
                   class="flex-1 flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm transition-colors"
                   style="color: {{ $inChannel ? 'var(--color-sidebar-text)' : 'var(--color-primary-700)' }};"
                   onmouseover="this.style.background='var(--color-sidebar-hover-bg)'; this.style.color='white'"
                   onmouseout="this.style.background='transparent'; this.style.color='{{ $inChannel ? 'var(--color-sidebar-text)' : 'var(--color-primary-700)' }}'">
                    <span class="text-xs opacity-70">{{ $channel->is_private ? '🔒' : '#' }}</span>
                    <span class="truncate">{{ $channel->channel_name }}</span>
                </a>
                @if(!$inChannel && !$channel->is_private)
                <form method="POST" action="{{ route('channels.join', [$workspace, $channel]) }}">
                    @csrf
                    <button type="submit" class="text-xs px-2 hidden group-hover:block" style="color: var(--color-accent-400);">Join</button>
                </form>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Members --}}
        <div class="p-3 border-t" style="border-color: var(--color-primary-800);">
            <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color: var(--color-primary-500);">Members ({{ $members->count() }})</p>
            @foreach($members->take(6) as $member)
            <div class="flex items-center gap-2 py-1">
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0" style="background: var(--color-accent-800); color: white;">
                    {{ strtoupper(substr($member->user->username, 0, 1)) }}
                </div>
                <span class="text-xs truncate" style="color: var(--color-primary-400);">{{ $member->user->username }}</span>
                @if($member->role->value === 'admin')
                <span class="ml-auto text-xs px-1.5 py-0.5 rounded" style="background: var(--color-accent-900); color: var(--color-accent-300);">Admin</span>
                @endif
            </div>
            @endforeach
        </div>
        @else
        {{-- Non-member message --}}
        <div class="flex-1 flex flex-col items-center justify-center p-4 text-center">
            <p class="text-xs mb-3" style="color: var(--color-primary-500);">Join this workspace to see channels and members.</p>
            <form method="POST" action="{{ route('workspaces.join', $workspace) }}">
                @csrf
                <button type="submit"
                        class="px-4 py-2 rounded-lg text-xs font-semibold text-white"
                        style="background: var(--color-accent-600);">
                    Join Workspace
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- Main area: workspace overview --}}
    <div class="flex-1 flex flex-col overflow-hidden" style="background: var(--color-bg-main);">
        <div class="px-6 py-4 border-b flex items-center justify-between" style="background: white; border-color: var(--color-border);">
            <div>
                <h1 class="text-lg font-semibold" style="color: var(--color-primary-900);">{{ $workspace->name }}</h1>
                @if($workspace->description)
                <p class="text-sm mt-0.5" style="color: var(--color-primary-500);">{{ $workspace->description }}</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @can('update', $workspace)
                <a href="{{ route('workspaces.edit', $workspace) }}"
                   class="text-xs px-3 py-1.5 rounded-lg border transition-all"
                   style="border-color: var(--color-border); color: var(--color-primary-600);">
                    ⚙ Settings
                </a>
                @endcan
                @if(!$isMember)
                <form method="POST" action="{{ route('workspaces.join', $workspace) }}">
                    @csrf
                    <button type="submit"
                            class="text-xs px-4 py-1.5 rounded-lg font-semibold text-white"
                            style="background: var(--color-accent-600);">
                        Join Workspace
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6">
            @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg text-sm" style="background: #dcfce7; color: #166534;">✓ {{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="mb-4 px-4 py-3 rounded-lg text-sm" style="background: #fee2e2; color: #991b1b;">{{ session('error') }}</div>
            @endif

            @if($isMember)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Channels grid --}}
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xs font-semibold uppercase tracking-widest" style="color: var(--color-primary-500);">Channels</h2>
                        @can('update', $workspace)
                        <a href="{{ route('workspaces.channels.create', $workspace) }}"
                           class="text-xs px-3 py-1 rounded-lg font-medium"
                           style="background: var(--color-primary-800); color: white;">
                            + New Channel
                        </a>
                        @endcan
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @forelse($channels as $channel)
                        @php $inChannel = $channel->users->isNotEmpty(); @endphp
                        <div class="rounded-xl border p-4" style="background: white; border-color: var(--color-border);">
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="font-semibold text-sm" style="color: var(--color-primary-900);">
                                    <span class="mr-1" style="color: var(--color-primary-400);">{{ $channel->is_private ? '🔒' : '#' }}</span>{{ $channel->channel_name }}
                                </h3>
                                @if($inChannel)
                                <a href="{{ route('channels.show', $channel) }}"
                                   class="text-xs font-semibold px-3 py-1 rounded-lg transition-all"
                                   style="background: var(--color-primary-800); color: white;">Open</a>
                                @elseif(!$channel->is_private)
                                <form method="POST" action="{{ route('channels.join', [$workspace, $channel]) }}">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs font-semibold px-3 py-1 rounded-lg border transition-all"
                                            style="border-color: var(--color-accent-500); color: var(--color-accent-600);">Join</button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @empty
                        <p class="text-sm col-span-2" style="color: var(--color-primary-500);">No channels yet. Create the first one!</p>
                        @endforelse
                    </div>
                </div>

                {{-- Invite Member (admin only) --}}
                @can('update', $workspace)
                <div class="rounded-xl border p-5" style="background: white; border-color: var(--color-border);">
                    <h2 class="text-sm font-semibold mb-1" style="color: var(--color-primary-900);">Invite a Member</h2>
                    <p class="text-xs mb-4" style="color: var(--color-primary-500);">Enter their email address to add them to this workspace.</p>
                    <form method="POST" action="{{ route('workspaces.invite', $workspace) }}" class="flex gap-2">
                        @csrf
                        <input type="email" name="email"
                               placeholder="colleague@company.com"
                               required
                               class="flex-1 px-3 py-2 rounded-lg border text-sm outline-none"
                               style="border-color: var(--color-border); background: var(--color-primary-50); color: var(--color-primary-900);">
                        <button type="submit"
                                class="px-4 py-2 rounded-lg text-sm font-semibold text-white transition-all hover:opacity-90"
                                style="background: var(--color-accent-600);">
                            Send Invitation
                        </button>
                    </form>
                    @error('email')
                    <p class="text-xs mt-2" style="color: #dc2626;">{{ $message }}</p>
                    @enderror
                </div>
                @endcan

                {{-- Members panel --}}
                <div class="rounded-xl border p-5" style="background: white; border-color: var(--color-border);">
                    <h2 class="text-sm font-semibold mb-3" style="color: var(--color-primary-900);">Members ({{ $members->count() }})</h2>
                    <div class="space-y-2">
                        @foreach($members as $member)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                                 style="background: var(--color-accent-800); color: white;">
                                {{ strtoupper(substr($member->user->username, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate" style="color: var(--color-primary-900);">{{ $member->user->username }}</p>
                                @if($member->user->name)
                                <p class="text-xs truncate" style="color: var(--color-primary-400);">{{ $member->user->name }}</p>
                                @endif
                            </div>
                            @if($member->role->value === 'admin')
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background: var(--color-accent-900); color: var(--color-accent-300);">Admin</span>
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
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8" style="color: var(--color-primary-400);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold mb-2" style="color: var(--color-primary-700);">You're not a member</h2>
                <p class="text-sm mb-6" style="color: var(--color-primary-500);">Join this workspace to access channels and collaborate with the team.</p>
                <form method="POST" action="{{ route('workspaces.join', $workspace) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold text-white transition-all hover:opacity-90"
                            style="background: var(--color-accent-600);">
                        Join Workspace
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
