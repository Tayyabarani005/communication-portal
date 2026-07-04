<x-app-layout>
    <x-slot name="title">Edit Profile</x-slot>

    {{-- Left sidebar --}}
    <div class="flex flex-col w-64 flex-shrink-0 border-r sidebar-scroll overflow-y-auto" style="background: var(--color-sidebar-bg); border-color: var(--color-sidebar-border);">
        <div class="p-4 border-b" style="border-color: var(--color-sidebar-border);">
            <p class="section-label">Settings</p>
        </div>
        <nav class="p-2">
            <div class="nav-item active mb-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                Profile
            </div>
            <a href="{{ route('workspaces.index') }}" class="nav-item mb-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z" />
                </svg>
                Workspaces
            </a>
        </nav>

        {{-- My Workspaces section in sidebar --}}
        @if($workspaces->isNotEmpty())
        <div class="p-2 border-t mt-2" style="border-color: var(--color-sidebar-border);">
            <p class="section-label mb-2">My Workspaces</p>
            @foreach($workspaces as $ws)
            <div class="mb-1">
                <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg">
                    <div class="w-6 h-6 rounded flex items-center justify-center text-xs font-bold flex-shrink-0"
                         style="background: var(--color-accent-700); color: white;">
                        {{ strtoupper(substr($ws->name, 0, 2)) }}
                    </div>
                    <span class="text-xs truncate flex-1" style="color: var(--color-sidebar-text);">{{ $ws->name }}</span>
                    @if($ws->my_role === 'admin')
                    <span class="text-xs px-1 rounded" style="background: rgba(59,130,246,0.15); color: var(--color-accent-300); font-size: 0.6rem;">Admin</span>
                    @endif
                </div>
                @if($ws->my_role === 'admin')
                <div class="flex gap-1 pl-8 mb-1">
                    <a href="{{ route('workspaces.edit', $ws) }}"
                       class="text-xs px-2 py-0.5 rounded transition-colors"
                       style="color: var(--color-sidebar-text-muted);"
                       onmouseover="this.style.color='white'"
                       onmouseout="this.style.color='var(--color-sidebar-text-muted)'">⚙ Settings</a>
                    <button onclick="openAddMemberModal('{{ $ws->workspace_id }}', '{{ addslashes($ws->name) }}')"
                            class="text-xs px-2 py-0.5 rounded transition-colors"
                            style="color: var(--color-sidebar-text-muted); background: none; border: none; cursor: pointer;"
                            onmouseover="this.style.color='white'"
                            onmouseout="this.style.color='var(--color-sidebar-text-muted)'">+ Member</button>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        <div class="mt-auto p-3 border-t" style="border-color: var(--color-sidebar-border);">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item w-full" style="color: #f87171; background: transparent; border: none; cursor: pointer; text-align: left;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Log Out
                </button>
            </form>
        </div>
    </div>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col overflow-hidden" style="background: var(--color-bg-main);">
        <div class="page-header">
            <div>
                <h1>Profile Settings</h1>
                <p>Manage your personal information and account preferences.</p>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6">
            <div class="max-w-2xl mx-auto space-y-6">

                {{-- Flash messages --}}
                @if(session('status') === 'profile-updated')
                <div class="alert-success flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    Profile updated successfully.
                </div>
                @endif
                @if(session('status') === 'password-updated')
                <div class="alert-success flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    Password updated successfully.
                </div>
                @endif

                {{-- Profile Information --}}
                <div class="card p-6">
                    <h2 class="text-sm font-semibold mb-1" style="color: var(--color-text-primary);">Profile Information</h2>
                    <p class="text-xs mb-5" style="color: var(--color-text-secondary);">Update your username, display name, email, and avatar.</p>

                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

                    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('patch')

                        {{-- Avatar --}}
                        <div>
                            <label class="form-label">Avatar</label>
                            <div class="flex items-center gap-4">
                                <div id="avatar-preview-wrap" class="w-16 h-16 rounded-full overflow-hidden flex items-center justify-center text-lg font-bold flex-shrink-0"
                                     style="background: var(--color-accent-600); color: white;">
                                    @if($user->avatar_url)
                                        <img id="avatar-preview-img" src="{{ $user->avatar_url }}" alt="{{ $user->username }}" class="w-full h-full object-cover">
                                    @else
                                        <span id="avatar-preview-initials">{{ strtoupper(substr($user->username, 0, 2)) }}</span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <label for="avatar" class="btn btn-secondary btn-sm cursor-pointer inline-flex">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                        </svg>
                                        Upload Photo
                                    </label>
                                    <input id="avatar" name="avatar" type="file" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                                    <p class="text-xs mt-1" style="color: var(--color-text-muted);">JPG, PNG or GIF · Max 2MB</p>
                                    @error('avatar')
                                    <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Username --}}
                        <div>
                            <label for="username" class="form-label">Username</label>
                            <input id="username" name="username" type="text"
                                   class="form-input"
                                   value="{{ old('username', $user->username) }}"
                                   required autofocus autocomplete="username">
                            @error('username')
                            <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Display Name --}}
                        <div>
                            <label for="name" class="form-label">Display Name <span class="font-normal normal-case" style="color: var(--color-text-muted);">(optional)</span></label>
                            <input id="name" name="name" type="text"
                                   class="form-input"
                                   value="{{ old('name', $user->name) }}"
                                   autocomplete="name"
                                   placeholder="Your full name">
                            @error('name')
                            <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="form-label">Email Address</label>
                            <input id="email" name="email" type="email"
                                   class="form-input"
                                   value="{{ old('email', $user->email) }}"
                                   required autocomplete="email">
                            @error('email')
                            <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                            @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                            <div class="mt-2">
                                <p class="text-xs" style="color: var(--color-text-secondary);">
                                    Your email is unverified.
                                    <button form="send-verification" class="underline text-xs" style="color: var(--color-accent-500); background: none; border: none; cursor: pointer;">
                                        Re-send verification
                                    </button>
                                </p>
                            </div>
                            @endif
                        </div>

                        <div class="pt-1">
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Change Password --}}
                <div class="card p-6">
                    <h2 class="text-sm font-semibold mb-1" style="color: var(--color-text-primary);">Change Password</h2>
                    <p class="text-xs mb-5" style="color: var(--color-text-secondary);">Ensure your account uses a strong, unique password.</p>

                    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                        @csrf
                        @method('put')

                        <div>
                            <label for="update_password_current_password" class="form-label">Current Password</label>
                            <input id="update_password_current_password" name="current_password" type="password"
                                   class="form-input" autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                            <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="update_password_password" class="form-label">New Password</label>
                            <input id="update_password_password" name="password" type="password"
                                   class="form-input" autocomplete="new-password">
                            @error('password', 'updatePassword')
                            <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="update_password_password_confirmation" class="form-label">Confirm New Password</label>
                            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                                   class="form-input" autocomplete="new-password">
                            @error('password_confirmation', 'updatePassword')
                            <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-1">
                            <button type="submit" class="btn btn-secondary">Update Password</button>
                        </div>
                    </form>
                </div>

                {{-- Delete Account --}}
                <div class="card p-6" style="border-color: #fca5a5;">
                    <h2 class="text-sm font-semibold mb-1" style="color: #991b1b;">Delete Account</h2>
                    <p class="text-xs mb-5" style="color: var(--color-text-secondary);">Once deleted, all your data will be permanently removed. This action cannot be undone.</p>

                    <form method="post" action="{{ route('profile.destroy') }}"
                          onsubmit="return confirm('Are you absolutely sure? This cannot be undone.')">
                        @csrf
                        @method('delete')

                        <div class="mb-4">
                            <label for="delete_password" class="form-label">Confirm Password</label>
                            <input id="delete_password" name="password" type="password"
                                   class="form-input" placeholder="Enter your password to confirm"
                                   style="border-color: #fca5a5;">
                            @error('password', 'userDeletion')
                            <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                            Delete Account
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- Add Member Modal (triggered from sidebar) --}}
    <div id="profile-add-member-modal"
         class="hidden fixed inset-0 flex items-center justify-center z-50"
         style="background: rgba(15,23,42,0.5); backdrop-filter: blur(2px);"
         onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="modal-box">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-base" style="color: var(--color-text-primary);">
                    Add Member to <span id="modal-ws-name" class="text-blue-600"></span>
                </h3>
                <button onclick="document.getElementById('profile-add-member-modal').classList.add('hidden')"
                        style="background: none; border: none; cursor: pointer; color: var(--color-text-muted); font-size: 1.25rem; line-height: 1;">✕</button>
            </div>
            <p class="text-sm mb-4" style="color: var(--color-text-secondary);">Enter the email address of the person to invite.</p>
            <form id="modal-add-member-form" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email"
                           placeholder="colleague@company.com"
                           required
                           class="form-input">
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="document.getElementById('profile-add-member-modal').classList.add('hidden')"
                            class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Invitation</button>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const wrap = document.getElementById('avatar-preview-wrap');
            const initials = document.getElementById('avatar-preview-initials');
            if (initials) initials.style.display = 'none';
            let img = document.getElementById('avatar-preview-img');
            if (!img) {
                img = document.createElement('img');
                img.id = 'avatar-preview-img';
                img.className = 'w-full h-full object-cover';
                img.alt = 'Avatar preview';
                wrap.appendChild(img);
            }
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function openAddMemberModal(workspaceId, workspaceName) {
    document.getElementById('modal-ws-name').textContent = workspaceName;
    document.getElementById('modal-add-member-form').action = '/workspaces/' + workspaceId + '/invite';
    document.getElementById('profile-add-member-modal').classList.remove('hidden');
}
</script>
@endpush
</x-app-layout>
