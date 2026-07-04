<x-guest-layout>
    {{-- Session Status --}}
    @if (session('status'))
        <div class="status-msg">{{ session('status') }}</div>
    @endif

    <div class="page-title">Welcome back</div>
    <div class="page-subtitle">Sign in to your Synapse account to continue.</div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   placeholder="you@company.com"
                   class="form-input"
                   required autofocus autocomplete="username">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input id="password"
                   type="password"
                   name="password"
                   placeholder="Your password"
                   class="form-input"
                   required autocomplete="current-password">
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember + Forgot --}}
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem;">
            <label class="checkbox-label">
                <input type="checkbox" name="remember" id="remember_me"
                       style="width:14px;height:14px;accent-color:#0284c7;">
                Remember me
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   style="font-size:0.75rem; color:#38bdf8; text-decoration:none;"
                   onmouseover="this.style.textDecoration='underline'"
                   onmouseout="this.style.textDecoration='none'">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit" class="btn-primary">
            Sign In →
        </button>
    </form>

    <div class="auth-footer">
        Don't have an account?
        <a href="{{ route('register') }}">Create one</a>
    </div>
</x-guest-layout>
