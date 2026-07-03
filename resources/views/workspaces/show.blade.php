<x-app-layout>
    <x-slot name="title">{{ $workspace->name }}</x-slot>

    {{-- Left: Channel sidebar --}}
    <div class="flex flex-col w-60 flex-shrink-0 border-r overflow-y-auto" style="background: var(--color-sidebar-bg); border-color: var(--color-primary-800);">
        <div class="p-4 border-b" style="border-color: var(--color-primary-800);">
            <h2 class="font-semibold text-sm" style="color: white;">{{ $workspace->name }}</h2>
            <p class="text-xs mt-0.5" style="color: var(--color-primary-500);">{{ $members->count() }} members</p>
        </div>

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
            @php $isMember = $channel->users->isNotEmpty(); @endphp
            <div class="flex items-center group">
                <a href="{{ $isMember ? route('channels.show', $channel) : '#' }}"
                   class="flex-1 flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm transition-colors"
                   style="color: {{ $isMember ? 'var(--color-sidebar-text)' : 'var(--color-primary-700)' }};"
                   onmouseover="this.style.background='var(--color-sidebar-hover-bg)'; this.style.color='white'"
                   onmouseout="this.style.background='transparent'; this.style.color='{{ $isMember ? 'var(--color-sidebar-text)' : 'var(--color-primary-700)' }}'">
                    <span class="text-xs opacity-70">{{ $channel->is_private ? '🔒' : '#' }}</span>
                    <span class="truncate">{{ $channel->channel_name }}</span>
                </a>
                @if(!$isMember && !$channel->is_private)
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
            @foreach($members->take(5) as $member)
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
    </div>

    {{-- Main area: workspace overview --}}
    <div class="flex-1 flex flex-col overflow-hidden" style="background: var(--color-bg-main);">
        <div class="px-6 py-4 border-b" style="background: white; border-color: var(--color-border);">
            <h1 class="text-lg font-semibold" style="color: var(--color-primary-900);">{{ $workspace->name }}</h1>
            @if($workspace->description)
            <p class="text-sm mt-0.5" style="color: var(--color-primary-500);">{{ $workspace->description }}</p>
            @endif
        </div>

        <div class="flex-1 overflow-y-auto p-6">
            @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg text-sm" style="background: #dcfce7; color: #166534;">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($channels as $channel)
                @php $isMember = $channel->users->isNotEmpty(); @endphp
                <div class="rounded-xl border p-4" style="background: white; border-color: var(--color-border);">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-sm" style="color: var(--color-primary-900);">
                            <span class="mr-1" style="color: var(--color-primary-400);">{{ $channel->is_private ? '🔒' : '#' }}</span>{{ $channel->channel_name }}
                        </h3>
                        @if($isMember)
                        <a href="{{ route('channels.show', $channel) }}" class="text-xs font-semibold px-3 py-1 rounded-lg transition-all" style="background: var(--color-primary-800); color: white;">Open</a>
                        @elseif(!$channel->is_private)
                        <form method="POST" action="{{ route('channels.join', [$workspace, $channel]) }}">
                            @csrf
                            <button type="submit" class="text-xs font-semibold px-3 py-1 rounded-lg border transition-all" style="border-color: var(--color-accent-500); color: var(--color-accent-600);">Join</button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
