<x-layouts.admin title="Detail — {{ $student->name }}">
    <x-slot:styles>
        <style>
            .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 24px; font-size: 13px; flex-wrap: wrap; }
            .breadcrumb a { color: #3b5bdb; text-decoration: none; font-weight: 600; }
            .breadcrumb a:hover { text-decoration: underline; }
            .breadcrumb span { color: #94a3b8; }

            /* Profile card */
            .profile-card {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; padding: 20px 24px;
                display: flex; align-items: center; gap: 16px; margin-bottom: 24px;
            }
            .profile-avatar {
                width: 50px; height: 50px; border-radius: 50%; background: #0f1b3d; color: #fff;
                display: flex; align-items: center; justify-content: center;
                font-size: 18px; font-weight: 800; flex-shrink: 0;
            }
            .profile-avatar img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
            .profile-name { font-size: 16px; font-weight: 700; color: #0f1b3d; margin-bottom: 5px; }
            .profile-chips { display: flex; gap: 8px; flex-wrap: wrap; }
            .profile-chip {
                display: inline-flex; align-items: center; gap: 4px;
                font-size: 11.5px; color: #6b7a99; background: #f0f2f7;
                padding: 3px 9px; border-radius: 4px;
            }

            /* Chapter section */
            .chapter-section {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; margin-bottom: 20px; overflow: hidden;
            }
            .chapter-header {
                padding: 13px 20px; background: #f8f9fc; border-bottom: 1px solid #e4e8f1;
                display: flex; align-items: center; gap: 10px;
            }
            .chapter-num {
                width: 24px; height: 24px; border-radius: 50%; background: #0f1b3d; color: #fff;
                display: flex; align-items: center; justify-content: center;
                font-size: 11px; font-weight: 800; flex-shrink: 0;
            }
            .chapter-title { font-size: 13.5px; font-weight: 700; color: #0f1b3d; }

            /* Stage group */
            .stage-group { border-bottom: 1px solid #f0f2f7; }
            .stage-group:last-child { border-bottom: none; }
            .stage-group-header {
                display: flex; align-items: center; gap: 8px;
                padding: 9px 20px; border-bottom: 1px solid #f0f2f7; background: #fafbfd;
            }
            .stage-pill {
                display: inline-flex; align-items: center; padding: 2px 9px;
                border-radius: 4px; font-size: 10px; font-weight: 800;
                text-transform: uppercase; letter-spacing: 0.06em;
            }
            .stage-predict     { background: #ede9fe; color: #5b21b6; }
            .stage-run         { background: #e0f2fe; color: #075985; }
            .stage-investigate { background: #fef3c7; color: #92400e; }
            .stage-modify      { background: #d1fae5; color: #065f46; }
            .stage-make        { background: #ffe4e6; color: #9f1239; }
            .stage-group-count { font-size: 11px; color: #94a3b8; }

            /* Activity item */
            .activity-item {
                padding: 14px 20px; border-bottom: 1px solid #f8f9fc;
                display: flex; flex-direction: column; gap: 10px;
            }
            .activity-item:last-child { border-bottom: none; }

            .activity-meta { display: flex; align-items: center; gap: 7px; flex-wrap: wrap; }
            .level-tag { font-size: 10.5px; font-weight: 600; color: #64748b; background: #f1f5f9; padding: 2px 7px; border-radius: 4px; }
            .status-done  { font-size: 11.5px; font-weight: 700; color: #15803d; display: inline-flex; align-items: center; gap: 3px; }
            .status-wrong { font-size: 11.5px; font-weight: 700; color: #dc2626; display: inline-flex; align-items: center; gap: 3px; }
            .status-empty { font-size: 11.5px; color: #94a3b8; }
            .score-badge  { display: inline-flex; align-items: center; padding: 2px 7px; border-radius: 4px; font-size: 10.5px; font-weight: 700; background: #eef2ff; color: #3b5bdb; }

            .question-box {
                font-size: 12.5px; color: #475569; line-height: 1.55;
                padding: 9px 12px; background: #f8f9fc; border-radius: 5px; border-left: 3px solid #e4e8f1;
            }
            .question-box strong { color: #1e293b; }

            /* Submission card */
            .sub-card { border: 1px solid #e4e8f1; border-radius: 6px; overflow: hidden; }
            .sub-header {
                display: flex; align-items: center; justify-content: space-between;
                padding: 8px 13px; font-size: 11.5px; font-weight: 700;
            }
            .sub-correct   { background: #f0fdf4; color: #15803d; border-bottom: 1px solid #bbf7d0; }
            .sub-incorrect { background: #fef2f2; color: #991b1b; border-bottom: 1px solid #fecaca; }
            .sub-body { padding: 12px 13px; display: flex; flex-direction: column; gap: 8px; }

            .ans-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 3px; }
            .ans-text  { font-size: 12.5px; color: #1e293b; line-height: 1.5; white-space: pre-wrap; }
            .ans-code  {
                font-family: 'Courier New', monospace; font-size: 12px; color: #1e293b;
                background: #f8f9fc; padding: 9px 11px; border-radius: 5px;
                white-space: pre-wrap; line-height: 1.6; border: 1px solid #e4e8f1;
            }
            .ai-box {
                padding: 9px 12px; background: #f0f7ff; border-radius: 5px;
                border-left: 3px solid #3b5bdb;
            }
            .ai-lbl { font-size: 9.5px; font-weight: 800; color: #3b5bdb; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px; }
            .ai-txt { font-size: 12px; color: #1e293b; line-height: 1.55; }

            /* Chat panel */
            .chat-toggle-btn {
                display: inline-flex; align-items: center; gap: 5px;
                font-size: 11.5px; font-weight: 600; color: #3b5bdb; background: #eef2ff;
                border: none; border-radius: 5px; padding: 5px 11px; cursor: pointer; margin-top: 2px; font-family: inherit;
            }
            .chat-toggle-btn:hover { background: #dbeafe; }
            .chat-panel {
                display: none; margin-top: 8px;
                border: 1px solid #e4e8f1; border-radius: 6px; overflow: hidden;
            }
            .chat-panel.open { display: block; }
            .chat-panel-inner {
                max-height: 280px; overflow-y: auto; padding: 10px 12px;
                display: flex; flex-direction: column; gap: 7px; background: #fafbfd;
            }
            .chat-row { display: flex; flex-direction: column; gap: 3px; }
            .bubble-user {
                align-self: flex-end; max-width: 76%;
                background: #0f1b3d; color: #fff; padding: 7px 11px;
                border-radius: 10px 10px 2px 10px; font-size: 12px; line-height: 1.45;
            }
            .bubble-ai {
                align-self: flex-start; max-width: 76%;
                background: #e2e8f0; color: #1e293b; padding: 7px 11px;
                border-radius: 10px 10px 10px 2px; font-size: 12px; line-height: 1.45;
            }
            .chat-ts { font-size: 9.5px; color: #94a3b8; align-self: flex-end; }

            .no-activity-txt { font-size: 12px; color: #94a3b8; font-style: italic; padding: 10px 20px; }

            /* Progress summary */
            .summary-card {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; margin-bottom: 24px; overflow: hidden;
            }
            .summary-card-header {
                padding: 11px 20px; background: #f8f9fc; border-bottom: 1px solid #e4e8f1;
                font-size: 10.5px; font-weight: 700; color: #6b7a99;
                text-transform: uppercase; letter-spacing: 0.05em;
            }
            .summary-card-body { padding: 18px 20px; display: flex; flex-direction: column; gap: 16px; }
            .summary-stats { display: flex; gap: 14px; flex-wrap: wrap; }
            .summary-stat { display: flex; flex-direction: column; gap: 2px; }
            .s-val { font-size: 24px; font-weight: 800; color: #0f1b3d; line-height: 1; }
            .s-lbl { font-size: 10.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em; }
            .divider-v { width: 1px; background: #e4e8f1; align-self: stretch; margin: 0 4px; }

            .overall-prog { }
            .prog-hd { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
            .prog-lbl { font-size: 10.5px; font-weight: 700; color: #6b7a99; text-transform: uppercase; letter-spacing: 0.05em; }
            .prog-val { font-size: 13px; font-weight: 800; color: #0f1b3d; }
            .prog-track { height: 8px; background: #f0f2f7; border-radius: 4px; overflow: hidden; }
            .prog-fill  { height: 100%; border-radius: 4px; background: linear-gradient(90deg, #3b5bdb, #748ffc); }

            .ch-prog-list { display: flex; flex-direction: column; gap: 8px; }
            .ch-prog-row  { display: flex; flex-direction: column; gap: 3px; }
            .ch-prog-top  { display: flex; justify-content: space-between; align-items: center; gap: 8px; }
            .ch-prog-name { font-size: 11.5px; color: #475569; font-weight: 500;
                white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 80%; }
            .ch-prog-pct  { font-size: 11px; font-weight: 700; color: #6b7a99; flex-shrink: 0; }
            .ch-prog-track { height: 4px; background: #f0f2f7; border-radius: 3px; overflow: hidden; }
            .ch-prog-bar   { height: 100%; border-radius: 3px; background: linear-gradient(90deg, #3b5bdb, #748ffc); }
        </style>
    </x-slot:styles>

    <div class="breadcrumb">
        <a href="{{ route('admin.hasil-kelas.index') }}">Hasil LKPD</a>
        <span>›</span>
        <a href="{{ route('admin.hasil-kelas.show', $course) }}">{{ $course->title }}</a>
        <span>›</span>
        <span>{{ $student->profile?->full_name ?? $student->username }}</span>
    </div>

    {{-- Profile --}}
    <div class="profile-card">
        <div class="profile-avatar">
            @if ($student->profile?->avatar)
                <img src="{{ $student->profile->avatarUrl() }}" alt="">
            @else
                {{ strtoupper(substr($student->name, 0, 1)) }}
            @endif
        </div>
        <div>
            <div class="profile-name">{{ $student->profile?->full_name ?? $student->username }}</div>
            <div class="profile-chips">
                <span class="profile-chip">{{ $student->email }}</span>
                @if($student->profile?->nim)
                    <span class="profile-chip">NIS: {{ $student->profile->nim }}</span>
                @endif
                @if($student->profile?->kelas)
                    <span class="profile-chip">{{ $student->profile->kelas->name }}</span>
                    <span class="profile-chip">{{ $student->profile->kelas->school->name }}</span>
                @endif
                @if($student->profile?->gender)
                    <span class="profile-chip">{{ $student->profile->gender }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Progress Summary --}}
    @php
        $allActivities   = $course->chapters->flatMap(fn($ch) => $ch->activities);
        $totalActivities = $allActivities->count();
        $correctSubs     = $student->submissions->where('is_correct', true);
        $completedCount  = $correctSubs->pluck('activity_id')->unique()->count();
        $progressPct     = $totalActivities > 0 ? round($completedCount / $totalActivities * 100) : 0;
        $scoredSubs      = $student->submissions->whereNotNull('score');
        $avgScore        = $scoredSubs->isNotEmpty() ? round($scoredSubs->avg('score')) : null;

        $chapterProgress = $course->chapters->map(function($ch) use ($correctSubs) {
            $total = $ch->activities->count();
            $done  = $ch->activities->filter(fn($a) => $correctSubs->pluck('activity_id')->contains($a->id))->count();
            return [
                'title'   => $ch->title,
                'total'   => $total,
                'done'    => $done,
                'percent' => $total > 0 ? round($done / $total * 100) : 0,
            ];
        });
    @endphp
    <div class="summary-card">
        <div class="summary-card-header">Ringkasan Progress</div>
        <div class="summary-card-body">
            {{-- Stats row --}}
            <div class="summary-stats">
                <div class="summary-stat">
                    <div class="s-val">{{ $completedCount }}/{{ $totalActivities }}</div>
                    <div class="s-lbl">Aktivitas Selesai</div>
                </div>
                <div class="divider-v"></div>
                <div class="summary-stat">
                    <div class="s-val">{{ $progressPct }}%</div>
                    <div class="s-lbl">Progress</div>
                </div>
                @if ($avgScore !== null)
                    <div class="divider-v"></div>
                    <div class="summary-stat">
                        <div class="s-val" style="color:#3b5bdb;">{{ $avgScore }}</div>
                        <div class="s-lbl">Rata-rata Skor</div>
                    </div>
                @endif
            </div>
            {{-- Overall progress bar --}}
            <div class="overall-prog">
                <div class="prog-hd">
                    <span class="prog-lbl">Progress Keseluruhan</span>
                    <span class="prog-val">{{ $progressPct }}%</span>
                </div>
                <div class="prog-track"><div class="prog-fill" style="width:{{ $progressPct }}%"></div></div>
            </div>
            {{-- Per-chapter --}}
            @if ($chapterProgress->isNotEmpty())
                <div class="ch-prog-list">
                    @foreach ($chapterProgress as $cp)
                        <div class="ch-prog-row">
                            <div class="ch-prog-top">
                                <span class="ch-prog-name">{{ $cp['title'] }}</span>
                                <span class="ch-prog-pct">{{ $cp['done'] }}/{{ $cp['total'] }} ({{ $cp['percent'] }}%)</span>
                            </div>
                            <div class="ch-prog-track"><div class="ch-prog-bar" style="width:{{ $cp['percent'] }}%"></div></div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @php $stageOrder = ['predict','run','investigate','modify','make']; @endphp

    {{-- Per Chapter --}}
    @foreach ($course->chapters as $chapter)
        <div class="chapter-section">
            <div class="chapter-header">
                <div class="chapter-num">{{ $loop->iteration }}</div>
                <div class="chapter-title">{{ $chapter->title }}</div>
            </div>

            @php $activitiesByStage = $chapter->activities->sortBy('order')->groupBy('stage'); @endphp

            @forelse ($stageOrder as $stageName)
                @if ($activitiesByStage->has($stageName))
                    @php $stageActivities = $activitiesByStage[$stageName]; @endphp
                    <div class="stage-group">
                        <div class="stage-group-header">
                            <span class="stage-pill stage-{{ $stageName }}">{{ ucfirst($stageName) }}</span>
                            <span class="stage-group-count">{{ $stageActivities->count() }} soal</span>
                        </div>

                        @foreach ($stageActivities as $activity)
                            @php
                                $submission = $activity->student_submission;
                                $chats      = $activity->chat_logs;
                                $chatId     = 'chat-' . $activity->id;
                            @endphp
                            <div class="activity-item">

                                {{-- Status --}}
                                <div class="activity-meta">
                                    @if ($activity->level)
                                        <span class="level-tag">Level {{ $activity->level }}</span>
                                    @endif
                                    @if ($submission && $submission->is_correct)
                                        <span class="status-done">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            Selesai
                                        </span>
                                    @elseif ($submission)
                                        <span class="status-wrong">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            Belum Benar
                                        </span>
                                    @else
                                        <span class="status-empty">Belum Dikerjakan</span>
                                    @endif
                                    @if ($submission && $submission->score !== null)
                                        <span class="score-badge">Skor: {{ $submission->score }}</span>
                                    @endif
                                </div>

                                {{-- Question --}}
                                <div class="question-box">
                                    <strong>Soal:</strong> {{ Str::limit(strip_tags($activity->question_text), 240) }}
                                </div>

                                {{-- Submission --}}
                                @if ($submission)
                                    <div class="sub-card">
                                        <div class="sub-header {{ $submission->is_correct ? 'sub-correct' : 'sub-incorrect' }}">
                                            <span>{{ $submission->is_correct ? '✓ Jawaban Benar' : '✗ Jawaban Belum Benar' }}</span>
                                            <span style="font-weight:400; opacity:0.7; font-size:11px;">{{ $submission->updated_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        <div class="sub-body">
                                            @if ($submission->answer_text)
                                                <div>
                                                    <div class="ans-label">Jawaban</div>
                                                    <div class="ans-text">{{ $submission->answer_text }}</div>
                                                </div>
                                            @endif
                                            @if ($submission->answer_code)
                                                <div>
                                                    <div class="ans-label">Kode SQL</div>
                                                    <div class="ans-code">{{ $submission->answer_code }}</div>
                                                </div>
                                            @endif
                                            @if ($submission->ai_feedback)
                                                <div class="ai-box">
                                                    <div class="ai-lbl">Feedback AI</div>
                                                    <div class="ai-txt">{{ $submission->ai_feedback }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- Chat (collapsible) --}}
                                @if ($chats->isNotEmpty())
                                    <div>
                                        <button class="chat-toggle-btn" onclick="toggleChat('{{ $chatId }}', this)">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.83L3 20l1.09-3.27C3.4 15.36 3 13.72 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                            {{ $chats->count() }} pesan AI
                                        </button>
                                        <div class="chat-panel" id="{{ $chatId }}">
                                            <div class="chat-panel-inner">
                                                @foreach ($chats as $chat)
                                                    <div class="chat-row">
                                                        <div class="bubble-user">{{ $chat->prompt_sent }}</div>
                                                        <div class="bubble-ai">{{ $chat->response_received }}</div>
                                                        <div class="chat-ts">{{ $chat->created_at->format('d M, H:i') }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (!$submission && $chats->isEmpty())
                                    <div class="no-activity-txt">Tidak ada aktivitas pada soal ini.</div>
                                @endif

                            </div>
                        @endforeach
                    </div>
                @endif
            @empty
                <div class="no-activity-txt">Tidak ada aktivitas di chapter ini.</div>
            @endforelse
        </div>
    @endforeach

    <x-slot:scripts>
        <script>
            function toggleChat(id, btn) {
                const panel = document.getElementById(id);
                const isOpen = panel.classList.toggle('open');
                btn.style.background = isOpen ? '#dbeafe' : '';
            }
        </script>
    </x-slot:scripts>

</x-layouts.admin>
