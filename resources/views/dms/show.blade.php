<x-app-layout>
    @php
        $otherUser = $conversation->dmParticipants
            ->firstWhere('user_id', '!=', auth()->user()->user_id)
            ?->user;
    @endphp
    <x-slot name="title">DM with {{ $otherUser?->username ?? 'Unknown' }}</x-slot>

    {{-- Left sidebar: DM list --}}
    <div class="flex flex-col w-60 flex-shrink-0 border-r" style="background: var(--color-sidebar-bg); border-color: var(--color-primary-800);">
        <div class="p-4 border-b" style="border-color: var(--color-primary-800);">
            <h2 class="text-sm font-semibold" style="color: white;">Direct Messages</h2>
        </div>
        @livewire('dm-sidebar')
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
</x-app-layout>
