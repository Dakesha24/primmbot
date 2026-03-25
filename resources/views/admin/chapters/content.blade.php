<x-layouts.admin title="Kelola Kegiatan">

    <x-slot:styles>
        <style>
            .breadcrumb {
                display: flex; align-items: center; gap: 8px;
                margin-bottom: 20px; font-size: 13px; flex-wrap: wrap;
            }
            .breadcrumb a { color: #3b5bdb; text-decoration: none; font-weight: 600; }
            .breadcrumb a:hover { text-decoration: underline; }
            .breadcrumb span { color: #9aa5b8; }

            .chapter-banner {
                background: #fff;
                border: 1px solid #d0d5e0;
                border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc;
                padding: 16px 20px;
                margin-bottom: 28px;
            }
            .chapter-banner h2 { font-size: 14px; font-weight: 700; color: #0f1b3d; margin-bottom: 3px; }
            .chapter-banner p  { font-size: 12.5px; color: #9aa5b8; }

            .section-label {
                font-size: 11px; font-weight: 700;
                color: #6b7a99;
                text-transform: uppercase; letter-spacing: 0.07em;
                margin-bottom: 10px; margin-top: 28px;
            }

            /* ── Container Box ── */
            .content-box {
                background: #fff;
                border: 1px solid #d0d5e0;
                border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc;
                overflow: hidden;
            }

            /* ── Row ── */
            .content-row {
                display: flex; align-items: center;
                padding: 13px 18px; gap: 13px;
                border-bottom: 1px solid #f0f2f7;
            }
            .content-row:last-child { border-bottom: none; }

            .row-num {
                width: 24px; height: 24px;
                background: #f0f2f7;
                border: 1px solid #d0d5e0;
                border-radius: 4px;
                display: flex; align-items: center; justify-content: center;
                font-size: 11px; font-weight: 700; color: #6b7a99;
                flex-shrink: 0;
            }

            .row-info { flex: 1; min-width: 0; }
            .row-info .row-label {
                font-size: 13px; font-weight: 700; color: #0f1b3d;
            }
            .row-info .row-preview {
                font-size: 12px; color: #9aa5b8; margin-top: 2px;
                white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
                max-width: 460px;
            }
            .row-info .row-empty {
                font-size: 12px; color: #c4cad6; font-style: italic; margin-top: 2px;
            }


            /* ── Stage Row (PRIMM) ── */
            .stage-row {
                border-bottom: 1px solid #f0f2f7;
            }
            .stage-row:last-child { border-bottom: none; }

            .stage-header {
                display: flex; align-items: center;
                padding: 13px 18px; gap: 13px;
            }
            .stage-header.clickable { cursor: pointer; user-select: none; }
            .stage-header.clickable:hover { background: #fafbfd; }

            .stage-letter {
                width: 28px; height: 28px;
                background: #0f1b3d; border-radius: 4px;
                display: flex; align-items: center; justify-content: center;
                font-size: 11px; font-weight: 700; color: #fff;
                flex-shrink: 0;
            }

            .stage-name { font-size: 13px; font-weight: 700; color: #0f1b3d; }
            .stage-desc { font-size: 12px; color: #9aa5b8; flex: 1; }

            .stage-chevron {
                color: #9aa5b8; transition: transform 0.2s; flex-shrink: 0;
            }
            .stage-chevron.open { transform: rotate(180deg); }

            /* ── Activity inline (Predict/Run) ── */
            .act-inline {
                padding: 10px 18px 12px 59px;
                border-top: 1px solid #f7f8fa;
                background: #fafbfd;
            }
            .act-inline .act-text { font-size: 12px; color: #4a5568; line-height: 1.5; }
            .act-inline .act-db   { font-size: 11px; color: #9aa5b8; margin-top: 3px; }

            /* ── Level List ── */
            .level-list { display: none; }
            .level-list.open { display: block; }

            .level-row {
                display: flex; align-items: center;
                padding: 10px 18px 10px 59px; gap: 12px;
                border-top: 1px solid #f7f8fa;
                background: #fafbfd;
            }

            .level-name { font-size: 12.5px; font-weight: 600; color: #4a5568; flex: 1; }
            .level-sub  { font-size: 11px; color: #9aa5b8; font-weight: 400; margin-left: 4px; }

            /* ── Buttons ── */
            .btn-buat {
                display: inline-flex; align-items: center; gap: 5px;
                padding: 5px 13px;
                background: #0f1b3d; color: #fff;
                border: none; border-radius: 4px;
                font-size: 11.5px; font-weight: 700; font-family: inherit;
                cursor: pointer; text-decoration: none;
                transition: background 0.12s; flex-shrink: 0;
            }
            .btn-buat:hover { background: #1a2d5a; }

            .btn-edit {
                display: inline-flex; align-items: center; gap: 5px;
                padding: 5px 13px;
                background: #f0f2f7; color: #0f1b3d;
                border: 1px solid #d0d5e0; border-radius: 4px;
                font-size: 11.5px; font-weight: 700; font-family: inherit;
                cursor: pointer; text-decoration: none;
                transition: background 0.12s; flex-shrink: 0;
            }
            .btn-edit:hover { background: #e4e8f0; }

            .btn-hapus {
                padding: 5px 11px;
                background: none; color: #dc2626;
                border: none; border-radius: 4px;
                font-size: 11.5px; font-weight: 700; font-family: inherit;
                cursor: pointer; transition: background 0.12s; flex-shrink: 0;
            }
            .btn-hapus:hover { background: #fef2f2; }
        </style>
    </x-slot:styles>

    @php
        $materialsByType = $materials->keyBy('type');
        $activitiesByStage = $activities->groupBy('stage');

        $materialTemplate = [
            'pendahuluan'      => 'Pendahuluan',
            'petunjuk_belajar' => 'Petunjuk Belajar',
            'tujuan'           => 'Tujuan Pembelajaran',
            'prasyarat'        => 'Prasyarat',
            'ringkasan_materi' => 'Ringkasan Materi',
        ];

        $investigateLevels = [
            'atoms'     => ['label' => 'Atoms',     'desc' => 'Elemen terkecil query'],
            'blocks'    => ['label' => 'Blocks',    'desc' => 'Fungsi tiap klausa'],
            'relations' => ['label' => 'Relations', 'desc' => 'Relasi antar tabel'],
            'macro'     => ['label' => 'Macro',     'desc' => 'Konteks penggunaan nyata'],
        ];

    @endphp

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('admin.courses.index') }}">Kelola LKPD</a>
        <span>›</span>
        <a href="{{ route('admin.chapters.index', $course) }}">{{ $course->title }}</a>
        <span>›</span>
        <span>{{ $chapter->title }}</span>
    </div>

    <!-- Chapter Banner -->
    <div class="chapter-banner">
        <h2>{{ $chapter->title }}</h2>
        <p>{{ $chapter->description ?? 'Tidak ada deskripsi.' }}</p>
    </div>

    {{-- ═══ PENDAHULUAN ═══ --}}
    <div class="section-label">Pendahuluan</div>
    <div class="content-box">
        @foreach($materialTemplate as $type => $label)
            @php $mat = $materialsByType->get($type) @endphp
            <div class="content-row">
                <div class="row-num">{{ $loop->iteration }}</div>
                <div class="row-info">
                    <div class="row-label">{{ $label }}</div>
                    @if($mat)
                        <div class="row-preview">{{ Str::limit(strip_tags($mat->content), 80) }}</div>
                    @else
                        <div class="row-empty">Belum diisi</div>
                    @endif
                </div>
                @if($mat)
                    <a href="{{ route('admin.materials.edit', [$course, $chapter, $mat]) }}" class="btn-edit">Edit</a>
                    <form method="POST" action="{{ route('admin.materials.destroy', [$course, $chapter, $mat]) }}"
                        onsubmit="return confirm('Hapus materi ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-hapus">Hapus</button>
                    </form>
                @else
                    <a href="{{ route('admin.materials.create', [$course, $chapter]) }}?type={{ $type }}" class="btn-buat">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Buat
                    </a>
                @endif
            </div>
        @endforeach
    </div>

    {{-- ═══ AKTIVITAS PRIMM ═══ --}}
    <div class="section-label" style="margin-top:32px;">Aktivitas PRIMM</div>
    <div class="content-box">

        {{-- Predict --}}
        @php $predict = $activitiesByStage->get('predict')?->first() @endphp
        <div class="stage-row">
            <div class="stage-header">
                <div class="stage-letter">P</div>
                <div class="stage-name">Predict</div>
                <div class="stage-desc">Siswa memprediksi output sebelum kode dijalankan</div>
                @if($predict)
                    <a href="{{ route('admin.activities.edit', [$course, $chapter, $predict]) }}" class="btn-edit">Edit</a>
                    <form method="POST" action="{{ route('admin.activities.destroy', [$course, $chapter, $predict]) }}"
                        onsubmit="return confirm('Hapus aktivitas Predict ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-hapus">Hapus</button>
                    </form>
                @else
                    <a href="{{ route('admin.activities.create', [$course, $chapter]) }}?stage=predict" class="btn-buat">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Buat
                    </a>
                @endif
            </div>
            @if($predict)
                <div class="act-inline">
                    <div class="act-text">{{ Str::limit($predict->question_text, 120) }}</div>
                    @if($predict->sandboxDatabase)
                        <div class="act-db">DB: {{ $predict->sandboxDatabase->name }}</div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Run --}}
        @php $run = $activitiesByStage->get('run')?->first() @endphp
        <div class="stage-row">
            <div class="stage-header">
                <div class="stage-letter">R</div>
                <div class="stage-name">Run</div>
                <div class="stage-desc">Siswa menjalankan kode dan merefleksikan hasilnya</div>
                @if($run)
                    <a href="{{ route('admin.activities.edit', [$course, $chapter, $run]) }}" class="btn-edit">Edit</a>
                    <form method="POST" action="{{ route('admin.activities.destroy', [$course, $chapter, $run]) }}"
                        onsubmit="return confirm('Hapus aktivitas Run ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-hapus">Hapus</button>
                    </form>
                @else
                    <a href="{{ route('admin.activities.create', [$course, $chapter]) }}?stage=run" class="btn-buat">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Buat
                    </a>
                @endif
            </div>
            @if($run)
                <div class="act-inline">
                    <div class="act-text">{{ Str::limit($run->question_text, 120) }}</div>
                    @if($run->sandboxDatabase)
                        <div class="act-db">DB: {{ $run->sandboxDatabase->name }}</div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Investigate --}}
        @php $investigateActs = $activitiesByStage->get('investigate', collect())->keyBy('level') @endphp
        <div class="stage-row">
            <div class="stage-header clickable" onclick="toggleStage('investigate')">
                <div class="stage-letter">I</div>
                <div class="stage-name">Investigate</div>
                <div class="stage-desc">{{ $investigateActs->count() }} / 4 level terisi</div>
                <svg class="stage-chevron" id="chevron-investigate" width="15" height="15" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </div>
            <div class="level-list" id="levels-investigate">
                @foreach($investigateLevels as $lvl => $info)
                    @php $act = $investigateActs->get($lvl) @endphp
                    <div class="level-row">
                        <div class="level-name">
                            {{ $info['label'] }}
                            <span class="level-sub">— {{ $info['desc'] }}</span>
                        </div>
                        @if($act)
                            <a href="{{ route('admin.activities.edit', [$course, $chapter, $act]) }}" class="btn-edit">Edit</a>
                            <form method="POST" action="{{ route('admin.activities.destroy', [$course, $chapter, $act]) }}"
                                onsubmit="return confirm('Hapus aktivitas Investigate {{ $lvl }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-hapus">Hapus</button>
                            </form>
                        @else
                            <a href="{{ route('admin.activities.create', [$course, $chapter]) }}?stage=investigate&level={{ $lvl }}" class="btn-buat">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                Buat
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Modify --}}
        @php $modifyActs = $activitiesByStage->get('modify', collect())->sortBy('level') @endphp
        <div class="stage-row">
            <div class="stage-header clickable" onclick="toggleStage('modify')">
                <div class="stage-letter">M</div>
                <div class="stage-name">Modify</div>
                <div class="stage-desc">{{ $modifyActs->count() }} level</div>
                <svg class="stage-chevron" id="chevron-modify" width="15" height="15" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </div>
            <div class="level-list" id="levels-modify">
                @forelse($modifyActs as $act)
                    <div class="level-row">
                        <div class="level-name">Level {{ $act->level }}</div>
                        <a href="{{ route('admin.activities.edit', [$course, $chapter, $act]) }}" class="btn-edit">Edit</a>
                        <form method="POST" action="{{ route('admin.activities.destroy', [$course, $chapter, $act]) }}"
                            onsubmit="return confirm('Hapus Level {{ $act->level }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-hapus">Hapus</button>
                        </form>
                    </div>
                @empty
                    <div class="level-row" style="color:#9aa5b8;font-size:12.5px;font-style:italic;">Belum ada level.</div>
                @endforelse
                <div class="level-row" style="border-top:1px dashed #e4e8f0;">
                    <a href="{{ route('admin.activities.create', [$course, $chapter]) }}?stage=modify" class="btn-buat">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Tambah Soal
                    </a>
                </div>
            </div>
        </div>

        {{-- Make --}}
        @php $makeActs = $activitiesByStage->get('make', collect())->sortBy('level') @endphp
        <div class="stage-row">
            <div class="stage-header clickable" onclick="toggleStage('make')">
                <div class="stage-letter">K</div>
                <div class="stage-name">Make</div>
                <div class="stage-desc">{{ $makeActs->count() }} level</div>
                <svg class="stage-chevron" id="chevron-make" width="15" height="15" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </div>
            <div class="level-list" id="levels-make">
                @forelse($makeActs as $act)
                    <div class="level-row">
                        <div class="level-name">Level {{ $act->level }}</div>
                        <a href="{{ route('admin.activities.edit', [$course, $chapter, $act]) }}" class="btn-edit">Edit</a>
                        <form method="POST" action="{{ route('admin.activities.destroy', [$course, $chapter, $act]) }}"
                            onsubmit="return confirm('Hapus Level {{ $act->level }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-hapus">Hapus</button>
                        </form>
                    </div>
                @empty
                    <div class="level-row" style="color:#9aa5b8;font-size:12.5px;font-style:italic;">Belum ada level.</div>
                @endforelse
                <div class="level-row" style="border-top:1px dashed #e4e8f0;">
                    <a href="{{ route('admin.activities.create', [$course, $chapter]) }}?stage=make" class="btn-buat">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Tambah Soal
                    </a>
                </div>
            </div>
        </div>

    </div>{{-- end content-box PRIMM --}}

    <x-slot:scripts>
        <script>
            function toggleStage(stage) {
                document.getElementById('levels-' + stage).classList.toggle('open');
                document.getElementById('chevron-' + stage).classList.toggle('open');
            }
        </script>
    </x-slot:scripts>

</x-layouts.admin>
