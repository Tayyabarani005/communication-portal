<x-app-layout>
    <x-slot name="title">Create Task — #{{ $channel->channel_name }}</x-slot>

    <div class="flex-1 flex items-center justify-center p-8" style="background: var(--color-bg-main);">
        <div class="w-full max-w-lg rounded-2xl shadow-lg border p-8" style="background: white; border-color: var(--color-border);">

            <div class="mb-6">
                <h1 class="text-xl font-bold" style="color: var(--color-primary-900);">Add a Task</h1>
                <p class="text-sm mt-1" style="color: var(--color-primary-500);">Keep your channel's projects organized and on schedule.</p>
            </div>

            <form method="POST" action="{{ route('tasks.store', $channel) }}" class="space-y-5">
                @csrf

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium mb-1" style="color: var(--color-primary-700);">Task Title</label>
                    <input type="text" id="title" name="title"
                           value="{{ old('title') }}"
                           class="w-full rounded-xl border px-3 py-2 outline-none text-sm"
                           style="border-color: var(--color-border); color: var(--color-primary-900);"
                           placeholder="e.g. Design homepage layout"
                           required autofocus>
                    @error('title')
                    <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium mb-1" style="color: var(--color-primary-700);">Description <span style="color: var(--color-primary-400);">(optional)</span></label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full rounded-xl border px-3 py-2 outline-none text-sm resize-none"
                              style="border-color: var(--color-border); color: var(--color-primary-900);"
                              placeholder="Provide more context about this task...">{{ old('description') }}</textarea>
                </div>

                {{-- Assignment & Due Date --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium mb-1" style="color: var(--color-primary-700);">Assignee</label>
                        <select id="assigned_to" name="assigned_to"
                                class="w-full rounded-xl border px-3 py-2 outline-none text-sm bg-white"
                                style="border-color: var(--color-border); color: var(--color-primary-900);">
                            <option value="">Unassigned</option>
                            @foreach($members as $member)
                            <option value="{{ $member->user_id }}" {{ old('assigned_to') == $member->user_id ? 'selected' : '' }}>
                                {{ $member->username }}
                            </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                        <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium mb-1" style="color: var(--color-primary-700);">Due Date</label>
                        <input type="date" id="due_date" name="due_date"
                               value="{{ old('due_date') }}"
                               class="w-full rounded-xl border px-3 py-2 outline-none text-sm bg-white"
                               style="border-color: var(--color-border); color: var(--color-primary-900);">
                        @error('due_date')
                        <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium mb-1" style="color: var(--color-primary-700);">Initial Status</label>
                    <select id="status" name="status"
                            class="w-full rounded-xl border px-3 py-2 outline-none text-sm bg-white"
                            style="border-color: var(--color-border); color: var(--color-primary-900);">
                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Done</option>
                    </select>
                    @error('status')
                    <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                    @enderror
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
                        Create Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
