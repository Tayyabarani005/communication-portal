{{-- DM Window Livewire Component --}}
<div class="flex flex-col h-full">
    {{-- Messages list --}}
    <div id="dm-messages-container"
         class="flex-1 overflow-y-auto p-4 space-y-1"
         style="background: var(--color-bg-main);"
         x-data="{}"
         x-on:dm-scroll-to-bottom.window="$el.scrollTop = $el.scrollHeight"
         x-init="$el.scrollTop = $el.scrollHeight">

        @forelse($messages as $msg)
        @php $isMine = ($msg['sender_id'] ?? null) == auth()->user()->user_id; @endphp
        <div class="flex items-start gap-3 group px-2 py-1.5 rounded-lg hover:bg-white transition-colors {{ $isMine ? 'flex-row-reverse' : '' }}">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5"
                 style="background: {{ $isMine ? 'var(--color-accent-600)' : 'var(--color-accent-800)' }}; color: white;">
                {{ strtoupper(substr($msg['sender']['username'] ?? '?', 0, 1)) }}
            </div>
            <div class="min-w-0 max-w-xs lg:max-w-md {{ $isMine ? 'items-end' : 'items-start' }} flex flex-col">
                <div class="flex items-baseline gap-2 {{ $isMine ? 'flex-row-reverse' : '' }}">
                    <span class="text-xs font-semibold" style="color: var(--color-primary-700);">
                        {{ $isMine ? 'You' : ($msg['sender']['username'] ?? 'Unknown') }}
                    </span>
                    <span class="text-xs" style="color: var(--color-primary-400);">
                        {{ isset($msg['sent_at']) ? \Carbon\Carbon::parse($msg['sent_at'])->format('H:i') : '' }}
                    </span>
                </div>
                <div class="inline-block px-3 py-2 rounded-xl text-sm mt-0.5 {{ $isMine ? 'rounded-tr-sm' : 'rounded-tl-sm' }}"
                     style="{{ $isMine
                        ? 'background: var(--color-accent-600); color: white;'
                        : 'background: white; color: var(--color-primary-800); border: 1px solid var(--color-border);' }}">
                    {{ $msg['msg_body'] }}
                </div>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center h-full py-20 text-center">
            <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3" style="background: var(--color-primary-100);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6" style="color: var(--color-primary-400);">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                </svg>
            </div>
            <p class="text-sm font-medium" style="color: var(--color-primary-600);">No messages yet</p>
            <p class="text-xs mt-1" style="color: var(--color-primary-400);">Start the conversation!</p>
        </div>
        @endforelse

    </div>

    {{-- Typing indicator --}}
    @if($showTyping)
    <div class="px-4 py-1 text-xs" style="color: var(--color-primary-500);">
        <span class="italic">{{ $typingUser }} is typing…</span>
        <span x-data x-init="setTimeout(() => $wire.hideTyping(), 3000)"></span>
    </div>
    @endif

    {{-- Reply preview bar --}}
    @if($parentId)
    <div class="mx-4 px-3 py-2 rounded-t-lg border-b-0 border text-xs flex items-center justify-between"
         style="background: var(--color-pin-bg); border-color: var(--color-pin-border); color: var(--color-pin-text);">
        <span>↩ Replying to: <em>{{ $replyPreview }}</em></span>
        <button wire:click="clearReply" class="ml-2 font-bold hover:opacity-60">✕</button>
    </div>
    @endif

    {{-- Message composer --}}
    <div class="p-4 border-t flex-shrink-0" style="background: white; border-color: var(--color-border);">
        <div class="flex items-end gap-3 rounded-xl border px-4 py-3" style="border-color: var(--color-border);">
            <textarea
                wire:model="body"
                wire:keydown.enter.prevent="send"
                wire:keydown.debounce.500ms="broadcastTyping"
                placeholder="Message…"
                rows="1"
                class="flex-1 resize-none outline-none text-sm bg-transparent"
                style="color: var(--color-primary-900);"
                id="dm-composer-{{ $conversation->conversation_id }}"
            ></textarea>
            <button wire:click="send"
                    class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-all hover:opacity-80"
                    style="background: var(--color-accent-600);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
            </button>
        </div>
        @error('body') <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p> @enderror
    </div>
</div>
