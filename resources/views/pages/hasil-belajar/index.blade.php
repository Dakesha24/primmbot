<x-layouts.app title="Hasil Belajar - PRIMMBOT">

    <style>
        .hb-header { margin-bottom: 2rem; }
        .hb-header h1 { font-size: 1.6rem; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
        .hb-header p  { font-size: 0.85rem; color: #64748b; margin-top: 0.3rem; }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.2rem;
        }

        .course-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            transition: all 0.25s;
        }
        .course-card:hover {
            background: rgba(255,255,255,0.06);
            border-color: rgba(255,255,255,0.14);
            transform: translateY(-3px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.25);
        }

        .course-title { font-size: 1rem; font-weight: 700; color: #fff; }
        .course-sub   { font-size: 0.75rem; color: #64748b; margin-top: 2px; }

        .badge-kelas {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 0.7rem; font-weight: 700; padding: 2px 8px;
            border-radius: 20px; margin-top: 4px;
        }
        .badge-spesifik { background: rgba(59,91,219,0.2); color: #748ffc; }
        .badge-umum     { background: rgba(255,255,255,0.08); color: #64748b; }

        /* Progress */
        .prog-section {}
        .prog-label {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 0.72rem; font-weight: 700; color: #64748b;
            text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px;
        }
        .prog-label span:last-child { color: #94a3b8; font-weight: 600; font-size: 0.75rem; }
        .prog-track {
            height: 7px; background: rgba(255,255,255,0.08);
            border-radius: 4px; overflow: hidden;
        }
        .prog-fill {
            height: 100%; border-radius: 4px;
            background: linear-gradient(90deg, #2563eb, #4f46e5);
            transition: width 0.6s ease;
        }

        /* Chapter mini bars */
        .ch-list  { display: flex; flex-direction: column; gap: 7px; }
        .ch-row   { display: flex; flex-direction: column; gap: 3px; }
        .ch-top   { display: flex; align-items: center; justify-content: space-between; }
        .ch-label { font-size: 0.72rem; color: #94a3b8; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 70%; }
        .ch-pct   { font-size: 0.68rem; color: #64748b; font-weight: 600; flex-shrink: 0; }
        .ch-track { height: 4px; background: rgba(255,255,255,0.08); border-radius: 3px; overflow: hidden; }
        .ch-bar   { height: 100%; border-radius: 3px; background: linear-gradient(90deg, #2563eb, #4f46e5); }

        /* Score */
        .score-box {
            display: inline-flex; align-items: center; gap: 5px;
            background: rgba(255,255,255,0.06); color: #94a3b8;
            font-size: 0.75rem; font-weight: 700;
            padding: 3px 10px; border-radius: 6px;
        }

        .btn-detail {
            display: block; text-align: center;
            padding: 0.6rem; border-radius: 10px;
            background: linear-gradient(135deg, var(--blue-600), #4f46e5);
            color: #fff; font-size: 0.82rem; font-weight: 600;
            text-decoration: none; transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(37,99,235,0.25);
        }
        .btn-detail:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37,99,235,0.35);
        }

        .empty-state {
            text-align: center; padding: 4rem 2rem;
            color: #475569;
        }
        .empty-state p { font-size: 0.9rem; margin-top: 0.5rem; }
    </style>

    <div class="hb-header fade-up">
        <h1>Hasil Belajar</h1>
        <p>Ringkasan progress kamu di setiap course yang diikuti.</p>
    </div>

    @if ($courses->isEmpty())
        <div class="empty-state fade-up">
            <svg width="48" height="48" fill="none" stroke="#475569" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 0.75rem;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p>Kamu belum terdaftar di course manapun.</p>
            <a href="{{ route('courses.index') }}" style="display:inline-block;margin-top:1rem;padding:0.6rem 1.4rem;background:rgba(37,99,235,0.15);color:#60a5fa;border-radius:8px;font-size:0.85rem;font-weight:600;text-decoration:none;">Lihat Kelas →</a>
        </div>
    @else
        <div class="course-grid">
            @foreach ($courses as $course)
                <div class="course-card fade-up" style="animation-delay: {{ $loop->index * 0.05 }}s">
                    <div>
                        <div class="course-title">{{ $course->title }}</div>
                        @if ($course->kelas)
                            <span class="badge-kelas badge-spesifik">📌 {{ $course->kelas->name }} — {{ $course->kelas->school->name }}</span>
                        @else
                            <span class="badge-kelas badge-umum">🌐 Umum</span>
                        @endif
                    </div>

                    {{-- Overall progress --}}
                    <div class="prog-section">
                        <div class="prog-label">
                            <span>Progress</span>
                            <span>{{ $course->progress_percent }}%</span>
                        </div>
                        <div class="prog-track">
                            <div class="prog-fill" style="width:{{ $course->progress_percent }}%"></div>
                        </div>
                    </div>

                    {{-- Per chapter --}}
                    @if ($course->chapter_stats->isNotEmpty())
                        <div class="ch-list">
                            @foreach ($course->chapter_stats as $ch)
                                <div class="ch-row">
                                    <div class="ch-top">
                                        <span class="ch-label">{{ $ch['title'] }}</span>
                                        <span class="ch-pct">{{ $ch['percent'] }}%</span>
                                    </div>
                                    <div class="ch-track">
                                        <div class="ch-bar" style="width:{{ $ch['percent'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Score --}}
                    <div>
                        <span class="score-box">Rata-rata Skor: {{ $course->avg_score ?? '-' }}</span>
                    </div>

                    <a href="{{ route('hasil-belajar.show', $course) }}" class="btn-detail">Lihat Detail →</a>
                </div>
            @endforeach
        </div>
    @endif

</x-layouts.app>
