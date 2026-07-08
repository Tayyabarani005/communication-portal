<x-app-layout>
    <x-slot name="title">Create Channel - {{ $workspace->name }}</x-slot>

    {{-- ══ Left Sidebar ══ --}}
    <div class="flex flex-col w-60 flex-shrink-0 border-r overflow-y-auto" style="background: var(--color-sidebar-bg); border-color: var(--color-primary-800);">
        <div class="p-4 border-b" style="border-color: var(--color-primary-800);">
            <a href="{{ route('workspaces.show', $workspace) }}"
               class="font-semibold text-sm hover:underline" style="color: white;">
                {{ $workspace->name }}
            </a>
        </div>
        @livewire('channel-sidebar', ['workspace' => $workspace])
        <div class="border-t p-2" style="border-color: var(--color-primary-800);">
            <div class="px-2 py-1.5 mb-1">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-primary-500);">Direct Messages</span>
            </div>
            @livewire('dm-sidebar')
        </div>
    </div>

    {{-- ══ Main Content ══ --}}
    <div class="flex-1 flex items-center justify-center p-8" style="background: var(--color-bg-main);">
        <div class="w-full max-w-lg rounded-2xl shadow-lg border p-8" style="background: white; border-color: var(--color-border);">

            <div class="mb-6">
                <h1 class="text-xl font-bold" style="color: var(--color-primary-900);">Create a Channel</h1>
                <p class="text-sm mt-1" style="color: var(--color-primary-500);">Channels are where your team communicates.</p>
            </div>

            <form method="POST" action="{{ route('workspaces.channels.store', $workspace) }}" class="space-y-5">
                @csrf

                {{-- Channel name --}}
                <div>
                    <label for="channel_name" class="block text-sm font-medium mb-1" style="color: var(--color-primary-700);">Channel Name</label>
                    <div class="flex items-center gap-2 rounded-xl border px-3 py-2" style="border-color: var(--color-border);">
                        <span class="text-sm" style="color: var(--color-primary-400);">#</span>
                        <input type="text" id="channel_name" name="channel_name"
                               value="{{ old('channel_name') }}"
                               class="flex-1 outline-none text-sm bg-transparent"
                               style="color: var(--color-primary-900);"
                               placeholder="e.g. marketing-team"
                               required autofocus>
                    </div>
                    @error('channel_name')
                    <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium mb-1" style="color: var(--color-primary-700);">Description <span style="color: var(--color-primary-400);">(optional)</span></label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full rounded-xl border px-3 py-2 outline-none text-sm resize-none"
                              style="border-color: var(--color-border); color: var(--color-primary-900);"
                              placeholder="What's this channel about?">{{ old('description') }}</textarea>
                </div>

                {{-- Visibility --}}
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--color-primary-700);">Visibility</label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_private" value="0" class="rounded" id="vis_public" {{ old('is_private', '0') == '0' ? 'checked' : '' }} onchange="toggleMemberSelect()">
                            <span class="text-sm" style="color: var(--color-primary-700);"># Public</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_private" value="1" class="rounded" id="vis_private" {{ old('is_private') == '1' ? 'checked' : '' }} onchange="toggleMemberSelect()">
                            <span class="text-sm" style="color: var(--color-primary-700);">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 inline" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                Private
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Member selection (visible only for Private) --}}
                <div id="member-select-section" style="display: none;">
                    <label class="block text-sm font-medium mb-2" style="color: var(--color-primary-700);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                        Select Members to Add
                    </label>
                    <div class="max-h-48 overflow-y-auto rounded-xl border p-2 space-y-1" style="border-color: var(--color-border);">
                        @foreach($members as $m)
                            @if($m->user_id !== auth()->user()->user_id)
                            <label class="flex items-center gap-3 px-3 py-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="checkbox" name="members[]" value="{{ $m->user_id }}" class="rounded" style="accent-color: var(--color-accent-600);">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0" style="background: var(--color-accent-700); color: white;">
                                    {{ strtoupper(substr($m->user->username ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <span class="text-sm font-medium" style="color: var(--color-primary-800);">{{ $m->user->username }}</span>
                                    <span class="text-xs ml-1" style="color: var(--color-primary-400);">{{ $m->role->value }}</span>
                                </div>
                            </label>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('workspaces.show', $workspace) }}"
                       class="px-4 py-2 rounded-xl text-sm border transition-all"
                       style="border-color: var(--color-border); color: var(--color-primary-700);">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                            style="background: var(--color-accent-600);">
                        Create Channel
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    function toggleMemberSelect() {
        const section = document.getElementById('member-select-section');
        const isPrivate = document.getElementById('vis_private').checked;
        section.style.display = isPrivate ? 'block' : 'none';
    }
    // On page load, check existing state
    document.addEventListener('DOMContentLoaded', toggleMemberSelect);
</script>
@endpush
