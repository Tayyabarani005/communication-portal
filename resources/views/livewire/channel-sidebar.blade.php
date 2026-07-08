<div class="flex-1 overflow-y-auto p-2">
    @foreach($channels as $ch)
    <a href="{{ $ch['url'] }}"
       wire:navigate
       class="flex items-center gap-2 px-3 py-1.5 rounded-lg mb-0.5 text-sm transition-colors group {{ $activeChannelId == $ch['channel_id'] ? 'font-semibold' : '' }}"
       style="background: {{ $activeChannelId == $ch['channel_id'] ? 'var(--color-sidebar-active-bg)' : 'transparent' }}; color: {{ $activeChannelId == $ch['channel_id'] ? 'white' : 'var(--color-sidebar-text)' }};"
       onmouseover="this.style.background='var(--color-sidebar-hover-bg)'; this.style.color='white'"
       onmouseout="this.style.background='{{ $activeChannelId == $ch['channel_id'] ? 'var(--color-sidebar-active-bg)' : 'transparent' }}'; this.style.color='{{ $activeChannelId == $ch['channel_id'] ? 'white' : 'var(--color-sidebar-text)' }}'">
        <span class="text-xs opacity-60">{{ $ch['is_private'] ? 'Private' : '#' }}</span>
        <span class="truncate">{{ $ch['channel_name'] }}</span>
        @if(($ch['unread'] ?? 0) > 0)
        <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-full flex-shrink-0"
              style="background: var(--color-accent-600); color: white;">
            {{ $ch['unread'] }}
        </span>
        @endif
    </a>
    @endforeach
</div>
