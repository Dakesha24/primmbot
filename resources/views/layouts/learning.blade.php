<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $chapter->title }} - PRIMMBOT</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/icon-logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        :root {
            --blue-950: #0a1628;
            --blue-900: #0f2044;
            --blue-800: #142c5c;
            --blue-600: #2563eb;
            --blue-400: #60a5fa;
            --cyan-400: #22d3ee;
            --sidebar-w: 280px
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--blue-950);
            color: #e2e8f0;
            min-height: 100vh
        }

        .topbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 50;
            height: 56px;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(10, 22, 40, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06)
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem
        }

        .topbar-logo {
            font-weight: 800;
            font-size: 1.15rem;
            color: #fff;
            text-decoration: none
        }

        .topbar-logo span {
            color: var(--cyan-400)
        }

        .btn-sidebar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s
        }

        .btn-sidebar:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #fff
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem
        }

        .btn-exit-kelas {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.4rem 0.9rem;
            border-radius: 8px;
            border: 1px solid rgba(248,113,113,0.3);
            background: rgba(248,113,113,0.08);
            color: #f87171;
            font-size: 0.8rem; font-weight: 600;
            font-family: inherit; cursor: pointer;
            transition: all 0.2s;
        }
        .btn-exit-kelas:hover {
            background: rgba(248,113,113,0.16);
            border-color: rgba(248,113,113,0.5);
            color: #fca5a5;
        }

        .sidebar {
            position: fixed;
            top: 56px;
            left: 0;
            width: var(--sidebar-w);
            height: calc(100vh - 56px);
            overflow-y: auto;
            background: rgba(15, 32, 68, 0.6);
            backdrop-filter: blur(12px);
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            z-index: 40;
            transition: transform 0.3s ease;
            padding-bottom: 2rem
        }

        .sidebar.closed {
            transform: translateX(-100%)
        }

        .sidebar-header {
            padding: 1.2rem 1.2rem 0.8rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06)
        }

        .sidebar-chapter-label {
            font-size: 0.65rem;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700
        }

        .sidebar-chapter-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #fff;
            margin-top: 0.2rem
        }

        .sidebar-section {
            padding: 0.8rem 0.8rem 0
        }

        .sidebar-section-title {
            font-size: 0.7rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 0.4rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 6px;
            transition: background 0.15s
        }

        .sidebar-section-title:hover {
            background: rgba(255, 255, 255, 0.03)
        }

        .sidebar-section-title svg {
            transition: transform 0.2s
        }

        .sidebar-section-title.collapsed svg {
            transform: rotate(-90deg)
        }

        .sidebar-items {
            padding: 0.2rem 0
        }

        .sidebar-items.hidden {
            display: none
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.6rem;
            margin: 0.1rem 0.3rem;
            border-radius: 6px;
            font-size: 0.8rem;
            color: #64748b;
            text-decoration: none;
            transition: all 0.15s;
            font-weight: 500
        }

        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.04);
            color: #cbd5e1
        }

        .sidebar-item.active {
            background: rgba(37, 99, 235, 0.15);
            color: var(--blue-400);
            font-weight: 600
        }

        .sidebar-item.completed {
            color: #4ade80
        }

        .sidebar-check {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center
        }

        .sidebar-check.done {
            background: rgba(74, 222, 128, 0.15)
        }

        .sidebar-check.pending {
            border: 1.5px solid rgba(255, 255, 255, 0.15)
        }

        .main {
            margin-left: var(--sidebar-w);
            padding-top: 56px;
            min-height: 100vh;
            transition: margin-left 0.3s ease
        }

        .main.expanded {
            margin-left: 0
        }

        .main-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem
        }

        .nav-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 0;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06)
        }

        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            font-family: inherit
        }

        .nav-btn-prev {
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: transparent
        }

        .nav-btn-prev:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.2)
        }

        .nav-btn-next {
            color: #fff;
            background: linear-gradient(135deg, var(--blue-600), #4f46e5);
            border: none;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.3)
        }

        .nav-btn-next:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(37, 99, 235, 0.4)
        }

        .content-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 14px;
            padding: 1.5rem;
            margin-bottom: 1.5rem
        }

        .content-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 1.5rem;
            letter-spacing: -0.3px
        }

        .prose {
            color: #94a3b8;
            line-height: 1.8;
            font-size: 0.9rem
        }

        .prose h3 {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.8rem
        }

        .prose h4 {
            color: #cbd5e1;
            font-size: 0.95rem;
            font-weight: 600;
            margin: 1.2rem 0 0.5rem
        }

        .prose p {
            margin-bottom: 0.8rem
        }

        .prose ul,
        .prose ol {
            margin: 0.5rem 0 1rem 1.2rem
        }

        .prose li {
            margin-bottom: 0.4rem
        }

        .prose strong {
            color: #cbd5e1
        }

        .prose code {
            background: rgba(255, 255, 255, 0.06);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            font-size: 0.85rem;
            color: var(--cyan-400)
        }

        .prose pre {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 10px;
            padding: 1rem;
            overflow-x: auto;
            margin: 0.8rem 0
        }

        .prose pre code {
            background: none;
            padding: 0;
            color: #4ade80
        }

        .glow-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.08;
            pointer-events: none;
            z-index: 0
        }

        .glow-1 {
            width: 400px;
            height: 400px;
            background: var(--blue-600);
            top: -10%;
            right: -5%
        }

        @media(max-width:768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px
            }

            .sidebar.open {
                transform: translateX(0)
            }

            .sidebar.closed {
                transform: translateX(-100%)
            }

            .main {
                margin-left: 0 !important
            }

            .main.expanded {
                margin-left: 0
            }
        }
    </style>
</head>

<body>
    <div class="glow-orb glow-1"></div>

    <!-- Modal Keluar Kelas -->
    <div id="exitModal" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);align-items:center;justify-content:center;">
        <div style="background:linear-gradient(135deg,#0f2044,#142c5c);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:2rem;max-width:380px;width:90%;text-align:center;box-shadow:0 24px 64px rgba(0,0,0,0.5);">
            <div style="width:52px;height:52px;border-radius:50%;background:rgba(248,113,113,0.12);display:flex;align-items:center;justify-content:center;margin:0 auto 1.2rem;">
                <svg width="22" height="22" fill="none" stroke="#f87171" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>
            <h3 style="font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:0.5rem;">Keluar dari Kelas?</h3>
            <p style="font-size:0.85rem;color:#94a3b8;margin-bottom:1.8rem;line-height:1.6;">Progressmu sudah tersimpan. Kamu bisa melanjutkan belajar kapan saja.</p>
            <div style="display:flex;gap:0.75rem;justify-content:center;">
                <button onclick="closeExitModal()"
                    style="padding:0.6rem 1.4rem;border-radius:10px;border:1px solid rgba(255,255,255,0.12);background:transparent;color:#94a3b8;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:inherit;transition:all 0.2s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='#fff'"
                    onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                    Lanjut Belajar
                </button>
                <a href="{{ route('courses.show', $chapter->course) }}"
                    style="padding:0.6rem 1.4rem;border-radius:10px;background:rgba(248,113,113,0.15);border:1px solid rgba(248,113,113,0.35);color:#f87171;font-size:0.85rem;font-weight:700;text-decoration:none;transition:all 0.2s;display:inline-flex;align-items:center;gap:0.4rem;"
                    onmouseover="this.style.background='rgba(248,113,113,0.25)'"
                    onmouseout="this.style.background='rgba(248,113,113,0.15)'">
                    Ya, Keluar
                </a>
            </div>
        </div>
    </div>

    <!-- Top Bar -->
    <div class="topbar">
        <div class="topbar-left">
            <button class="btn-sidebar" onclick="toggleSidebar()" title="Toggle Sidebar">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <a href="{{ route('dashboard') }}" class="topbar-logo">PRIMM<span>BOT</span></a>
        </div>
        <div class="topbar-right">
            <button onclick="openExitModal()" class="btn-exit-kelas">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Keluar
            </button>
        </div>
    </div>

    <!-- Overlay Mobile -->
    <div id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:35;"
        onclick="document.getElementById('sidebar').classList.remove('open');this.style.display='none';"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <p class="sidebar-chapter-label">Chapter {{ $chapter->order }}</p>
            <h2 class="sidebar-chapter-title">{{ $chapter->title }}</h2>
        </div>

        {{-- Pendahuluan --}}
        <div class="sidebar-section">
            <div class="sidebar-section-title" onclick="toggleSection(this)">
                Pendahuluan
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <div class="sidebar-items">
                @php
                    $matLabels = [
                        'pendahuluan' => 'Pendahuluan Kelas',
                        'petunjuk_belajar' => 'Petunjuk Belajar',
                        'tujuan' => 'Tujuan Pembelajaran',
                        'prasyarat' => 'Prasyarat Tools',
                    ];
                @endphp
                @foreach ($chapter->lessonMaterials->whereIn('type', ['pendahuluan', 'petunjuk_belajar', 'tujuan', 'prasyarat'])->sortBy('order') as $mat)
                    <a href="{{ route('learning.material', [$chapter, $mat->type]) }}"
                        class="sidebar-item {{ request()->is('*materi/' . $mat->type) ? 'active' : '' }}">
                        {{ $matLabels[$mat->type] ?? ucfirst($mat->type) }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Ringkasan Materi --}}
        <div class="sidebar-section">
            <div class="sidebar-section-title" onclick="toggleSection(this)">
                Ringkasan Materi
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <div class="sidebar-items">
                @php
                    $ringkasanMat = $chapter->lessonMaterials->where('type', 'ringkasan_materi')->first();
                    $ringkasanCompleted = $ringkasanMat && in_array($ringkasanMat->id, $completedMaterialIds ?? []);
                @endphp
                <a href="{{ route('learning.summary', $chapter) }}"
                    class="sidebar-item {{ request()->routeIs('learning.summary') ? 'active' : '' }}">
                    Ringkasan Materi
                </a>
            </div>
        </div>

        {{-- Kegiatan Inti --}}
        <div class="sidebar-section">
            <div class="sidebar-section-title" onclick="toggleSection(this)">
                Kegiatan Inti
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <div class="sidebar-items">
                @php
                    $stages = [
                        'predict' => 'Predict',
                        'run' => 'Run',
                        'investigate' => 'Investigate',
                        'modify' => 'Modify',
                        'make' => 'Make',
                    ];
                @endphp
                @foreach ($stages as $stageKey => $stageLabel)
                    @php
                        $stageActivities = $chapter->activities->where('stage', $stageKey);
                        $firstActivity = $stageActivities->first();
                        $isCompleted =
                            $stageActivities->isNotEmpty() &&
                            $stageActivities->every(fn($a) => in_array($a->id, $completedActivityIds ?? []));
                        $isActive = isset($activity) && $activity->stage === $stageKey;
                    @endphp
                    @if ($firstActivity)
                        <a href="{{ route('learning.activity', [$chapter, $firstActivity]) }}"
                            class="sidebar-item {{ $isActive ? 'active' : '' }}">
                            @if ($isCompleted)
                                <span class="sidebar-check done"><svg width="10" height="10" fill="#4ade80"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg></span>
                            @else
                                <span class="sidebar-check pending"></span>
                            @endif
                            {{ $stageLabel }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main" id="mainContent">
        <div class="main-inner">
            @yield('content')

            @hasSection('nav_prev')
                <div class="nav-bottom">
                    @yield('nav_prev')
                    @yield('nav_next')
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const main = document.getElementById('mainContent');
            const overlay = document.getElementById('sidebarOverlay');
            const isMobile = window.innerWidth <= 768;

            if (isMobile) {
                const isOpen = sidebar.classList.toggle('open');
                sidebar.classList.remove('closed');
                overlay.style.display = isOpen ? 'block' : 'none';
            } else {
                sidebar.classList.toggle('closed');
                sidebar.classList.remove('open');
                main.classList.toggle('expanded');
                overlay.style.display = 'none';
                localStorage.setItem('sidebarClosed', sidebar.classList.contains('closed'));
            }
        }

        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                const sidebarBtn = document.querySelector('.btn-sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                if (sidebar.classList.contains('open') && !sidebar.contains(e.target) && !sidebarBtn.contains(e
                        .target)) {
                    sidebar.classList.remove('open');
                    overlay.style.display = 'none';
                }
            }
        });

        function openExitModal() {
            document.getElementById('exitModal').style.display = 'flex';
        }
        function closeExitModal() {
            document.getElementById('exitModal').style.display = 'none';
        }
        document.getElementById('exitModal').addEventListener('click', function(e) {
            if (e.target === this) closeExitModal();
        });

        function toggleSection(el) {
            el.classList.toggle('collapsed');
            el.nextElementSibling.classList.toggle('hidden');
        }

        if (localStorage.getItem('sidebarClosed') === 'true') {
            document.getElementById('sidebar').classList.add('closed');
            document.getElementById('mainContent').classList.add('expanded');
        }
    </script>

    @stack('scripts')
</body>

</html>
