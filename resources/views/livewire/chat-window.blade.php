<div class="flex flex-col h-full">
    {{-- Messages list --}}
    <div id="messages-container"
         class="flex-1 overflow-y-auto p-4 space-y-1"
         style="background: var(--color-bg-main);"
         x-data="{}"
         x-on:scroll-to-bottom.window="$el.scrollTop = $el.scrollHeight"
         x-init="$el.scrollTop = $el.scrollHeight">

        @foreach($messages as $msg)
        <div class="flex items-start gap-3 group px-2 py-1.5 rounded-lg hover:bg-white transition-colors">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5"
                 style="background: var(--color-accent-700); color: white;">
                {{ strtoupper(substr($msg['sender']['username'] ?? '?', 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-baseline gap-2">
                    <span class="text-sm font-semibold" style="color: var(--color-primary-900);">{{ $msg['sender']['username'] ?? 'Unknown' }}</span>
                    <span class="text-xs" style="color: var(--color-primary-400);">
                        {{ isset($msg['sent_at']) ? \Carbon\Carbon::parse($msg['sent_at'])->format('H:i') : '' }}
                    </span>
                </div>
                @if(!empty($msg['parent_id']))
                <div class="text-xs italic mb-1 pl-2 border-l-2" style="color: var(--color-primary-400); border-color: var(--color-primary-300);">
                    ↩ Reply
                </div>
                @endif
                <p class="text-sm leading-relaxed" style="color: var(--color-primary-800);">{{ $msg['msg_body'] }}</p>
            </div>
            {{-- Actions on hover --}}
            <div class="hidden group-hover:flex items-center gap-1 flex-shrink-0">
                <button wire:click="setReply({{ $msg['message_id'] }}, '{{ addslashes(substr($msg['msg_body'], 0, 40)) }}')"
                        class="p-1 rounded hover:bg-gray-200 transition-colors" title="Reply" style="color: var(--color-primary-400);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                </button>
            </div>
        </div>
        @endforeach

        @if(empty($messages))
        <div class="flex flex-col items-center justify-center h-full py-20 text-center">
            <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3" style="background: var(--color-primary-100);">
                <span class="text-xl">#</span>
            </div>
            <p class="text-sm font-medium" style="color: var(--color-primary-600);">This is the very beginning of #{{ $channel->channel_name }}</p>
            <p class="text-xs mt-1" style="color: var(--color-primary-400);">Send a message to get the conversation started!</p>
        </div>
        @endif
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
                placeholder="Message #{{ $channel->channel_name }}"
                rows="1"
                class="flex-1 resize-none outline-none text-sm bg-transparent"
                style="color: var(--color-primary-900);"
                id="chat-composer-{{ $channel->channel_id }}"
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
