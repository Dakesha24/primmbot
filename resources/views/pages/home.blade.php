<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PRIMMBOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --blue-950: #0a1628;
            --blue-900: #0f2044;
            --blue-800: #142c5c;
            --blue-600: #2563eb;
            --blue-400: #60a5fa;
            --blue-300: #93c5fd;
            --cyan-400: #22d3ee;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--blue-950);
            color: #e2e8f0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* === Ambient glow === */
        .glow-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.15;
            pointer-events: none;
            z-index: 0;
        }
        .glow-1 { width: 500px; height: 500px; background: var(--blue-600); top: -10%; right: -5%; }
        .glow-2 { width: 400px; height: 400px; background: var(--cyan-400); bottom: 10%; left: -8%; }

        /* === Navbar === */
        nav {
            position: fixed; top: 0; width: 100%; z-index: 50;
            padding: 1.2rem 2.5rem;
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(10, 22, 40, 0.7);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .logo {
            font-weight: 800; font-size: 1.3rem; color: #fff;
            letter-spacing: -0.5px;
        }
        .logo span { color: var(--cyan-400); }
        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a {
            color: #94a3b8; text-decoration: none; font-size: 0.875rem;
            font-weight: 500; transition: color 0.2s;
        }
        .nav-links a:hover { color: #fff; }

        /* === Hero === */
        .hero {
            position: relative; z-index: 1;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 7rem 2.5rem 4rem;
            max-width: 1200px; margin: 0 auto;
            gap: 4rem;
        }
        .hero-text { flex: 1; max-width: 540px; }
        .hero-text .badge {
            display: inline-block;
            padding: 0.35rem 1rem;
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(34, 211, 238, 0.1);
            color: var(--cyan-400);
            border: 1px solid rgba(34, 211, 238, 0.2);
            margin-bottom: 1.5rem;
            letter-spacing: 0.5px;
        }
        .hero-text h1 {
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1.1;
            color: #fff;
            letter-spacing: -1px;
            margin-bottom: 1.2rem;
        }
        .hero-text h1 .gradient {
            background: linear-gradient(135deg, var(--blue-400), var(--cyan-400));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-text p {
            font-size: 1.05rem;
            line-height: 1.7;
            color: #94a3b8;
            margin-bottom: 2rem;
        }
        .hero-buttons { display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .btn {
            padding: 0.75rem 1.8rem;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--blue-600), #4f46e5);
            color: #fff;
            box-shadow: 0 4px 24px rgba(37, 99, 235, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(37, 99, 235, 0.45);
        }
        .btn-outline {
            background: transparent;
            color: #cbd5e1;
            border: 1px solid rgba(255,255,255,0.15);
        }
        .btn-outline:hover {
            background: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.3);
            color: #fff;
        }

        /* === Hero Image === */
        .hero-visual {
            flex: 1;
            max-width: 480px;
            position: relative;
        }
        .hero-visual img {
            width: 100%;
            border-radius: 16px;
            position: relative;
            z-index: 2;
        }
        .hero-visual .img-glow {
            position: absolute;
            inset: -20px;
            background: linear-gradient(135deg, rgba(37,99,235,0.2), rgba(34,211,238,0.15));
            border-radius: 24px;
            filter: blur(40px);
            z-index: 1;
        }

        /* === Features === */
        .features {
            position: relative; z-index: 1;
            max-width: 1200px; margin: 0 auto;
            padding: 2rem 2.5rem 5rem;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        .feature-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 14px;
            padding: 2rem;
            transition: all 0.3s;
        }
        .feature-card:hover {
            background: rgba(255,255,255,0.06);
            border-color: rgba(255,255,255,0.12);
            transform: translateY(-4px);
        }
        .feature-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        .fi-blue { background: rgba(37,99,235,0.15); color: var(--blue-400); }
        .fi-cyan { background: rgba(34,211,238,0.15); color: var(--cyan-400); }
        .fi-purple { background: rgba(168,85,247,0.15); color: #c084fc; }
        .feature-card h3 {
            font-size: 1rem; font-weight: 700; color: #fff;
            margin-bottom: 0.5rem;
        }
        .feature-card p {
            font-size: 0.875rem; line-height: 1.6; color: #64748b;
        }

        /* === Footer === */
        footer {
            position: relative; z-index: 1;
            text-align: center;
            padding: 2rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            color: #475569;
            font-size: 0.8rem;
        }

        /* === Responsive === */
        @media (max-width: 768px) {
            .hero { flex-direction: column; text-align: center; padding-top: 8rem; gap: 2.5rem; }
            .hero-text h1 { font-size: 2.2rem; }
            .hero-buttons { justify-content: center; }
            .hero-visual { max-width: 320px; }
            .features { grid-template-columns: 1fr; }
            .nav-links { gap: 1rem; }
        }

        /* === Fade-in animation === */
        .fade-up {
            opacity: 0; transform: translateY(20px);
            animation: fadeUp 0.6s ease forwards;
        }
        .fade-up-d1 { animation-delay: 0.1s; }
        .fade-up-d2 { animation-delay: 0.2s; }
        .fade-up-d3 { animation-delay: 0.3s; }
        .fade-up-d4 { animation-delay: 0.4s; }
        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <!-- Ambient Glow -->
    <div class="glow-orb glow-1"></div>
    <div class="glow-orb glow-2"></div>

    <!-- Navbar -->
    <nav>
        <div class="logo">PRIMM<span>BOT</span></div>
        <div class="nav-links">
            <a href="#features">Tentang</a>
            <a href="#faq">FAQ</a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary" style="padding: 0.5rem 1.2rem; font-size: 0.8rem;">Dashboard</a>
            @else
                <a href="{{ route('login') }}">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 0.5rem 1.2rem; font-size: 0.8rem;">Daftar</a>
            @endauth
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="hero-text">
            <div class="badge fade-up">✦ Platform e-LKPD Interaktif</div>
            <h1 class="fade-up fade-up-d1">
                Belajar SQL dengan<br>
                <span class="gradient">AI Assistant</span>
            </h1>
            <p class="fade-up fade-up-d2">
                Asah kemampuan logical thinking kamu di materi Basis Data lewat model pembelajaran PRIMM, ditemani Virtual Assistant yang siap membantu kapan saja!
            </p>
            <div class="hero-buttons fade-up fade-up-d3">
                <a href="{{ route('register') }}" class="btn btn-primary">Mulai Belajar</a>
                <a href="#features" class="btn btn-outline">Pelajari Lebih</a>
            </div>
        </div>

        <div class="hero-visual fade-up fade-up-d4">
            <div class="img-glow"></div>
            {{-- Ganti src dengan gambar Anda --}}
            <img src="{{ asset('assets/images/hero.png') }}" alt="PRIMMBOT Hero"
                 onerror="this.style.display='none'; this.parentElement.querySelector('.img-placeholder').style.display='flex';">
            <div class="img-placeholder" style="display:none; width:100%; aspect-ratio:4/3; background:rgba(255,255,255,0.03); border:1px dashed rgba(255,255,255,0.15); border-radius:16px; align-items:center; justify-content:center; color:#475569; font-size:0.9rem; position:relative; z-index:2;">
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features" id="features">
        <div class="feature-card fade-up fade-up-d1">
            <div class="feature-icon fi-blue">🧠</div>
            <h3>Model PRIMM</h3>
            <p>Belajar bertahap melalui Predict, Run, Investigate, Modified, dan Make untuk membangun pemahaman SQL yang mendalam.</p>
        </div>
        <div class="feature-card fade-up fade-up-d2">
            <div class="feature-icon fi-cyan">🤖</div>
            <h3>AI Virtual Assistant</h3>
            <p>Asisten virtual cerdas yang membimbing tanpa memberikan jawaban langsung — mendorongmu berpikir kritis dan mandiri.</p>
        </div>
        <div class="feature-card fade-up fade-up-d3">
            <div class="feature-icon fi-purple">⚡</div>
            <h3>SQL Editor Interaktif</h3>
            <p>Tulis dan jalankan query SQL secara langsung di browser. Lihat hasilnya real-time dan dapatkan feedback instan.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; {{ date('Y') }} PRIMMBOT — Platform e-LKPD untuk Logical Thinking</p>
    </footer>

</body>
</html>