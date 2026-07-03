<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Synapse') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex items-center justify-center" style="background: var(--color-primary-950); font-family: 'Inter', sans-serif;">

    {{-- Subtle dot-grid background --}}
    <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(var(--color-primary-300) 1px, transparent 1px); background-size: 24px 24px;"></div>

    <div class="relative z-10 w-full max-w-sm mx-auto px-4">
        {{-- Logo --}}
        <div class="flex flex-col items-center mb-8">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3" style="background: var(--color-accent-600);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
            </div>
            <h1 class="text-xl font-bold tracking-widest uppercase" style="color: white; letter-spacing: 0.3em;">SYNAPSE</h1>
            <p class="text-xs mt-1" style="color: var(--color-primary-400);">Global Communications Architecture</p>
        </div>

        {{-- Card --}}
        <div class="rounded-2xl shadow-2xl p-8" style="background: white;">
            {{ $slot }}
        </div>

        <p class="text-center text-xs mt-6" style="color: var(--color-primary-500);">
            &copy; {{ date('Y') }} Synapse &middot; <a href="#" class="hover:underline">Privacy Policy</a> &middot; <a href="#" class="hover:underline">Terms of Service</a>
        </p>
    </div>
</body>
</html>
