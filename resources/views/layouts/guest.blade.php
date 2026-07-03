<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Synapse') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #020617;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        /* Animated grid background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(2, 132, 199, 0.15), transparent),
                radial-gradient(circle at 1px 1px, rgba(148,163,184,0.07) 1px, transparent 0);
            background-size: 100% 100%, 28px 28px;
            pointer-events: none;
            z-index: 0;
        }
        .auth-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 0 1rem;
        }
        .card-inner {
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(51, 65, 85, 0.8);
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            backdrop-filter: blur(20px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.03);
        }
        .logo-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2rem;
        }
        .logo-icon {
            width: 3.5rem;
            height: 3.5rem;
            background: linear-gradient(135deg, #0284c7, #0369a1);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.875rem;
            box-shadow: 0 0 30px rgba(2,132,199,0.4);
        }
        .logo-title {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: 0.3em;
            color: white;
            text-transform: uppercase;
        }
        .logo-sub {
            font-size: 0.7rem;
            color: #64748b;
            margin-top: 0.25rem;
            letter-spacing: 0.05em;
        }
        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .form-input {
            width: 100%;
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(51, 65, 85, 0.8);
            border-radius: 0.625rem;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            color: white;
            outline: none;
            transition: all 0.15s;
            font-family: 'Inter', sans-serif;
        }
        .form-input::placeholder { color: #475569; }
        .form-input:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2,132,199,0.15);
            background: rgba(30, 41, 59, 1);
        }
        .form-group { margin-bottom: 1rem; }
        .form-error {
            font-size: 0.75rem;
            color: #f87171;
            margin-top: 0.35rem;
        }
        .btn-primary {
            width: 100%;
            padding: 0.7rem 1rem;
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            border: none;
            border-radius: 0.625rem;
            cursor: pointer;
            transition: all 0.15s;
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.01em;
            box-shadow: 0 4px 15px rgba(2,132,199,0.3);
            margin-top: 0.5rem;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(2,132,199,0.4);
        }
        .btn-primary:active { transform: translateY(0); }
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.8rem;
            color: #475569;
        }
        .auth-footer a {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 500;
        }
        .auth-footer a:hover { text-decoration: underline; }
        .divider {
            border: none;
            border-top: 1px solid rgba(51, 65, 85, 0.6);
            margin: 1.25rem 0;
        }
        .page-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.35rem;
        }
        .page-subtitle {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 1.5rem;
        }
        .status-msg {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
            border-radius: 0.5rem;
            padding: 0.625rem 0.875rem;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: #94a3b8;
            cursor: pointer;
        }
        .copyright {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.7rem;
            color: #334155;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="logo-wrap">
            <div class="logo-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" style="width:1.75rem;height:1.75rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
            </div>
            <div class="logo-title">Synapse</div>
            <div class="logo-sub">Global Communications Architecture</div>
        </div>

        <div class="card-inner">
            {{ $slot }}
        </div>

        <div class="copyright">
            &copy; {{ date('Y') }} Synapse &middot; All rights reserved
        </div>
    </div>
</body>
</html>
