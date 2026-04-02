@extends('layouts.learning')

@section('content')
    <style>
        .main-inner {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            height: calc(100vh - 56px);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .chat-widget { border-radius:14px; border:1px solid rgba(255,255,255,0.08); overflow:hidden; background:rgba(255,255,255,0.02); }
        .chat-header { padding:0.7rem 1rem; background:rgba(37,99,235,0.15); border-bottom:1px solid rgba(255,255,255,0.06); display:flex; align-items:center; gap:0.7rem; }
        .chat-bot-icon { width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg,var(--blue-600),#4f46e5); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .chat-body { height:280px; overflow-y:auto; padding:1rem; display:flex; flex-direction:column; gap:0.75rem; }
        .chat-body::-webkit-scrollbar { width:4px; }
        .chat-body::-webkit-scrollbar-track { background:transparent; }
        .chat-body::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.1); border-radius:4px; }
        .chat-row { display:flex; align-items:flex-end; gap:0.5rem; }
        .chat-row.user-row { flex-direction:row-reverse; }
        .chat-avatar-small { width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg,var(--blue-600),#4f46e5); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .chat-bubble { max-width:78%; padding:0.6rem 0.85rem; font-size:0.8rem; line-height:1.55; word-break:break-word; }
        .bot-bubble { background:rgba(37,99,235,0.18); color:#93c5fd; border-radius:14px 14px 14px 4px; }
        .user-bubble { background:rgba(255,255,255,0.08); color:#e2e8f0; border-radius:14px 14px 4px 14px; white-space:pre-wrap; }
        #btn-submit { padding:0.6rem 1.2rem; border-radius:8px; border:none; background:linear-gradient(135deg,var(--blue-600),#4f46e5); color:#fff; font-size:0.85rem; font-weight:600; cursor:pointer; font-family:inherit; box-shadow:0 4px 16px rgba(37,99,235,0.3); transition:all 0.2s; }
        #btn-submit:disabled:not(.btn-done) { background:rgba(20,20,30,0.9) !important; color:#475569 !important; cursor:not-allowed !important; box-shadow:none !important; border:1px solid rgba(255,255,255,0.08) !important; pointer-events:none !important; }
        #btn-submit.btn-done { background:linear-gradient(135deg,#16a34a,#15803d) !important; color:#fff !important; cursor:default !important; pointer-events:none !important; }
        .chat-typing { display:flex; gap:5px; align-items:center; padding:0.5rem 0.75rem; }
        .chat-typing span { width:7px; height:7px; border-radius:50%; background:#60a5fa; animation:typingBounce 1.2s infinite; }
        .chat-typing span:nth-child(2) { animation-delay:0.2s; }
        .chat-typing span:nth-child(3) { animation-delay:0.4s; }
        @keyframes typingBounce { 0%,60%,100%{ transform:translateY(0); opacity:0.4; } 30%{ transform:translateY(-6px); opacity:1; } }
        .chat-footer { padding:0.6rem; border-top:1px solid rgba(255,255,255,0.06); display:flex; gap:0.5rem; background:rgba(0,0,0,0.12); }
        .chat-input { flex:1; padding:0.5rem 0.85rem; border-radius:20px; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.05); color:#fff; font-size:0.82rem; font-family:inherit; outline:none; transition:border-color 0.2s; }
        .chat-input:focus { border-color:rgba(37,99,235,0.5); }
        .chat-send-btn { width:36px; height:36px; border-radius:50%; border:none; background:linear-gradient(135deg,var(--blue-600),#4f46e5); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:opacity 0.2s; }
        .chat-send-btn:disabled { opacity:0.45; cursor:not-allowed; }

        .inv-header {
            padding: 0.6rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            flex-shrink: 0;
            background: rgba(10,22,40,0.5);
            backdrop-filter: blur(8px);
        }

        .inv-layout {
            flex: 1;
            display: grid;
            grid-template-columns: 2fr 3fr;
            gap: 0;
            overflow: hidden;
            min-height: 0;
        }

        .inv-left {
            overflow-y: auto;
            padding: 1.4rem 1.5rem;
            border-right: 1px solid rgba(255,255,255,0.06);
            min-width: 0;
        }

        .inv-right {
            overflow-y: auto;
            padding: 1.4rem 1.5rem;
            min-width: 0;
        }

        .inv-left::-webkit-scrollbar,
        .inv-right::-webkit-scrollbar { width: 7px; }
        .inv-left::-webkit-scrollbar-track,
        .inv-right::-webkit-scrollbar-track { background: rgba(255,255,255,0.03); }
        .inv-left::-webkit-scrollbar-thumb,
        .inv-right::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.18); border-radius: 6px; }
        .inv-left::-webkit-scrollbar-thumb:hover,
        .inv-right::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.32); }

        .inv-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.6rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.07);
            background: rgba(10,22,40,0.5);
            backdrop-filter: blur(8px);
            flex-shrink: 0;
        }

        .inv-nav .nav-btn {
            padding: 0.45rem 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 8px;
            gap: 0.35rem;
        }

        .inv-nav .nav-btn-prev {
            color: #94a3b8;
            border-color: rgba(255,255,255,0.18);
        }

        .inv-nav .nav-btn-prev:hover {
            color: #e2e8f0;
            background: rgba(255,255,255,0.06);
            border-color: rgba(255,255,255,0.28);
        }

        .inv-nav .nav-btn-next {
            color: #cbd5e1;
            background: rgba(100,116,139,0.25);
            border: 1px solid rgba(148,163,184,0.3);
            box-shadow: none;
        }

        .inv-nav .nav-btn-next:hover {
            color: #fff;
            background: rgba(100,116,139,0.38);
            border-color: rgba(148,163,184,0.5);
            transform: none;
            box-shadow: none;
        }

        @media (max-width: 900px) {
            .inv-layout { grid-template-columns: 1fr; overflow-y: auto; }
            .inv-left { border-right: none; border-bottom: 1px solid rgba(255,255,255,0.06); }
        }

        .editor-wrap {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 10px;
            overflow: hidden;
            margin: 1rem 0;
        }

        .editor-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.4rem 0.7rem;
            background: rgba(255,255,255,0.02);
            border-bottom: 1px solid rgba(255,255,255,0.06);
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
            border: 1px solid rgba(255,255,255,0.1);
            color: #94a3b8;
        }

        .btn-reset:hover {
            background: rgba(255,255,255,0.05);
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
            border-top: 1px solid rgba(255,255,255,0.06);
            overflow-x: auto;
        }

        .editor-output table {
            width: max-content;
            min-width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }

        .editor-output th {
            background: rgba(255,255,255,0.05);
            color: #94a3b8;
            font-weight: 600;
            padding: 0.4rem 0.5rem;
            text-align: left;
            border: 1px solid rgba(255,255,255,0.08);
            font-size: 0.72rem;
        }

        .editor-output td {
            padding: 0.35rem 0.5rem;
            color: #cbd5e1;
            border: 1px solid rgba(255,255,255,0.06);
            font-size: 0.78rem;
        }

        .editor-output tr:nth-child(even) td {
            background: rgba(255,255,255,0.02);
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
            border-radius: 0;
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

    </style>

    <div class="inv-header">
        <h1 style="font-size:1.05rem;font-weight:800;color:#fff;letter-spacing:0.06em;text-transform:uppercase;">Investigate</h1>
        <button onclick="document.getElementById('help-modal').style.display='flex'" style="width:20px;height:20px;border-radius:50%;border:1px solid rgba(255,255,255,0.18);background:rgba(255,255,255,0.06);color:#94a3b8;font-size:0.7rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.14)';this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,0.06)';this.style.color='#94a3b8'">?</button>
    </div>
    <div id="help-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);z-index:1000;align-items:center;justify-content:center;" onclick="if(event.target===this)this.style.display='none'">
        <div style="background:#0d1f3a;border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:1.5rem 1.75rem;max-width:460px;width:90%;position:relative;">
            <button onclick="document.getElementById('help-modal').style.display='none'" style="position:absolute;top:0.9rem;right:1rem;background:transparent;border:none;color:#64748b;font-size:1.1rem;cursor:pointer;line-height:1;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#64748b'">✕</button>
            <h3 style="font-size:0.95rem;font-weight:700;color:#fff;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.5rem;">
                <span style="background:rgba(37,99,235,0.2);color:var(--blue-400);padding:0.2rem 0.7rem;border-radius:6px;font-size:0.8rem;">INVESTIGATE</span>
                Petunjuk Tahap
            </h3>
            <ul style="font-size:0.85rem;color:#94a3b8;line-height:1.8;padding-left:1.2rem;">
                <li>Coba modifikasi kode SQL di editor dan klik <strong style="color:#cbd5e1;">Run ▶</strong> untuk melihat hasilnya</li>
                <li>Analisis bagaimana query bekerja berdasarkan perubahan yang kamu buat</li>
                <li>Jawab pertanyaan analisis di kolom jawaban</li>
                <li>Klik <strong style="color:#cbd5e1;">Submit</strong> untuk mendapat umpan balik dari AI</li>
            </ul>
        </div>
    </div>

    <div class="inv-layout">

        {{-- KIRI: Code Editor + Output --}}
        <div class="inv-left">
            @if ($activity->code_snippet)
                <div class="editor-wrap">
                    <div class="editor-bar">
                        <span class="editor-bar-title">SQL Editor</span>
                        <div class="editor-bar-btns">
                            <button id="btn-reset" class="btn-editor btn-reset" style="display:flex;align-items:center;gap:0.3rem;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h5M20 20v-5h-5M4 9a9 9 0 0115.36-6.36M20 15a9 9 0 01-15.36 6.36"/>
                                </svg>
                                Reset
                            </button>
                            <button id="btn-run" class="btn-editor btn-run">Run ▶</button>
                        </div>
                    </div>
                    <textarea id="sql-editor" class="editor-code" spellcheck="false">{{ $activity->code_snippet }}</textarea>
                    <div id="sql-output" class="editor-output">
                        <span style="color:#475569;font-style:italic;">Klik Run untuk melihat hasil...</span>
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

            @if (!empty($sandboxTables))
                <div class="db-card" style="margin-top:0.8rem;">
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

                {{-- Modal ERD --}}
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
                        <div id="score-display" style="flex:1;font-size:0.82rem;line-height:1.5;"></div>
                        <button id="btn-submit">Submit</button>
                    </div>

                    @php $navStyle = "display:inline-flex;align-items:center;gap:0.5rem;padding:0.55rem 1.2rem;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.15);border-radius:10px;color:#cbd5e1;font-size:0.85rem;font-weight:600;text-decoration:none;transition:all 0.2s;"; @endphp
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:1.25rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,0.06);">
                        @if ($prevActivity)
                            <a href="{{ route('learning.activity', [$chapter, $prevActivity]) }}" style="{{ $navStyle }}"
                                onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,0.05)';this.style.color='#cbd5e1'">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                Soal Sebelumnya
                            </a>
                        @else
                            <div></div>
                        @endif
                        @if ($nextActivity)
                            @if ($canProceedWithinStage)
                                <a href="{{ route('learning.activity', [$chapter, $nextActivity]) }}" style="{{ $navStyle }}"
                                    onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,0.05)';this.style.color='#cbd5e1'">
                                    Soal Berikutnya
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            @else
                                <span onclick="showStageGateAlert('Selesaikan soal ini terlebih dahulu.')"
                                    style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.55rem 1.2rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:10px;color:#475569;font-size:0.85rem;font-weight:600;cursor:not-allowed;">
                                    Soal Berikutnya
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </span>
                            @endif
                        @else
                            <div></div>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Feedback Guru --}}
            @if ($teacherReview)
            <div style="margin-bottom:1rem; border-radius:12px; border:1px solid rgba(251,191,36,0.25); background:rgba(251,191,36,0.05); padding:1rem 1.1rem;">
                <div style="font-size:10px; font-weight:800; color:#fbbf24; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.5rem;">Feedback Guru</div>
                @if ($teacherReview->score !== null)
                    <div style="margin-bottom:0.4rem;"><span style="font-size:12px; font-weight:700; color:#fbbf24; background:rgba(251,191,36,0.1); padding:2px 8px; border-radius:4px;">Skor Koreksi: {{ $teacherReview->score }}/100</span></div>
                @endif
                @if ($teacherReview->feedback)
                    <div style="font-size:13px; color:#e2e8f0; line-height:1.55; white-space:pre-wrap;">{{ $teacherReview->feedback }}</div>
                @endif
            </div>
            @endif

            {{-- Virtual Assistant --}}
            <div class="chat-widget" style="margin-top:1.2rem;">
                <div class="chat-header">
                    <div class="chat-bot-icon">
                        <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1 1 .03 2.7-1.388 2.7H4.186c-1.418 0-2.389-1.7-1.388-2.7L4.2 15.3"/></svg>
                    </div>
                    <div>
                        <div style="font-size:0.85rem;font-weight:700;color:#fff;line-height:1.2;">PRIMM Bot</div>
                        <div style="font-size:0.7rem;color:#4ade80;display:flex;align-items:center;gap:0.3rem;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#4ade80;display:inline-block;"></span>Online
                        </div>
                    </div>
                </div>
                <div id="chat-messages" class="chat-body">
                    @if ($chatLogs->isEmpty() && !($submission && $submission->ai_feedback))
                    <div class="chat-row">
                        <div class="chat-avatar-small"><svg width="13" height="13" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1 1 .03 2.7-1.388 2.7H4.186c-1.418 0-2.389-1.7-1.388-2.7L4.2 15.3"/></svg></div>
                        <div class="chat-bubble bot-bubble">Halo! Saya PRIMM Bot 👋 Di tahap Investigate, kita analisis query lebih dalam. Kalau ada bagian query yang belum kamu pahami, langsung tanyakan ke sini ya!</div>
                    </div>
                    @else
                    @foreach ($chatLogs as $log)
                    <div class="chat-row user-row">
                        <div class="chat-bubble user-bubble" style="white-space:pre-wrap;">{{ $log->prompt_sent }}</div>
                    </div>
                    <div class="chat-row">
                        <div class="chat-avatar-small"><svg width="13" height="13" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1 1 .03 2.7-1.388 2.7H4.186c-1.418 0-2.389-1.7-1.388-2.7L4.2 15.3"/></svg></div>
                        <div class="chat-bubble bot-bubble">{{ $log->response_received }}</div>
                    </div>
                    @endforeach
                    @if ($submission && $submission->ai_feedback)
                    <div class="chat-row user-row">
                        <div class="chat-bubble user-bubble" style="white-space:pre-wrap;">Submit jawaban:
{{ $submission->answer_text }}</div>
                    </div>
                    <div class="chat-row">
                        <div class="chat-avatar-small"><svg width="13" height="13" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1 1 .03 2.7-1.388 2.7H4.186c-1.418 0-2.389-1.7-1.388-2.7L4.2 15.3"/></svg></div>
                        <div class="chat-bubble bot-bubble">{{ $submission->ai_feedback }}<br><small style="opacity:0.65;font-size:11px;">Keruntutan: {{ $submission->score_keruntutan }} | Argumen: {{ $submission->score_berargumen }} | Kesimpulan: {{ $submission->score_kesimpulan }} → Total: {{ $submission->score }}/100</small></div>
                    </div>
                    @endif
                    @endif
                </div>
                <div class="chat-footer">
                    <input type="text" id="chat-input" class="chat-input" placeholder="Ketik pertanyaanmu...">
                    <button id="btn-chat" class="chat-send-btn">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Nav bar --}}
    <div class="inv-nav">
        @php $runActivity = $chapter->activities->where('stage', 'run')->first(); @endphp
        @if($runActivity)
            <a href="{{ route('learning.activity', [$chapter, $runActivity]) }}" class="nav-btn nav-btn-prev">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Tahap Run
            </a>
        @else
            <div></div>
        @endif
        @php $modifyActivity = $chapter->activities->where('stage', 'modify')->first(); @endphp
        @if($modifyActivity)
            @if($canProceedToNextStage)
                <a href="{{ route('learning.activity', [$chapter, $modifyActivity]) }}" class="nav-btn nav-btn-next">
                    Tahap Modify
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <button class="nav-btn nav-btn-next" style="opacity:0.4;cursor:not-allowed;"
                    onclick="showStageGateAlert('Selesaikan semua soal tahap Investigate terlebih dahulu.')">
                    Tahap Modify
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            @endif
        @else
            <div></div>
        @endif
    </div>

    {{-- Mermaid Library --}}
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
    </script>
@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const activityId = {{ $activity->id }};
            const sandboxTables = @json($sandboxTables ?? []);
            const answerText = document.getElementById('answer-text');
            const sqlEditor = document.getElementById('sql-editor');
            const btnRun = document.getElementById('btn-run');
            const btnReset = document.getElementById('btn-reset');
            const btnSubmit    = document.getElementById('btn-submit');
            const scoreDisplay = document.getElementById('score-display');
            const sqlOutput    = document.getElementById('sql-output');

            function updateScoreDisplay(score, isCorrect, detail) {
                if (!scoreDisplay) return;
                const color = isCorrect ? '#4ade80' : '#f87171';
                const label = isCorrect ? '✅ Lulus!' : '❌ Coba lagi';
                const sub   = detail ? `Keruntutan: ${detail.keruntutan} | Argumen: ${detail.berargumen} | Kesimpulan: ${detail.kesimpulan}` : '';
                scoreDisplay.innerHTML = `<span style="color:${color};font-weight:600;">Skor: ${score}/100 ${label}</span>${sub ? `<br><small style="color:#94a3b8;font-size:11px;">${sub}</small>` : ''}`;
            }
            const defaultCode = @json($activity->code_snippet ?? '');

            if (Object.keys(sandboxTables).length > 0) {
                loadTables();
                const erdBtn = document.querySelector('[onclick*="erdModal"]');
                if (erdBtn) {
                    erdBtn.addEventListener('click', () => setTimeout(() => renderErd('erd-large', generateDynamicErd()), 100));
                }
            }

            async function loadTables() {
                const container = document.getElementById('tables-container');
                if (!container) return;
                const tableKeys = Object.keys(sandboxTables);

                if (tableKeys.length === 0) {
                    container.innerHTML = '<p style="font-size:0.8rem;color:#475569;">Tidak ada tabel terkait.</p>';
                    return;
                }

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
                            tbody.innerHTML = `<tr><td colspan="${data.columns.length}" style="text-align:center; color:#64748b;">Tabel kosong.</td></tr>`;
                        } else {
                            tbody.innerHTML = `<tr><td colspan="${data.columns.length}" style="color:#f87171;">Gagal memuat.</td></tr>`;
                        }
                    } catch (e) {
                        const tbody = document.querySelector(`#table-data-${displayName} tbody`);
                        if (tbody) tbody.innerHTML = `<tr><td colspan="${data.columns.length}" style="color:#f87171;">Error koneksi.</td></tr>`;
                    }
                }

                const dynamicErdCode = generateDynamicErd();
                renderErd('erd-small', dynamicErdCode);
            }

            function generateDynamicErd() {
                const tableKeys = Object.keys(sandboxTables);
                if (tableKeys.length === 0) return "erDiagram\n    Database_Kosong";
                let code = "erDiagram\n";
                for (const [displayName, data] of Object.entries(sandboxTables)) {
                    const safeName = displayName.replace(/\s+/g, '_');
                    code += `    ${safeName} {\n`;
                    data.columns.forEach(col => {
                        let safeType = col.type.split('(')[0];
                        code += `        ${safeType} ${col.name}\n`;
                    });
                    code += `    }\n`;
                }
                for (const [displayName, data] of Object.entries(sandboxTables)) {
                    const safeName = displayName.replace(/\s+/g, '_');
                    data.columns.forEach(col => {
                        if (col.key === 'MUL' || col.name.startsWith('id_')) {
                            const targetName = col.name.replace('id_', '');
                            const hasTarget = tableKeys.find(k => k.toLowerCase() === targetName.toLowerCase());
                            if (hasTarget) {
                                const safeTarget = hasTarget.replace(/\s+/g, '_');
                                code += `    ${safeTarget} ||--o{ ${safeName} : ${col.name}\n`;
                            }
                        }
                    });
                }
                return code;
            }

            async function renderErd(elementId, code) {
                const el = document.getElementById(elementId);
                if (!el) return;
                try {
                    const { svg } = await mermaid.render(elementId + '-svg', code);
                    el.innerHTML = svg;
                } catch (err) {
                    el.innerHTML = '<span style="color:#f87171">Gagal memuat ERD</span>';
                }
            }

            if (btnReset && sqlEditor) {
                btnReset.addEventListener('click', function() {
                    sqlEditor.value = defaultCode;
                    sqlOutput.innerHTML = '<span style="color:#475569;font-style:italic;">Klik Run untuk melihat hasil...</span>';
                });
            }

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
                                query,
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

            if (btnSubmit) {
                btnSubmit.addEventListener('click', async function() {
                    const text = answerText.value.trim();
                    if (!text) {
                        addChat('assistant', '⚠️ Tulis jawabanmu dulu.');
                        return;
                    }
                    if (!confirm('Yakin ingin submit?')) return;
                    addChat('user', 'Submit jawaban:\n' + text);
                    showTyping();
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
                        if (data.success) {
                            updateScoreDisplay(data.score, data.is_correct, data.score_detail);
                            const scoreInfo = data.score_detail
                                ? ` (Keruntutan: ${data.score_detail.keruntutan}, Argumen: ${data.score_detail.berargumen}, Kesimpulan: ${data.score_detail.kesimpulan} → Total: ${data.score}/100)`
                                : ` (Skor: ${data.score}/100)`;
                            if (data.is_correct) {
                                addChat('assistant', '✅ ' + data.feedback + scoreInfo);
                                btnSubmit.classList.add('btn-done');
                                btnSubmit.style.removeProperty('background');
                                btnSubmit.textContent = 'Selesai ✓';
                                btnSubmit.disabled = true;
                            } else {
                                addChat('assistant', data.feedback + scoreInfo + ' Perbaiki jawabanmu dan coba lagi.');
                                btnSubmit.disabled = false;
                                btnSubmit.textContent = 'Submit';
                            }
                        } else {
                            addChat('assistant', '❌ ' + (data.error || 'Terjadi kesalahan.'));
                            btnSubmit.disabled = false;
                            btnSubmit.textContent = 'Submit';
                        }
                    } catch (e) {
                        addChat('assistant', '❌ Terjadi kesalahan koneksi.');
                        btnSubmit.disabled = false;
                        btnSubmit.textContent = 'Submit';
                    }
                });
            }

            const botAvatarSvg = `<svg width="13" height="13" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1 1 .03 2.7-1.388 2.7H4.186c-1.418 0-2.389-1.7-1.388-2.7L4.2 15.3"/></svg>`;

            function addChat(role, msg) {
                const cb = document.getElementById('chat-messages');
                if (!cb) return;
                const typing = cb.querySelector('.chat-typing-row');
                if (typing) typing.remove();
                const row = document.createElement('div');
                row.className = 'chat-row' + (role === 'user' ? ' user-row' : '');
                if (role === 'assistant') {
                    const av = document.createElement('div');
                    av.className = 'chat-avatar-small';
                    av.innerHTML = botAvatarSvg;
                    const bub = document.createElement('div');
                    bub.className = 'chat-bubble bot-bubble';
                    bub.textContent = msg;
                    row.appendChild(av);
                    row.appendChild(bub);
                } else {
                    const bub = document.createElement('div');
                    bub.className = 'chat-bubble user-bubble';
                    bub.textContent = msg;
                    row.appendChild(bub);
                }
                cb.appendChild(row);
                cb.scrollTop = cb.scrollHeight;
            }

            function showTyping() {
                const cb = document.getElementById('chat-messages');
                if (!cb) return;
                const row = document.createElement('div');
                row.className = 'chat-row chat-typing-row';
                row.innerHTML = `<div class="chat-avatar-small">${botAvatarSvg}</div><div class="chat-bubble bot-bubble chat-typing"><span></span><span></span><span></span></div>`;
                cb.appendChild(row);
                cb.scrollTop = cb.scrollHeight;
            }

            // Virtual Assistant Chat
            const btnChat   = document.getElementById('btn-chat');
            const chatInput = document.getElementById('chat-input');

            if (btnChat && chatInput) {
                async function sendChat() {
                    const msg = chatInput.value.trim();
                    if (!msg) return;
                    addChat('user', msg);
                    chatInput.value = '';
                    btnChat.disabled = true;
                    showTyping();
                    try {
                        const res = await fetch('{{ route('api.chat') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                            body: JSON.stringify({ activity_id: activityId, message: msg }),
                        });
                        const data = await res.json();
                        const reply = data.success ? data.response : 'Maaf, terjadi kesalahan. Coba lagi.';
                        addChat('assistant', reply);
                    } catch (e) {
                        addChat('assistant', 'Maaf, tidak dapat terhubung ke asisten.');
                    }
                    btnChat.disabled = false;
                }
                btnChat.addEventListener('click', sendChat);
                chatInput.addEventListener('keydown', e => { if (e.key === 'Enter') sendChat(); });
            }

            @if ($submission && $submission->score !== null)
                updateScoreDisplay({{ $submission->score }}, {{ $submission->is_correct ? 'true' : 'false' }}, @json(['keruntutan' => $submission->score_keruntutan, 'berargumen' => $submission->score_berargumen, 'kesimpulan' => $submission->score_kesimpulan]));
                @if ($submission->is_correct)
                btnSubmit.classList.add('btn-done');
                btnSubmit.style.removeProperty('background');
                btnSubmit.textContent = 'Selesai ✓';
                btnSubmit.disabled = true;
                @endif
            @endif

            const chatBoxEl = document.getElementById('chat-messages');
            if (chatBoxEl) chatBoxEl.scrollTop = chatBoxEl.scrollHeight;
        });
    </script>
@endpush
