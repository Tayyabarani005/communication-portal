<x-app-layout>
    <x-slot name="title">Direct Messages</x-slot>

    {{-- Left sidebar: DM list --}}
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
        @livewire('dm-sidebar')
    </div>

    {{-- Main: empty state --}}
    <div class="flex-1 flex flex-col items-center justify-center" style="background: var(--color-bg-main);">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4" style="background: var(--color-primary-100);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8" style="color: var(--color-primary-400);">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
            </svg>
        </div>
        <h2 class="text-lg font-semibold mb-2" style="color: var(--color-primary-700);">Your Messages</h2>
        <p class="text-sm mb-4" style="color: var(--color-primary-500);">Select a conversation or start a new one.</p>
        <button onclick="document.getElementById('new-dm-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
                style="background: var(--color-accent-600);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            New Message
        </button>
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
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--color-primary-500);">Username or Email</label>
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

    const form = document.getElementById('new-dm-form');
    form.action = `/dms/start/${userId}`;
}
</script>
@endpush
</x-app-layout>
