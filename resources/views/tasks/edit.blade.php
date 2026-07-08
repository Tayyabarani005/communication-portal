<x-app-layout>
    <x-slot name="title">Edit Task - {{ $task->title }}</x-slot>

    <div class="flex-1 flex items-center justify-center p-8" style="background: var(--color-bg-main);">
        <div class="w-full max-w-lg rounded-2xl shadow-lg border p-8" style="background: white; border-color: var(--color-border);">

            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold" style="color: var(--color-primary-900);">Edit Task</h1>
                    <p class="text-sm mt-1" style="color: var(--color-primary-500);">Update this task's attributes and assignee.</p>
                </div>
                <form method="POST" action="{{ route('tasks.destroy', [$channel, $task]) }}" onsubmit="return confirm('Are you sure you want to delete this task?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-red-200 hover:bg-red-50 transition-colors" style="color: #dc2626;">
                        Delete Task
                    </button>
                </form>
            </div>

            <form method="POST" action="{{ route('tasks.update', [$channel, $task]) }}" class="space-y-5">
                @csrf
                @method('PATCH')

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium mb-1" style="color: var(--color-primary-700);">Task Title</label>
                    <input type="text" id="title" name="title"
                           value="{{ old('title', $task->title) }}"
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
                              placeholder="Provide more context about this task...">{{ old('description', $task->description) }}</textarea>
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
                            <option value="{{ $member->user_id }}" {{ old('assigned_to', $task->assigned_to) == $member->user_id ? 'selected' : '' }}>
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
                               value="{{ old('due_date', $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '') }}"
                               class="w-full rounded-xl border px-3 py-2 outline-none text-sm bg-white"
                               style="border-color: var(--color-border); color: var(--color-primary-900);">
                        @error('due_date')
                        <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium mb-1" style="color: var(--color-primary-700);">Status</label>
                    <select id="status" name="status"
                            class="w-full rounded-xl border px-3 py-2 outline-none text-sm bg-white"
                            style="border-color: var(--color-border); color: var(--color-primary-900);">
                        <option value="pending" {{ old('status', $task->status->value) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ old('status', $task->status->value) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="done" {{ old('status', $task->status->value) == 'done' ? 'selected' : '' }}>Done</option>
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
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
