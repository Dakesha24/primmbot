@extends('layouts.learning')

@section('content')
    <style>
        .main-inner {
            max-width: 100% !important;
            padding: 2rem 1.5rem !important;
        }
    </style>
    <h1 style="text-align:center;font-size:1.4rem;font-weight:800;color:#fff;margin-bottom:1.5rem;letter-spacing:-0.3px;">
        PREDICT</h1>

    <div class="predict-layout">

        {{-- KIRI: Deskripsi Soal --}}
        <div class="predict-left">
            <div class="content-card">
                <h3
                    style="font-size:0.95rem;font-weight:700;color:#fff;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
                    <svg width="18" height="18" fill="none" stroke="var(--cyan-400)" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Deskripsi Soal
                </h3>
                <div class="prose db-table">{!! str_replace(
                    '<table>',
                    '<div class="db-table-scroll"><table>',
                    str_replace('</table>', '</table></div>', $activity->description),
                ) !!}</div>
            </div>
        </div>

        {{-- KANAN: Pertanyaan + Jawab + Virtual Assistant --}}
        <div class="predict-right">
            {{-- Pertanyaan --}}
            <div class="content-card">
                <h3
                    style="font-size:0.95rem;font-weight:700;color:#fff;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
                    <svg width="18" height="18" fill="none" stroke="var(--blue-400)" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Pertanyaan
                </h3>
                <p style="font-size:0.9rem;color:#94a3b8;line-height:1.7;margin-bottom:1.2rem;">
                    {{ $activity->question_text }}</p>

                @if ($activity->code_snippet)
                    <div
                        style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.06);border-radius:10px;padding:1rem;overflow-x:auto;">
                        <pre style="margin:0;color:#4ade80;font-family:'Courier New',monospace;font-size:0.85rem;line-height:1.6;">{{ $activity->code_snippet }}</pre>
                    </div>
                @endif
            </div>

            {{-- Jawab --}}
            <div class="content-card" style="margin-top:1.2rem;">
                <h3 style="font-size:0.95rem;font-weight:700;color:#fff;margin-bottom:0.8rem;">Jawab</h3>
                <textarea id="answer-text" rows="5"
                    style="width:100%;padding:0.8rem;border-radius:10px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.05);color:#fff;font-size:0.85rem;font-family:inherit;outline:none;resize:vertical;line-height:1.6;"
                    placeholder="Tulis jawabanmu di sini...">{{ $submission->answer_text ?? '' }}</textarea>

                <div style="display:flex;align-items:center;gap:0.75rem;margin-top:1rem;">
                    <button id="btn-cek"
                        style="padding:0.6rem 1.2rem;border-radius:8px;border:1px solid rgba(255,255,255,0.12);background:transparent;color:#cbd5e1;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:inherit;transition:all 0.2s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.06)';this.style.borderColor='rgba(255,255,255,0.25)'"
                        onmouseout="this.style.background='transparent';this.style.borderColor='rgba(255,255,255,0.12)'">
                        Cek
                    </button>
                    <button id="btn-submit"
                        style="padding:0.6rem 1.2rem;border-radius:8px;border:none;background:linear-gradient(135deg,var(--blue-600),#4f46e5);color:#fff;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:inherit;box-shadow:0 4px 16px rgba(37,99,235,0.3);transition:all 0.2s;">
                        Submit
                    </button>
                </div>

                @if ($submission && $submission->ai_feedback)
                    <div id="feedback-box"
                        style="margin-top:1rem;padding:0.8rem 1rem;border-radius:10px;background:rgba(234,179,8,0.1);border:1px solid rgba(234,179,8,0.2);font-size:0.85rem;color:#fde047;">
                        {{ $submission->ai_feedback }}
                    </div>
                @endif
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

    <style>
        .predict-layout {
            display: grid;
            grid-template-columns: 2fr 3fr;
            gap: 1.2rem;
            align-items: start;
        }

        .predict-left {
            min-width: 0;
        }

        .predict-right {
            min-width: 0;
        }

        .db-table {
            margin-bottom: 1rem;
        }

        .db-table-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--cyan-400);
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .db-table-scroll {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 300px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 8px;
        }

        .db-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
            white-space: nowrap;
        }

        .db-table th {
            background: rgba(255, 255, 255, 0.06);
            color: #94a3b8;
            font-weight: 600;
            padding: 0.5rem 0.7rem;
            text-align: left;
            border: 1px solid rgba(255, 255, 255, 0.08);
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .db-table td {
            padding: 0.45rem 0.7rem;
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .db-table tr:nth-child(even) td {
            background: rgba(255, 255, 255, 0.02);
        }

        @media (max-width: 768px) {
            .predict-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('nav_prev')
    @php
        $ringkasanMat = $chapter->lessonMaterials->where('type', 'ringkasan_materi')->first();
    @endphp
    @if ($ringkasanMat)
        <a href="{{ route('learning.summary', $chapter) }}" class="nav-btn nav-btn-prev">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Ringkasan Materi
        </a>
    @else
        <div></div>
    @endif
@endsection

@section('nav_next')
    @php
        $runActivity = $chapter->activities->where('stage', 'run')->first();
    @endphp
    @if ($runActivity)
        <a href="{{ route('learning.activity', [$chapter, $runActivity]) }}" class="nav-btn nav-btn-next">
            Tahap Run
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
            const btnCek = document.getElementById('btn-cek');
            const btnSubmit = document.getElementById('btn-submit');

            if (btnCek) {
                btnCek.addEventListener('click', async function() {
                    const text = answerText.value.trim();
                    if (!text) {
                        showFeedback('Tulis jawabanmu terlebih dahulu.', 'red');
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
                            showFeedback(data.feedback, 'yellow');
                            addChatMessage('assistant', data.feedback);
                        } else {
                            showFeedback(data.error, 'red');
                        }
                    } catch (e) {
                        showFeedback('Terjadi kesalahan koneksi.', 'red');
                    }
                    btnCek.disabled = false;
                    btnCek.textContent = 'Cek';
                });
            }

            if (btnSubmit) {
                btnSubmit.addEventListener('click', async function() {
                    const text = answerText.value.trim();
                    if (!text) {
                        showFeedback('Tulis jawabanmu terlebih dahulu.', 'red');
                        return;
                    }
                    if (!confirm('Apakah kamu yakin ingin mengirim jawaban?')) return;
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
                            if (data.is_correct) {
                                showFeedback('✅ ' + data.feedback + ' (Skor: ' + data.score + '/100)',
                                    'green');
                                addChatMessage('assistant', '✅ ' + data.feedback);
                                btnSubmit.textContent = 'Selesai ✓';
                                btnSubmit.disabled = true;
                                btnSubmit.style.background = '#16a34a';
                                btnSubmit.style.boxShadow = 'none';
                            } else {
                                showFeedback('⚠️ ' + data.feedback + ' (Skor: ' + data.score +
                                    '/100). Perbaiki jawabanmu.', 'yellow');
                                addChatMessage('assistant', '⚠️ ' + data.feedback);
                                btnSubmit.disabled = false;
                                btnSubmit.textContent = 'Submit';
                            }
                        } else {
                            showFeedback(data.error, 'red');
                            btnSubmit.disabled = false;
                            btnSubmit.textContent = 'Submit';
                        }
                    } catch (e) {
                        showFeedback('Terjadi kesalahan koneksi.', 'red');
                        btnSubmit.disabled = false;
                        btnSubmit.textContent = 'Submit';
                    }
                });
            }

            function showFeedback(msg, color) {
                let box = document.getElementById('feedback-box');
                if (!box) {
                    box = document.createElement('div');
                    box.id = 'feedback-box';
                    btnCek.closest('.content-card').appendChild(box);
                }
                const colors = {
                    red: 'background:rgba(248,113,113,0.1);border:1px solid rgba(248,113,113,0.2);color:#fca5a5;',
                    yellow: 'background:rgba(234,179,8,0.1);border:1px solid rgba(234,179,8,0.2);color:#fde047;',
                    green: 'background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);color:#86efac;',
                };
                box.style.cssText = 'margin-top:1rem;padding:0.8rem 1rem;border-radius:10px;font-size:0.85rem;' + (
                    colors[color] || colors.yellow);
                box.textContent = msg;
            }

            function addChatMessage(role, msg) {
                const chatBox = document.getElementById('chat-messages');
                if (!chatBox) return;
                const placeholder = chatBox.querySelector('p[style*="italic"]');
                if (placeholder) placeholder.remove();
                const div = document.createElement('div');
                div.style.cssText =
                    'margin-bottom:0.6rem;padding:0.5rem 0.7rem;border-radius:8px;font-size:0.8rem;line-height:1.5;max-width:90%;' +
                    (role === 'assistant' ? 'background:rgba(37,99,235,0.15);color:var(--blue-300);' :
                        'background:rgba(255,255,255,0.06);color:#cbd5e1;margin-left:auto;');
                div.textContent = msg;
                chatBox.appendChild(div);
                chatBox.scrollTop = chatBox.scrollHeight;
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
