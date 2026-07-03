<x-app-layout>
    <x-slot name="title">Direct Messages</x-slot>

    {{-- Left sidebar: DM list --}}
    <div class="flex flex-col w-60 flex-shrink-0 border-r" style="background: var(--color-sidebar-bg); border-color: var(--color-primary-800);">
        <div class="p-4 border-b" style="border-color: var(--color-primary-800);">
            <h2 class="text-sm font-semibold" style="color: white;">Direct Messages</h2>
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
        <p class="text-sm" style="color: var(--color-primary-500);">Select a conversation or start a new one.</p>
    </div>
</x-app-layout>
