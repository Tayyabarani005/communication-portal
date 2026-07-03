<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Synapse — Enterprise Access & Collaboration platform for teams.">
    <title>{{ config('app.name', 'Synapse') }} — {{ $title ?? 'Dashboard' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased" style="font-family: 'Inter', sans-serif; background: var(--color-bg-main);">

<div class="flex h-screen overflow-hidden">

    {{-- ══ Left: Narrow workspace icon rail ══ --}}
    <div class="flex flex-col items-center w-16 py-4 gap-3 flex-shrink-0" style="background: var(--color-primary-950);">
        {{-- Logo --}}
        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-2" style="background: var(--color-accent-600);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
            </svg>
        </div>

        {{-- Workspace icons --}}
        @auth
        @foreach(auth()->user()->workspaces as $ws)
            <a href="{{ route('workspaces.show', $ws) }}"
               title="{{ $ws->name }}"
               class="w-10 h-10 rounded-xl flex items-center justify-center text-xs font-bold transition-all duration-150 hover:rounded-lg"
               style="background: var(--color-primary-800); color: var(--color-primary-300);">
                {{ strtoupper(substr($ws->name, 0, 2)) }}
            </a>
        @endforeach
        <a href="{{ route('workspaces.create') }}"
           title="New Workspace"
           class="w-10 h-10 rounded-xl border-2 border-dashed flex items-center justify-center transition-all duration-150 hover:border-solid"
           style="border-color: var(--color-primary-700); color: var(--color-primary-500);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </a>
        @endauth

        {{-- Spacer + bottom icons --}}
        <div class="mt-auto flex flex-col items-center gap-3">
            <a href="{{ route('dms.index') }}" title="Direct Messages" class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors" style="color: var(--color-primary-400);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                </svg>
            </a>
            @auth
            <a href="{{ route('profile.edit') }}" class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center text-xs font-bold" style="background: var(--color-accent-700); color: white;">
                {{ strtoupper(substr(auth()->user()->username, 0, 2)) }}
            </a>
            @endauth
        </div>
    </div>

    {{-- ══ Main content (sidebar + chat) ══ --}}
    <div class="flex flex-1 overflow-hidden">
        {{ $slot }}
    </div>
</div>

@livewireScripts
@stack('scripts')
</body>
</html>
