<x-app-layout>
    <x-slot name="title">Edit Channel - #{{ $channel->channel_name }}</x-slot>

    <div class="flex-1 flex items-center justify-center p-8" style="background: var(--color-bg-main);">
        <div class="w-full max-w-lg rounded-2xl shadow-lg border p-8" style="background: white; border-color: var(--color-border);">

            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold" style="color: var(--color-primary-900);">Edit Channel</h1>
                    <p class="text-sm mt-1" style="color: var(--color-primary-505);">Update the channel configuration and preferences.</p>
                </div>
                <form method="POST" action="{{ route('channels.destroy', $channel) }}" onsubmit="return confirm('Are you sure you want to delete this channel? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-red-200 hover:bg-red-50 transition-colors" style="color: #dc2626;">
                        Delete Channel
                    </button>
                </form>
            </div>

            <form method="POST" action="{{ route('channels.update', $channel) }}" class="space-y-5">
                @csrf
                @method('PATCH')

                {{-- Channel name --}}
                <div>
                    <label for="channel_name" class="block text-sm font-medium mb-1" style="color: var(--color-primary-700);">Channel Name</label>
                    <div class="flex items-center gap-2 rounded-xl border px-3 py-2" style="border-color: var(--color-border);">
                        <span class="text-sm" style="color: var(--color-primary-400);">#</span>
                        <input type="text" id="channel_name" name="channel_name"
                               value="{{ old('channel_name', $channel->channel_name) }}"
                               class="flex-1 outline-none text-sm bg-transparent"
                               style="color: var(--color-primary-900);"
                               placeholder="e.g. marketing-team"
                               required autofocus>
                    </div>
                    @error('channel_name')
                    <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Visibility --}}
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--color-primary-700);">Visibility</label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_private" value="0" class="rounded" {{ old('is_private', $channel->is_private ? '1' : '0') == '0' ? 'checked' : '' }}>
                            <span class="text-sm" style="color: var(--color-primary-700);"># Public</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_private" value="1" class="rounded" {{ old('is_private', $channel->is_private ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <span class="text-sm" style="color: var(--color-primary-700);">Private</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('channels.show', $channel) }}"
                       class="px-4 py-2 rounded-xl text-sm border transition-all"
                       style="border-color: var(--color-border); color: var(--color-primary-700);">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                            style="background: var(--color-accent-600);">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
