<x-app-layout>
    <x-slot name="title">Workspaces</x-slot>

    {{-- Left sidebar --}}
    <div class="flex flex-col w-64 flex-shrink-0 border-r" style="background: var(--color-sidebar-bg); border-color: var(--color-primary-800);">
        <div class="p-4 border-b" style="border-color: var(--color-primary-800);">
            <h2 class="text-xs font-semibold uppercase tracking-widest" style="color: var(--color-sidebar-text);">Workspaces</h2>
        </div>
        <nav class="flex-1 overflow-y-auto p-2">
            @foreach($workspaces as $workspace)
            <a href="{{ route('workspaces.show', $workspace) }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg mb-1 transition-colors duration-100 group"
               style="color: var(--color-sidebar-text);"
               onmouseover="this.style.background='var(--color-sidebar-hover-bg)'; this.style.color='var(--color-sidebar-text-active)'"
               onmouseout="this.style.background='transparent'; this.style.color='var(--color-sidebar-text)'">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0" style="background: var(--color-accent-700); color: white;">
                    {{ strtoupper(substr($workspace->name, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium truncate">{{ $workspace->name }}</p>
                    <p class="text-xs truncate" style="color: var(--color-primary-500);">{{ $workspace->workspaceMembers->count() }} members</p>
                </div>
            </a>
            @endforeach
        </nav>
        <div class="p-3 border-t" style="border-color: var(--color-primary-800);">
            <a href="{{ route('workspaces.create') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
               style="color: var(--color-accent-400);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Create workspace
            </a>
        </div>
    </div>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col overflow-hidden" style="background: var(--color-bg-main);">
        <div class="border-b px-6 py-4" style="background: white; border-color: var(--color-border);">
            <h1 class="text-lg font-semibold" style="color: var(--color-primary-900);">Welcome back, {{ auth()->user()->username }} 👋</h1>
            <p class="text-sm mt-0.5" style="color: var(--color-primary-500);">Synapse connects your teams, tools, and communication in one high-precision hub.</p>
        </div>

        <div class="flex-1 overflow-y-auto p-6">
            @if($workspaces->isEmpty())
            <div class="flex flex-col items-center justify-center h-full text-center py-20">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4" style="background: var(--color-primary-100);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8" style="color: var(--color-primary-400);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold mb-2" style="color: var(--color-primary-700);">No workspaces yet</h2>
                <p class="text-sm mb-4" style="color: var(--color-primary-500);">Create your first workspace to get started.</p>
                <a href="{{ route('workspaces.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-all"
                   style="background: var(--color-accent-600);">
                    Create a Workspace
                </a>
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($workspaces as $workspace)
                <a href="{{ route('workspaces.show', $workspace) }}"
                   class="group block rounded-xl p-5 border transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5"
                   style="background: white; border-color: var(--color-border);">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0" style="background: var(--color-accent-700); color: white;">
                            {{ strtoupper(substr($workspace->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-sm" style="color: var(--color-primary-900);">{{ $workspace->name }}</h3>
                            @if($workspace->description)
                            <p class="text-xs mt-1 line-clamp-2" style="color: var(--color-primary-500);">{{ $workspace->description }}</p>
                            @endif
                            <p class="text-xs mt-2 font-medium" style="color: var(--color-primary-400);">{{ $workspace->workspaceMembers->count() }} members</p>
                        </div>
                    </div>
                </a>
                @endforeach

                {{-- Create new card --}}
                <a href="{{ route('workspaces.create') }}"
                   class="flex flex-col items-center justify-center rounded-xl p-5 border-2 border-dashed transition-all duration-200 hover:border-solid min-h-[120px]"
                   style="border-color: var(--color-primary-200); color: var(--color-primary-400);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 mb-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span class="text-sm font-medium">Start a Workspace</span>
                </a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
