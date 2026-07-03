<x-guest-layout>
    <div class="page-title">Create your account</div>
    <div class="page-subtitle">Join Synapse and start collaborating with your team.</div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Username --}}
        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <input id="username"
                   type="text"
                   name="username"
                   value="{{ old('username') }}"
                   placeholder="e.g. john_doe"
                   class="form-input"
                   required autofocus autocomplete="username">
            @error('username')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   placeholder="you@company.com"
                   class="form-input"
                   required autocomplete="email">
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
                   placeholder="Min 8 characters"
                   class="form-input"
                   required autocomplete="new-password">
            @error('password')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input id="password_confirmation"
                   type="password"
                   name="password_confirmation"
                   placeholder="Repeat your password"
                   class="form-input"
                   required autocomplete="new-password">
            @error('password_confirmation')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary">
            Create Account →
        </button>
    </form>

    <div class="auth-footer">
        Already have an account?
        <a href="{{ route('login') }}">Sign in</a>
    </div>
</x-guest-layout>
