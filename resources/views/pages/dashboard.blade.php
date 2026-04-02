<x-layouts.app title="Dashboard - PRIMMBASE">

    <style>
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.2rem;
        }
        @media (max-width: 768px) {
            .grid-2 { grid-template-columns: 1fr; }
        }

        .dash-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.25s ease;
        }
        .dash-card:hover {
            background: rgba(255,255,255,0.06);
            border-color: rgba(255,255,255,0.13);
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.25);
        }

        .dash-card-img {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .dash-card-img-placeholder {
            display: none;
            width: 100%;
            height: 160px;
            align-items: center;
            justify-content: center;
            color: #475569;
            font-size: 0.8rem;
            border: 1px dashed rgba(255,255,255,0.1);
            border-radius: 10px;
            text-align: center;
            line-height: 1.6;
        }

        .dash-card-body { padding: 1.2rem 1.5rem 1.5rem; }
        .dash-card-title { font-size: 1.05rem; font-weight: 700; color: #fff; margin-bottom: 0.3rem; }
        .dash-card-desc  { font-size: 0.8rem; color: #64748b; margin-bottom: 1.2rem; line-height: 1.5; }

        .dash-btn {
            display: block; text-align: center;
            padding: 0.65rem; border-radius: 10px;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: #fff; font-size: 0.85rem; font-weight: 600;
            text-decoration: none; transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(37,99,235,0.25);
        }
        .dash-btn:hover {
            opacity: 0.88;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37,99,235,0.35);
        }
        .dash-btn-alt {
            display: block; text-align: center;
            padding: 0.65rem; border-radius: 10px;
            background: linear-gradient(135deg, #0891b2, #0e7490);
            color: #fff; font-size: 0.85rem; font-weight: 600;
            text-decoration: none; transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(8,145,178,0.25);
        }
        .dash-btn-alt:hover {
            opacity: 0.88;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(8,145,178,0.35);
        }
    </style>

    <div class="page-header fade-up">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Selamat datang, {{ Auth::user()->profile->full_name ?? Auth::user()->username }}!</p>
    </div>

    <div class="grid-2">
        {{-- Card LKPD --}}
        <div class="dash-card fade-up fade-up-d1">
            <div class="dash-card-img">
                <img src="{{ asset('assets/images/kelas.png') }}" alt="LKPD"
                    style="max-height:160px;max-width:100%;object-fit:contain;"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="dash-card-img-placeholder"><br>public/assets/images/</div>
            </div>
            <div class="dash-card-body">
                <div class="dash-card-title">LKPD</div>
                <p class="dash-card-desc">Mulai belajar disini</p>
                <a href="{{ route('courses.index') }}" class="dash-btn">Mulai Belajar →</a>
            </div>
        </div>

        {{-- Card Hasil Belajar --}}
        <div class="dash-card fade-up fade-up-d2">
            <div class="dash-card-img">
                <img src="{{ asset('assets/images/hasil-belajar.png') }}" alt="Hasil Belajar"
                    style="max-height:160px;max-width:100%;object-fit:contain;"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="dash-card-img-placeholder">Taruh hasil-belajar.png di<br>public/assets/images/</div>
            </div>
            <div class="dash-card-body">
                <div class="dash-card-title">Hasil Belajar</div>
                <p class="dash-card-desc">Lihat hasil pembelajaran kamu</p>
                <a href="{{ route('hasil-belajar.index') }}" class="dash-btn-alt">Lihat Hasil →</a>
            </div>
        </div>
    </div>

</x-layouts.app>
