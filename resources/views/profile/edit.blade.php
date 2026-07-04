<x-app-layout>
    <x-slot name="title">Edit Profile</x-slot>

    {{-- Left sidebar --}}
    <div class="flex flex-col w-64 flex-shrink-0 border-r overflow-y-auto" style="background: var(--color-sidebar-bg); border-color: var(--color-primary-800);">
        <div class="p-4 border-b" style="border-color: var(--color-primary-800);">
            <h2 class="text-xs font-semibold uppercase tracking-widest" style="color: var(--color-sidebar-text);">Account Settings</h2>
        </div>
        <nav class="p-2">
            <div class="px-3 py-2 rounded-lg text-sm font-medium"
                 style="background: var(--color-sidebar-active-bg); color: white;">
                👤 Profile
            </div>
            <a href="{{ route('workspaces.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors mt-1"
               style="color: var(--color-sidebar-text);"
               onmouseover="this.style.background='var(--color-sidebar-hover-bg)'; this.style.color='white'"
               onmouseout="this.style.background='transparent'; this.style.color='var(--color-sidebar-text)'">
                🏠 Workspaces
            </a>
            <a href="{{ route('dms.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors mt-1"
               style="color: var(--color-sidebar-text);"
               onmouseover="this.style.background='var(--color-sidebar-hover-bg)'; this.style.color='white'"
               onmouseout="this.style.background='transparent'; this.style.color='var(--color-sidebar-text)'">
                💬 Messages
            </a>
        </nav>

        <div class="mt-auto p-3 border-t" style="border-color: var(--color-primary-800);">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors"
                        style="color: #f87171; background: transparent; border: none; cursor: pointer; text-align: left;"
                        onmouseover="this.style.background='rgba(239,68,68,0.1)'"
                        onmouseout="this.style.background='transparent'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Log Out
                </button>
            </form>
        </div>
    </div>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col overflow-hidden" style="background: var(--color-bg-main);">
        <div class="border-b px-6 py-4" style="background: white; border-color: var(--color-border);">
            <h1 class="text-lg font-semibold" style="color: var(--color-primary-900);">Profile Settings</h1>
            <p class="text-sm mt-0.5" style="color: var(--color-primary-500);">Manage your personal information and preferences.</p>
        </div>

        <div class="flex-1 overflow-y-auto p-6">
            <div class="max-w-xl mx-auto space-y-6">

                {{-- Success flash --}}
                @if (session('status') === 'profile-updated')
                <div class="px-4 py-3 rounded-lg text-sm" style="background: #dcfce7; color: #166534; border: 1px solid #86efac;">
                    ✓ Profile updated successfully.
                </div>
                @endif
                @if (session('status') === 'password-updated')
                <div class="px-4 py-3 rounded-lg text-sm" style="background: #dcfce7; color: #166534; border: 1px solid #86efac;">
                    ✓ Password updated successfully.
                </div>
                @endif

                {{-- ── Profile Information ── --}}
                <div class="rounded-xl border p-6" style="background: white; border-color: var(--color-border);">
                    <h2 class="text-sm font-semibold mb-1" style="color: var(--color-primary-900);">Profile Information</h2>
                    <p class="text-xs mb-5" style="color: var(--color-primary-500);">Update your account's username, display name, email and avatar.</p>

                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

                    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf
                        @method('patch')

                        {{-- Username --}}
                        <div>
                            <label for="username" class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--color-primary-600);">Username</label>
                            <input id="username" name="username" type="text"
                                   class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none transition-all focus:ring-2"
                                   style="border-color: var(--color-border); background: var(--color-primary-50); color: var(--color-primary-900);"
                                   value="{{ old('username', $user->username) }}"
                                   required autofocus autocomplete="username">
                            @error('username')
                                <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Display Name --}}
                        <div>
                            <label for="name" class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--color-primary-600);">Display Name <span class="font-normal normal-case" style="color: var(--color-primary-400);">(optional)</span></label>
                            <input id="name" name="name" type="text"
                                   class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none transition-all focus:ring-2"
                                   style="border-color: var(--color-border); background: var(--color-primary-50); color: var(--color-primary-900);"
                                   value="{{ old('name', $user->name) }}"
                                   autocomplete="name"
                                   placeholder="Your full name">
                            @error('name')
                                <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--color-primary-600);">Email Address</label>
                            <input id="email" name="email" type="email"
                                   class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none transition-all focus:ring-2"
                                   style="border-color: var(--color-border); background: var(--color-primary-50); color: var(--color-primary-900);"
                                   value="{{ old('email', $user->email) }}"
                                   required autocomplete="email">
                            @error('email')
                                <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                            @enderror

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-2">
                                    <p class="text-xs" style="color: var(--color-primary-500);">
                                        Your email address is unverified.
                                        <button form="send-verification" class="underline text-xs" style="color: var(--color-accent-500); background: none; border: none; cursor: pointer;">
                                            Re-send verification email
                                        </button>
                                    </p>
                                    @if (session('status') === 'verification-link-sent')
                                        <p class="mt-1 text-xs" style="color: #16a34a;">A new verification link has been sent.</p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Avatar URL --}}
                        <div>
                            <label for="avatar_url" class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--color-primary-600);">Avatar URL <span class="font-normal normal-case" style="color: var(--color-primary-400);">(optional)</span></label>
                            <input id="avatar_url" name="avatar_url" type="url"
                                   class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none transition-all focus:ring-2"
                                   style="border-color: var(--color-border); background: var(--color-primary-50); color: var(--color-primary-900);"
                                   value="{{ old('avatar_url', $user->avatar_url) }}"
                                   placeholder="https://example.com/avatar.jpg">
                            @error('avatar_url')
                                <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-all hover:opacity-90"
                                    style="background: var(--color-accent-600);">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ── Change Password ── --}}
                <div class="rounded-xl border p-6" style="background: white; border-color: var(--color-border);">
                    <h2 class="text-sm font-semibold mb-1" style="color: var(--color-primary-900);">Change Password</h2>
                    <p class="text-xs mb-5" style="color: var(--color-primary-500);">Ensure your account is using a strong, unique password.</p>

                    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                        @csrf
                        @method('put')

                        <div>
                            <label for="update_password_current_password" class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--color-primary-600);">Current Password</label>
                            <input id="update_password_current_password" name="current_password" type="password"
                                   class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none transition-all focus:ring-2"
                                   style="border-color: var(--color-border); background: var(--color-primary-50); color: var(--color-primary-900);"
                                   autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                                <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="update_password_password" class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--color-primary-600);">New Password</label>
                            <input id="update_password_password" name="password" type="password"
                                   class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none transition-all focus:ring-2"
                                   style="border-color: var(--color-border); background: var(--color-primary-50); color: var(--color-primary-900);"
                                   autocomplete="new-password">
                            @error('password', 'updatePassword')
                                <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="update_password_password_confirmation" class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--color-primary-600);">Confirm New Password</label>
                            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                                   class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none transition-all focus:ring-2"
                                   style="border-color: var(--color-border); background: var(--color-primary-50); color: var(--color-primary-900);"
                                   autocomplete="new-password">
                            @error('password_confirmation', 'updatePassword')
                                <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-all hover:opacity-90"
                                    style="background: var(--color-primary-800);">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ── Delete Account ── --}}
                <div class="rounded-xl border p-6" style="background: white; border-color: #fecaca;">
                    <h2 class="text-sm font-semibold mb-1" style="color: #991b1b;">Delete Account</h2>
                    <p class="text-xs mb-5" style="color: var(--color-primary-500);">Once deleted, all resources and data associated with this account will be permanently removed.</p>

                    <form method="post" action="{{ route('profile.destroy') }}"
                          onsubmit="return confirm('Are you sure? This cannot be undone.')">
                        @csrf
                        @method('delete')

                        <div class="mb-4">
                            <label for="delete_password" class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--color-primary-600);">Confirm your password</label>
                            <input id="delete_password" name="password" type="password"
                                   class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none transition-all focus:ring-2"
                                   style="border-color: #fca5a5; background: #fff1f2; color: var(--color-primary-900);"
                                   placeholder="Enter your password to confirm">
                            @error('password', 'userDeletion')
                                <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-all hover:opacity-90"
                                style="background: #dc2626;">
                            Delete Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
