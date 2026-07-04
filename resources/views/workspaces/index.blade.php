<x-app-layout>
    <x-slot name="title">Workspaces</x-slot>

    {{-- Left sidebar --}}
    <div class="flex flex-col w-60 flex-shrink-0 border-r sidebar-scroll overflow-y-auto" style="background: var(--color-sidebar-bg); border-color: var(--color-sidebar-border);">
        <div class="p-4 border-b" style="border-color: var(--color-sidebar-border);">
            <p class="section-label">My Workspaces</p>
        </div>
        <nav class="flex-1 p-2">
            @forelse($myWorkspaces as $workspace)
            <a href="{{ route('workspaces.show', $workspace) }}" class="nav-item mb-0.5">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0"
                     style="background: var(--color-accent-700); color: white;">
                    {{ strtoupper(substr($workspace->name, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium truncate" style="color: inherit;">{{ $workspace->name }}</p>
                    <p class="text-xs" style="color: var(--color-sidebar-text-muted);">{{ $workspace->workspaceMembers->count() }} members</p>
                </div>
            </a>
            @empty
            <p class="text-xs px-3 py-2" style="color: var(--color-sidebar-text-muted);">No workspaces yet</p>
            @endforelse
        </nav>
        <div class="p-3 border-t" style="border-color: var(--color-sidebar-border);">
            <a href="{{ route('workspaces.create') }}" class="nav-item" style="color: var(--color-accent-400);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Create workspace
            </a>
        </div>
    </div>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col overflow-hidden" style="background: var(--color-bg-main);">
        <div class="page-header">
            <div>
                <h1>Welcome back, {{ auth()->user()->username }} 👋</h1>
                <p>Your team communication hub — channels, messages, and tasks in one place.</p>
            </div>
            <a href="{{ route('workspaces.create') }}" class="btn btn-primary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                New Workspace
            </a>
        </div>

        <div class="flex-1 overflow-y-auto p-6">
            {{-- Flash messages --}}
            @if(session('success'))
            <div class="alert-success mb-5 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('info'))
            <div class="alert-info mb-5">{{ session('info') }}</div>
            @endif
            @if(session('error'))
            <div class="alert-error mb-5">{{ session('error') }}</div>
            @endif

            {{-- My Workspaces --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xs font-semibold uppercase tracking-widest" style="color: var(--color-text-secondary);">Your Workspaces</h2>
                </div>
                @if($myWorkspaces->isEmpty())
                <div class="card p-10 text-center">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3" style="background: var(--color-accent-50);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6" style="color: var(--color-accent-500);">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z" />
                        </svg>
                    </div>
                    <p class="text-sm mb-4" style="color: var(--color-text-secondary);">You haven't joined any workspaces yet.</p>
                    <a href="{{ route('workspaces.create') }}" class="btn btn-primary">Create a Workspace</a>
                </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($myWorkspaces as $workspace)
                    <a href="{{ route('workspaces.show', $workspace) }}"
                       class="card p-5 block transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 group">
                        <div class="flex items-start gap-3">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0"
                                 style="background: var(--color-accent-600); color: white;">
                                {{ strtoupper(substr($workspace->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-sm group-hover:text-blue-600 transition-colors" style="color: var(--color-text-primary);">{{ $workspace->name }}</h3>
                                @if($workspace->description)
                                <p class="text-xs mt-1 line-clamp-2" style="color: var(--color-text-secondary);">{{ $workspace->description }}</p>
                                @endif
                                <p class="text-xs mt-2 font-medium" style="color: var(--color-text-muted);">{{ $workspace->workspaceMembers->count() }} members</p>
                            </div>
                        </div>
                    </a>
                    @endforeach

                    {{-- Create new card --}}
                    <a href="{{ route('workspaces.create') }}"
                       class="flex flex-col items-center justify-center rounded-xl p-5 border-2 border-dashed transition-all duration-200 hover:border-solid min-h-[120px] group"
                       style="border-color: var(--color-border-dark); color: var(--color-text-muted);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 mb-2 group-hover:text-blue-500 transition-colors">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span class="text-sm font-medium group-hover:text-blue-500 transition-colors">New Workspace</span>
                    </a>
                </div>
                @endif
            </div>

            {{-- Discover Other Workspaces --}}
            @if($otherWorkspaces->isNotEmpty())
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xs font-semibold uppercase tracking-widest" style="color: var(--color-text-secondary);">Discover Workspaces</h2>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background: var(--color-accent-50); color: var(--color-accent-700);">{{ $otherWorkspaces->count() }} available</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($otherWorkspaces as $workspace)
                    @php $hasPendingRequest = in_array($workspace->workspace_id, $pendingRequestIds); @endphp
                    <div class="card p-5">
                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0"
                                 style="background: var(--color-primary-100); color: var(--color-primary-600);">
                                {{ strtoupper(substr($workspace->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-sm" style="color: var(--color-text-primary);">{{ $workspace->name }}</h3>
                                @if($workspace->description)
                                <p class="text-xs mt-1 line-clamp-2" style="color: var(--color-text-secondary);">{{ $workspace->description }}</p>
                                @endif
                                <p class="text-xs mt-2 font-medium" style="color: var(--color-text-muted);">{{ $workspace->workspaceMembers->count() }} members</p>
                            </div>
                        </div>
                        @if($hasPendingRequest)
                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium" style="background: var(--color-warning-bg); color: var(--color-warning-text); border: 1px solid var(--color-warning-border);">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Request Pending
                        </div>
                        @else
                        <form method="POST" action="{{ route('workspaces.join', $workspace) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm w-full justify-center"
                                    style="background: var(--color-accent-50); color: var(--color-accent-700); border: 1px solid var(--color-accent-200);"
                                    onmouseover="this.style.background='var(--color-accent-100)'"
                                    onmouseout="this.style.background='var(--color-accent-50)'">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                                Request to Join
                            </button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
