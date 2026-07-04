<x-app-layout>
    @php
        $otherUser = $conversation->dmParticipants
            ->firstWhere('user_id', '!=', auth()->user()->user_id)
            ?->user;
    @endphp
    <x-slot name="title">DM with {{ $otherUser?->username ?? 'Unknown' }}</x-slot>

    {{-- Left sidebar: DM list (always visible) --}}
    <div class="flex flex-col w-60 flex-shrink-0 border-r" style="background: var(--color-sidebar-bg); border-color: var(--color-primary-800);">
        <div class="p-4 border-b flex items-center justify-between" style="border-color: var(--color-primary-800);">
            <h2 class="text-sm font-semibold" style="color: white;">Direct Messages</h2>
            {{-- New Message button --}}
            <button onclick="document.getElementById('new-dm-modal').classList.remove('hidden')"
                    title="New Message"
                    class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors"
                    style="background: var(--color-primary-700); color: var(--color-primary-300); border: none; cursor: pointer;"
                    onmouseover="this.style.background='var(--color-accent-600)'; this.style.color='white'"
                    onmouseout="this.style.background='var(--color-primary-700)'; this.style.color='var(--color-primary-300)'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </button>
        </div>
        @livewire('dm-sidebar', ['activeConversationId' => $conversation->conversation_id])
    </div>

    {{-- Main: DM window --}}
    <div class="flex flex-col flex-1 overflow-hidden">
        {{-- DM Header --}}
        <div class="flex items-center gap-3 px-5 py-3 border-b flex-shrink-0" style="background: white; border-color: var(--color-border);">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                 style="background: var(--color-accent-700); color: white;">
                {{ strtoupper(substr($otherUser?->username ?? '?', 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-semibold" style="color: var(--color-primary-900);">
                    {{ $otherUser?->username ?? 'Unknown' }}
                </p>
                @if($otherUser?->name)
                <p class="text-xs" style="color: var(--color-primary-500);">{{ $otherUser->name }}</p>
                @endif
            </div>
        </div>

        {{-- Livewire DM Window --}}
        @livewire('direct-message-window', ['conversation' => $conversation])
    </div>

    {{-- New DM Modal --}}
    <div id="new-dm-modal"
         class="hidden fixed inset-0 flex items-center justify-center z-50"
         style="background: rgba(0,0,0,0.6);"
         onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="rounded-2xl shadow-2xl p-6 w-full max-w-sm"
             style="background: var(--color-sidebar-bg); border: 1px solid rgba(51,65,85,0.8);">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-sm" style="color: white;">New Direct Message</h3>
                <button onclick="document.getElementById('new-dm-modal').classList.add('hidden')"
                        style="background: none; border: none; cursor: pointer; color: var(--color-primary-400);">✕</button>
            </div>
            <p class="text-xs mb-4" style="color: var(--color-primary-500);">Start a conversation with a team member.</p>
            <form method="POST" id="new-dm-form" action="#">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--color-primary-500);">Search Users</label>
                    <input id="dm-search-input"
                           type="text"
                           placeholder="Search users..."
                           autocomplete="off"
                           class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                           style="background: rgba(30,41,59,0.8); border: 1px solid rgba(51,65,85,0.8); color: white;"
                           oninput="searchUsers(this.value)">
                    <div id="dm-user-results" class="mt-2 rounded-lg overflow-hidden" style="border: 1px solid rgba(51,65,85,0.5); display:none;"></div>
                </div>
                <div id="dm-selected-user" style="display:none;" class="mb-4">
                    <p class="text-xs" style="color: var(--color-primary-400);">Starting conversation with: <span id="dm-selected-name" class="font-semibold" style="color: white;"></span></p>
                </div>
                <button type="submit" id="dm-start-btn"
                        class="w-full px-4 py-2 rounded-lg text-sm font-semibold text-white transition-all hover:opacity-90"
                        style="background: var(--color-accent-600);" disabled>
                    Start Conversation
                </button>
            </form>
        </div>
    </div>

@push('scripts')
<script>
let selectedUserId = null;

async function searchUsers(query) {
    const resultsEl = document.getElementById('dm-user-results');
    if (query.length < 2) { resultsEl.style.display = 'none'; return; }
    try {
        const resp = await fetch(`/users/search?q=${encodeURIComponent(query)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!resp.ok) { resultsEl.style.display = 'none'; return; }
        const users = await resp.json();
        if (!users.length) {
            resultsEl.innerHTML = '<div style="padding:0.5rem 0.75rem; font-size:0.75rem; color:#64748b;">No users found</div>';
        } else {
            resultsEl.innerHTML = users.map(u =>
                `<div onclick="selectUser(${u.user_id}, '${u.username}')"
                      style="padding:0.5rem 0.75rem; font-size:0.8rem; color:white; cursor:pointer; background: rgba(15,23,42,0.9);"
                      onmouseover="this.style.background='rgba(2,132,199,0.2)'"
                      onmouseout="this.style.background='rgba(15,23,42,0.9)'">
                    <span style="font-weight:600">${u.username}</span>
                    ${u.name ? `<span style="color:#64748b; margin-left:0.5rem">${u.name}</span>` : ''}
                </div>`
            ).join('');
        }
        resultsEl.style.display = 'block';
    } catch(e) {
        resultsEl.style.display = 'none';
    }
}

function selectUser(userId, username) {
    selectedUserId = userId;
    document.getElementById('dm-search-input').value = username;
    document.getElementById('dm-user-results').style.display = 'none';
    document.getElementById('dm-selected-name').textContent = username;
    document.getElementById('dm-selected-user').style.display = 'block';
    document.getElementById('dm-start-btn').disabled = false;
    document.getElementById('new-dm-form').action = `/dms/start/${userId}`;
}
</script>
@endpush
</x-app-layout>
