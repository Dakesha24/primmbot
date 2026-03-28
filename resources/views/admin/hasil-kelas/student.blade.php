<x-layouts.admin title="Detail — {{ $student->name }}">
    <x-slot:styles>
        <style>
            /* ── Breadcrumb ── */
            .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 24px; font-size: 13px; flex-wrap: wrap; }
            .breadcrumb a { color: #3b5bdb; text-decoration: none; font-weight: 600; }
            .breadcrumb a:hover { text-decoration: underline; }
            .breadcrumb span { color: #94a3b8; }

            /* ── Profile card ── */
            .profile-card {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; padding: 20px 24px;
                display: flex; align-items: center; gap: 16px; margin-bottom: 20px;
            }
            .profile-avatar {
                width: 46px; height: 46px; border-radius: 50%; background: #0f1b3d; color: #fff;
                display: flex; align-items: center; justify-content: center;
                font-size: 17px; font-weight: 800; flex-shrink: 0;
            }
            .profile-avatar img { width: 46px; height: 46px; border-radius: 50%; object-fit: cover; }
            .profile-name { font-size: 15px; font-weight: 700; color: #0f1b3d; margin-bottom: 6px; }
            .profile-chips { display: flex; gap: 6px; flex-wrap: wrap; }
            .profile-chip { font-size: 11.5px; color: #64748b; background: #f0f2f7; padding: 2px 8px; border-radius: 4px; }

            /* ── Summary card ── */
            .summary-card {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; margin-bottom: 20px;
            }
            .summary-card-header {
                padding: 10px 20px; border-bottom: 1px solid #e4e8f1;
                font-size: 10.5px; font-weight: 700; color: #94a3b8;
                text-transform: uppercase; letter-spacing: 0.05em;
            }
            .summary-card-body { padding: 18px 20px; display: flex; flex-direction: column; gap: 14px; }
            .summary-stats { display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end; }
            .summary-stat { display: flex; flex-direction: column; gap: 2px; }
            .s-val { font-size: 26px; font-weight: 800; color: #0f1b3d; line-height: 1; }
            .s-lbl { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em; margin-top: 2px; }
            .divider-v { width: 1px; background: #e4e8f1; align-self: stretch; margin: 0 6px; }

            .prog-hd { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
            .prog-lbl { font-size: 10.5px; font-weight: 600; color: #94a3b8; }
            .prog-val { font-size: 12px; font-weight: 700; color: #0f1b3d; }
            .prog-track { height: 6px; background: #f0f2f7; border-radius: 3px; overflow: hidden; }
            .prog-fill  { height: 100%; border-radius: 3px; background: #3b5bdb; }

            .ch-prog-list { display: flex; flex-direction: column; gap: 7px; }
            .ch-prog-row  { display: flex; flex-direction: column; gap: 3px; }
            .ch-prog-top  { display: flex; justify-content: space-between; gap: 8px; }
            .ch-prog-name { font-size: 11.5px; color: #334155;
                white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 82%; }
            .ch-prog-pct  { font-size: 11px; font-weight: 600; color: #64748b; flex-shrink: 0; }
            .ch-prog-track { height: 3px; background: #f0f2f7; border-radius: 2px; overflow: hidden; }
            .ch-prog-bar   { height: 100%; border-radius: 2px; background: #3b5bdb; }

            /* ── Chapter section ── */
            .chapter-section {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; margin-bottom: 16px; overflow: hidden;
            }
            .chapter-header {
                padding: 12px 20px; background: #f8f9fc; border-bottom: 1px solid #e4e8f1;
                display: flex; align-items: center; gap: 10px;
            }
            .chapter-num {
                width: 22px; height: 22px; border-radius: 50%; background: #0f1b3d; color: #fff;
                display: flex; align-items: center; justify-content: center;
                font-size: 10px; font-weight: 800; flex-shrink: 0;
            }
            .chapter-title { font-size: 13px; font-weight: 700; color: #0f1b3d; }

            /* ── Stage group ── */
            .stage-group { border-bottom: 1px solid #f0f2f7; }
            .stage-group:last-child { border-bottom: none; }
            .stage-group-header {
                display: flex; align-items: center; gap: 8px;
                padding: 7px 20px; border-bottom: 1px solid #f0f2f7; background: #fafbfd;
            }
            .stage-pill {
                display: inline-flex; align-items: center; padding: 2px 8px;
                border-radius: 3px; font-size: 9.5px; font-weight: 800;
                text-transform: uppercase; letter-spacing: 0.07em;
                background: #f0f2f7; color: #475569;
            }
            .stage-group-count { font-size: 11px; color: #94a3b8; }

            /* ── Activity item ── */
            .activity-item {
                padding: 14px 20px; border-bottom: 1px solid #f8f9fc;
                display: flex; flex-direction: column; gap: 9px;
            }
            .activity-item:last-child { border-bottom: none; }

            /* Meta row */
            .activity-meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
            .level-tag { font-size: 10px; font-weight: 600; color: #475569; background: #f0f2f7; border: 1px solid #e4e8f1; padding: 1px 7px; border-radius: 3px; }
            .status-done  { font-size: 11.5px; font-weight: 700; color: #16a34a; display: inline-flex; align-items: center; gap: 4px; }
            .status-wrong { font-size: 11.5px; font-weight: 600; color: #dc2626; display: inline-flex; align-items: center; gap: 4px; }
            .status-empty { font-size: 11.5px; color: #64748b; }
            .meta-sep { color: #cbd5e1; font-size: 13px; }

            /* Skor — badge menonjol */
            .score-ai-txt {
                font-size: 11.5px; font-weight: 600; color: #1e293b;
                background: #f0f2f7; border: 1px solid #e4e8f1;
                padding: 2px 8px; border-radius: 4px;
            }
            .score-guru-txt {
                font-size: 11.5px; font-weight: 700; color: #0f1b3d;
                background: #e8ecf3; border: 1px solid #c8cfdc;
                padding: 2px 8px; border-radius: 4px;
            }

            /* Tombol Koreksi skor — terlihat jelas */
            .edit-score-btn {
                font-size: 11px; font-weight: 600; color: #3b5bdb;
                background: #eef2ff; border: 1px solid #c7d2fe;
                border-radius: 4px; padding: 2px 9px;
                cursor: pointer; font-family: inherit; text-decoration: none;
            }
            .edit-score-btn:hover { background: #e0e7ff; border-color: #a5b4fc; }

            /* Question box */
            .question-box {
                font-size: 12.5px; color: #334155; line-height: 1.55;
                padding: 8px 12px; background: #f8f9fc; border-radius: 5px;
                border-left: 2px solid #e4e8f1;
            }
            .question-box strong { color: #0f1b3d; }

            /* Submission card */
            .sub-card { border: 1px solid #e4e8f1; border-radius: 6px; overflow: hidden; }
            .sub-header {
                display: flex; align-items: center; justify-content: space-between;
                padding: 7px 13px; background: #f8f9fc; border-bottom: 1px solid #e4e8f1;
                font-size: 11px;
            }
            .sub-status { display: flex; align-items: center; gap: 6px; }
            .sub-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
            .sub-dot-ok  { background: #22c55e; }
            .sub-dot-bad { background: #f87171; }
            .sub-status-lbl { font-weight: 600; color: #1e293b; }
            .sub-date { font-size: 10.5px; color: #64748b; }
            .sub-body { padding: 12px 13px; display: flex; flex-direction: column; gap: 9px; }

            .ans-label { font-size: 9.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 3px; }
            .ans-text  { font-size: 12.5px; color: #1e293b; line-height: 1.5; white-space: pre-wrap; }
            .ans-code  {
                font-family: 'Courier New', monospace; font-size: 12px; color: #1e293b;
                background: #f8f9fc; padding: 8px 11px; border-radius: 5px;
                white-space: pre-wrap; line-height: 1.6; border: 1px solid #e4e8f1;
            }
            .ai-box {
                padding: 9px 12px; background: #f8f9fc; border-radius: 5px;
                border-left: 2px solid #cbd5e1;
            }
            .ai-lbl { font-size: 9.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px; }
            .ai-txt { font-size: 12px; color: #1e293b; line-height: 1.55; }

            .guru-box {
                padding: 9px 12px; background: #f8f9fc; border-radius: 5px;
                border-left: 2px solid #0f1b3d;
            }
            .guru-lbl-row { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
            .guru-lbl { font-size: 9.5px; font-weight: 700; color: #0f1b3d; text-transform: uppercase; letter-spacing: 0.06em; }
            .guru-score { font-size: 11px; font-weight: 700; color: #0f1b3d; margin-bottom: 4px; }
            .guru-txt { font-size: 12px; color: #1e293b; line-height: 1.55; white-space: pre-wrap; }

            /* ── Review panel ── */
            .review-panel {
                display: none; border: 1px solid #e4e8f1; border-radius: 6px;
                background: #fff; overflow: hidden;
            }
            .review-panel.open { display: block; }
            .review-panel-head {
                padding: 8px 13px; background: #f8f9fc; border-bottom: 1px solid #e4e8f1;
                font-size: 10px; font-weight: 700; color: #475569;
                text-transform: uppercase; letter-spacing: 0.05em;
                display: flex; align-items: center; justify-content: space-between;
            }
            .review-panel-body { padding: 14px 13px; display: flex; flex-direction: column; gap: 11px; }

            /* Existing review display inside panel */
            .review-existing { display: flex; flex-direction: column; gap: 6px; }
            .review-score-row { font-size: 12.5px; font-weight: 700; color: #1e293b; }
            .review-feedback-txt { font-size: 12px; color: #1e293b; line-height: 1.55; white-space: pre-wrap; }
            .review-divider { height: 1px; background: #f0f2f7; }

            /* Form fields */
            .form-group { display: flex; flex-direction: column; gap: 4px; }
            .form-label { font-size: 10.5px; font-weight: 700; color: #475569; }
            .form-note  { font-size: 10px; color: #64748b; }
            .form-input, .form-textarea {
                width: 100%; font-size: 13px; font-family: inherit; color: #1e293b;
                border: 1px solid #d1d9e6; border-radius: 5px; padding: 7px 10px;
                background: #fff; box-sizing: border-box; outline: none;
                transition: border-color 0.15s;
            }
            .form-input:focus, .form-textarea:focus { border-color: #3b5bdb; }
            .form-input { max-width: 110px; }
            .form-textarea { min-height: 76px; resize: vertical; line-height: 1.5; }
            .form-actions { display: flex; gap: 8px; align-items: center; }
            .btn-save {
                font-size: 12px; font-weight: 600; color: #fff; background: #0f1b3d;
                border: none; border-radius: 5px; padding: 6px 16px; cursor: pointer; font-family: inherit;
                transition: background 0.15s;
            }
            .btn-save:hover { background: #1a2e5a; }
            .btn-cancel {
                font-size: 12px; color: #64748b; background: none;
                border: none; cursor: pointer; font-family: inherit; padding: 0;
            }
            .btn-cancel:hover { color: #1e293b; }
            .saved-badge {
                font-size: 10.5px; color: #16a34a; font-weight: 600;
                background: #f0fdf4; border: 1px solid #bbf7d0;
                padding: 2px 7px; border-radius: 4px;
            }

            /* ── Chat panel ── */
            .chat-toggle-btn {
                display: inline-flex; align-items: center; gap: 5px;
                font-size: 11px; font-weight: 600; color: #64748b; background: #f0f2f7;
                border: 1px solid #e4e8f1; border-radius: 4px; padding: 4px 10px;
                cursor: pointer; font-family: inherit;
            }
            .chat-toggle-btn:hover { background: #e8ecf3; }
            .chat-panel { display: none; margin-top: 7px; border: 1px solid #e4e8f1; border-radius: 6px; overflow: hidden; }
            .chat-panel.open { display: block; }
            .chat-panel-inner {
                max-height: 260px; overflow-y: auto; padding: 10px 12px;
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
                background: #e8ecf3; color: #1e293b; padding: 7px 11px;
                border-radius: 10px 10px 10px 2px; font-size: 12px; line-height: 1.45;
            }
            .chat-ts { font-size: 9.5px; color: #94a3b8; align-self: flex-end; }

            .no-activity-txt { font-size: 12px; color: #94a3b8; font-style: italic; }
        </style>
    </x-slot:styles>

    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:24px;">
        <div class="breadcrumb" style="margin-bottom:0;">
            <a href="{{ route('admin.hasil-kelas.index') }}">Hasil LKPD</a>
            <span>›</span>
            <a href="{{ route('admin.hasil-kelas.show', $course) }}">{{ $course->title }}</a>
            <span>›</span>
            <span>{{ $student->profile?->full_name ?? $student->username }}</span>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('admin.hasil-kelas.student.pdf', [$course, $student]) }}"
               style="display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600;
                      color:#fff; background:#0f1b3d; border-radius:5px; padding:6px 14px;
                      text-decoration:none; transition:background 0.15s;"
               onmouseover="this.style.background='#1a2e5a'" onmouseout="this.style.background='#0f1b3d'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                PDF
            </a>
            <a href="{{ route('admin.hasil-kelas.student.excel', [$course, $student]) }}"
               style="display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600;
                      color:#166534; background:#dcfce7; border:1px solid #bbf7d0; border-radius:5px; padding:6px 14px;
                      text-decoration:none; transition:background 0.15s;"
               onmouseover="this.style.background='#bbf7d0'" onmouseout="this.style.background='#dcfce7'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Excel Hasil
            </a>
            <a href="{{ route('admin.hasil-kelas.student.chat', [$course, $student]) }}"
               style="display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600;
                      color:#1e40af; background:#dbeafe; border:1px solid #bfdbfe; border-radius:5px; padding:6px 14px;
                      text-decoration:none; transition:background 0.15s;"
               onmouseover="this.style.background='#bfdbfe'" onmouseout="this.style.background='#dbeafe'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.83L3 20l1.09-3.27C3.4 15.36 3 13.72 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Excel Riwayat Chat
            </a>
        </div>
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
            <div class="summary-stats">
                <div class="summary-stat">
                    <div class="s-val">{{ $completedCount }}<span style="font-size:16px;color:#94a3b8;">/{{ $totalActivities }}</span></div>
                    <div class="s-lbl">Aktivitas Selesai</div>
                </div>
                <div class="divider-v"></div>
                <div class="summary-stat">
                    <div class="s-val">{{ $progressPct }}<span style="font-size:16px;color:#94a3b8;">%</span></div>
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
            <div>
                <div class="prog-hd">
                    <span class="prog-lbl">Progress Keseluruhan</span>
                    <span class="prog-val">{{ $progressPct }}%</span>
                </div>
                <div class="prog-track"><div class="prog-fill" style="width:{{ $progressPct }}%"></div></div>
            </div>
            @if ($chapterProgress->isNotEmpty())
                <div class="ch-prog-list">
                    @foreach ($chapterProgress as $cp)
                        <div class="ch-prog-row">
                            <div class="ch-prog-top">
                                <span class="ch-prog-name">{{ $cp['title'] }}</span>
                                <span class="ch-prog-pct">{{ $cp['done'] }}/{{ $cp['total'] }}</span>
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
                            <span class="stage-pill">{{ ucfirst($stageName) }}</span>
                            <span class="stage-group-count">{{ $stageActivities->count() }} soal</span>
                        </div>

                        @foreach ($stageActivities as $activity)
                            @php
                                $submission   = $activity->student_submission;
                                $chats        = $activity->chat_logs;
                                $chatId       = 'chat-' . $activity->id;
                                $review       = $submission?->teacherReview;
                                $reviewPanelId = 'review-panel-' . ($submission?->id ?? $activity->id);
                                $justSaved    = $submission && session('review_saved') == $submission->id;
                            @endphp
                            <div class="activity-item">

                                {{-- Meta row --}}
                                <div class="activity-meta">
                                    @if ($activity->level)
                                        <span class="level-tag">{{ $activity->level }}</span>
                                    @endif

                                    @if ($submission && $submission->is_correct)
                                        <span class="status-done">
                                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            Selesai
                                        </span>
                                    @elseif ($submission)
                                        <span class="status-wrong">Belum benar</span>
                                    @else
                                        <span class="status-empty">Belum dikerjakan</span>
                                    @endif

                                    @if ($submission && $submission->score !== null)
                                        <span class="meta-sep">·</span>
                                        <span class="score-ai-txt">Skor AI: {{ $submission->score }}</span>
                                        @if ($review && $review->score !== null)
                                            <span class="score-guru-txt">· Skor Guru: {{ $review->score }}</span>
                                        @endif
                                        <button class="edit-score-btn" onclick="toggleReview('{{ $reviewPanelId }}')">
                                            {{ $review ? 'Ubah koreksi' : 'Koreksi skor' }}
                                        </button>
                                    @endif
                                </div>

                                {{-- Question --}}
                                <div class="question-box">
                                    <strong>Soal:</strong> {{ Str::limit(strip_tags($activity->question_text), 240) }}
                                </div>

                                {{-- Submission --}}
                                @if ($submission)
                                    <div class="sub-card">
                                        <div class="sub-header">
                                            <div class="sub-status">
                                                <span class="sub-dot {{ $submission->is_correct ? 'sub-dot-ok' : 'sub-dot-bad' }}"></span>
                                                <span class="sub-status-lbl">{{ $submission->is_correct ? 'Jawaban benar' : 'Belum benar' }}</span>
                                            </div>
                                            <span class="sub-date">{{ $submission->updated_at->format('d M Y, H:i') }}</span>
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

                                            {{-- Feedback Guru — tampil di bawah feedback AI --}}
                                            @if ($review && $review->feedback)
                                                <div class="guru-box">
                                                    <div class="guru-lbl-row">
                                                        <span class="guru-lbl">Feedback Guru</span>
                                                        @if ($justSaved)<span class="saved-badge">Tersimpan</span>@endif
                                                    </div>
                                                    <div class="guru-txt">{{ $review->feedback }}</div>
                                                </div>
                                            @elseif ($justSaved)
                                                <div style="font-size:11px; color:#16a34a; font-weight:600;">✓ Koreksi tersimpan</div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Review form panel — selalu tertutup, toggle via tombol "Koreksi skor" --}}
                                    <div class="review-panel" id="{{ $reviewPanelId }}">
                                        <div class="review-panel-head">
                                            <span>{{ $review ? 'Edit Koreksi Guru' : 'Koreksi Guru' }}</span>
                                        </div>
                                        <div class="review-panel-body">
                                            @if ($review)
                                                <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
                                                    @csrf @method('PUT')
                                                    <div style="display:flex; flex-direction:column; gap:10px;">
                                                        <div class="form-group">
                                                            <label class="form-label">Skor koreksi (0–100)</label>
                                                            <span class="form-note">Kosongkan untuk tidak mengganti skor AI</span>
                                                            <input class="form-input" type="number" name="score" min="0" max="100" value="{{ $review->score }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="form-label">Feedback untuk siswa</label>
                                                            <textarea class="form-textarea" name="feedback">{{ $review->feedback }}</textarea>
                                                        </div>
                                                        <div class="form-actions">
                                                            <button type="submit" class="btn-save">Simpan</button>
                                                            <button type="button" class="btn-cancel" onclick="toggleReview('{{ $reviewPanelId }}')">Batal</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.reviews.store', $submission) }}">
                                                    @csrf
                                                    <div style="display:flex; flex-direction:column; gap:10px;">
                                                        <div class="form-group">
                                                            <label class="form-label">Skor koreksi (0–100)</label>
                                                            <span class="form-note">Kosongkan untuk tidak mengganti skor AI</span>
                                                            <input class="form-input" type="number" name="score" min="0" max="100" placeholder="—">
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="form-label">Feedback untuk siswa</label>
                                                            <textarea class="form-textarea" name="feedback" placeholder="Tulis catatan atau arahan..."></textarea>
                                                        </div>
                                                        <div class="form-actions">
                                                            <button type="submit" class="btn-save">Simpan</button>
                                                            <button type="button" class="btn-cancel" onclick="toggleReview('{{ $reviewPanelId }}')">Batal</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- Chat (collapsible) --}}
                                @if ($chats->isNotEmpty())
                                    <div>
                                        <button class="chat-toggle-btn" onclick="toggleChat('{{ $chatId }}', this)">
                                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.83L3 20l1.09-3.27C3.4 15.36 3 13.72 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                            Riwayat chat ({{ $chats->count() }})
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
                <div class="no-activity-txt" style="padding:12px 20px;">Tidak ada aktivitas di chapter ini.</div>
            @endforelse
        </div>
    @endforeach

    <x-slot:scripts>
        <script>
            function toggleChat(id, btn) {
                const panel = document.getElementById(id);
                const isOpen = panel.classList.toggle('open');
                btn.style.background = isOpen ? '#e8ecf3' : '';
            }

            function toggleReview(id) {
                const panel = document.getElementById(id);
                panel.classList.toggle('open');
            }
        </script>
    </x-slot:scripts>

</x-layouts.admin>
