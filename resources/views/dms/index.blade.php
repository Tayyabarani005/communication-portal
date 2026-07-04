<x-app-layout>
    <x-slot name="title">Direct Messages</x-slot>

    {{-- Left sidebar: DM list --}}
    <div class="flex flex-col w-60 flex-shrink-0 border-r sidebar-scroll overflow-y-auto" style="background: var(--color-sidebar-bg); border-color: var(--color-sidebar-border);">
        <div class="p-4 border-b flex items-center justify-between" style="border-color: var(--color-sidebar-border);">
            <h2 class="text-sm font-semibold" style="color: var(--color-sidebar-text-active);">Direct Messages</h2>
            <button onclick="document.getElementById('new-dm-modal').classList.remove('hidden')"
                    title="New Message"
                    class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors"
                    style="background: rgba(255,255,255,0.06); color: var(--color-sidebar-text); border: none; cursor: pointer;"
                    onmouseover="this.style.background='rgba(255,255,255,0.12)'; this.style.color='white'"
                    onmouseout="this.style.background='rgba(255,255,255,0.06)'; this.style.color='var(--color-sidebar-text)'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </button>
        </div>
        @livewire('dm-sidebar')
    </div>

    {{-- Main: empty state --}}
    <div class="flex-1 flex flex-col items-center justify-center" style="background: var(--color-bg-main);">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4" style="background: var(--color-accent-50);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8" style="color: var(--color-accent-500);">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
            </svg>
        </div>
        <h2 class="text-lg font-semibold mb-2" style="color: var(--color-text-primary);">Direct Messages</h2>
        <p class="text-sm mb-5" style="color: var(--color-text-secondary);">Select a conversation or start a new one.</p>
        <button onclick="document.getElementById('new-dm-modal').classList.remove('hidden')"
                class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Start New Conversation
        </button>
    </div>

    {{-- New DM Modal --}}
    <div id="new-dm-modal"
         class="hidden fixed inset-0 flex items-center justify-center z-50"
         style="background: rgba(15,23,42,0.5); backdrop-filter: blur(2px);"
         onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="modal-box">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-base" style="color: var(--color-text-primary);">New Direct Message</h3>
                <button onclick="document.getElementById('new-dm-modal').classList.add('hidden')"
                        style="background: none; border: none; cursor: pointer; color: var(--color-text-muted); font-size: 1.25rem; line-height: 1;">✕</button>
            </div>
            <p class="text-sm mb-4" style="color: var(--color-text-secondary);">Search for a team member to start a conversation.</p>

            <div class="mb-4 relative">
                <label class="form-label">Username or Email</label>
                <input id="dm-search-input"
                       type="text"
                       placeholder="Search users..."
                       autocomplete="off"
                       class="form-input"
                       oninput="searchUsers(this.value)">
                <div id="dm-user-results"
                     class="absolute left-0 right-0 mt-1 rounded-lg overflow-hidden shadow-lg"
                     style="background: white; border: 1px solid var(--color-border); display: none; z-index: 10;">
                </div>
            </div>

            <div id="dm-selected-user" style="display:none;" class="mb-4 flex items-center gap-3 p-3 rounded-lg" style="background: var(--color-accent-50); border: 1px solid var(--color-accent-200);">
                <div class="avatar-initials w-8 h-8 text-sm" id="dm-selected-avatar">?</div>
                <div>
                    <p class="text-sm font-semibold" style="color: var(--color-text-primary);" id="dm-selected-name"></p>
                    <p class="text-xs" style="color: var(--color-text-muted);">Click "Start Conversation" to begin</p>
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button"
                        onclick="document.getElementById('new-dm-modal').classList.add('hidden')"
                        class="btn btn-secondary">Cancel</button>
                <button id="dm-start-btn"
                        onclick="startConversation()"
                        class="btn btn-primary"
                        disabled
                        style="opacity: 0.5; cursor: not-allowed;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                    Start Conversation
                </button>
            </div>
        </div>
    </div>

    {{-- Hidden form for DM start --}}
    <form id="dm-start-form" method="POST" action="" style="display:none;">
        @csrf
    </form>

@push('scripts')
<script>
let selectedUserId = null;

async function searchUsers(query) {
    const resultsEl = document.getElementById('dm-user-results');
    if (query.length < 2) {
        resultsEl.style.display = 'none';
        return;
    }

    try {
        const url = `{{ route('users.search') }}?q=${encodeURIComponent(query)}`;
        const resp = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!resp.ok) { resultsEl.style.display = 'none'; return; }
        const users = await resp.json();

        if (!users.length) {
            resultsEl.innerHTML = '<div style="padding:0.75rem 1rem; font-size:0.8125rem; color:#94a3b8;">No users found</div>';
        } else {
            resultsEl.innerHTML = users.map(u =>
                `<div onclick="selectUser(${u.user_id}, '${u.username.replace(/'/g, "\\'")}')"
                      style="padding:0.625rem 1rem; font-size:0.8125rem; color:#0f172a; cursor:pointer; display:flex; align-items:center; gap:0.75rem;"
                      onmouseover="this.style.background='#f1f5f9'"
                      onmouseout="this.style.background='white'">
                    <div style="width:2rem;height:2rem;border-radius:9999px;background:#2563eb;color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.75rem;flex-shrink:0;">
                        ${u.username.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <div style="font-weight:600">${u.username}</div>
                        ${u.name ? `<div style="color:#64748b;font-size:0.75rem;">${u.name}</div>` : ''}
                    </div>
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

    // Show selected user info
    const selectedEl = document.getElementById('dm-selected-user');
    document.getElementById('dm-selected-name').textContent = username;
    document.getElementById('dm-selected-avatar').textContent = username.charAt(0).toUpperCase();
    selectedEl.style.display = 'flex';

    // Enable start button
    const btn = document.getElementById('dm-start-btn');
    btn.disabled = false;
    btn.style.opacity = '1';
    btn.style.cursor = 'pointer';
}

function startConversation() {
    if (!selectedUserId) return;
    const form = document.getElementById('dm-start-form');
    form.action = `/dms/start/${selectedUserId}`;
    form.submit();
}

// Clear state when modal closes
document.getElementById('new-dm-modal').addEventListener('click', function(e) {
    if (e.target === this) resetDmModal();
});

function resetDmModal() {
    selectedUserId = null;
    document.getElementById('dm-search-input').value = '';
    document.getElementById('dm-user-results').style.display = 'none';
    document.getElementById('dm-selected-user').style.display = 'none';
    const btn = document.getElementById('dm-start-btn');
    btn.disabled = true;
    btn.style.opacity = '0.5';
    btn.style.cursor = 'not-allowed';
}
</script>
@endpush
</x-app-layout>
