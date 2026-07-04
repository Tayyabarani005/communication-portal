<div class="flex-1 overflow-y-auto p-2" x-data x-on:scroll-to-bottom.window="$el.scrollTop = $el.scrollHeight">
    @forelse($conversations as $conv)
    <a href="{{ $conv['url'] }}"
       class="flex items-center gap-2 px-3 py-2 rounded-lg mb-0.5 text-sm transition-colors {{ $activeConversationId == $conv['conversation_id'] ? 'font-semibold' : '' }}"
       style="background: {{ $activeConversationId == $conv['conversation_id'] ? 'var(--color-sidebar-active-bg)' : 'transparent' }}; color: {{ $activeConversationId == $conv['conversation_id'] ? 'white' : 'var(--color-sidebar-text)' }};"
       onmouseover="this.style.background='var(--color-sidebar-hover-bg)'; this.style.color='white'"
       onmouseout="this.style.background='{{ $activeConversationId == $conv['conversation_id'] ? 'var(--color-sidebar-active-bg)' : 'transparent' }}'; this.style.color='{{ $activeConversationId == $conv['conversation_id'] ? 'white' : 'var(--color-sidebar-text)' }}'">
        <div class="relative flex-shrink-0">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold"
                 style="background: var(--color-accent-800); color: white;">
                {{ strtoupper(substr($conv['other_username'], 0, 1)) }}
            </div>
            @if($conv['is_online'])
                <span class="absolute bottom-0 right-0 block h-2 w-2 rounded-full ring-1 ring-white" style="background-color: #22c55e;"></span>
            @else
                <span class="absolute bottom-0 right-0 block h-2 w-2 rounded-full ring-1 ring-white" style="background-color: #9ca3af;"></span>
            @endif
        </div>
        <span class="truncate flex-1 text-xs">{{ $conv['other_username'] }}</span>
        @if(($conv['unread'] ?? 0) > 0)
        <span class="text-xs font-bold px-1.5 py-0.5 rounded-full flex-shrink-0"
              style="background: var(--color-accent-600); color: white;">
            {{ $conv['unread'] }}
        </span>
        @endif
    </a>
    @empty
    <p class="text-xs px-3 py-4 text-center" style="color: var(--color-primary-600);">No direct messages yet.<br>Click + to start one.</p>
    @endforelse
</div>
