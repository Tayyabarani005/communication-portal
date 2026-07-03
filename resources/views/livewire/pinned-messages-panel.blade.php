{{-- Pinned Messages Panel Livewire Component --}}
<div class="relative">
    {{-- Toggle button --}}
    <button wire:click="toggle"
            class="p-2 rounded-lg transition-colors hover:bg-gray-100"
            title="Pinned Messages"
            style="color: var(--color-primary-500);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
        </svg>
    </button>

    {{-- Slide-over panel --}}
    @if($open)
    <div class="absolute right-0 top-10 w-80 z-50 rounded-xl shadow-xl border overflow-hidden"
         style="background: white; border-color: var(--color-border);">

        <div class="flex items-center justify-between px-4 py-3 border-b"
             style="border-color: var(--color-border); background: var(--color-pin-bg);">
            <span class="text-sm font-semibold" style="color: var(--color-pin-text);">📌 Pinned Messages</span>
            <button wire:click="toggle" class="text-xs" style="color: var(--color-pin-text);">✕</button>
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
                            title="Unpin">✕</button>
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
