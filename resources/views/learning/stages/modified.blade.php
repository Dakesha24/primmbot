@extends('layouts.learning')

@section('content')
    <style>
        .main-inner {
            max-width: 100% !important;
            padding: 2rem 1.5rem !important;
        }

        .mod-layout {
            display: grid;
            grid-template-columns: 2fr 3fr;
            gap: 1.2rem;
            align-items: start;
        }

        .mod-left {
            min-width: 0;
        }

        .mod-right {
            min-width: 0;
        }

        .db-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 1.2rem;
            margin-bottom: 1rem;
        }

        .db-table {
            margin-bottom: 1rem;
        }

        .db-table-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--cyan-400);
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .db-table-scroll {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 250px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 8px;
        }

        .db-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.78rem;
            white-space: nowrap;
        }

        .db-table th {
            background: rgba(255, 255, 255, 0.05);
            color: #94a3b8;
            font-weight: 600;
            padding: 0.45rem 0.6rem;
            text-align: left;
            border: 1px solid rgba(255, 255, 255, 0.08);
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .db-table td {
            padding: 0.4rem 0.6rem;
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .db-table tr:nth-child(even) td {
            background: rgba(255, 255, 255, 0.02);
        }

        .erd-container {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            padding: 0.8rem;
            margin-top: 0.5rem;
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

        .editor-wrap {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 10px;
            overflow: hidden;
            margin: 1rem 0;
        }

        .editor-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.4rem 0.7rem;
            background: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .editor-bar-title {
            font-size: 0.7rem;
            font-weight: 600;
            color: #64748b;
        }

        .editor-bar-btns {
            display: flex;
            gap: 0.4rem;
        }

        .btn-editor {
            padding: 0.3rem 0.6rem;
            border-radius: 5px;
            border: none;
            font-size: 0.7rem;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.15s;
        }

        .btn-run {
            background: #a6e3a1;
            color: #0a1628;
        }

        .btn-run:hover {
            background: #94e2d5;
        }

        .btn-reset {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
        }

        .btn-reset:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
        }

        .editor-code {
            width: 100%;
            min-height: 100px;
            padding: 0.8rem;
            border: none;
            outline: none;
            resize: vertical;
            background: transparent;
            color: #a6e3a1;
            font-family: 'Courier New', monospace;
            font-size: 0.83rem;
            line-height: 1.7;
        }

        .editor-code::placeholder {
            color: #475569;
        }

        .editor-output {
            padding: 0.8rem;
            min-height: 60px;
            color: #cdd6f4;
            font-family: 'Courier New', monospace;
            font-size: 0.78rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .editor-output table {
            width: 100%;
            border-collapse: collapse;
        }

        .editor-output th {
            background: rgba(255, 255, 255, 0.05);
            color: #94a3b8;
            font-weight: 600;
            padding: 0.4rem 0.5rem;
            text-align: left;
            border: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.72rem;
        }

        .editor-output td {
            padding: 0.35rem 0.5rem;
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 0.78rem;
        }

        .editor-output tr:nth-child(even) td {
            background: rgba(255, 255, 255, 0.02);
        }

        @media (max-width: 768px) {
            .mod-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <h1 style="text-align:center;font-size:1.4rem;font-weight:800;color:#fff;margin-bottom:1.5rem;">MODIFIED</h1>

    <div class="mod-layout">

        {{-- KIRI: Tabel Database + ERD --}}
        <div class="mod-left">
            <div class="db-card">
                <h3
                    style="font-size:0.9rem;font-weight:700;color:#fff;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
                    <svg width="16" height="16" fill="none" stroke="var(--cyan-400)" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                    </svg>
                    Database
                </h3>
                <div id="tables-container">
                    <p style="font-size:0.8rem;color:#475569;font-style:italic;">Memuat tabel...</p>
                </div>

                {{-- ERD --}}
                <div class="erd-container">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">
                        <p
                            style="font-size:0.7rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">
                            Relasi Tabel</p>
                        <button onclick="document.getElementById('erdModal').style.display='flex'"
                            style="padding:0.25rem 0.5rem;border-radius:5px;border:1px solid rgba(255,255,255,0.1);background:transparent;color:#64748b;font-size:0.65rem;cursor:pointer;display:flex;align-items:center;gap:0.3rem;transition:all 0.2s;"
                            onmouseover="this.style.color='#fff';this.style.borderColor='rgba(255,255,255,0.25)'"
                            onmouseout="this.style.color='#64748b';this.style.borderColor='rgba(255,255,255,0.1)'">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                            </svg>
                            Fullscreen
                        </button>
                    </div>
                    <div id="erd-small" style="text-align:center;"></div>
                </div>
            </div>
        </div>

        {{-- Modal ERD Fullscreen --}}
        <div id="erdModal"
            style="display:none;position:fixed;inset:0;z-index:100;background:rgba(0,0,0,0.7);backdrop-filter:blur(8px);align-items:center;justify-content:center;"
            onclick="if(event.target===this)this.style.display='none'">
            <div
                style="background:linear-gradient(135deg,#0f2044,#142c5c);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:2rem;max-width:700px;width:90%;max-height:85vh;overflow:auto;position:relative;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
                    <h3 style="font-size:1.1rem;font-weight:700;color:#fff;">Relasi Tabel</h3>
                    <button onclick="document.getElementById('erdModal').style.display='none'"
                        style="width:32px;height:32px;border-radius:8px;border:1px solid rgba(255,255,255,0.1);background:transparent;color:#94a3b8;cursor:pointer;display:flex;align-items:center;justify-content:center;"
                        onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#94a3b8'">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div id="erd-large" style="text-align:center;"></div>
            </div>
        </div>

        {{-- KANAN: Card Soal + Card VA --}}
        <div class="mod-right">
            {{-- Card Soal --}}
            <div class="content-card">
                {{-- Header: Level + Nav --}}
                <div
                    style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:0.6rem;">
                    <div class="level-tabs">
                        @php
                            $levelMap = ['mudah' => 'Level 1', 'sedang' => 'Level 2', 'tantang' => 'Level 3'];
                            $allModActivities = $chapter->activities
                                ->where('stage', 'modified')
                                ->sortBy('order')
                                ->values();
                        @endphp
                        @foreach ($levelMap as $levelKey => $levelLabel)
                            @php
                                $levelActs = $allModActivities->where('level', $levelKey);
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
                            <span class="q-nav-btn" disabled><svg width="14" height="14" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg></span>
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
                            <span class="q-nav-btn" disabled><svg width="14" height="14" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg></span>
                        @endif
                    </div>
                </div>

                {{-- Pertanyaan Perintah SQL --}}
                <h3
                    style="font-size:0.95rem;font-weight:700;color:#fff;margin-bottom:0.6rem;display:flex;align-items:center;gap:0.5rem;">
                    <svg width="16" height="16" fill="none" stroke="var(--blue-400)" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                    {{ $levelMap[$activity->level] ?? 'Level' }} — Perintah
                </h3>
                <p style="font-size:0.9rem;color:#94a3b8;line-height:1.7;">{{ $activity->question_text }}</p>

                {{-- Code Editor --}}
                <div class="editor-wrap">
                    <div class="editor-bar">
                        <span class="editor-bar-title">SQL Editor</span>
                        <div class="editor-bar-btns">
                            <button id="btn-reset" class="btn-editor btn-reset"
                                style="display:flex;align-items:center;gap:0.3rem;">
                                <svg width="12" height="12" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 4v5h5M20 20v-5h-5M4 9a9 9 0 0115.36-6.36M20 15a9 9 0 01-15.36 6.36" />
                                </svg>
                                Reset
                            </button>
                            <button id="btn-run" class="btn-editor btn-run">Run ▶</button>
                        </div>
                    </div>
                    <textarea id="sql-editor" class="editor-code" spellcheck="false" placeholder="Tulis query SQL...">{{ $submission->answer_code ?? ($activity->editor_default_code ?? '') }}</textarea>
                    <div id="sql-output" class="editor-output">
                        <span style="color:#475569;font-style:italic;">Memuat output...</span>
                    </div>
                </div>

                {{-- Pertanyaan Penjelasan --}}
                @if ($activity->description)
                    <div style="border-top:1px solid rgba(255,255,255,0.06);padding-top:1.2rem;margin-top:0.5rem;">
                        <h3
                            style="font-size:0.95rem;font-weight:700;color:#fff;margin-bottom:0.6rem;display:flex;align-items:center;gap:0.5rem;">
                            <svg width="16" height="16" fill="none" stroke="var(--cyan-400)" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Pertanyaan Penjelasan
                        </h3>
                        <p style="font-size:0.9rem;color:#94a3b8;line-height:1.7;">{{ $activity->description }}</p>
                    </div>
                @endif


                {{-- Jawab --}}
                <div style="border-top:1px solid rgba(255,255,255,0.06);padding-top:1.2rem;">
                    <h4 style="font-size:0.9rem;font-weight:700;color:#fff;margin-bottom:0.6rem;">Jawab</h4>
                    <textarea id="answer-text" rows="4"
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

            {{-- Card Virtual Assistant --}}
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

    {{-- Mermaid.js --}}
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <script>
        mermaid.initialize({
            startOnLoad: false,
            theme: 'dark',
            themeVariables: {
                primaryColor: '#1e3a5f',
                primaryTextColor: '#cbd5e1',
                lineColor: '#60a5fa',
                primaryBorderColor: '#2563eb'
            }
        });

        const erdCode = `erDiagram
    penerbit ||--o{ buku : id_penerbit
    penulis ||--o{ buku : id_penulis
    buku {
        string id_buku
        string judul_buku
        string id_penerbit
        string id_penulis
    }
    penerbit {
        string id_penerbit
        string nama_penerbit
    }
    penulis {
        string id_penulis
        string nama_penulis
    }`;

        async function renderErd(elementId) {
            const el = document.getElementById(elementId);
            if (!el) return;
            const {
                svg
            } = await mermaid.render(elementId + '-svg', erdCode);
            el.innerHTML = svg;
        }

        renderErd('erd-small');

        document.getElementById('erdModal').addEventListener('transitionend', () => renderErd('erd-large'));
        const origOpen = document.getElementById('erdModal').style;
        const erdBtn = document.querySelector('[onclick*="erdModal"]');
        erdBtn.addEventListener('click', () => setTimeout(() => renderErd('erd-large'), 100));
    </script>
@endsection

@section('nav_prev')
    @php $investigateActivity = $chapter->activities->where('stage', 'investigate')->sortByDesc('order')->first(); @endphp
    @if ($investigateActivity)
        <a href="{{ route('learning.activity', [$chapter, $investigateActivity]) }}" class="nav-btn nav-btn-prev">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Tahap Investigate
        </a>
    @else
        <div></div>
    @endif
@endsection

@section('nav_next')
    @php $makeActivity = $chapter->activities->where('stage', 'make')->first(); @endphp
    @if ($makeActivity)
        <a href="{{ route('learning.activity', [$chapter, $makeActivity]) }}" class="nav-btn nav-btn-next">
            Tahap Make
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
            const defaultCode = @json($activity->editor_default_code ?? '');
            const sandboxTables = @json($sandboxTables ?? []); // Data tabel dari controller

            const answerText = document.getElementById('answer-text');
            const sqlEditor = document.getElementById('sql-editor');
            const btnRun = document.getElementById('btn-run');
            const btnReset = document.getElementById('btn-reset');
            const btnCek = document.getElementById('btn-cek');
            const btnSubmit = document.getElementById('btn-submit');
            const sqlOutput = document.getElementById('sql-output');

            loadTables();

            if (sqlEditor.value.trim()) runQuery();
            sqlEditor.focus();
            sqlEditor.setSelectionRange(sqlEditor.value.length, sqlEditor.value.length);

            btnReset.addEventListener('click', function() {
                sqlEditor.value = defaultCode;
                runQuery();
            });

            btnRun.addEventListener('click', runQuery);

            async function runQuery() {
                const query = sqlEditor.value.trim();
                if (!query) {
                    sqlOutput.innerHTML = '<span style="color:#f87171;">Query kosong.</span>';
                    return;
                }

                btnRun.disabled = true;
                btnRun.textContent = 'Running...';
                sqlOutput.innerHTML = '<span style="color:#475569;">Mengeksekusi...</span>';

                try {
                    const res = await fetch('{{ route('api.sql.run') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            query: query,
                            // Kirim ID database untuk keperluan validasi keamanan di backend
                            database_id: {{ $activity->sandbox_database_id ?? 'null' }}
                        }),
                    });
                    const data = await res.json();

                    if (data.success && data.rows.length > 0) {
                        let h = '<table><thead><tr>';
                        data.columns.forEach(c => h += '<th>' + c + '</th>');
                        h += '</tr></thead><tbody>';
                        data.rows.forEach(r => {
                            h += '<tr>';
                            data.columns.forEach(c => h += '<td>' + (r[c] ?? 'NULL') + '</td>');
                            h += '</tr>';
                        });
                        h +=
                        `</tbody></table><p style="margin-top:0.4rem;font-size:0.68rem;color:#64748b;">${data.row_count} baris</p>`;
                        sqlOutput.innerHTML = h;
                    } else if (data.success) {
                        sqlOutput.innerHTML = '<span style="color:#64748b;">Tidak ada data.</span>';
                    } else {
                        sqlOutput.innerHTML = '<span style="color:#f87171;">' + data.error + '</span>';
                    }
                } catch (e) {
                    sqlOutput.innerHTML = '<span style="color:#f87171;">Kesalahan koneksi.</span>';
                }
                btnRun.disabled = false;
                btnRun.textContent = 'Run ▶';
            }

            async function loadTables() {
                const container = document.getElementById('tables-container');
                const tableKeys = Object.keys(sandboxTables);

                if (tableKeys.length === 0) {
                    container.innerHTML =
                        '<p style="font-size:0.8rem;color:#475569;">Tidak ada tabel terkait.</p>';
                    return;
                }

                // Render struktur tabel HTML awal
                let h = '';
                for (const [displayName, data] of Object.entries(sandboxTables)) {
                    h += `<div class="db-table" id="table-view-${displayName}">
                        <p class="db-table-title">${displayName}</p>
                        <div class="db-table-scroll">
                            <table id="table-data-${displayName}">
                                <thead><tr>`;
                    data.columns.forEach(col => h += `<th>${col.name}</th>`);
                    h += `</tr></thead><tbody><tr><td colspan="${data.columns.length}" style="text-align:center; font-style:italic; color:#64748b;">Memuat data...</td></tr></tbody>
                            </table>
                        </div>
                      </div>`;
                }
                container.innerHTML = h;

                // Fetch data per tabel secara asinkron
                for (const [displayName, data] of Object.entries(sandboxTables)) {
                    try {
                        const res = await fetch('{{ route('api.sql.run') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                query: `SELECT * FROM \`${data.real_name}\` LIMIT 10`,
                                database_id: {{ $activity->sandbox_database_id ?? 'null' }}
                            }),
                        });

                        const d2 = await res.json();
                        const tbody = document.querySelector(`#table-data-${displayName} tbody`);

                        if (d2.success && d2.rows.length > 0) {
                            let tbodyHtml = '';
                            d2.rows.forEach(r => {
                                tbodyHtml += '<tr>';
                                data.columns.forEach(col => {
                                    tbodyHtml += `<td>${r[col.name] ?? 'NULL'}</td>`;
                                });
                                tbodyHtml += '</tr>';
                            });
                            tbody.innerHTML = tbodyHtml;
                        } else if (d2.success) {
                            tbody.innerHTML =
                                `<tr><td colspan="${data.columns.length}" style="text-align:center; color:#64748b;">Tabel kosong.</td></tr>`;
                        } else {
                            tbody.innerHTML =
                                `<tr><td colspan="${data.columns.length}" style="color:#f87171;">Gagal memuat.</td></tr>`;
                        }
                    } catch (e) {
                        const tbody = document.querySelector(`#table-data-${displayName} tbody`);
                        if (tbody) tbody.innerHTML =
                            `<tr><td colspan="${data.columns.length}" style="color:#f87171;">Error koneksi.</td></tr>`;
                    }
                }
            }

            // Generate script Mermaid ERD secara dinamis berdasarkan kolom
            function generateDynamicErd() {
                const tableKeys = Object.keys(sandboxTables);
                if (tableKeys.length === 0) return "erDiagram\n    Database_Kosong";

                let code = "erDiagram\n";

                // Generate entitas dan atribut
                for (const [displayName, data] of Object.entries(sandboxTables)) {
                    const safeName = displayName.replace(/\s+/g, '_');
                    code += `    ${safeName} {\n`;
                    data.columns.forEach(col => {
                        let safeType = col.type.split('(')[0];
                        code += `        ${safeType} ${col.name}\n`;
                    });
                    code += `    }\n`;
                }

                // Generate relasi sederhana berdasarkan penamaan 'id_tabel'
                for (const [displayName, data] of Object.entries(sandboxTables)) {
                    const safeName = displayName.replace(/\s+/g, '_');
                    data.columns.forEach(col => {
                        if (col.key === 'MUL' || col.name.startsWith('id_')) {
                            const targetName = col.name.replace('id_', '');
                            const hasTarget = tableKeys.find(k => k.toLowerCase() === targetName
                                .toLowerCase());
                            if (hasTarget) {
                                const safeTarget = hasTarget.replace(/\s+/g, '_');
                                code += `    ${safeTarget} ||--o{ ${safeName} : ${col.name}\n`;
                            }
                        }
                    });
                }
                return code;
            }

            // --- MERMAID INITIALIZATION ---
            // Hapus atau abaikan const erdCode = `erDiagram...` yang lama
            const dynamicErdCode = generateDynamicErd();

            async function renderErd(elementId) {
                const el = document.getElementById(elementId);
                if (!el) return;
                try {
                    const {
                        svg
                    } = await mermaid.render(elementId + '-svg', dynamicErdCode);
                    el.innerHTML = svg;
                } catch (err) {
                    el.innerHTML = '<span style="color:#f87171">Gagal memuat ERD</span>';
                }
            }

            renderErd('erd-small');

            document.getElementById('erdModal').addEventListener('transitionend', () => renderErd('erd-large'));
            const erdBtn = document.querySelector('[onclick*="erdModal"]');
            if (erdBtn) {
                erdBtn.addEventListener('click', () => setTimeout(() => renderErd('erd-large'), 100));
            }

            // CEK & SUBMIT LOGIC (Tidak ada perubahan signifikan, disederhanakan)
            btnCek.addEventListener('click', async function() {
                const text = answerText ? answerText.value.trim() : '';
                const code = sqlEditor.value.trim();
                if (!text && !code) return showFB('Tulis jawabanmu dulu.', 'red');

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
                            answer_text: text,
                            answer_code: code
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

            btnSubmit.addEventListener('click', async function() {
                const text = answerText ? answerText.value.trim() : '';
                const code = sqlEditor.value.trim();
                if (!text && !code) return showFB('Tulis jawabanmu dulu.', 'red');
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
                            answer_text: text,
                            answer_code: code
                        }),
                    });
                    const data = await res.json();
                    if (data.success && data.is_correct) {
                        showFB('✅ ' + data.feedback + ' (Skor: ' + data.score + '/100)', 'green');
                        addChat('assistant', '✅ ' + data.feedback);
                        btnSubmit.textContent = 'Selesai ✓';
                        btnSubmit.style.background = '#16a34a';
                    } else if (data.success) {
                        showFB('⚠️ ' + data.feedback, 'yellow');
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

            function showFB(msg, color) {
                let box = document.getElementById('feedback-box');
                if (!box) {
                    box = document.createElement('div');
                    box.id = 'feedback-box';
                    // Attach above the VA card
                    document.querySelector('.mod-right').insertBefore(box, document.querySelector(
                        '.content-card:last-child'));
                }
                const c = {
                    red: 'background:rgba(248,113,113,0.1);border:1px solid rgba(248,113,113,0.2);color:#fca5a5;',
                    yellow: 'background:rgba(234,179,8,0.1);border:1px solid rgba(234,179,8,0.2);color:#fde047;',
                    green: 'background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);color:#86efac;'
                };
                box.style.cssText = 'margin-bottom:1rem;padding:0.8rem;border-radius:10px;font-size:0.85rem;' + (c[
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
            @endif
        });
    </script>
@endpush
