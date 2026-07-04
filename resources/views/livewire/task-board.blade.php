{{-- Task Board Livewire Component --}}
<div class="p-4 space-y-4">
    @foreach([
        ['key' => 'pending',     'label' => 'Pending',     'color' => '#f59e0b'],
        ['key' => 'in_progress', 'label' => 'In Progress', 'color' => '#0284c7'],
        ['key' => 'done',        'label' => 'Done',        'color' => '#16a34a'],
    ] as $col)
    <div>
        <div class="flex items-center gap-2 mb-2">
            <div class="w-2 h-2 rounded-full" style="background: {{ $col['color'] }};"></div>
            <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-primary-600);">
                {{ $col['label'] }}
            </span>
            <span class="ml-auto text-xs px-1.5 py-0.5 rounded-full"
                  style="background: var(--color-primary-100); color: var(--color-primary-500);">
                {{ count($columns[$col['key']] ?? []) }}
            </span>
        </div>

        @forelse($columns[$col['key']] ?? [] as $task)
        <div class="rounded-lg border p-3 mb-2 text-sm" style="background: white; border-color: var(--color-border);">
            <p class="font-medium leading-snug mb-1" style="color: var(--color-primary-900);">
                {{ $task['title'] }}
            </p>
            @if($task['assigned_to'] ?? null)
            <p class="text-xs mb-1 flex items-center gap-1.5" style="color: var(--color-primary-500);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                {{ $task['assignee']['username'] ?? 'Assigned' }}
            </p>
            @endif
            @if($task['due_date'] ?? null)
            <p class="text-xs mb-2 flex items-center gap-1.5" style="color: var(--color-primary-400);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                Due {{ \Carbon\Carbon::parse($task['due_date'])->format('M j') }}
            </p>
            @endif
            <div class="flex items-center flex-wrap gap-1.5 mt-2">
                @foreach([['key'=>'pending','label'=>'Pending'],['key'=>'in_progress','label'=>'In Progress'],['key'=>'done','label'=>'Done']] as $s)
                @if($s['key'] !== $col['key'])
                <button wire:click="updateStatus({{ $task['task_id'] }}, '{{ $s['key'] }}')"
                        class="text-xs px-2 py-0.5 rounded-md border transition-all hover:bg-gray-50"
                        style="border-color: var(--color-border); color: var(--color-primary-500);">
                    → {{ $s['label'] }}
                </button>
                @endif
                @endforeach
            </div>
        </div>
        @empty
        <div class="text-center py-3 rounded-lg border border-dashed" style="border-color: var(--color-border);">
            <p class="text-xs" style="color: var(--color-primary-400);">No {{ strtolower($col['label']) }} tasks</p>
        </div>
        @endforelse
    </div>
    @endforeach
</div>
