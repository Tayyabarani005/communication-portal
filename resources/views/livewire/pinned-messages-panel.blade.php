{{-- Pinned Messages Panel Livewire Component --}}
<div class="relative">
    {{-- Toggle button with pin count badge --}}
    <button wire:click="toggle"
            class="relative p-2 rounded-lg transition-colors hover:bg-gray-100"
            title="Pinned Messages"
            style="color: {{ $pinCount > 0 ? '#d97706' : 'var(--color-primary-500)' }};">
        <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $pinCount > 0 ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11.25h-3.25V5.5A1.5 1.5 0 0 0 14.25 4h-4.5A1.5 1.5 0 0 0 8.25 5.5v5.75H5a.75.75 0 0 0-.53 1.28l3.18 3.18a.75.75 0 0 0 .53.22h7.64a.75.75 0 0 0 .53-.22l3.18-3.18a.75.75 0 0 0-.53-1.28zM12 17.25v3.5" />
        </svg>
        @if($pinCount > 0)
        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full text-white flex items-center justify-center font-bold"
              style="background: #d97706; font-size: 0.55rem; line-height: 1;">{{ $pinCount }}</span>
        @endif
    </button>

    {{-- Slide-over panel --}}
    @if($open)
    <div class="absolute right-0 top-10 w-80 z-50 rounded-xl shadow-xl border overflow-hidden"
         style="background: white; border-color: var(--color-border);">

        <div class="flex items-center justify-between px-4 py-3 border-b"
             style="border-color: var(--color-border); background: var(--color-pin-bg);">
            <span class="text-sm font-semibold" style="color: var(--color-pin-text);">Pinned Messages</span>
            <button wire:click="toggle" class="text-xs" style="color: var(--color-pin-text);">&times;</button>
        </div>

        <div class="overflow-y-auto max-h-80 divide-y" style="divide-color: var(--color-border);">
            @forelse($pins as $pin)
            <div class="px-4 py-3">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold mb-1" style="color: var(--color-primary-600);">
                            {{ $pin['pinnable']['sender']['username'] ?? 'Unknown' }}
                        </p>
                        <p class="text-sm leading-snug line-clamp-3" style="color: var(--color-primary-800);">
                            {{ $pin['pinnable']['msg_body'] ?? '' }}
                        </p>
                    </div>
                    <button wire:click="unpin({{ $pin['pin_id'] }})"
                            class="flex-shrink-0 text-xs hover:opacity-60 transition-opacity"
                            style="color: var(--color-primary-400);"
                            title="Unpin">&times;</button>
                </div>
            </div>
            @empty
            <div class="px-4 py-6 text-center">
                <p class="text-sm" style="color: var(--color-primary-400);">No pinned messages yet.</p>
                <p class="text-xs mt-1" style="color: var(--color-primary-400);">
                    Pin important messages to keep them accessible here.
                </p>
            </div>
            @endforelse
        </div>
    </div>
    @endif
</div>
