<x-app-layout>
    <x-slot name="title">Create Workspace</x-slot>

    <div class="flex-1 flex items-center justify-center p-6" style="background: var(--color-bg-main);">
        <div class="w-full max-w-md rounded-2xl shadow-xl p-8" style="background: white;">
            <div class="mb-6">
                <h1 class="text-xl font-bold" style="color: var(--color-primary-900);">Naming your space.</h1>
                <p class="text-sm mt-1" style="color: var(--color-primary-500);">Every great collaboration starts with a clear identity. Choose a name that reflects your team's mission.</p>
            </div>

            <form method="POST" action="{{ route('workspaces.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--color-primary-600);">Workspace Name</label>
                    <input id="name" name="name" type="text"
                           value="{{ old('name') }}"
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
                              style="border-color: var(--color-border); background: var(--color-primary-50);">{{ old('description') }}</textarea>
                </div>

                <div class="mb-6">
                    <label for="avatar_url" class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--color-primary-600);">Workspace Avatar URL <span class="font-normal lowercase" style="color: var(--color-primary-400);">(optional)</span></label>
                    <input id="avatar_url" name="avatar_url" type="url"
                           value="{{ old('avatar_url') }}"
                           placeholder="https://example.com/logo.png"
                           class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none transition-all"
                           style="border-color: var(--color-border); background: var(--color-primary-50);">
                    @error('avatar_url')
                        <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('workspaces.index') }}" class="text-sm" style="color: var(--color-primary-500);">Cancel</a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-all hover:opacity-90"
                            style="background: var(--color-primary-800);">
                        Continue →
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
