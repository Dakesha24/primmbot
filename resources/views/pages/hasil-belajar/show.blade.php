<x-layouts.app title="Hasil Belajar — {{ $course->title }}">

    <style>
        .back-link {
            display: inline-flex; align-items: center; gap: 6px;
            color: #64748b; font-size: 0.8rem; font-weight: 600;
            text-decoration: none; margin-bottom: 1.5rem;
        }
        .back-link:hover { color: #94a3b8; }

        /* Header */
        .course-header {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 1.8rem 2rem;
            margin-bottom: 1.5rem;
        }
        .course-header h1 { font-size: 1.3rem; font-weight: 800; color: #fff; }
        .course-sub { font-size: 0.78rem; color: #64748b; margin-top: 3px; }

        /* Summary stats */
        .stats-row { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 1.4rem; }
        .stat-box {
            background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px; padding: 12px 20px;
        }
        .stat-label { font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-value { font-size: 1.3rem; font-weight: 800; color: #fff; margin-top: 1px; }

        /* Overall progress bar */
        .overall-prog { margin-top: 1.5rem; }
        .overall-prog-top {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 6px;
        }
        .overall-prog-label { font-size: 0.78rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
        .overall-prog-pct   { font-size: 0.78rem; font-weight: 700; color: #94a3b8; }
        .overall-prog-track {
            height: 10px; background: rgba(255,255,255,0.08); border-radius: 99px; overflow: hidden;
        }
        .overall-prog-fill {
            height: 100%; border-radius: 99px;
            background: linear-gradient(90deg, #2563eb, #4f46e5);
            transition: width 0.8s ease;
        }

        /* Chapter overview */
        .chapter-overview {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 1.2rem 1.5rem;
            margin-bottom: 1.5rem;
        }
        .chapter-overview-title {
            font-size: 0.72rem; font-weight: 700; color: #64748b;
            text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.9rem;
        }
        .ch-list { display: flex; flex-direction: column; gap: 10px; }
        .ch-row  { display: flex; flex-direction: column; gap: 4px; }
        .ch-row-top { display: flex; align-items: center; justify-content: space-between; }
        .ch-row-label { font-size: 0.78rem; font-weight: 600; color: #e2e8f0; }
        .ch-row-pct   { font-size: 0.72rem; font-weight: 700; color: #94a3b8; }
        .ch-track { height: 6px; background: rgba(255,255,255,0.07); border-radius: 3px; overflow: hidden; }
        .ch-bar   { height: 100%; border-radius: 3px; background: linear-gradient(90deg, #2563eb, #4f46e5); transition: width 0.6s ease; }

        /* Stage colors — distinct per tahap */
        .c-predict     { color: #60a5fa; } .b-predict     { background: #2563eb; }
        .c-run         { color: #2dd4bf; } .b-run         { background: #0d9488; }
        .c-investigate { color: #fbbf24; } .b-investigate { background: #d97706; }
        .c-modify    { color: #a78bfa; } .b-modify    { background: #7c3aed; }
        .c-make        { color: #fb923c; } .b-make        { background: #ea580c; }

        /* Chapter */
        .chapter-section {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 14px;
            margin-bottom: 1.2rem;
            overflow: hidden;
        }
        .chapter-header {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 18px;
            background: rgba(255,255,255,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .chapter-num {
            width: 24px; height: 24px; border-radius: 50%;
            background: rgba(37,99,235,0.3); color: #93c5fd;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; flex-shrink: 0;
        }
        .chapter-title { font-size: 0.9rem; font-weight: 700; color: #e2e8f0; }

        /* Stage group */
        .stage-group { border-bottom: 1px solid rgba(255,255,255,0.05); }
        .stage-group:last-child { border-bottom: none; }
        .stage-group-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 9px 18px;
            background: rgba(255,255,255,0.04);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .stage-group-name {
            font-size: 0.82rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: 0.08em; color: #93c5fd;
        }
        .stage-group-count { font-size: 0.72rem; color: #475569; font-weight: 600; }

        /* Activity item */
        .activity-item {
            padding: 12px 18px 14px 22px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            border-left: 3px solid rgba(37,99,235,0.25);
            display: flex; flex-direction: column; gap: 8px;
        }
        .activity-item:last-child { border-bottom: none; }

        .act-meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .level-tag {
            font-size: 0.65rem; font-weight: 600; color: #64748b;
            background: rgba(255,255,255,0.06); padding: 1px 7px; border-radius: 4px;
        }
        .status-done  { font-size: 0.7rem; font-weight: 700; color: #86efac; }
        .status-wrong { font-size: 0.7rem; font-weight: 700; color: #94a3b8; }
        .status-empty { font-size: 0.7rem; color: #475569; }

        .score-badge {
            display: inline-flex; align-items: baseline; gap: 2px;
            font-size: 1.1rem; font-weight: 800; color: #fff;
            background: rgba(255,255,255,0.07);
            padding: 3px 10px; border-radius: 8px;
        }
        .score-badge-unit { font-size: 0.65rem; font-weight: 600; color: #64748b; }

        .question-preview {
            font-size: 0.82rem; color: #cbd5e1; line-height: 1.6;
            padding: 7px 10px;
            background: rgba(255,255,255,0.03);
            border-radius: 6px;
        }

        /* Submission */
        .sub-card {
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 8px; overflow: hidden;
        }
        .sub-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 7px 11px; font-size: 0.72rem; font-weight: 700;
        }
        .sub-correct   { background: rgba(255,255,255,0.04); color: #86efac; border-bottom: 1px solid rgba(255,255,255,0.07); }
        .sub-incorrect { background: rgba(255,255,255,0.03); color: #94a3b8;  border-bottom: 1px solid rgba(255,255,255,0.06); }
        .sub-body { padding: 9px 11px; display: flex; flex-direction: column; gap: 5px; }

        .ans-label { font-size: 0.65rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; }
        .ans-text  { font-size: 0.82rem; color: #e2e8f0; line-height: 1.6; white-space: pre-wrap; }
        .ans-code  {
            font-family: 'Courier New', monospace; font-size: 0.8rem; color: #93c5fd;
            background: rgba(255,255,255,0.05); padding: 9px 11px;
            border-radius: 5px; white-space: pre-wrap; line-height: 1.6;
        }
        .ai-box {
            padding: 9px 11px;
            background: transparent; border-radius: 6px;
            border: 1px solid rgba(96,165,250,0.2);
        }
        .ai-lbl { font-size: 0.62rem; font-weight: 800; color: #60a5fa; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px; }
        .ai-txt  { font-size: 0.82rem; color: #cbd5e1; line-height: 1.6; }

        .no-act-txt { font-size: 0.72rem; color: #374151; font-style: italic; padding: 10px 18px; }

        @media (max-width: 768px) {
            .stats-row { gap: 8px; }
            .stat-box  { padding: 8px 12px; }
        }
    </style>

    <div class="fade-up" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.75rem;">
        <a href="{{ route('hasil-belajar.index') }}" class="back-link" style="margin-bottom:0;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Hasil Belajar
        </a>
        <div style="display:flex; gap:0.5rem;">
            <a href="{{ route('hasil-belajar.excel', $course) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:0.45rem 1rem;border-radius:8px;background:rgba(21,128,61,0.15);border:1px solid rgba(21,128,61,0.3);color:#4ade80;font-size:0.78rem;font-weight:600;text-decoration:none;transition:all 0.2s;"
               onmouseover="this.style.background='rgba(21,128,61,0.25)'" onmouseout="this.style.background='rgba(21,128,61,0.15)'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Excel
            </a>
            <a href="{{ route('hasil-belajar.pdf', $course) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:0.45rem 1rem;border-radius:8px;background:rgba(37,99,235,0.15);border:1px solid rgba(37,99,235,0.3);color:#60a5fa;font-size:0.78rem;font-weight:600;text-decoration:none;transition:all 0.2s;"
               onmouseover="this.style.background='rgba(37,99,235,0.25)'" onmouseout="this.style.background='rgba(37,99,235,0.15)'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                PDF
            </a>
        </div>
    </div>

    {{-- Course header --}}
    <div class="course-header fade-up">
        <h1>{{ $course->title }}</h1>
        <div class="course-sub">
            @if ($course->kelas)
                {{ $course->kelas->school->name }} · {{ $course->kelas->name }}
            @else
                Course Umum
            @endif
        </div>

        @if ($stats['avg_score'] !== null)
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-label">Rata-rata Skor</div>
                <div class="stat-value">{{ $stats['avg_score'] }}<span style="font-size:0.85rem;font-weight:600;color:#475569;">/100</span></div>
            </div>
        </div>
        @endif

        <div class="overall-prog">
            <div class="overall-prog-top">
                <span class="overall-prog-label">Progress Keseluruhan</span>
                <span class="overall-prog-pct">{{ $stats['percent'] }}%</span>
            </div>
            <div class="overall-prog-track">
                <div class="overall-prog-fill" style="width:{{ $stats['percent'] }}%"></div>
            </div>
        </div>
    </div>

    {{-- Chapter overview --}}
    @if ($chapterStats->isNotEmpty())
        <div class="chapter-overview fade-up">
            <div class="chapter-overview-title">Progress per Bab</div>
            <div class="ch-list">
                @foreach ($chapterStats as $ch)
                    <div class="ch-row">
                        <div class="ch-row-top">
                            <span class="ch-row-label">{{ $ch['title'] }}</span>
                            <span class="ch-row-pct">{{ $ch['completed'] }}/{{ $ch['total'] }} soal &nbsp;·&nbsp; {{ $ch['percent'] }}%</span>
                        </div>
                        <div class="ch-track">
                            <div class="ch-bar" style="width:{{ $ch['percent'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @php $stageOrder = ['predict','run','investigate','modify','make']; @endphp

    {{-- Per chapter --}}
    <script>
        function toggleChat(btn) {
            const body = btn.nextElementSibling;
            const chevron = btn.querySelector('.chat-chevron');
            const isOpen = body.style.display === 'flex';
            body.style.display = isOpen ? 'none' : 'flex';
            chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
        }
    </script>

    @foreach ($course->chapters as $chapter)
        <div class="chapter-section fade-up" style="animation-delay:{{ $loop->index * 0.04 }}s">
            <div class="chapter-header">
                <div class="chapter-num">{{ $loop->iteration }}</div>
                <div class="chapter-title">{{ $chapter->title }}</div>
            </div>

            @php $byStage = $chapter->activities->groupBy('stage'); @endphp

            @foreach ($stageOrder as $stageName)
                @if ($byStage->has($stageName))
                    @php $acts = $byStage[$stageName]; @endphp
                    <div class="stage-group">
                        <div class="stage-group-header">
                            <span class="stage-group-name">{{ ucfirst($stageName) }}</span>
                            <span class="stage-group-count">{{ $acts->count() }} soal</span>
                        </div>

                        @foreach ($acts as $activity)
                            @php $sub = $activity->my_submission; @endphp
                            <div class="activity-item">
                                <div class="act-meta">
                                    @if ($activity->level)
                                        <span class="level-tag">{{ $activity->level }}</span>
                                    @endif
                                    @if ($sub && $sub->is_correct)
                                        <span class="status-done">✓ Benar</span>
                                    @elseif ($sub)
                                        <span class="status-wrong">✗ Belum benar</span>
                                    @else
                                        <span class="status-empty">Belum dikerjakan</span>
                                    @endif
                                    @if ($sub && $sub->score !== null)
                                        <span class="score-badge">{{ $sub->score }}<span class="score-badge-unit">/100</span></span>
                                    @endif
                                </div>

                                <div class="question-preview">
                                    {{ Str::limit(strip_tags($activity->question_text), 180) }}
                                </div>

                                @if ($sub)
                                    <div class="sub-card">
                                        <div class="sub-header {{ $sub->is_correct ? 'sub-correct' : 'sub-incorrect' }}">
                                            <span>Jawaban {{ $sub->is_correct ? 'Benar' : 'Terakhir' }}</span>
                                            @if ($sub->score !== null)
                                                <span>Skor: {{ $sub->score }}/100</span>
                                            @endif
                                        </div>
                                        <div class="sub-body">
                                            @if ($sub->answer_text)
                                                <span class="ans-label">Jawaban</span>
                                                <div class="ans-text">{{ $sub->answer_text }}</div>
                                            @endif
                                            @if ($sub->answer_code)
                                                <span class="ans-label">Kode SQL</span>
                                                <div class="ans-code">{{ $sub->answer_code }}</div>
                                            @endif
                                            @if ($sub->ai_feedback)
                                                <div class="ai-box" style="margin-top:4px;">
                                                    <div class="ai-lbl">Feedback PRIMMBASE</div>
                                                    <div class="ai-txt">{{ $sub->ai_feedback }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if ($activity->chat_logs->isNotEmpty())
                                    <div style="border:1px solid rgba(255,255,255,0.07);border-radius:8px;overflow:hidden;margin-top:2px;">
                                        <button type="button"
                                            onclick="toggleChat(this)"
                                            style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:7px 11px;background:rgba(255,255,255,0.04);border:none;cursor:pointer;font-family:inherit;">
                                            <span style="font-size:0.65rem;font-weight:800;color:#60a5fa;text-transform:uppercase;letter-spacing:0.06em;">
                                                Percakapan dengan PRIMMBASE
                                                <span style="font-weight:600;color:#475569;margin-left:4px;">({{ $activity->chat_logs->count() }} pesan)</span>
                                            </span>
                                            <svg class="chat-chevron" width="13" height="13" fill="none" stroke="#475569" stroke-width="2" viewBox="0 0 24 24" style="transition:transform 0.2s;flex-shrink:0;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
                                            </svg>
                                        </button>
                                        <div class="chat-body" style="display:none;flex-direction:column;gap:6px;padding:9px 11px;border-top:1px solid rgba(255,255,255,0.06);">
                                            @foreach ($activity->chat_logs as $log)
                                                <div style="display:flex;flex-direction:column;gap:4px;">
                                                    <div style="background:rgba(255,255,255,0.05);border-radius:6px;padding:7px 10px;">
                                                        <div style="font-size:0.62rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:3px;">Kamu</div>
                                                        <div style="font-size:0.8rem;color:#cbd5e1;line-height:1.55;">{{ $log->prompt_sent }}</div>
                                                    </div>
                                                    <div style="background:rgba(37,99,235,0.08);border:1px solid rgba(96,165,250,0.15);border-radius:6px;padding:7px 10px;">
                                                        <div style="font-size:0.62rem;font-weight:700;color:#60a5fa;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:3px;">PRIMMBASE</div>
                                                        <div style="font-size:0.8rem;color:#cbd5e1;line-height:1.55;">{{ $log->response_received }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach


</x-layouts.app>
