{{-- DM Window Livewire Component --}}
<div class="flex flex-col flex-1 min-h-0" wire:poll.2s="refreshMessages">
    {{-- Messages list --}}
    <div id="dm-messages-container"
         class="flex-1 overflow-y-auto p-4 space-y-1"
         style="background: var(--color-bg-main);"
         x-data="{ playSound() {
             try {
                 const ctx = new (window.AudioContext || window.webkitAudioContext)();
                 const osc = ctx.createOscillator();
                 const gain = ctx.createGain();
                 osc.connect(gain); gain.connect(ctx.destination);
                 osc.frequency.value = 880; osc.type = 'sine';
                 gain.gain.setValueAtTime(0.08, ctx.currentTime);
                 gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
                 osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.3);
             } catch(e) {}
         }}"
         x-on:dm-scroll-to-bottom.window="$el.scrollTop = $el.scrollHeight; playSound()"
         x-init="$el.scrollTop = $el.scrollHeight">

        @forelse($messages as $msg)
        @php $isMine = ($msg['sender_id'] ?? null) == auth()->user()->user_id; @endphp
        <div class="flex items-start gap-3 group px-2 py-1.5 rounded-lg hover:bg-white transition-colors {{ $isMine ? 'flex-row-reverse' : '' }}">
            <div class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5"
                 style="background: {{ $isMine ? 'var(--color-accent-600)' : 'var(--color-accent-800)' }}; color: white;">
                @if(!empty($msg['sender']['avatar_url']))
                    <img src="{{ $msg['sender']['avatar_url'] }}" alt="{{ $msg['sender']['username'] }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($msg['sender']['username'] ?? '?', 0, 1)) }}
                @endif
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
                @if(!empty($msg['msg_body']) || (empty($msg['files']) || count($msg['files']) === 0))
                <div class="inline-block px-3 py-2 rounded-xl text-sm mt-0.5 {{ $isMine ? 'rounded-tr-sm' : 'rounded-tl-sm' }}"
                     style="{{ $isMine
                        ? 'background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;'
                        : 'background: white; color: var(--color-primary-800); border: 1px solid var(--color-border);' }}">
                    {!! preg_replace('/@(\w+)/', '<span style="background: rgba(3,105,161,0.15); color: #0369a1; padding: 0 3px; border-radius: 3px; font-weight: 500;">@$1</span>', e($msg['msg_body'] ?? '')) !!}
                </div>
                @endif

                {{-- Attached files --}}
                @if(!empty($msg['files']) && count($msg['files']) > 0)
                <div class="mt-1">
                    @foreach($msg['files'] as $file)
                    <a href="{{ route('files.download', $file['file_id']) }}" class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-xs border transition-colors hover:bg-gray-50" style="border-color: var(--color-border); color: var(--color-accent-600);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                        {{ $file['file_name'] }}
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
            {{-- Reply action on hover --}}
            <div class="hidden group-hover:flex items-center gap-1 flex-shrink-0">
                <button wire:click="setReply({{ $msg['dm_message_id'] ?? $msg['id'] ?? 0 }}, '{{ addslashes(substr($msg['msg_body'], 0, 40)) }}')"
                        class="p-1 rounded hover:bg-gray-200 transition-colors" title="Reply" style="color: var(--color-primary-400);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                </button>
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

    {{-- Typing indicator with animated dots --}}
    @if($showTyping)
    <div class="px-4 py-1.5 text-xs flex items-center gap-1" style="color: var(--color-primary-500);">
        <span class="font-medium">{{ $typingUser }}</span>
        <span>is typing</span>
        <span class="flex gap-0.5 ml-0.5">
            <span class="w-1 h-1 rounded-full animate-bounce" style="background: var(--color-primary-400); animation-delay: 0s;"></span>
            <span class="w-1 h-1 rounded-full animate-bounce" style="background: var(--color-primary-400); animation-delay: 0.15s;"></span>
            <span class="w-1 h-1 rounded-full animate-bounce" style="background: var(--color-primary-400); animation-delay: 0.3s;"></span>
        </span>
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
    <div class="p-4 border-t flex-shrink-0 relative" style="background: white; border-color: var(--color-border);"
         x-data="{
             showEmoji: false,
             emojis: ['😀','😂','😍','👍','👎','🎉','🔥','❤️','💯','😢','😮','🤔','👀','🚀','✅','❌','⭐','💡','📎','🎯'],
             mentionsOpen: false,
             mentionQuery: '',
             mentionUsers: [],
             mentionIndex: 0,
             mentionStartIdx: -1,
             textarea: null,
             init() {
                 this.textarea = this.$el.querySelector('textarea');
                 if (this.textarea) {
                     this.textarea.addEventListener('input', () => this.handleInput());
                     this.textarea.addEventListener('keydown', (e) => this.handleKeydown(e));
                 }
             },
             handleInput() {
                 const text = this.textarea.value;
                 const cursor = this.textarea.selectionStart;
                 const textBeforeCursor = text.slice(0, cursor);
                 const lastAtIndex = textBeforeCursor.lastIndexOf('@');
                 
                 if (lastAtIndex !== -1) {
                     const charBeforeAt = lastAtIndex > 0 ? textBeforeCursor[lastAtIndex - 1] : ' ';
                     if (/\s/.test(charBeforeAt)) {
                         const query = textBeforeCursor.slice(lastAtIndex + 1);
                         if (!/\s/.test(query)) {
                             this.mentionStartIdx = lastAtIndex;
                             this.mentionQuery = query;
                             this.fetchMentionSuggestions();
                             return;
                         }
                     }
                 }
                 this.closeMentions();
             },
             fetchMentionSuggestions() {
                 if (this.mentionQuery.length >= 1) {
                     fetch(`/users/search?q=${encodeURIComponent(this.mentionQuery)}`)
                         .then(res => res.json())
                         .then(data => {
                             this.mentionUsers = data;
                             this.mentionsOpen = this.mentionUsers.length > 0;
                             this.mentionIndex = 0;
                         });
                 } else {
                     this.closeMentions();
                 }
             },
             closeMentions() {
                 this.mentionsOpen = false;
                 this.mentionUsers = [];
                 this.mentionIndex = 0;
                 this.mentionStartIdx = -1;
             },
             selectMention(user) {
                 const text = this.textarea.value;
                 const cursor = this.textarea.selectionStart;
                 const before = text.slice(0, this.mentionStartIdx);
                 const after = text.slice(cursor);
                 const newValue = before + '@' + user.username + ' ' + after;
                 
                 this.textarea.value = newValue;
                 this.$wire.set('body', newValue);
                 this.closeMentions();
                 
                 this.$nextTick(() => {
                     this.textarea.focus();
                     const newCursorPos = before.length + user.username.length + 2;
                     this.textarea.setSelectionRange(newCursorPos, newCursorPos);
                 });
             },
             handleKeydown(e) {
                 if (!this.mentionsOpen) return;
                 if (e.key === 'ArrowDown') {
                     e.preventDefault();
                     this.mentionIndex = (this.mentionIndex + 1) % this.mentionUsers.length;
                 } else if (e.key === 'ArrowUp') {
                     e.preventDefault();
                     this.mentionIndex = (this.mentionIndex - 1 + this.mentionUsers.length) % this.mentionUsers.length;
                 } else if (e.key === 'Enter') {
                     e.preventDefault();
                     if (this.mentionUsers[this.mentionIndex]) {
                         this.selectMention(this.mentionUsers[this.mentionIndex]);
                     }
                 } else if (e.key === 'Escape') {
                     e.preventDefault();
                     this.closeMentions();
                 }
             }
         }">

        {{-- File attachment preview --}}
        @if($attachment)
        <div class="mb-2 flex items-center gap-2 px-3 py-2 rounded-lg border text-xs" style="border-color: var(--color-border); background: var(--color-primary-50);">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="color: var(--color-accent-600);"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
            <span style="color: var(--color-primary-700);">{{ $attachment->getClientOriginalName() }}</span>
            <button wire:click="$set('attachment', null)" class="ml-auto font-bold" style="color: var(--color-primary-400);">✕</button>
        </div>
        @endif

        <div class="flex items-end gap-2 rounded-xl border px-3 py-2" style="border-color: var(--color-border);">
            {{-- File upload button --}}
            <label class="cursor-pointer p-1.5 rounded-lg transition-colors hover:bg-gray-100 flex-shrink-0" title="Attach file" style="color: var(--color-primary-400);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                </svg>
                <input type="file" wire:model="attachment" class="hidden">
            </label>

            {{-- Emoji picker button --}}
            <button type="button" @click="showEmoji = !showEmoji" class="p-1.5 rounded-lg transition-colors hover:bg-gray-100 flex-shrink-0" title="Emoji" style="color: var(--color-primary-400);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                </svg>
            </button>

            {{-- Textarea --}}
            <textarea
                wire:model="body"
                wire:keydown.enter.prevent="send"
                wire:keydown.debounce.500ms="broadcastTyping"
                placeholder="Message…  •  Use @username to mention"
                rows="1"
                class="flex-1 resize-none outline-none text-sm bg-transparent"
                style="color: var(--color-primary-900);"
                id="dm-composer-{{ $conversation->conversation_id }}"
            ></textarea>

            {{-- Send button --}}
            <button wire:click="send"
                    class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-all hover:opacity-80"
                    style="background: var(--color-accent-600);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
            </button>
        </div>

        {{-- Mentions autocompletion dropdown --}}
        <div x-show="mentionsOpen"
             class="absolute bottom-20 left-4 z-50 bg-white border rounded-xl shadow-lg p-2 max-h-48 overflow-y-auto w-64 divide-y divide-gray-100"
             style="border-color: var(--color-border);"
             x-transition>
            <template x-for="(u, index) in mentionUsers" :key="u.user_id">
                <button type="button"
                        class="w-full flex items-center gap-2 px-3 py-2 text-left text-sm transition-colors rounded-lg"
                        :class="index === mentionIndex ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50'"
                        @click="selectMention(u)"
                        @mouseenter="mentionIndex = index">
                    <div class="w-6 h-6 rounded-full overflow-hidden bg-gray-200 flex-shrink-0 flex items-center justify-center text-xs font-bold text-white">
                        <template x-if="u.avatar_url">
                            <img :src="u.avatar_url" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!u.avatar_url">
                            <span x-text="u.username.substring(0, 1).toUpperCase()"></span>
                        </template>
                    </div>
                    <div class="truncate">
                        <span class="font-medium text-xs block" x-text="u.username"></span>
                        <span x-show="u.name" class="text-[10px] text-gray-400 block" x-text="u.name"></span>
                    </div>
                </button>
            </template>
        </div>

        {{-- Emoji grid dropdown --}}
        <div x-show="showEmoji" @click.outside="showEmoji = false" x-transition
             class="absolute bottom-20 left-4 z-50 bg-white border rounded-xl shadow-lg p-3 grid grid-cols-10 gap-1"
             style="border-color: var(--color-border);">
            <template x-for="emoji in emojis">
                <button type="button"
                        class="w-8 h-8 flex items-center justify-center text-lg hover:bg-gray-100 rounded-lg transition-colors cursor-pointer"
                        @click="$wire.set('body', $wire.get('body') + emoji); showEmoji = false"
                        x-text="emoji"></button>
            </template>
        </div>

        @error('body') <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p> @enderror
        @error('attachment') <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p> @enderror
    </div>
</div>
