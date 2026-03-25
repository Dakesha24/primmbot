<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'PRIMMBOT' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --blue-950: #0a1628;
            --blue-900: #0f2044;
            --blue-800: #142c5c;
            --blue-600: #2563eb;
            --blue-400: #60a5fa;
            --blue-300: #93c5fd;
            --cyan-400: #22d3ee;
            --card-bg: rgba(255, 255, 255, 0.04);
            --card-border: rgba(255, 255, 255, 0.08);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--blue-950);
            color: #e2e8f0;
            min-height: 100vh;
        }

        /* Glow */
        .glow-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.1;
            pointer-events: none;
            z-index: 0;
        }

        .glow-1 {
            width: 500px;
            height: 500px;
            background: var(--blue-600);
            top: -10%;
            right: -5%;
        }

        .glow-2 {
            width: 400px;
            height: 400px;
            background: var(--cyan-400);
            bottom: 5%;
            left: -8%;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 50;
            padding: 0.9rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(10, 22, 40, 0.8);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 2.5rem;
        }

        .nav-logo {
            font-weight: 800;
            font-size: 1.25rem;
            color: #fff;
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .nav-logo span {
            color: var(--cyan-400);
        }

        .nav-menu {
            display: flex;
            gap: 0.5rem;
        }

        .nav-menu a {
            color: #64748b;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 0.5rem 0.9rem;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .nav-menu a:hover {
            color: #cbd5e1;
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-menu a.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.08);
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.4rem 0.8rem;
            border-radius: 10px;
            cursor: pointer;
            position: relative;
            transition: background 0.2s;
        }

        .nav-user:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--blue-600), #4f46e5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            color: #fff;
        }

        .nav-username {
            font-size: 0.85rem;
            color: #cbd5e1;
            font-weight: 500;
        }

        /* Dropdown */
        .dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 0.5rem;
            background: rgba(15, 32, 68, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 0.4rem;
            min-width: 180px;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4);
        }

        .dropdown.show {
            display: block;
        }

        .dropdown a,
        .dropdown button {
            display: block;
            width: 100%;
            padding: 0.6rem 0.9rem;
            border: none;
            background: none;
            color: #94a3b8;
            font-size: 0.85rem;
            font-family: inherit;
            text-align: left;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
        }

        .dropdown a:hover,
        .dropdown button:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
        }

        .dropdown .divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.08);
            margin: 0.3rem 0.5rem;
        }

        .dropdown .logout-btn {
            color: #f87171;
        }

        .dropdown .logout-btn:hover {
            background: rgba(248, 113, 113, 0.1);
        }

        /* Main content */
        .main-content {
            position: relative;
            z-index: 1;
            padding-top: 5rem;
            min-height: 100vh;
        }

        .content-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            padding: 1.5rem 2rem;
        }

        /* Page header */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            color: #64748b;
            font-size: 0.9rem;
            margin-top: 0.3rem;
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 1.5rem;
            transition: all 0.25s;
        }

        .card:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.12);
        }

        .card-clickable {
            cursor: pointer;
            text-decoration: none;
            display: block;
            color: inherit;
        }

        .card-clickable:hover {
            transform: translateY(-3px);
        }

        /* Form styles (reusable) */
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

        .form-input,
        .form-select {
            width: 100%;
            padding: 0.7rem 0.9rem;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 0.9rem;
            font-family: inherit;
            outline: none;
            transition: all 0.2s;
        }

        .form-input:focus,
        .form-select:focus {
            border-color: var(--blue-400);
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.15);
        }

        .form-input::placeholder {
            color: #475569;
        }

        .form-select option {
            background: #0f2044;
            color: #fff;
        }

        .form-error {
            color: #f87171;
            font-size: 0.75rem;
            margin-top: 0.3rem;
        }

        .btn {
            padding: 0.75rem 1.8rem;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            border: none;
            transition: all 0.25s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--blue-600), #4f46e5);
            color: #fff;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 28px rgba(37, 99, 235, 0.4);
        }

        .btn-outline {
            background: transparent;
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.3);
            color: #fff;
        }

        /* Alerts */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.2);
            color: #86efac;
        }

        .alert-warning {
            background: rgba(234, 179, 8, 0.1);
            border: 1px solid rgba(234, 179, 8, 0.2);
            color: #fde047;
        }

        .alert-error {
            background: rgba(248, 113, 113, 0.1);
            border: 1px solid rgba(248, 113, 113, 0.2);
            color: #fca5a5;
        }

        /* Grid */
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .grid-3 {
                grid-template-columns: 1fr;
            }

            .nav-menu {
                display: none;
            }

            .content-wrapper {
                padding: 1rem;
            }
        }

        /* Fade in */
        .fade-up {
            opacity: 0;
            transform: translateY(16px);
            animation: fadeUp 0.5s ease forwards;
        }

        .fade-up-d1 {
            animation-delay: 0.05s;
        }

        .fade-up-d2 {
            animation-delay: 0.1s;
        }

        .fade-up-d3 {
            animation-delay: 0.15s;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="glow-orb glow-1"></div>
    <div class="glow-orb glow-2"></div>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-left">
            <a href="{{ route('dashboard') }}" class="nav-logo">PRIMM<span>BOT</span></a>
            <div class="nav-menu">
                <a href="{{ route('dashboard') }}"
                    class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                @if (Auth::user()->profile && Auth::user()->profile->isComplete())
                    <a href="{{ route('courses.index') }}"
                        class="{{ request()->routeIs('courses.*') || request()->routeIs('chapters.*') ? 'active' : '' }}">LKPD</a>
                    <a href="{{ route('hasil-belajar.index') }}"
                        class="{{ request()->routeIs('hasil-belajar.*') ? 'active' : '' }}">Hasil Belajar</a>
                @endif
            </div>
        </div>
        <div class="nav-right">
            <div class="nav-user" onclick="document.getElementById('userDropdown').classList.toggle('show')">
                <div class="nav-avatar">
                    @if (Auth::user()->profile && Auth::user()->profile->avatar)
                        <img src="{{ Auth::user()->profile->avatarUrl() }}"
                            style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    @else
                        <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    @endif
                </div>
                <span class="nav-username">{{ Auth::user()->profile->full_name ?? Auth::user()->username }}</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2">
                    <path d="M6 9l6 6 6-6" />
                </svg>

                <div class="dropdown" id="userDropdown" onclick="event.stopPropagation()">
                    <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:0.5rem;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        Profil Saya
                    </a>
                    <div class="divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn" style="display:flex;align-items:center;gap:0.5rem;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success fade-up">{{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning fade-up">{{ session('warning') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error fade-up">{{ session('error') }}</div>
            @endif
            {{ $slot }}
        </div>
    </div>

    <script>
        // Close dropdown on outside click
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            const userBtn = document.querySelector('.nav-user');
            if (dropdown && !userBtn.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    </script>
</body>

</html>
