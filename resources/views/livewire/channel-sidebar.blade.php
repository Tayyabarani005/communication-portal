<div class="flex-1 overflow-y-auto p-2">
    <div class="flex items-center justify-between px-2 py-1.5 mb-1">
        <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-primary-500);">Channels</span>
        @can('view', $workspace)
        <a href="{{ route('workspaces.channels.create', $workspace) }}"
           class="w-5 h-5 rounded flex items-center justify-center text-xs transition-colors"
           style="color: var(--color-sidebar-text-muted);"
           title="Create Channel"
           onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.color='white'"
           onmouseout="this.style.background='transparent'; this.style.color='var(--color-sidebar-text-muted)'">+</a>
        @endcan
    </div>

    @foreach($channels as $ch)
    <div class="relative flex items-center mb-0.5"
         onmouseenter="this.querySelector('.ch-del')&&(this.querySelector('.ch-del').style.opacity='1')"
         onmouseleave="this.querySelector('.ch-del')&&(this.querySelector('.ch-del').style.opacity='0')">
        <a href="{{ $ch['url'] }}"
           wire:navigate
           class="flex items-center gap-2 flex-1 px-3 py-1.5 rounded-lg text-sm transition-colors {{ $activeChannelId == $ch['channel_id'] ? 'font-semibold' : '' }}"
           style="background: {{ $activeChannelId == $ch['channel_id'] ? 'var(--color-sidebar-active-bg)' : 'transparent' }}; color: {{ $activeChannelId == $ch['channel_id'] ? 'white' : 'var(--color-sidebar-text)' }};"
           onmouseover="this.style.background='var(--color-sidebar-hover-bg)'; this.style.color='white'"
           onmouseout="this.style.background='{{ $activeChannelId == $ch['channel_id'] ? 'var(--color-sidebar-active-bg)' : 'transparent' }}'; this.style.color='{{ $activeChannelId == $ch['channel_id'] ? 'white' : 'var(--color-sidebar-text)' }}'">
            <span class="text-xs opacity-60">{{ $ch['is_private'] ? '🔒' : '#' }}</span>
            <span class="truncate">{{ $ch['channel_name'] }}</span>
            @if(($ch['unread'] ?? 0) > 0)
            <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-full flex-shrink-0"
                  style="background: var(--color-accent-600); color: white;">
                {{ $ch['unread'] }}
            </span>
            @endif
        </a>
        @if($ch['can_delete'] ?? false)
        <form method="POST" action="{{ $ch['delete_url'] }}"
              onsubmit="return confirm('Delete #{{ $ch['channel_name'] }}? All messages will be permanently removed.')"
              class="ch-del absolute right-1 transition-opacity"
              style="opacity: 0;">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="p-1 rounded transition-colors"
                    style="color: rgba(255,255,255,0.55); background: none; border: none; cursor: pointer;"
                    onmouseover="this.style.color='#f87171'; this.style.background='rgba(239,68,68,0.15)'"
                    onmouseout="this.style.color='rgba(255,255,255,0.55)'; this.style.background='none'"
                    title="Delete channel">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:14px;height:14px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </button>
        </form>
        @endif
    </div>
    @endforeach
</div>
