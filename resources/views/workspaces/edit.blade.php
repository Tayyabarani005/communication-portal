<x-app-layout>
    <x-slot name="title">Edit Workspace — {{ $workspace->name }}</x-slot>

    <div class="flex-1 flex items-center justify-center p-6" style="background: var(--color-bg-main);">
        <div class="w-full max-w-md rounded-2xl shadow-xl p-8" style="background: white;">
            <div class="mb-6">
                <h1 class="text-xl font-bold" style="color: var(--color-primary-900);">Edit your space</h1>
                <p class="text-sm mt-1" style="color: var(--color-primary-500);">Modify your workspace settings and details below.</p>
            </div>

            <form method="POST" action="{{ route('workspaces.update', $workspace) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--color-primary-600);">Workspace Name</label>
                    <input id="name" name="name" type="text"
                           value="{{ old('name', $workspace->name) }}"
                           placeholder="e.g. Global Communications"
                           class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none transition-all focus:ring-2"
                           style="border-color: var(--color-border); background: var(--color-primary-50); focus-ring-color: var(--color-accent-400);"
                           required>
                    @error('name')
                        <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--color-primary-600);">Description <span class="font-normal lowercase" style="color: var(--color-primary-400);">(optional)</span></label>
                    <textarea id="description" name="description" rows="3"
                               placeholder="What is this workspace about?"
                               class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none resize-none"
                               style="border-color: var(--color-border); background: var(--color-primary-50);">{{ old('description', $workspace->description) }}</textarea>
                </div>

                <div class="mb-6">
                    <label for="avatar" class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--color-primary-600);">Workspace Avatar <span class="font-normal lowercase" style="color: var(--color-primary-400);">(optional)</span></label>
                    @if($workspace->avatar_url)
                    <div class="mb-2 flex items-center gap-2">
                        <img src="{{ $workspace->avatar_url }}" alt="Current avatar" class="w-10 h-10 rounded-lg object-cover">
                        <span class="text-xs" style="color: var(--color-primary-400);">Current avatar — upload a new one to replace it</span>
                    </div>
                    @endif
                    <input id="avatar" name="avatar" type="file" accept="image/*"
                           class="w-full px-3 py-2 rounded-lg border text-sm outline-none transition-all cursor-pointer"
                           style="border-color: var(--color-border); background: var(--color-primary-50);">
                    <p class="text-xs mt-1" style="color: var(--color-primary-400);">Upload a PNG, JPG, or GIF (max 2MB)</p>
                    @error('avatar')
                        <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('workspaces.show', $workspace) }}" class="text-sm" style="color: var(--color-primary-500);">Cancel</a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-all hover:opacity-90"
                            style="background: var(--color-primary-800);">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
