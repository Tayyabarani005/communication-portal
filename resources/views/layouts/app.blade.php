<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Synapse — Enterprise Communication & Collaboration platform for teams.">
    <title>{{ config('app.name', 'Synapse') }} — {{ $title ?? 'Dashboard' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased" style="font-family: 'Inter', sans-serif;">

<div class="flex h-screen overflow-hidden">

    {{-- ══ Left: Narrow workspace icon rail ══ --}}
    <div class="flex flex-col items-center w-14 py-3 gap-2 flex-shrink-0" style="background: var(--color-rail-bg);">

        {{-- Logo / Home --}}
        <a href="{{ route('workspaces.index') }}" title="All Workspaces"
           class="w-9 h-9 rounded-xl flex items-center justify-center mb-1 hover:opacity-80 transition-all"
           style="background: var(--color-accent-600);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
            </svg>
        </a>

        <div style="width:28px; height:1px; background: rgba(255,255,255,0.06);"></div>

        {{-- Workspace Icons --}}
        @auth
        @foreach(auth()->user()->workspaces as $ws)
            <a href="{{ route('workspaces.show', $ws) }}"
               title="{{ $ws->name }}"
               class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-bold transition-all hover:rounded-lg relative group"
               style="background: var(--color-primary-700); color: var(--color-primary-300);">
                {{ strtoupper(substr($ws->name, 0, 2)) }}
                <span class="absolute left-full ml-2 px-2 py-1 rounded text-xs font-medium whitespace-nowrap pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity z-50"
                      style="background: var(--color-primary-800); color: white; font-size: 0.7rem;">
                    {{ $ws->name }}
                </span>
            </a>
        @endforeach

        {{-- Add new workspace --}}
        <a href="{{ route('workspaces.create') }}"
           title="New Workspace"
           class="w-9 h-9 rounded-xl border-2 border-dashed flex items-center justify-center hover:border-solid transition-all"
           style="border-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.3);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </a>
        @endauth

        {{-- Bottom icons --}}
        <div class="mt-auto flex flex-col items-center gap-2">
            @auth

            {{-- DM --}}
            <a href="{{ route('dms.index') }}" title="Direct Messages"
               class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors"
               style="color: rgba(255,255,255,0.35);"
               onmouseover="this.style.background='rgba(255,255,255,0.07)'; this.style.color='white'"
               onmouseout="this.style.background='transparent'; this.style.color='rgba(255,255,255,0.35)'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                </svg>
            </a>

            {{-- Notifications Bell --}}
            @php
                $pendingJoinRequests = \App\Models\WorkspaceJoinRequest::whereHas('workspace.workspaceMembers', function($q) {
                    $q->where('user_id', auth()->user()->user_id)->where('role', 'admin');
                })->where('status', 'pending')->count();

                $myAcceptedRequests = \App\Models\WorkspaceJoinRequest::where('user_id', auth()->user()->user_id)
                    ->where('status', 'accepted')
                    ->whereDate('updated_at', '>=', now()->subDays(7))
                    ->count();

                $notifCount = $pendingJoinRequests + $myAcceptedRequests;
            @endphp

            <div class="relative" id="notif-wrapper">
                <button onclick="toggleNotifications()"
                        title="Notifications"
                        class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors relative"
                        style="color: rgba(255,255,255,0.35); background: transparent; border: none; cursor: pointer;"
                        onmouseover="this.style.background='rgba(255,255,255,0.07)'; this.style.color='white'"
                        onmouseout="this.style.background='transparent'; this.style.color='rgba(255,255,255,0.35)'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    @if($notifCount > 0)
                    <span id="notif-badge" class="absolute -top-1 -right-1 w-4 h-4 rounded-full flex items-center justify-center text-white font-bold"
                          style="background: #ef4444; font-size: 0.55rem;">
                        {{ $notifCount > 9 ? '9+' : $notifCount }}
                    </span>
                    @endif
                </button>

                {{-- Notification Dropdown --}}
                <div id="notif-dropdown"
                     class="hidden absolute bottom-full left-full ml-2 mb-2 rounded-xl shadow-2xl overflow-hidden"
                     style="background: white; border: 1px solid var(--color-border); width: 320px; z-index: 100;">
                    <div class="px-4 py-3 border-b flex items-center justify-between" style="border-color: var(--color-border);">
                        <span class="font-semibold text-sm" style="color: var(--color-text-primary);">Notifications</span>
                        <button onclick="toggleNotifications()" style="background:none;border:none;cursor:pointer;color:var(--color-text-muted);">✕</button>
                    </div>
                    <div class="overflow-y-auto" style="max-height: 320px;">
                        @php
                            // Admin: pending join requests for workspaces I admin
                            $adminWorkspaceIds = \App\Models\WorkspaceMember::where('user_id', auth()->user()->user_id)
                                ->where('role', 'admin')
                                ->pluck('workspace_id');

                            $pendingRequests = \App\Models\WorkspaceJoinRequest::whereIn('workspace_id', $adminWorkspaceIds)
                                ->where('status', 'pending')
                                ->with(['user', 'workspace'])
                                ->latest()
                                ->get();

                            // My accepted/rejected requests
                            $myRequests = \App\Models\WorkspaceJoinRequest::where('user_id', auth()->user()->user_id)
                                ->whereIn('status', ['accepted', 'rejected'])
                                ->whereDate('updated_at', '>=', now()->subDays(7))
                                ->with('workspace')
                                ->latest()
                                ->get();
                        @endphp

                        @if($pendingRequests->isEmpty() && $myRequests->isEmpty())
                        <div class="px-4 py-8 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 mx-auto mb-2" style="color: var(--color-text-muted);">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            <p class="text-sm" style="color: var(--color-text-muted);">No notifications</p>
                        </div>
                        @endif

                        {{-- Admin: pending join requests --}}
                        @foreach($pendingRequests as $req)
                        <div class="px-4 py-3 border-b" style="border-color: var(--color-border);">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                                     style="background: var(--color-accent-100); color: var(--color-accent-700);">
                                    {{ strtoupper(substr($req->user->username, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium" style="color: var(--color-text-primary);">
                                        <span class="font-semibold">{{ $req->user->username }}</span> wants to join <span class="font-semibold">{{ $req->workspace->name }}</span>
                                    </p>
                                    <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">{{ $req->created_at->diffForHumans() }}</p>
                                    <div class="flex gap-2 mt-2">
                                        <form method="POST" action="{{ route('workspaces.join-requests.approve', [$req->workspace, $req]) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary">Accept</button>
                                        </form>
                                        <form method="POST" action="{{ route('workspaces.join-requests.reject', [$req->workspace, $req]) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-secondary" style="color: #dc2626;">Decline</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        {{-- My request status updates --}}
                        @foreach($myRequests as $req)
                        <div class="px-4 py-3 border-b" style="border-color: var(--color-border);">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                     style="background: {{ $req->status === 'accepted' ? 'var(--color-success-bg)' : 'var(--color-error-bg)' }}; color: {{ $req->status === 'accepted' ? 'var(--color-success-text)' : 'var(--color-error-text)' }}; font-size: 1rem;">
                                    {{ $req->status === 'accepted' ? '✓' : '✕' }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm" style="color: var(--color-text-primary);">
                                        Your request to join <span class="font-semibold">{{ $req->workspace->name }}</span> was
                                        <span class="font-semibold" style="color: {{ $req->status === 'accepted' ? 'var(--color-success-text)' : 'var(--color-error-text)' }}">{{ $req->status }}</span>.
                                    </p>
                                    <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">{{ $req->updated_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Profile Avatar --}}
            <a href="{{ route('profile.edit') }}"
               title="Edit Profile"
               class="w-9 h-9 rounded-full overflow-hidden flex items-center justify-center text-xs font-bold hover:ring-2 hover:ring-blue-400 transition-all"
               style="background: var(--color-accent-700); color: white;">
                @if(auth()->user()->avatar_url)
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->username }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr(auth()->user()->username, 0, 2)) }}
                @endif
            </a>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        title="Log Out"
                        class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors"
                        style="color: rgba(255,255,255,0.3); background: transparent; border: none; cursor: pointer;"
                        onmouseover="this.style.background='rgba(239,68,68,0.15)'; this.style.color='#f87171'"
                        onmouseout="this.style.background='transparent'; this.style.color='rgba(255,255,255,0.3)'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                </button>
            </form>
            @endauth
        </div>
    </div>

    {{-- ══ Main content (sidebar + chat) ══ --}}
    <div class="flex flex-1 overflow-hidden">
        {{ $slot }}
    </div>
</div>

<script>
let notificationsSeen = false;

function toggleNotifications() {
    const dropdown = document.getElementById('notif-dropdown');
    dropdown.classList.toggle('hidden');

    // If opening the dropdown, clear the badge and mark as seen
    if (!dropdown.classList.contains('hidden') && !notificationsSeen) {
        const badge = document.getElementById('notif-badge');
        if (badge) badge.style.display = 'none';

        fetch('{{ route("notifications.markSeen") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then(res => {
            if (res.ok) notificationsSeen = true;
        }).catch(err => console.error(err));
    }
}

// Close when clicking outside
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('notif-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('notif-dropdown')?.classList.add('hidden');
    }
});
</script>

@livewireScripts
@stack('scripts')
</body>
</html>
