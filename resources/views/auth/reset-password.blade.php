<x-guest-layout>
    <div class="page-title">Reset Password</div>
    <div class="page-subtitle">Choose a strong new password for your account.</div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        {{-- Token --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email', $request->email) }}"
                   class="form-input"
                   required autofocus autocomplete="username">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- New Password --}}
        <div class="form-group">
            <label for="password" class="form-label">New Password</label>
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
            Reset Password →
        </button>
    </form>
</x-guest-layout>
