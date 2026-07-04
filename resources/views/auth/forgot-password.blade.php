<x-guest-layout>
    <div class="page-title">Forgot Password?</div>
    <div class="page-subtitle">Enter your email and we'll send you a reset link.</div>

    @if (session('status'))
        <div class="status-msg">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   placeholder="you@company.com"
                   class="form-input"
                   required autofocus>
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary">
            Send Reset Link →
        </button>
    </form>

    <div class="auth-footer">
        Remembered it?
        <a href="{{ route('login') }}">Sign in</a>
    </div>
</x-guest-layout>
