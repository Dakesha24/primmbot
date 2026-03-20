@extends('layouts.learning')

@section('content')
    <style>
        .main-inner {
            max-width: 100% !important;
            padding: 2rem 1.5rem !important;
        }

        .inv-layout {
            display: grid;
            grid-template-columns: 2fr 3fr;
            gap: 1.2rem;
            align-items: start;
        }

        .inv-left {
            min-width: 0;
        }

        .inv-right {
            min-width: 0;
        }

        .editor-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            overflow: hidden;
        }

        .editor-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0.8rem;
            background: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .editor-toolbar-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .editor-toolbar-dots {
            display: flex;
            gap: 0.35rem;
        }

        .editor-toolbar-dots span {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .editor-textarea {
            width: 100%;
            min-height: 140px;
            padding: 1rem;
            border: none;
            outline: none;
            resize: vertical;
            background: transparent;
            color: #a6e3a1;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            line-height: 1.7;
        }

        .editor-textarea::placeholder {
            color: #475569;
        }

        .editor-output {
            padding: 1rem;
            min-height: 80px;
            color: #cdd6f4;
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
        }

        .editor-output table {
            width: 100%;
            border-collapse: collapse;
        }

        .editor-output th {
            background: rgba(255, 255, 255, 0.05);
            color: #94a3b8;
            font-weight: 600;
            padding: 0.45rem 0.6rem;
            text-align: left;
            border: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.75rem;
        }

        .editor-output td {
            padding: 0.4rem 0.6rem;
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 0.8rem;
        }

        .editor-output tr:nth-child(even) td {
            background: rgba(255, 255, 255, 0.02);
        }

        .btn-run {
            padding: 0.35rem 0.7rem;
            border-radius: 6px;
            border: none;
            background: #a6e3a1;
            color: #0a1628;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.15s;
        }

        .btn-run:hover {
            background: #94e2d5;
        }

        .level-tabs {
            display: flex;
            gap: 0.4rem;
            flex-wrap: wrap;
        }

        .level-tab {
            padding: 0.3rem 0.7rem;
            border-radius: 99px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.2s;
            cursor: default;
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #475569;
            background: transparent;
        }

        .level-tab.active {
            background: rgba(37, 99, 235, 0.15);
            border-color: rgba(37, 99, 235, 0.3);
            color: var(--blue-400);
        }

        .level-tab.completed {
            background: rgba(74, 222, 128, 0.1);
            border-color: rgba(74, 222, 128, 0.2);
            color: #4ade80;
        }

        .q-nav {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .q-nav-btn {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            text-decoration: none;
        }

        .q-nav-btn:hover:not([disabled]) {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.2);
        }

        .q-nav-btn[disabled] {
            opacity: 0.3;
            cursor: not-allowed;
            pointer-events: none;
        }

        .q-nav-counter {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .inv-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <h1 style="text-align:center;font-size:1.4rem;font-weight:800;color:#fff;margin-bottom:1.5rem;letter-spacing:-0.3px;">
        INVESTIGATE</h1>

    <div class="inv-layout">

        {{-- KIRI: Code Editor + Output --}}
        <div class="inv-left">
            @if ($activity->code_snippet)
                <div class="editor-card">
                    <div class="editor-toolbar">
                        <div class="editor-toolbar-dots">
                            <span style="background:#f38ba8;"></span>
                            <span style="background:#f9e2af;"></span>
                            <span style="background:#a6e3a1;"></span>
                        </div>
                        <span class="editor-toolbar-title">SQL Query</span>
                        <button id="btn-run" class="btn-run">Run ▶</button>
                    </div>
                    <textarea id="sql-editor" class="editor-textarea">{{ $activity->code_snippet }}</textarea>
                </div>

                <div class="editor-card" style="margin-top:0.8rem;">
                    <div class="editor-toolbar">
                        <span class="editor-toolbar-title">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                            Output
                        </span>
                    </div>
                    <div id="sql-output" class="editor-output">
                        <span style="color:#45475a;font-style:italic;">Klik Run untuk melihat hasil...</span>
                    </div>
                </div>
            @endif

            {{-- Deskripsi jika ada --}}
            @if ($activity->description)
                <div class="content-card" style="margin-top:0.8rem;">
                    <h3 style="font-size:0.85rem;font-weight:700;color:#fff;margin-bottom:0.8rem;">Deskripsi</h3>
                    <div class="prose" style="font-size:0.85rem;">{!! $activity->description !!}</div>
                </div>
            @endif
        </div>

        {{-- KANAN: Pertanyaan + Level + Nav + Jawab + VA --}}
        <div class="inv-right">
            {{-- Card Pertanyaan + Jawab --}}
            <div class="content-card">
                {{-- Header: Level Tabs + Nav --}}
                <div
                    style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:0.6rem;">
                    <div class="level-tabs">
                        @php
                            $levels = [
                                'atoms' => 'Atoms',
                                'blocks' => 'Blocks',
                                'relations' => 'Relations',
                                'macro' => 'Macro',
                            ];
                            $allInvActivities = $chapter->activities
                                ->where('stage', 'investigate')
                                ->sortBy('order')
                                ->values();
                        @endphp
                        @foreach ($levels as $levelKey => $levelLabel)
                            @php
                                $levelActs = $allInvActivities->where('level', $levelKey);
                                $levelDone =
                                    $levelActs->isNotEmpty() &&
                                    $levelActs->every(fn($a) => in_array($a->id, $completedActivityIds ?? []));
                                $levelActive = $activity->level === $levelKey;
                            @endphp
                            <span class="level-tab {{ $levelActive ? 'active' : '' }} {{ $levelDone ? 'completed' : '' }}">
                                @if ($levelDone)
                                    ✓
                                @endif
                                {{ $levelLabel }}
                            </span>
                        @endforeach
                    </div>

                    <div class="q-nav">
                        @if ($prevActivity)
                            <a href="{{ route('learning.activity', [$chapter, $prevActivity]) }}" class="q-nav-btn">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                        @else
                            <span class="q-nav-btn" disabled>
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                            </span>
                        @endif

                        <span class="q-nav-counter">{{ $currentNumber }} / {{ $totalSiblings }}</span>

                        @if ($nextActivity)
                            <a href="{{ route('learning.activity', [$chapter, $nextActivity]) }}" class="q-nav-btn">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @else
                            <span class="q-nav-btn" disabled>
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Pertanyaan --}}
                <h3
                    style="font-size:0.95rem;font-weight:700;color:#fff;margin-bottom:0.8rem;display:flex;align-items:center;gap:0.5rem;">
                    <svg width="18" height="18" fill="none" stroke="var(--blue-400)" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Pertanyaan
                    <span style="margin-left:auto;font-size:0.7rem;color:#475569;font-weight:600;">Level
                        {{ ucfirst($activity->level) }}</span>
                </h3>
                <p style="font-size:0.9rem;color:#94a3b8;line-height:1.7;margin-bottom:1.5rem;">
                    {{ $activity->question_text }}</p>

                {{-- Jawab --}}
                <div style="border-top:1px solid rgba(255,255,255,0.06);padding-top:1.2rem;">
                    <h4 style="font-size:0.9rem;font-weight:700;color:#fff;margin-bottom:0.6rem;">Jawab</h4>
                    <textarea id="answer-text" rows="5"
                        style="width:100%;padding:0.8rem;border-radius:10px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.05);color:#fff;font-size:0.85rem;font-family:inherit;outline:none;resize:vertical;line-height:1.6;"
                        placeholder="Tulis jawabanmu di sini...">{{ $submission->answer_text ?? '' }}</textarea>

                    <div style="display:flex;align-items:center;gap:0.75rem;margin-top:1rem;">
                        <button id="btn-cek"
                            style="padding:0.6rem 1.2rem;border-radius:8px;border:1px solid rgba(255,255,255,0.12);background:transparent;color:#cbd5e1;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:inherit;transition:all 0.2s;"
                            onmouseover="this.style.background='rgba(255,255,255,0.06)'"
                            onmouseout="this.style.background='transparent'">Cek</button>
                        <button id="btn-submit"
                            style="padding:0.6rem 1.2rem;border-radius:8px;border:none;background:linear-gradient(135deg,var(--blue-600),#4f46e5);color:#fff;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:inherit;box-shadow:0 4px 16px rgba(37,99,235,0.3);transition:all 0.2s;">Submit</button>
                    </div>

                    @if ($submission && $submission->ai_feedback)
                        <div id="feedback-box"
                            style="margin-top:1rem;padding:0.8rem 1rem;border-radius:10px;background:rgba(234,179,8,0.1);border:1px solid rgba(234,179,8,0.2);font-size:0.85rem;color:#fde047;">
                            {{ $submission->ai_feedback }}</div>
                    @endif
                </div>
            </div>

            {{-- Virtual Assistant --}}
            <div class="content-card" style="margin-top:1.2rem;">
                <h3
                    style="font-size:0.95rem;font-weight:700;color:#fff;margin-bottom:0.8rem;display:flex;align-items:center;gap:0.5rem;">
                    <svg width="18" height="18" fill="none" stroke="var(--cyan-400)" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Virtual Assistant
                </h3>
                <div id="chat-messages"
                    style="min-height:100px;max-height:220px;overflow-y:auto;padding:0.5rem;background:rgba(0,0,0,0.15);border-radius:10px;margin-bottom:0.8rem;">
                    <p style="font-size:0.8rem;color:#475569;font-style:italic;">Tanyakan sesuatu ke asisten virtual...</p>
                </div>
                <div style="display:flex;gap:0.5rem;">
                    <input type="text" id="chat-input"
                        style="flex:1;padding:0.55rem 0.8rem;border-radius:8px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.05);color:#fff;font-size:0.85rem;font-family:inherit;outline:none;"
                        placeholder="Ketik pertanyaanmu...">
                    <button id="btn-chat"
                        style="padding:0.55rem 0.8rem;border-radius:8px;border:none;background:linear-gradient(135deg,var(--blue-600),#4f46e5);color:#fff;cursor:pointer;display:flex;align-items:center;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('nav_prev')
    @php $runActivity = $chapter->activities->where('stage', 'run')->first(); @endphp
    @if ($runActivity)
        <a href="{{ route('learning.activity', [$chapter, $runActivity]) }}" class="nav-btn nav-btn-prev">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Tahap Run
        </a>
    @else
        <div></div>
    @endif
@endsection

@section('nav_next')
    @php $modifiedActivity = $chapter->activities->where('stage', 'modified')->first(); @endphp
    @if ($modifiedActivity)
        <a href="{{ route('learning.activity', [$chapter, $modifiedActivity]) }}" class="nav-btn nav-btn-next">
            Tahap Modified
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    @else
        <div></div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const activityId = {{ $activity->id }};
            const answerText = document.getElementById('answer-text');
            const sqlEditor = document.getElementById('sql-editor');
            const btnRun = document.getElementById('btn-run');
            const btnCek = document.getElementById('btn-cek');
            const btnSubmit = document.getElementById('btn-submit');
            const sqlOutput = document.getElementById('sql-output');

            if (btnRun && sqlEditor) {
                btnRun.addEventListener('click', async function() {
                    const query = sqlEditor.value.trim();
                    if (!query) {
                        sqlOutput.innerHTML = '<span style="color:#f38ba8;">Query kosong.</span>';
                        return;
                    }
                    btnRun.disabled = true;
                    btnRun.textContent = 'Running...';
                    sqlOutput.innerHTML = '<span style="color:#45475a;">Mengeksekusi...</span>';
                    try {
                        const res = await fetch('{{ route('api.sql.run') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                query
                            }),
                        });
                        const data = await res.json();
                        if (data.success && data.rows.length > 0) {
                            let h = '<table><thead><tr>';
                            data.columns.forEach(c => h += '<th>' + c + '</th>');
                            h += '</tr></thead><tbody>';
                            data.rows.forEach(r => {
                                h += '<tr>';
                                data.columns.forEach(c => h += '<td>' + (r[c] ?? 'NULL') +
                                    '</td>');
                                h += '</tr>';
                            });
                            h += '</tbody></table><p style="margin-top:0.5rem;font-size:0.7rem;color:#6c7086;">' +
                                data.row_count + ' baris</p>';
                            sqlOutput.innerHTML = h;
                        } else if (data.success) {
                            sqlOutput.innerHTML = '<span style="color:#6c7086;">Tidak ada data.</span>';
                        } else {
                            sqlOutput.innerHTML = '<span style="color:#f38ba8;">' + data.error +
                                '</span>';
                        }
                    } catch (e) {
                        sqlOutput.innerHTML = '<span style="color:#f38ba8;">Kesalahan koneksi.</span>';
                    }
                    btnRun.disabled = false;
                    btnRun.textContent = 'Run ▶';
                });
            }

            if (btnCek) {
                btnCek.addEventListener('click', async function() {
                    const text = answerText.value.trim();
                    if (!text) {
                        showFB('Tulis jawabanmu dulu.', 'red');
                        return;
                    }
                    btnCek.disabled = true;
                    btnCek.textContent = 'Memeriksa...';
                    try {
                        const res = await fetch('{{ route('api.submission.check') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                activity_id: activityId,
                                answer_text: text
                            }),
                        });
                        const data = await res.json();
                        if (data.success) {
                            showFB(data.feedback, 'yellow');
                            addChat('assistant', data.feedback);
                        } else {
                            showFB(data.error, 'red');
                        }
                    } catch (e) {
                        showFB('Kesalahan koneksi.', 'red');
                    }
                    btnCek.disabled = false;
                    btnCek.textContent = 'Cek';
                });
            }

            if (btnSubmit) {
                btnSubmit.addEventListener('click', async function() {
                    const text = answerText.value.trim();
                    if (!text) {
                        showFB('Tulis jawabanmu dulu.', 'red');
                        return;
                    }
                    if (!confirm('Yakin ingin submit?')) return;
                    btnSubmit.disabled = true;
                    btnSubmit.textContent = 'Mengirim...';
                    try {
                        const res = await fetch('{{ route('api.submission.submit') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                activity_id: activityId,
                                answer_text: text
                            }),
                        });
                        const data = await res.json();
                        if (data.success && data.is_correct) {
                            showFB('✅ ' + data.feedback + ' (Skor: ' + data.score + '/100)', 'green');
                            addChat('assistant', '✅ ' + data.feedback);
                            btnSubmit.textContent = 'Selesai ✓';
                            btnSubmit.disabled = true;
                            btnSubmit.style.background = '#16a34a';
                            btnSubmit.style.boxShadow = 'none';
                        } else if (data.success) {
                            showFB('⚠️ ' + data.feedback + ' Perbaiki jawabanmu.', 'yellow');
                            addChat('assistant', '⚠️ ' + data.feedback);
                            btnSubmit.disabled = false;
                            btnSubmit.textContent = 'Submit';
                        } else {
                            showFB(data.error, 'red');
                            btnSubmit.disabled = false;
                            btnSubmit.textContent = 'Submit';
                        }
                    } catch (e) {
                        showFB('Kesalahan koneksi.', 'red');
                        btnSubmit.disabled = false;
                        btnSubmit.textContent = 'Submit';
                    }
                });
            }

            function showFB(msg, color) {
                let box = document.getElementById('feedback-box');
                if (!box) {
                    box = document.createElement('div');
                    box.id = 'feedback-box';
                    document.querySelector('div[style*="border-top"]').appendChild(box);
                }
                const c = {
                    red: 'background:rgba(248,113,113,0.1);border:1px solid rgba(248,113,113,0.2);color:#fca5a5;',
                    yellow: 'background:rgba(234,179,8,0.1);border:1px solid rgba(234,179,8,0.2);color:#fde047;',
                    green: 'background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);color:#86efac;'
                };
                box.style.cssText = 'margin-top:1rem;padding:0.8rem;border-radius:10px;font-size:0.85rem;' + (c[
                    color] || c.yellow);
                box.textContent = msg;
            }

            function addChat(role, msg) {
                const cb = document.getElementById('chat-messages');
                if (!cb) return;
                const p = cb.querySelector('p[style*="italic"]');
                if (p) p.remove();
                const d = document.createElement('div');
                d.style.cssText =
                    'margin-bottom:0.6rem;padding:0.5rem 0.7rem;border-radius:8px;font-size:0.8rem;line-height:1.5;max-width:90%;' +
                    (role === 'assistant' ? 'background:rgba(37,99,235,0.15);color:var(--blue-300);' :
                        'background:rgba(255,255,255,0.06);color:#cbd5e1;margin-left:auto;');
                d.textContent = msg;
                cb.appendChild(d);
                cb.scrollTop = cb.scrollHeight;
            }

            @if ($submission && $submission->is_correct)
                btnSubmit.textContent = 'Selesai ✓';
                btnSubmit.disabled = true;
                btnSubmit.style.background = '#16a34a';
                btnSubmit.style.boxShadow = 'none';
            @endif
        });
    </script>
@endpush
