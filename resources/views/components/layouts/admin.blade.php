<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} — PRIMMBOT</title>
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
            background: #f0f2f7;
            color: #1e293b;
            min-height: 100vh;
        }

        /* ── Topbar ── */
        .admin-topbar {
            height: 64px;
            background: #0f1b3d;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 60;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .topbar-brand {
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
        }

        .topbar-badge {
            font-size: 9px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.1);
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .topbar-page {
            font-size: 15px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
            margin-left: 24px;
            padding-left: 24px;
            border-left: 1px solid rgba(255, 255, 255, 0.12);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
        }

        .topbar-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.7);
            padding: 7px 16px;
            border-radius: 8px;
            font-size: 12px;
            cursor: pointer;
            font-family: inherit;
            font-weight: 600;
            transition: all 0.15s;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        /* ── Sidebar ── */
        .admin-sidebar {
            width: 250px;
            background: #fff;
            border-right: 1px solid #e4e8f1;
            position: fixed;
            top: 64px;
            left: 0;
            bottom: 0;
            z-index: 50;
            display: flex;
            flex-direction: column;
            padding: 24px 14px;
        }

        .nav-label {
            font-size: 10px;
            font-weight: 700;
            color: #a0aec0;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 14px 14px 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 14px;
            border-radius: 10px;
            color: #64748b;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.15s;
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
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        /* ── Content ── */
        .admin-content {
            margin-left: 250px;
            margin-top: 64px;
            padding: 32px;
            min-height: calc(100vh - 64px);
        }

        /* ── Alert ── */
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
            padding: 12px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ── Modal ── */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 27, 61, 0.45);
            backdrop-filter: blur(6px);
            z-index: 100;
            justify-content: center;
            align-items: center;
        }

        .modal-backdrop.active {
            display: flex;
        }

        .modal-box {
            background: #fff;
            border-radius: 18px;
            padding: 32px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 32px 64px rgba(15, 27, 61, 0.18);
        }

        .modal-box h2 {
            font-size: 18px;
            font-weight: 700;
            color: #0f1b3d;
            margin-bottom: 24px;
        }

        .modal-close {
            float: right;
            background: none;
            border: none;
            font-size: 22px;
            color: #94a3b8;
            cursor: pointer;
            line-height: 1;
            margin-top: -4px;
        }

        .modal-close:hover {
            color: #0f1b3d;
        }

        /* ── Form ── */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            color: #1e293b;
            background: #fff;
            transition: all 0.15s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0f1b3d;
            box-shadow: 0 0 0 3px rgba(15, 27, 61, 0.08);
        }

        .form-group textarea {
            resize: vertical;
        }

        .form-group input[type="number"] {
            width: 120px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 26px;
        }

        .btn-primary {
            background: #0f1b3d;
            color: #fff;
            padding: 10px 28px;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.15s;
        }

        .btn-primary:hover {
            background: #1a2d5a;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #64748b;
            padding: 10px 22px;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .form-errors {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13px;
        }
    </style>
    {{ $styles ?? '' }}
</head>

<body>

    <!-- Topbar -->
    <header class="admin-topbar">
        <div class="topbar-left">
            <span class="topbar-brand">PRIMMBOT</span>
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
        <span class="nav-label">Menu</span>

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

        <a href="{{ route('admin.courses.index') }}"
            class="nav-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
            </svg>
            Kelola Kelas
        </a>

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

        <a href="#" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
            </svg>
            Hasil Kelas
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

        <a href="#" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
            Pengumuman
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
