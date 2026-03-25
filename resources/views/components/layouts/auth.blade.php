<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'PRIMMBOT' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --blue-950: #0a1628;
            --blue-900: #0f2044;
            --blue-800: #142c5c;
            --blue-600: #2563eb;
            --blue-400: #60a5fa;
            --cyan-400: #22d3ee;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--blue-950);
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        .glow-orb {
            position: fixed; border-radius: 50%; filter: blur(120px);
            opacity: 0.12; pointer-events: none; z-index: 0;
        }
        .glow-1 { width: 500px; height: 500px; background: var(--blue-600); top: -15%; right: -10%; }
        .glow-2 { width: 350px; height: 350px; background: var(--cyan-400); bottom: -10%; left: -5%; }

        .auth-container {
            position: relative; z-index: 1;
            width: 100%; max-width: 440px;
            padding: 1.5rem;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-header a {
            text-decoration: none;
        }
        .auth-logo {
            font-weight: 800; font-size: 1.6rem; color: #fff;
            letter-spacing: -0.5px;
        }
        .auth-logo span { color: var(--cyan-400); }
        .auth-subtitle {
            color: #64748b; font-size: 0.875rem; margin-top: 0.5rem;
        }

        .auth-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 2rem;
            backdrop-filter: blur(12px);
        }

        .auth-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }
        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 0.4rem;
            letter-spacing: 0.3px;
        }
        .form-input {
            width: 100%;
            padding: 0.7rem 0.9rem;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
            color: #fff;
            font-size: 0.9rem;
            font-family: inherit;
            outline: none;
            transition: all 0.2s;
        }
        .form-input:focus {
            border-color: var(--blue-400);
            box-shadow: 0 0 0 3px rgba(96,165,250,0.15);
        }
        .form-input::placeholder { color: #475569; }
        .form-input:-webkit-autofill,
        .form-input:-webkit-autofill:hover,
        .form-input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #202a3b inset;
            -webkit-text-fill-color: #fff;
            caret-color: #fff;
        }

        .form-error {
            color: #f87171;
            font-size: 0.75rem;
            margin-top: 0.3rem;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 0.75rem;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            border: none;
            transition: all 0.25s;
            text-align: center;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--blue-600), #4f46e5);
            color: #fff;
            box-shadow: 0 4px 20px rgba(37,99,235,0.3);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 28px rgba(37,99,235,0.4);
        }

        .divider {
            display: flex; align-items: center; gap: 1rem;
            margin: 1.5rem 0;
            color: #475569; font-size: 0.8rem;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px;
            background: rgba(255,255,255,0.08);
        }

        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            width: 100%;
            padding: 0.7rem;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
            color: #cbd5e1;
            font-size: 0.875rem;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-google:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.2);
            color: #fff;
        }
        .btn-google svg { flex-shrink: 0; }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: #64748b;
        }
        .auth-footer a {
            color: var(--blue-400);
            text-decoration: none;
            font-weight: 600;
        }
        .auth-footer a:hover { color: var(--cyan-400); }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .remember-row input[type="checkbox"] {
            accent-color: var(--blue-600);
            width: 16px; height: 16px;
            cursor: pointer;
        }
        .remember-row label {
            font-size: 0.8rem; color: #94a3b8; cursor: pointer;
        }

        /* Alert */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.8rem;
            margin-bottom: 1.2rem;
        }
        .alert-error {
            background: rgba(248,113,113,0.1);
            border: 1px solid rgba(248,113,113,0.2);
            color: #fca5a5;
        }
        .alert-success {
            background: rgba(34,197,94,0.1);
            border: 1px solid rgba(34,197,94,0.2);
            color: #86efac;
        }

        /* Fade in */
        .fade-up {
            opacity: 0; transform: translateY(16px);
            animation: fadeUp 0.5s ease forwards;
        }
        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="glow-orb glow-1"></div>
    <div class="glow-orb glow-2"></div>

    <div class="auth-container fade-up">
        <div class="auth-header">
            <a href="{{ route('home') }}">
                <div class="auth-logo">PRIMM<span>BOT</span></div>
            </a>
            <p class="auth-subtitle">Platform e-LKPD Interaktif</p>
        </div>

        <div class="auth-card">
            {{ $slot }}
        </div>
    </div>
</body>
</html>