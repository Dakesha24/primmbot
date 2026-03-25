<x-layouts.admin title="Hasil LKPD">
    <x-slot:styles>
        <style>
            .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; }
            .page-header h1 { font-size: 20px; font-weight: 800; color: #0f1b3d; margin-bottom: 4px; }
            .page-header p  { font-size: 13px; color: #64748b; }

            .course-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
                gap: 20px;
            }

            .course-card {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; overflow: hidden;
                display: flex; flex-direction: column;
                transition: box-shadow 0.15s, border-color 0.15s;
            }
            .course-card:hover { border-color: #b0bbcf; box-shadow: 3px 3px 0 #aab3c6; }

            .card-accent { height: 3px; background: linear-gradient(90deg, #0f1b3d 0%, #3b5bdb 100%); }

            .card-body { padding: 20px 22px; flex: 1; display: flex; flex-direction: column; gap: 16px; }

            .card-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
            .card-head-left { flex: 1; min-width: 0; }
            .card-title { font-size: 15px; font-weight: 800; color: #0f1b3d; margin-bottom: 5px; line-height: 1.3; }

            .badge-kelas {
                display: inline-flex; align-items: center; gap: 4px;
                font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 4px;
            }
            .badge-umum     { background: #f1f5f9; color: #64748b; }
            .badge-spesifik { background: #eef2ff; color: #3b5bdb; }

            .enrolled-stat { text-align: right; flex-shrink: 0; }
            .enrolled-num  { font-size: 26px; font-weight: 800; color: #0f1b3d; line-height: 1; }
            .enrolled-lbl  { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; }

            /* Overall progress */
            .overall-section {}
            .prog-header {
                display: flex; justify-content: space-between; align-items: center;
                margin-bottom: 6px;
            }
            .prog-label { font-size: 10.5px; font-weight: 700; color: #6b7a99; text-transform: uppercase; letter-spacing: 0.05em; }
            .prog-pct   { font-size: 13px; font-weight: 800; color: #0f1b3d; }
            .prog-track {
                height: 8px; background: #f0f2f7; border-radius: 4px; overflow: hidden;
            }
            .prog-fill {
                height: 100%; border-radius: 4px;
                background: linear-gradient(90deg, #3b5bdb, #748ffc);
                transition: width 0.5s ease;
            }

            /* Per-chapter mini bars */
            .ch-section {}
            .ch-section-label {
                font-size: 10.5px; font-weight: 700; color: #94a3b8;
                text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;
            }
            .ch-list { display: flex; flex-direction: column; gap: 7px; }
            .ch-row  { display: flex; flex-direction: column; gap: 3px; }
            .ch-top  { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
            .ch-name { font-size: 11.5px; color: #475569; font-weight: 500;
                white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 78%; }
            .ch-pct  { font-size: 11px; font-weight: 700; color: #6b7a99; flex-shrink: 0; }
            .ch-track { height: 4px; background: #f0f2f7; border-radius: 3px; overflow: hidden; }
            .ch-bar   { height: 100%; border-radius: 3px; background: linear-gradient(90deg, #3b5bdb, #748ffc); }

            /* Footer score + action */
            .card-footer-section { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-top: auto; }
            .avg-score-chip {
                display: inline-flex; align-items: center; gap: 5px;
                font-size: 11.5px; font-weight: 700; color: #6b7a99;
                background: #f0f2f7; padding: 4px 10px; border-radius: 4px;
            }

            .btn-lihat {
                display: inline-flex; align-items: center; gap: 5px;
                padding: 8px 16px; border-radius: 5px;
                background: #0f1b3d; color: #fff;
                font-size: 12px; font-weight: 700; text-decoration: none;
                transition: background 0.15s;
            }
            .btn-lihat:hover { background: #1a2d5a; }
            .btn-lihat-disabled {
                display: inline-flex; align-items: center;
                padding: 8px 16px; border-radius: 5px;
                background: #f1f5f9; font-size: 12px; font-weight: 600; color: #94a3b8;
            }

            .empty-state {
                grid-column: 1 / -1; background: #fff; border: 2px dashed #d5dbe8;
                border-radius: 6px; padding: 60px 40px; text-align: center;
            }
            .empty-state svg { margin-bottom: 14px; color: #c7d0e2; }
            .empty-state p { font-size: 13px; color: #94a3b8; }
        </style>
    </x-slot:styles>

    <div class="page-header">
        <div>
            <h1>Hasil LKPD</h1>
            <p>Rata-rata progress PRIMM per LKPD. Klik untuk melihat detail per siswa.</p>
        </div>
    </div>

    <div class="course-grid">
        @forelse ($courses as $course)
            <div class="course-card">
                <div class="card-accent"></div>
                <div class="card-body">

                    {{-- Header --}}
                    <div class="card-head">
                        <div class="card-head-left">
                            <div class="card-title">{{ $course->title }}</div>
                            @if ($course->kelas)
                                <span class="badge-kelas badge-spesifik">
                                    {{ $course->kelas->school->name }} · {{ $course->kelas->name }}
                                </span>
                            @else
                                <span class="badge-kelas badge-umum">Umum</span>
                            @endif
                        </div>
                        <div class="enrolled-stat">
                            <div class="enrolled-num">{{ $course->enrolled_count }}</div>
                            <div class="enrolled-lbl">Siswa</div>
                        </div>
                    </div>

                    {{-- Overall Progress --}}
                    <div class="overall-section">
                        <div class="prog-header">
                            <span class="prog-label">Rata-rata Progress</span>
                            <span class="prog-pct">{{ $course->avg_progress }}%</span>
                        </div>
                        <div class="prog-track">
                            <div class="prog-fill" style="width:{{ $course->avg_progress }}%"></div>
                        </div>
                    </div>

                    {{-- Per Chapter --}}
                    @if ($course->chapter_stats->isNotEmpty())
                        <div class="ch-section">
                            <div class="ch-section-label">Progress per Chapter</div>
                            <div class="ch-list">
                                @foreach ($course->chapter_stats as $ch)
                                    <div class="ch-row">
                                        <div class="ch-top">
                                            <span class="ch-name">{{ $ch['title'] }}</span>
                                            <span class="ch-pct">{{ $ch['avg_percent'] }}%</span>
                                        </div>
                                        <div class="ch-track">
                                            <div class="ch-bar" style="width:{{ $ch['avg_percent'] }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Score + Action --}}
                    <div class="card-footer-section">
                        <span class="avg-score-chip">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            Skor: {{ $course->avg_score ?? '—' }}
                        </span>
                        @if ($course->enrolled_count > 0)
                            <a href="{{ route('admin.hasil-kelas.show', $course) }}" class="btn-lihat">
                                Lihat Siswa →
                            </a>
                        @else
                            <span class="btn-lihat-disabled">Belum ada siswa</span>
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div class="empty-state">
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p>Belum ada LKPD yang dibuat.</p>
            </div>
        @endforelse
    </div>

</x-layouts.admin>
