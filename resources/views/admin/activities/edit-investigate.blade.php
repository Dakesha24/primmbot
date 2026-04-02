<x-layouts.admin title="Edit Aktivitas — Investigate">
    <x-slot:styles>
        <style>
            .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 24px; font-size: 13px; flex-wrap: wrap; }
            .breadcrumb a { color: #3b5bdb; text-decoration: none; font-weight: 600; }
            .breadcrumb a:hover { text-decoration: underline; }
            .breadcrumb span { color: #94a3b8; }

            .inv-grid { display: grid; grid-template-columns: 360px 1fr; gap: 24px; align-items: start; }

            .db-panel { background: #fff; border: 1px solid #e4e8f1; border-radius: 6px; box-shadow: 3px 3px 0 #c8cfdc; position: sticky; top: 86px; max-height: calc(100vh - 106px); overflow-y: auto; }
            .db-panel-head { padding: 14px 18px 12px; border-bottom: 1px solid #e4e8f1; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; background: #fff; z-index: 2; }
            .db-panel-head .panel-title { font-size: 12px; font-weight: 700; color: #0f1b3d; text-transform: uppercase; letter-spacing: 0.05em; }
            .stage-pill { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 4px; background: #ede9fe; color: #5b21b6; }
            .db-panel-inner { padding: 16px 18px; }
            .db-selector { display: flex; gap: 8px; align-items: center; margin-bottom: 4px; }
            .db-selector select { flex: 1; padding: 8px 10px; border: 1.5px solid #dde1ea; border-radius: 5px; font-size: 12.5px; font-family: inherit; color: #1a2332; background: #fff; transition: border-color 0.15s; }
            .db-selector select:focus { outline: none; border-color: #0f1b3d; }
            .db-new-link { padding: 8px 12px; border-radius: 5px; font-size: 11px; font-weight: 700; color: #3b5bdb; background: #eef2ff; text-decoration: none; white-space: nowrap; }
            .db-new-link:hover { background: #dbeafe; }
            .db-hint { font-size: 11px; color: #94a3b8; margin-bottom: 16px; }
            .panel-empty { font-size: 12px; color: #94a3b8; text-align: center; padding: 28px 0; line-height: 1.6; }
            .panel-loading { font-size: 12px; color: #6b7a99; padding: 10px 0; }

            .db-table-item { border: 1px solid #e4e8f1; border-radius: 5px; margin-bottom: 8px; overflow: hidden; }
            .db-table-head { display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; cursor: pointer; background: #f8f9fc; user-select: none; transition: background 0.12s; }
            .db-table-head:hover { background: #f0f2f7; }
            .db-table-head .tname { font-size: 12px; font-weight: 700; color: #0f1b3d; }
            .db-table-head .tname code { font-size: 10px; background: #eef2ff; color: #3b5bdb; padding: 1px 5px; border-radius: 3px; margin-left: 6px; font-weight: 400; }
            .db-table-head .tcount { font-size: 10px; color: #6b7a99; background: #f0f2f7; padding: 2px 7px; border-radius: 10px; flex-shrink: 0; }
            .db-table-head .chevron { font-size: 10px; color: #94a3b8; margin-left: 8px; transition: transform 0.15s; }
            .db-table-body { display: none; padding: 10px 12px; border-top: 1px solid #e4e8f1; }
            .db-table-body.open { display: block; }

            .col-list { display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 10px; }
            .col-chip { display: inline-flex; align-items: center; gap: 3px; font-size: 10.5px; padding: 2px 7px; border-radius: 3px; border: 1px solid #e4e8f1; background: #fafafa; color: #1a2332; font-weight: 500; }
            .col-chip .col-type { color: #3b5bdb; font-size: 9.5px; }
            .col-chip.pk { border-color: #fbbf24; background: #fffbeb; }
            .col-chip.fk { border-color: #a78bfa; background: #f5f3ff; }

            .mini-table-wrap { overflow-x: auto; border: 1px solid #e4e8f1; border-radius: 4px; }
            .mini-table { width: 100%; border-collapse: collapse; font-size: 11px; }
            .mini-table th { background: #f0f2f7; padding: 5px 8px; text-align: left; font-weight: 700; color: #4a5568; white-space: nowrap; border-bottom: 1px solid #e4e8f1; }
            .mini-table td { padding: 4px 8px; border-bottom: 1px solid #f0f2f7; color: #1a2332; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .mini-table tbody tr:last-child td { border-bottom: none; }
            .mini-table tbody tr:hover td { background: #f8f9fc; }

            .erd-section { margin-top: 16px; padding-top: 14px; border-top: 1px dashed #e4e8f1; }
            .erd-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
            .erd-label { font-size: 10px; font-weight: 700; color: #6b7a99; text-transform: uppercase; letter-spacing: 0.06em; }
            .erd-fullscreen-btn { font-size: 11px; font-weight: 700; color: #3b5bdb; background: #eef2ff; border: none; padding: 3px 8px; border-radius: 4px; cursor: pointer; font-family: inherit; }
            .erd-fullscreen-btn:hover { background: #dbeafe; }
            #erd-container { min-height: 60px; overflow: hidden; }
            #erd-container svg { max-width: 100%; height: auto; }

            .form-panel { background: #fff; border: 1px solid #e4e8f1; border-radius: 6px; box-shadow: 3px 3px 0 #c8cfdc; padding: 28px 32px; }
            .section-title { font-size: 13px; font-weight: 700; color: #0f1b3d; margin-bottom: 3px; }
            .field-hint { font-size: 11px; color: #94a3b8; }
            .section-divider { border: none; border-top: 1px dashed #e4e8f1; margin: 22px 0; }

            /* Admin editor wrap */
            .admin-editor-wrap { border: 1px solid #e4e8f1; border-radius: 6px; overflow: hidden; margin-bottom: 4px; }
            .admin-editor-bar { display: flex; align-items: center; justify-content: space-between; padding: 8px 14px; background: #f8f9fc; border-bottom: 1px solid #e4e8f1; }
            .admin-editor-bar-label { font-size: 11px; font-weight: 700; color: #6b7a99; text-transform: uppercase; letter-spacing: 0.06em; }
            .admin-btn-run { display: inline-flex; align-items: center; gap: 5px; padding: 5px 14px; background: #3b5bdb; color: #fff; border: none; border-radius: 4px; font-size: 11.5px; font-weight: 700; font-family: inherit; cursor: pointer; transition: background 0.15s; }
            .admin-btn-run:hover { background: #2f4cc0; }
            .admin-btn-run:disabled { opacity: 0.6; cursor: not-allowed; }
            textarea.admin-editor-code { font-family: 'Courier New', monospace; font-size: 13px; line-height: 1.7; background: #f0f4f8; color: #0f1b3d; font-weight: 500; border: none; padding: 12px 14px; width: 100%; min-height: 140px; resize: vertical; display: block; box-sizing: border-box; }
            textarea.admin-editor-code:focus { outline: none; background: #eaf0f8; }
            textarea.admin-editor-code::placeholder { color: #94a3b8; }

            .sql-result-box { border-top: 1px solid #e4e8f1; overflow: hidden; }
            .sql-result-header { display: flex; align-items: center; gap: 7px; padding: 8px 14px; font-size: 12px; font-weight: 600; border-bottom: 1px solid #e4e8f1; }
            .sql-result-header.success { background: #f0fdf4; color: #16a34a; }
            .sql-result-header.error { background: #fef2f2; color: #dc2626; }
            .sql-result-header.info { background: #f0f9ff; color: #0369a1; }
            .sql-result-scroll { overflow-x: auto; max-height: 260px; }
            .result-table { width: 100%; border-collapse: collapse; font-size: 12px; }
            .result-table th { background: #f0f2f7; padding: 7px 12px; text-align: left; font-weight: 700; color: #4a5568; position: sticky; top: 0; white-space: nowrap; border-bottom: 1px solid #e4e8f1; }
            .result-table td { padding: 6px 12px; border-bottom: 1px solid #f0f2f7; color: #1a2332; }
            .result-table tbody tr:last-child td { border-bottom: none; }
            .result-table tbody tr:hover td { background: #f8f9fc; }
            .sql-error-msg { padding: 14px; font-size: 12px; color: #991b1b; font-family: 'Courier New', monospace; background: #fef2f2; line-height: 1.6; }

            .erd-modal { display: none; position: fixed; inset: 0; background: rgba(10,18,40,0.55); backdrop-filter: blur(4px); z-index: 200; align-items: center; justify-content: center; }
            .erd-modal.active { display: flex; }
            .erd-modal-box { background: #fff; border-radius: 8px; border: 1px solid #e4e8f1; padding: 24px; max-width: 800px; width: 92%; max-height: 88vh; overflow: auto; box-shadow: 0 20px 48px rgba(10,18,40,0.18); position: relative; }
            .erd-modal-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
            .erd-modal-head h3 { font-size: 15px; font-weight: 700; color: #0f1b3d; }
            .erd-close { width: 30px; height: 30px; border-radius: 5px; border: 1px solid #e4e8f1; background: none; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 15px; }
            .erd-close:hover { color: #0f1b3d; background: #f0f2f7; }
            #erd-large svg { max-width: 100%; height: auto; }

            .form-errors { background: #fef2f2; border: 1px solid #fecaca; border-left: 3px solid #dc2626; color: #991b1b; padding: 10px 14px; border-radius: 5px; font-size: 12.5px; margin-bottom: 20px; line-height: 1.7; }

            .level-row { display: flex; align-items: center; gap: 8px; margin-bottom: 22px; }
            .level-badge-display { display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 5px; font-size: 12px; font-weight: 700; }
            .level-badge-display.atoms    { background: #dbeafe; color: #1e40af; }
            .level-badge-display.blocks   { background: #d1fae5; color: #065f46; }
            .level-badge-display.relations{ background: #ede9fe; color: #5b21b6; }
            .level-badge-display.macro    { background: #fef3c7; color: #92400e; }
            .btn-level-help { width: 22px; height: 22px; border-radius: 50%; border: 1.5px solid #dde1ea; background: #f8f9fc; color: #6b7a99; font-size: 11px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.15s; font-family: inherit; padding: 0; }
            .btn-level-help:hover { background: #ede9fe; border-color: #5b21b6; color: #5b21b6; }

            .level-modal { display: none; position: fixed; inset: 0; background: rgba(10,18,40,0.45); backdrop-filter: blur(4px); z-index: 300; align-items: center; justify-content: center; }
            .level-modal.active { display: flex; }
            .level-modal-box { background: #fff; border-radius: 8px; border: 1px solid #e4e8f1; padding: 28px 32px; max-width: 480px; width: 92%; box-shadow: 0 20px 48px rgba(10,18,40,0.15); }
            .level-modal-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
            .level-modal-head h3 { font-size: 14px; font-weight: 700; color: #0f1b3d; }
            .level-modal-close { width: 28px; height: 28px; border-radius: 5px; border: 1px solid #e4e8f1; background: none; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; }
            .level-modal-close:hover { color: #0f1b3d; background: #f0f2f7; }
            .level-item { display: flex; gap: 12px; padding: 11px 0; border-bottom: 1px solid #f0f2f7; }
            .level-item:last-child { border-bottom: none; padding-bottom: 0; }
            .level-badge { flex-shrink: 0; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; align-self: flex-start; margin-top: 1px; white-space: nowrap; }
            .level-badge.atoms { background: #dbeafe; color: #1e40af; }
            .level-badge.blocks { background: #d1fae5; color: #065f46; }
            .level-badge.relations { background: #ede9fe; color: #5b21b6; }
            .level-badge.macro { background: #fef3c7; color: #92400e; }
            .level-desc { font-size: 12px; color: #4a5568; line-height: 1.7; }
            .level-desc strong { color: #0f1b3d; display: block; margin-bottom: 1px; }
        </style>
    </x-slot:styles>

    <div class="breadcrumb">
        <a href="{{ route('admin.courses.index') }}">Kelola LKPD</a>
        <span>›</span>
        <a href="{{ route('admin.chapters.index', $course) }}">{{ $course->title }}</a>
        <span>›</span>
        <a href="{{ route('admin.chapters.content', [$course, $chapter]) }}">{{ $chapter->title }}</a>
        <span>›</span>
        <span>Edit Aktivitas — Investigate</span>
    </div>

    {{-- Level Help Modal --}}
    <div class="level-modal" id="levelModal" onclick="if(event.target===this)closeLevelModal()">
        <div class="level-modal-box">
            <div class="level-modal-head">
                <h3>Panduan Level — Investigate</h3>
                <button class="level-modal-close" onclick="closeLevelModal()">✕</button>
            </div>
            <div class="level-item">
                <span class="level-badge atoms">Atoms</span>
                <div class="level-desc">
                    <strong>Elemen Tunggal</strong>
                    Pertanyaan tentang elemen bahasa tunggal atau baris kode tertentu. Contoh: "Apa fungsi dari kata kunci JOIN pada baris ke-3?"
                </div>
            </div>
            <div class="level-item">
                <span class="level-badge blocks">Blocks</span>
                <div class="level-desc">
                    <strong>Sekumpulan Baris</strong>
                    Pertanyaan mengenai operasi dari sekumpulan baris kode, misalnya satu klausa atau sub-query. Contoh: "Apa yang dilakukan oleh blok WHERE pada query ini?"
                </div>
            </div>
            <div class="level-item">
                <span class="level-badge relations">Relations</span>
                <div class="level-desc">
                    <strong>Hubungan Antar Blok</strong>
                    Bagaimana antar blok kode saling berhubungan. Contoh: "Bagaimana klausa ON menghubungkan tabel penerbit dan buku?"
                </div>
            </div>
            <div class="level-item">
                <span class="level-badge macro">Macro</span>
                <div class="level-desc">
                    <strong>Keseluruhan Struktur</strong>
                    Pemahaman tentang keseluruhan tujuan query dalam konteks lebih luas. Contoh: "Apa tujuan keseluruhan dari query ini dan kapan kamu akan menggunakannya?"
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.activities.update', [$course, $chapter, $activity]) }}" id="activityForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="stage" value="investigate">

        <div class="inv-grid">

            {{-- ── LEFT: Database Panel ── --}}
            <div class="db-panel">
                <div class="db-panel-head">
                    <span class="panel-title">Panel Database</span>
                    <span class="stage-pill">INVESTIGATE</span>
                </div>
                <div class="db-panel-inner">
                    <div class="db-selector">
                        <select name="sandbox_database_id" id="dbSelect" onchange="loadDbSchema()">
                            <option value="">— Pilih database —</option>
                            @foreach($sandboxDatabases as $sdb)
                                <option value="{{ $sdb->id }}"
                                    {{ old('sandbox_database_id', $activity->sandbox_database_id) == $sdb->id ? 'selected' : '' }}>
                                    {{ $sdb->name }} ({{ $sdb->prefix }})
                                </option>
                            @endforeach
                        </select>
                        <a href="{{ route('admin.sandbox.index') }}" target="_blank" class="db-new-link">+ Buat</a>
                    </div>
                    <div class="db-hint">Database wajib dipilih untuk tahap Investigate.</div>
                    <div id="db-dynamic">
                        <div class="panel-empty">Pilih database untuk melihat<br>struktur tabel dan diagram relasi.</div>
                    </div>
                </div>
            </div>

            {{-- ── RIGHT: Form ── --}}
            <div class="form-panel">
                @if($errors->any())
                    <div class="form-errors">
                        @foreach($errors->all() as $e)
                            <div>{{ $e }}</div>
                        @endforeach
                    </div>
                @endif

                {{-- Level --}}
                @php $currentLevel = old('level', $activity->level ?? ''); @endphp
                <input type="hidden" name="level" value="{{ $currentLevel }}">
                <div class="level-row">
                    <span class="level-badge-display {{ $currentLevel }}">
                        {{ ucfirst($currentLevel ?: '—') }}
                    </span>
                    <button type="button" class="btn-level-help" onclick="openLevelModal()" title="Panduan level Investigate">?</button>
                </div>

                <hr class="section-divider" style="margin-top:0;">

                {{-- Kode SQL --}}
                <div class="section-title">Kode SQL</div>
                <div class="field-hint" style="margin-bottom:12px;">Query SQL yang diberikan ke siswa untuk dianalisis. Siswa dapat mengedit dan menjalankan query ini.</div>

                <div class="admin-editor-wrap">
                    <div class="admin-editor-bar">
                        <span class="admin-editor-bar-label">SQL Query</span>
                        <button type="button" id="btnRun" class="admin-btn-run" onclick="runSql()">
                            <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg>
                            Jalankan ▶
                        </button>
                    </div>
                    <textarea name="code_snippet" id="sqlInput" class="admin-editor-code" rows="6"
                        placeholder="SELECT *&#10;FROM products&#10;JOIN categories ON products.category_id = categories.id;">{{ old('code_snippet', $activity->code_snippet) }}</textarea>
                    <div id="sql-result-wrap"></div>
                </div>

                <hr class="section-divider">

                <hr class="section-divider">

                {{-- Pertanyaan Analisis --}}
                <div class="form-group">
                    <label>Pertanyaan Analisis *</label>
                    <textarea name="question_text" rows="3" required
                        placeholder="Contoh: Apa fungsi dari klausa JOIN pada query di atas?">{{ old('question_text', $activity->question_text) }}</textarea>
                    <div class="field-hint" style="margin-top:5px;">Pertanyaan yang mendorong siswa menganalisis bagian tertentu dari query SQL.</div>
                </div>

                <hr class="section-divider">

                <div class="form-group">
                    <label style="font-size:13px;font-weight:600;color:#1a2332;">KKM (Nilai Minimum Lulus)</label>
                    <input type="number" name="kkm" value="{{ old('kkm', $activity->kkm ?? 70) }}" min="0" max="100"
                        style="width:120px;padding:8px 10px;border:1.5px solid #dde1ea;border-radius:5px;font-size:13px;font-family:inherit;">
                    <div class="field-hint" style="margin-top:4px;">Skor minimum (0–100) agar siswa dinyatakan lulus. Default: 70</div>
                </div>

                <hr class="section-divider">

                <div class="form-group">
                    <label style="font-size:13px;font-weight:600;color:#1a2332;">Contoh Jawaban Ideal <span style="font-size:11px;font-weight:400;color:#94a3b8;">(Opsional — Acuan Kualitas Berpikir untuk AI)</span></label>
                    <textarea name="reference_answer" rows="5"
                        style="width:100%;padding:10px 12px;border:1.5px solid #dde1ea;border-radius:5px;font-size:13px;font-family:inherit;line-height:1.6;resize:vertical;"
                        placeholder="Tulis contoh jawaban yang menunjukkan kualitas reasoning yang diharapkan. AI menggunakan ini sebagai acuan penilaian — bukan untuk mencocokkan kata per kata.">{{ old('reference_answer', $activity->reference_answer) }}</textarea>
                    <div class="field-hint" style="margin-top:5px;">Jika diisi: AI menggunakan ini untuk menilai kualitas argumen. Jika kosong: AI hanya menilai dari rubrik.</div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.chapters.content', [$course, $chapter]) }}"
                        class="btn-secondary" style="text-decoration:none;">Batal</a>
                    <button type="submit" class="btn-primary">Perbarui Aktivitas</button>
                </div>
            </div>
        </div>
    </form>

    {{-- ERD Fullscreen Modal --}}
    <div class="erd-modal" id="erdModal" onclick="if(event.target===this)closeErd()">
        <div class="erd-modal-box">
            <div class="erd-modal-head">
                <h3>Relasi Tabel — ERD</h3>
                <button class="erd-close" onclick="closeErd()">✕</button>
            </div>
            <div id="erd-large" style="text-align:center;"></div>
        </div>
    </div>

    <x-slot:scripts>
        <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
        <script>
            mermaid.initialize({ startOnLoad: false, theme: 'default', er: { diagramPadding: 20 } });
            let currentSchema = [];

            async function loadDbSchema() {
                const id = document.getElementById('dbSelect').value;
                const dyn = document.getElementById('db-dynamic');
                if (!id) {
                    dyn.innerHTML = '<div class="panel-empty">Pilih database untuk melihat<br>struktur tabel dan diagram relasi.</div>';
                    currentSchema = [];
                    return;
                }
                dyn.innerHTML = '<div class="panel-loading">⏳ Memuat struktur database...</div>';
                try {
                    const res = await fetch('/admin/sandbox/' + id + '/schema');
                    const tables = await res.json();
                    currentSchema = tables;
                    renderDbPanel(tables);
                } catch (e) {
                    dyn.innerHTML = '<div class="panel-empty" style="color:#ef4444;">Gagal memuat database.</div>';
                }
            }

            function renderDbPanel(tables) {
                if (!tables.length) {
                    document.getElementById('db-dynamic').innerHTML = '<div class="panel-empty">Database ini belum memiliki tabel.</div>';
                    return;
                }
                let html = '';
                tables.forEach((t, i) => {
                    html += `<div class="db-table-item">
                        <div class="db-table-head" onclick="toggleTbl(${i}, this)">
                            <span class="tname">${escHtml(t.display_name)} <code>${escHtml(t.table_name)}</code></span>
                            <span style="display:flex;align-items:center;gap:6px;">
                                <span class="tcount">${t.total} baris</span>
                                <span class="chevron">▾</span>
                            </span>
                        </div>
                        <div class="db-table-body${i === 0 ? ' open' : ''}" id="tbl-${i}">`;
                    html += '<div class="col-list">';
                    t.columns.forEach(col => {
                        const isPK = col.key === 'PRI';
                        const isFK = col.key === 'MUL';
                        const cls = isPK ? 'col-chip pk' : isFK ? 'col-chip fk' : 'col-chip';
                        const typeShort = (col.type || '').split('(')[0];
                        html += `<span class="${cls}">${escHtml(col.name)} <span class="col-type">${typeShort}</span></span>`;
                    });
                    html += '</div>';
                    if (t.columns.length > 0) {
                        html += '<div class="mini-table-wrap"><table class="mini-table"><thead><tr>';
                        t.columns.forEach(c => html += `<th>${escHtml(c.name)}</th>`);
                        html += '</tr></thead><tbody>';
                        if (t.rows.length > 0) {
                            t.rows.forEach(row => {
                                html += '<tr>';
                                t.columns.forEach(c => {
                                    const val = row[c.name] !== null && row[c.name] !== undefined
                                        ? escHtml(String(row[c.name]).substring(0, 30))
                                        : '<em style="color:#94a3b8">NULL</em>';
                                    html += `<td>${val}</td>`;
                                });
                                html += '</tr>';
                            });
                        } else {
                            html += `<tr><td colspan="${t.columns.length}" style="text-align:center;color:#94a3b8;font-style:italic;padding:8px;">Tabel kosong</td></tr>`;
                        }
                        html += '</tbody></table></div>';
                        if (t.total > 10) html += `<div style="font-size:10px;color:#94a3b8;margin-top:6px;">${t.total} baris total — menampilkan 10 pertama</div>`;
                    }
                    html += '</div></div>';
                });
                html += `<div class="erd-section">
                    <div class="erd-header">
                        <span class="erd-label">Relasi Tabel (ERD)</span>
                        <button type="button" class="erd-fullscreen-btn" onclick="openErd()">⤢ Fullscreen</button>
                    </div>
                    <div id="erd-container"></div>
                </div>`;
                document.getElementById('db-dynamic').innerHTML = html;
                renderErd('erd-container', buildErd(tables));
            }

            function toggleTbl(i, headEl) {
                const body = document.getElementById('tbl-' + i);
                const chevron = headEl.querySelector('.chevron');
                const isOpen = body.classList.toggle('open');
                if (chevron) chevron.style.transform = isOpen ? 'rotate(180deg)' : '';
            }

            function buildErd(tables) {
                if (!tables.length) return 'erDiagram\n    No_Tables';
                let code = 'erDiagram\n';
                const keys = tables.map(t => t.display_name);
                tables.forEach(t => {
                    const safe = t.display_name.replace(/[^a-zA-Z0-9_]/g, '_');
                    code += `    ${safe} {\n`;
                    t.columns.forEach(col => {
                        const typeShort = (col.type || 'varchar').split('(')[0].replace(/[^a-zA-Z0-9_]/g, '');
                        const safeName = col.name.replace(/[^a-zA-Z0-9_]/g, '_');
                        code += `        ${typeShort} ${safeName}\n`;
                    });
                    code += '    }\n';
                });
                tables.forEach(t => {
                    const safe = t.display_name.replace(/[^a-zA-Z0-9_]/g, '_');
                    t.columns.forEach(col => {
                        if (col.key === 'MUL' || col.name.startsWith('id_')) {
                            const target = col.name.replace(/^id_/, '');
                            const found = keys.find(k => k.toLowerCase() === target.toLowerCase());
                            if (found) {
                                const safeTarget = found.replace(/[^a-zA-Z0-9_]/g, '_');
                                const safeCol = col.name.replace(/[^a-zA-Z0-9_]/g, '_');
                                code += `    ${safeTarget} ||--o{ ${safe} : "${safeCol}"\n`;
                            }
                        }
                    });
                });
                return code;
            }

            let erdRenderCount = 0;
            async function renderErd(elementId, code) {
                const el = document.getElementById(elementId);
                if (!el) return;
                try {
                    erdRenderCount++;
                    const { svg } = await mermaid.render('mermaid-svg-' + erdRenderCount, code);
                    el.innerHTML = svg;
                } catch (err) {
                    el.innerHTML = '<span style="font-size:11px;color:#ef4444;">Gagal render ERD.</span>';
                }
            }

            function openErd() {
                document.getElementById('erdModal').classList.add('active');
                if (currentSchema.length) renderErd('erd-large', buildErd(currentSchema));
            }
            function closeErd() { document.getElementById('erdModal').classList.remove('active'); }

            async function runSql() {
                const sql = document.getElementById('sqlInput').value.trim();
                const dbId = document.getElementById('dbSelect').value;
                const resultWrap = document.getElementById('sql-result-wrap');
                const btn = document.getElementById('btnRun');
                if (!sql) { alert('Tulis kode SQL terlebih dahulu.'); return; }
                if (!dbId) { alert('Pilih database terlebih dahulu.'); return; }
                btn.disabled = true;
                btn.innerHTML = `<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Menjalankan...`;
                try {
                    const res = await fetch('{{ route('api.sql.run') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                        body: JSON.stringify({ query: sql, database_id: parseInt(dbId) })
                    });
                    const data = await res.json();
                    if (data.success && data.type === 'select') {
                        let html = `<div class="sql-result-box"><div class="sql-result-header success"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>${data.row_count} baris ditemukan</div><div class="sql-result-scroll">`;
                        if (data.columns.length > 0) {
                            html += '<table class="result-table"><thead><tr>';
                            data.columns.forEach(c => html += `<th>${escHtml(c)}</th>`);
                            html += '</tr></thead><tbody>';
                            data.rows.forEach(row => {
                                html += '<tr>';
                                data.columns.forEach(c => { html += `<td>${row[c] !== null && row[c] !== undefined ? escHtml(String(row[c])) : '<em style="color:#94a3b8">NULL</em>'}</td>`; });
                                html += '</tr>';
                            });
                            html += '</tbody></table>';
                        } else {
                            html += '<div style="padding:14px;font-size:12px;color:#94a3b8;">Tidak ada kolom yang dikembalikan.</div>';
                        }
                        html += '</div></div>';
                        resultWrap.innerHTML = html;
                    } else if (data.success) {
                        resultWrap.innerHTML = `<div class="sql-result-box"><div class="sql-result-header info"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>${escHtml(data.message ?? 'Berhasil dijalankan')}</div></div>`;
                    } else {
                        resultWrap.innerHTML = `<div class="sql-result-box"><div class="sql-result-header error"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>SQL Error</div><div class="sql-error-msg">${escHtml(data.error)}</div></div>`;
                    }
                } catch (e) {
                    resultWrap.innerHTML = `<div class="sql-result-box"><div class="sql-result-header error">Koneksi gagal</div><div class="sql-error-msg">Tidak dapat terhubung ke server.</div></div>`;
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = `<svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg> Jalankan ▶`;
                }
            }

            function escHtml(str) {
                return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            }

            if (document.getElementById('dbSelect').value) loadDbSchema();

            function openLevelModal() { document.getElementById('levelModal').classList.add('active'); }
            function closeLevelModal() { document.getElementById('levelModal').classList.remove('active'); }
            document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLevelModal(); });
        </script>
    </x-slot:scripts>
</x-layouts.admin>
