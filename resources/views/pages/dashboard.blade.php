<x-layouts.app title="Dashboard - PRIMMBOT">

    <div class="page-header fade-up">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Selamat datang, {{ Auth::user()->profile->full_name ?? Auth::user()->username }}!</p>
    </div>

    <div class="grid-3">
        {{-- Card Kelas --}}
        <div class="dash-card fade-up fade-up-d1"
            style="background:linear-gradient(135deg, #1e3a5f, #1a2d4a);border:1px solid rgba(96,165,250,0.15);border-radius:16px;overflow:hidden;display:flex;flex-direction:column;">
            <div style="flex:1;display:flex;align-items:center;justify-content:center;padding:2rem 1.5rem;">
                <img src="{{ asset('assets/images/kelas.png') }}" alt="Kelas"
                    style="max-height:160px;max-width:100%;object-fit:contain;"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div
                    style="display:none;width:100%;height:160px;align-items:center;justify-content:center;color:#475569;font-size:0.8rem;border:1px dashed rgba(255,255,255,0.1);border-radius:10px;">
                    Taruh kelas.png di<br>public/assets/images/
                </div>
            </div>
            <div style="padding:1.2rem 1.5rem 1.5rem;border-top:1px solid rgba(255,255,255,0.06);">
                <h3 style="font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:0.3rem;">Kelas</h3>
                <p style="font-size:0.8rem;color:#94a3b8;margin-bottom:1.2rem;line-height:1.5;">Mulai belajar disini</p>
                <a href="{{ route('courses.index') }}"
                    style="display:block;text-align:center;padding:0.65rem;border-radius:10px;background:linear-gradient(135deg,#2563eb,#4f46e5);color:#fff;font-size:0.85rem;font-weight:600;text-decoration:none;transition:all 0.25s;box-shadow:0 4px 16px rgba(37,99,235,0.3);"
                    onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 24px rgba(37,99,235,0.4)'"
                    onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 16px rgba(37,99,235,0.3)'">
                    Mulai Belajar →
                </a>
            </div>
        </div>

        {{-- Card Hasil Belajar --}}
        <div class="dash-card fade-up fade-up-d2"
            style="background:linear-gradient(135deg, #1a3a2a, #162d26);border:1px solid rgba(74,222,128,0.12);border-radius:16px;overflow:hidden;display:flex;flex-direction:column;opacity:0.6;">
            <div style="flex:1;display:flex;align-items:center;justify-content:center;padding:2rem 1.5rem;">
                <img src="{{ asset('assets/images/hasil-belajar.png') }}" alt="Hasil Belajar"
                    style="max-height:160px;max-width:100%;object-fit:contain;"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div
                    style="display:none;width:100%;height:160px;align-items:center;justify-content:center;color:#475569;font-size:0.8rem;border:1px dashed rgba(255,255,255,0.1);border-radius:10px;">
                    Taruh hasil-belajar.png di<br>public/assets/images/
                </div>
            </div>
            <div style="padding:1.2rem 1.5rem 1.5rem;border-top:1px solid rgba(255,255,255,0.06);">
                <h3 style="font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:0.3rem;">Hasil Belajar</h3>
                <p style="font-size:0.8rem;color:#94a3b8;margin-bottom:1.2rem;line-height:1.5;">Lihat hasil
                    pembelajaran kamu</p>
                <div
                    style="display:block;text-align:center;padding:0.65rem;border-radius:10px;border:1px solid rgba(255,255,255,0.1);color:#64748b;font-size:0.85rem;font-weight:600;">
                    Segera Hadir
                </div>
            </div>
        </div>

        {{-- Card Pengumuman --}}
        <div class="dash-card fade-up fade-up-d3"
            style="background:linear-gradient(135deg, #3a2a1a, #2d2416);border:1px solid rgba(250,204,21,0.12);border-radius:16px;overflow:hidden;display:flex;flex-direction:column;opacity:0.6;">
            <div style="flex:1;display:flex;align-items:center;justify-content:center;padding:2rem 1.5rem;">
                <img src="{{ asset('assets/images/pengumuman.png') }}" alt="Pengumuman"
                    style="max-height:160px;max-width:100%;object-fit:contain;"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div
                    style="display:none;width:100%;height:160px;align-items:center;justify-content:center;color:#475569;font-size:0.8rem;border:1px dashed rgba(255,255,255,0.1);border-radius:10px;">
                    Taruh pengumuman.png di<br>public/assets/images/
                </div>
            </div>
            <div style="padding:1.2rem 1.5rem 1.5rem;border-top:1px solid rgba(255,255,255,0.06);">
                <h3 style="font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:0.3rem;">Pengumuman</h3>
                <p style="font-size:0.8rem;color:#94a3b8;margin-bottom:1.2rem;line-height:1.5;">Informasi terbaru dari
                    guru</p>
                <div
                    style="display:block;text-align:center;padding:0.65rem;border-radius:10px;border:1px solid rgba(255,255,255,0.1);color:#64748b;font-size:0.85rem;font-weight:600;">
                    Segera Hadir
                </div>
            </div>
        </div>
    </div>

    <style>
        .dash-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .dash-card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .dash-card:active {
            transform: translateY(-2px) scale(0.98);
        }
    </style>

</x-layouts.app>
