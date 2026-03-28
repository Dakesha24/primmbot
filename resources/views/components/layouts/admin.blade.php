<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} — PRIMMBOT</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/icon-logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f4f5f7;
            color: #1a2332;
            min-height: 100vh;
        }

        /* ── Topbar ── */
        .admin-topbar {
            height: 58px;
            background: #0f1b3d;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 60;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
        }

        .topbar-brand img {
            height: 28px;
            width: auto;
            object-fit: contain;
        }

        .topbar-badge {
            font-size: 9px;
            font-weight: 700;
            color: rgba(255,255,255,0.5);
            background: rgba(255,255,255,0.08);
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .topbar-page {
            font-size: 13px;
            font-weight: 500;
            color: rgba(255,255,255,0.6);
            margin-left: 16px;
            padding-left: 16px;
            border-left: 1px solid rgba(255,255,255,0.1);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.75);
            font-size: 13px;
            font-weight: 500;
        }

        .topbar-avatar {
            width: 28px;
            height: 28px;
            border-radius: 4px;
            background: rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
        }

        .btn-logout {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.6);
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            font-family: inherit;
            font-weight: 600;
            transition: all 0.15s;
        }

        .btn-logout:hover {
            background: rgba(239,68,68,0.12);
            border-color: rgba(239,68,68,0.3);
            color: #fca5a5;
        }

        /* ── Sidebar ── */
        .admin-sidebar {
            width: 240px;
            background: #fff;
            border-right: 1px solid #e8eaf0;
            position: fixed;
            top: 58px;
            left: 0;
            bottom: 0;
            z-index: 50;
            display: flex;
            flex-direction: column;
            padding: 20px 10px;
            overflow-y: auto;
        }

        .nav-label {
            font-size: 10px;
            font-weight: 700;
            color: #b0b8c8;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 14px 12px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 5px;
            color: #4a5568;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.12s;
        }

        .nav-item:hover {
            background: #f0f2f7;
            color: #0f1b3d;
        }

        .nav-item.active {
            background: #0f1b3d;
            color: #fff;
        }

        .nav-item svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            opacity: 0.75;
        }

        .nav-item.active svg { opacity: 1; }

        /* ── Content ── */
        .admin-content {
            margin-left: 240px;
            margin-top: 58px;
            padding: 28px 32px;
            min-height: calc(100vh - 58px);
        }

        /* ── Page Header ── */
        .page-header {
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 20px;
            font-weight: 700;
            color: #0f1b3d;
            margin-bottom: 2px;
        }

        .page-header p {
            font-size: 13px;
            color: #6b7a99;
        }

        /* ── Card ── */
        .card {
            background: #fff;
            border: 1px solid #e8eaf0;
            border-radius: 6px;
            padding: 24px;
        }

        /* ── Alert ── */
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-left: 3px solid #16a34a;
            color: #166534;
            padding: 11px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 3px solid #dc2626;
            color: #991b1b;
            padding: 11px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        /* ── Modal ── */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10,18,40,0.5);
            backdrop-filter: blur(4px);
            z-index: 100;
            justify-content: center;
            align-items: center;
        }

        .modal-backdrop.active {
            display: flex;
        }

        .modal-box {
            background: #fff;
            border-radius: 8px;
            padding: 28px 32px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 48px rgba(10,18,40,0.18);
            border: 1px solid #e8eaf0;
        }

        .modal-box h2 {
            font-size: 16px;
            font-weight: 700;
            color: #0f1b3d;
            margin-bottom: 20px;
        }

        .modal-close {
            float: right;
            background: none;
            border: none;
            font-size: 20px;
            color: #94a3b8;
            cursor: pointer;
            line-height: 1;
            margin-top: -2px;
        }

        .modal-close:hover { color: #0f1b3d; }

        /* ── Form ── */
        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 5px;
            letter-spacing: 0.02em;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid #dde1ea;
            border-radius: 5px;
            font-size: 13.5px;
            font-family: inherit;
            color: #1a2332;
            background: #fff;
            transition: all 0.15s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0f1b3d;
            box-shadow: 0 0 0 3px rgba(15,27,61,0.07);
        }

        .form-group textarea { resize: vertical; }

        .form-group input[type="number"] { width: 120px; }

        .form-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #f0f2f7;
        }

        .btn-primary {
            background: #0f1b3d;
            color: #fff;
            padding: 9px 24px;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.15s;
        }

        .btn-primary:hover { background: #1a2d5a; }

        .btn-secondary {
            background: #f4f5f7;
            color: #4a5568;
            padding: 9px 20px;
            border: 1px solid #dde1ea;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.12s;
        }

        .btn-secondary:hover {
            background: #e8eaf0;
            color: #1a2332;
        }

        .form-errors {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 3px solid #dc2626;
            color: #991b1b;
            padding: 11px 14px;
            border-radius: 4px;
            margin-bottom: 18px;
            font-size: 13px;
        }
    </style>
    {{ $styles ?? '' }}
</head>

<body>

    <!-- Topbar -->
    <header class="admin-topbar">
        <div class="topbar-left">
            <span class="topbar-brand"><img src="/assets/images/logo.png" alt="PRIMMBOT">PRIMMBOT</span>
            <span class="topbar-badge">Admin Panel</span>
            <span class="topbar-page">{{ $title ?? 'Dashboard' }}</span>
        </div>
        <div class="topbar-right">
            <div class="topbar-user">
                <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                {{ auth()->user()->name }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <span class="nav-label">Utama</span>

        <a href="{{ route('admin.dashboard') }}"
            class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="3" width="7" height="7" rx="1" />
                <rect x="3" y="14" width="7" height="7" rx="1" />
                <rect x="14" y="14" width="7" height="7" rx="1" />
            </svg>
            Dashboard
        </a>

        <span class="nav-label">Konten</span>

        <a href="{{ route('admin.courses.index') }}"
            class="nav-item {{ request()->routeIs('admin.courses.*') || request()->routeIs('admin.chapters.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
            </svg>
            Kelola LKPD
        </a>

        <a href="{{ route('admin.sandbox.index') }}"
            class="nav-item {{ request()->routeIs('admin.sandbox.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <ellipse cx="12" cy="5" rx="9" ry="3" />
                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3" />
                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5" />
            </svg>
            Kelola Database
        </a>

        <span class="nav-label">Siswa</span>

        <a href="{{ route('admin.students.index') }}"
            class="nav-item {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            Kelola Siswa
        </a>

        <a href="{{ route('admin.hasil-kelas.index') }}"
            class="nav-item {{ request()->routeIs('admin.hasil-kelas.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
            </svg>
            Hasil LKPD
        </a>

        <span class="nav-label">Pengaturan</span>

        <a href="{{ route('admin.schools.index') }}"
            class="nav-item {{ request()->routeIs('admin.schools.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            Sekolah
        </a>

        <a href="{{ route('admin.tahun-ajaran.index') }}"
            class="nav-item {{ request()->routeIs('admin.tahun-ajaran.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Tahun Ajaran
        </a>

        <a href="{{ route('admin.kelas.index') }}"
            class="nav-item {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <line x1="23" y1="11" x2="17" y2="11"/><line x1="20" y1="8" x2="20" y2="14"/>
            </svg>
            Kelas
        </a>
    </aside>

    <!-- Content -->
    <main class="admin-content">
        @if (session('success'))
            <div class="alert-success">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{ $slot }}
    </main>

    {{ $scripts ?? '' }}
</body>

</html>
