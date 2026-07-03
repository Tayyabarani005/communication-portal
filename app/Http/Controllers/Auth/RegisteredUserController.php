<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['sometimes', 'nullable', 'string', 'max:255'],
            'username' => ['sometimes', 'nullable', 'string', 'max:50', 'alpha_dash', 'unique:'.User::class],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Derive a username from the name field when username isn't provided (Breeze test compatibility)
        $username = $request->username
            ?? \Illuminate\Support\Str::slug($request->name ?? 'user') . rand(100, 999);

        $user = User::create([
            'name'          => $request->name ?? $username,
            'username'      => $username,
            'email'         => $request->email,
            'password_hash' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
