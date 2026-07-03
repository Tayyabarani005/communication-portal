<x-app-layout>
    <x-slot name="title">Create Channel — {{ $workspace->name }}</x-slot>

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
                            <input type="radio" name="is_private" value="0" class="rounded" {{ old('is_private', '0') == '0' ? 'checked' : '' }}>
                            <span class="text-sm" style="color: var(--color-primary-700);"># Public</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_private" value="1" class="rounded" {{ old('is_private') == '1' ? 'checked' : '' }}>
                            <span class="text-sm" style="color: var(--color-primary-700);">🔒 Private</span>
                        </label>
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
