<x-app-layout>
    <x-slot name="title">#{{ $channel->channel_name }}</x-slot>

    {{-- ══ Left Sidebar ══ --}}
    <div class="flex flex-col w-60 flex-shrink-0 border-r overflow-y-auto" style="background: var(--color-sidebar-bg); border-color: var(--color-primary-800);">
        {{-- Workspace header --}}
        <div class="p-4 border-b" style="border-color: var(--color-primary-800);">
            <a href="{{ route('workspaces.show', $channel->workspace) }}"
               class="font-semibold text-sm hover:underline" style="color: white;">
                {{ $channel->workspace->name }}
            </a>
        </div>

        {{-- Livewire Channel sidebar --}}
        @livewire('channel-sidebar', ['workspace' => $channel->workspace])

        {{-- DM section --}}
        <div class="border-t p-2" style="border-color: var(--color-primary-800);">
            <div class="px-2 py-1.5 mb-1">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-primary-500);">Direct Messages</span>
            </div>
            @livewire('dm-sidebar')
        </div>
    </div>

    {{-- ══ Main: Chat Window ══ --}}
    <div class="flex flex-col flex-1 overflow-hidden">
        {{-- Channel header --}}
        <div class="flex items-center justify-between px-5 py-3 border-b flex-shrink-0" style="background: white; border-color: var(--color-border);">
            <div class="flex items-center gap-2">
                <span class="text-lg font-semibold" style="color: var(--color-primary-900);">
                    {{ $channel->is_private ? '🔒' : '#' }} {{ $channel->channel_name }}
                </span>
                @if($channel->is_private)
                <span class="text-xs px-2 py-0.5 rounded-full" style="background: var(--color-primary-100); color: var(--color-primary-600);">Private</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                {{-- Tasks link --}}
                <a href="#tasks" class="p-2 rounded-lg transition-colors hover:bg-gray-100" title="Tasks" style="color: var(--color-primary-500);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                    </svg>
                </a>
                {{-- Pins button --}}
                @livewire('pinned-messages-panel', ['channel' => $channel])
                @can('update', $channel)
                <a href="{{ route('channels.edit', $channel) }}" class="p-2 rounded-lg transition-colors hover:bg-gray-100" style="color: var(--color-primary-500);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </a>
                @endcan
            </div>
        </div>

        {{-- Livewire Chat Window --}}
        @livewire('chat-window', ['channel' => $channel])
    </div>

    {{-- ══ Right: Task Board panel (collapsible) ══ --}}
    <div id="tasks" class="hidden lg:flex flex-col w-72 border-l flex-shrink-0 overflow-y-auto" style="background: var(--color-bg-main); border-color: var(--color-border);">
        <div class="flex items-center justify-between px-4 py-3 border-b" style="border-color: var(--color-border); background: white;">
            <span class="text-sm font-semibold" style="color: var(--color-primary-800);">Tasks</span>
            <a href="{{ route('tasks.create', $channel) }}" class="text-xs font-semibold px-2 py-1 rounded-lg" style="background: var(--color-accent-600); color: white;">+ New</a>
        </div>
        @livewire('task-board', ['channel' => $channel])
    </div>

</x-app-layout>
