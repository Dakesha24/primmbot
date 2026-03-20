<x-layouts.admin title="Edit Aktivitas">
    <x-slot:styles>
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
        <style>
            .breadcrumb {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 24px;
                font-size: 13px;
                flex-wrap: wrap;
            }

            .breadcrumb a {
                color: #3b5bdb;
                text-decoration: none;
                font-weight: 600;
            }

            .breadcrumb a:hover {
                text-decoration: underline;
            }

            .breadcrumb span {
                color: #94a3b8;
            }

            .form-card {
                background: #fff;
                border: 1px solid #e4e8f1;
                border-radius: 16px;
                padding: 32px;
                max-width: 900px;
            }

            .form-card h2 {
                font-size: 18px;
                font-weight: 700;
                color: #0f1b3d;
                margin-bottom: 6px;
            }

            .form-card .subtitle {
                font-size: 13px;
                color: #94a3b8;
                margin-bottom: 24px;
            }

            .stage-badge {
                display: inline-block;
                padding: 6px 16px;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 700;
                margin-bottom: 24px;
            }

            .badge-predict {
                background: #fef3c7;
                color: #92400e;
            }

            .badge-run {
                background: #d1fae5;
                color: #065f46;
            }

            .badge-investigate {
                background: #dbeafe;
                color: #1e40af;
            }

            .badge-modified {
                background: #f3e8ff;
                color: #6b21a8;
            }

            .badge-make {
                background: #ffe4e6;
                color: #9f1239;
            }

            .form-row {
                display: flex;
                gap: 16px;
                margin-bottom: 18px;
            }

            .form-row .form-group {
                flex: 1;
                margin-bottom: 0;
            }

            .field-hint {
                font-size: 11px;
                color: #94a3b8;
                margin-top: 4px;
            }

            .form-group textarea.code-area {
                font-family: 'Courier New', monospace;
                font-size: 13px;
                line-height: 1.6;
                background: #0f1b3d;
                color: #a5f3fc;
                border-color: #1e293b;
                min-height: 120px;
            }

            .form-group textarea.code-area:focus {
                border-color: #3b5bdb;
                box-shadow: 0 0 0 3px rgba(59, 91, 219, 0.1);
            }

            .form-group textarea.code-area::placeholder {
                color: #475569;
            }

            #desc-editor {
                height: 250px;
                background: #fff;
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: 14px;
            }

            .ql-toolbar.ql-snow {
                border-radius: 10px 10px 0 0;
                border-color: #e2e8f0;
                background: #f8f9fc;
            }

            .ql-container.ql-snow {
                border-color: #e2e8f0;
                border-radius: 0 0 10px 10px;
            }

            .section-divider {
                border: none;
                border-top: 1px dashed #e4e8f1;
                margin: 24px 0;
            }

            .section-label {
                font-size: 14px;
                font-weight: 700;
                color: #0f1b3d;
                margin-bottom: 16px;
            }
        </style>
    </x-slot:styles>

    <div class="breadcrumb">
        <a href="{{ route('admin.courses.index') }}">Kelola Kelas</a>
        <span>›</span>
        <a href="{{ route('admin.chapters.index', $course) }}">{{ $course->title }}</a>
        <span>›</span>
        <a href="{{ route('admin.chapters.content', [$course, $chapter]) }}">{{ $chapter->title }}</a>
        <span>›</span>
        <span>Edit Aktivitas</span>
    </div>

    <div class="form-card">
        <h2>Edit Aktivitas</h2>
        <span class="stage-badge badge-{{ $activity->stage }}">{{ ucfirst($activity->stage) }}</span>

        @if ($errors->any())
            <div class="form-errors">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.activities.update', [$course, $chapter, $activity]) }}"
            id="activityForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="stage" value="{{ $activity->stage }}">

            <!-- Database Sandbox -->
            <div class="form-group" style="margin-bottom: 18px;">
                <label>Database Sandbox</label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <select name="sandbox_database_id" id="dbSelect" onchange="loadDbPreview()"
                        style="flex:1; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; font-family:inherit; color:#1e293b; background:#fff;">
                        <option value="">— Pilih Database —</option>
                        @foreach ($sandboxDatabases as $sdb)
                            <option value="{{ $sdb->id }}"
                                {{ old('sandbox_database_id', $activity->sandbox_database_id) == $sdb->id ? 'selected' : '' }}>
                                {{ $sdb->name }} ({{ $sdb->prefix }})</option>
                        @endforeach
                    </select>
                    <a href="{{ route('admin.sandbox.index') }}"
                        style="padding:10px 16px; border-radius:10px; font-size:12px; font-weight:700; color:#3b5bdb; background:#eef2ff; text-decoration:none; white-space:nowrap;">+
                        Buat Database</a>
                </div>
                <div style="font-size:11px; color:#94a3b8; margin-top:4px;">Database yang digunakan siswa saat
                    menjalankan SQL.</div>
                <div id="dbPreview" style="margin-top:16px;"></div>
            </div>

            <hr style="border:none; border-top:1px dashed #e4e8f1; margin:24px 0;">

            <!-- Order + Level -->
            <div class="form-row">
                <div class="form-group">
                    <label>Urutan *</label>
                    <input type="number" name="order" value="{{ old('order', $activity->order) }}" required
                        min="0">
                </div>

                @if ($activity->stage == 'investigate')
                    <div class="form-group">
                        <label>Level *</label>
                        <select name="level" required>
                            <option value="atoms" {{ old('level', $activity->level) == 'atoms' ? 'selected' : '' }}>
                                Atoms</option>
                            <option value="blocks" {{ old('level', $activity->level) == 'blocks' ? 'selected' : '' }}>
                                Blocks</option>
                            <option value="relations"
                                {{ old('level', $activity->level) == 'relations' ? 'selected' : '' }}>Relations
                            </option>
                            <option value="macro" {{ old('level', $activity->level) == 'macro' ? 'selected' : '' }}>
                                Macro</option>
                        </select>
                    </div>
                @elseif(in_array($activity->stage, ['modified', 'make']))
                    <div class="form-group">
                        <label>Level *</label>
                        <select name="level" required>
                            <option value="mudah" {{ old('level', $activity->level) == 'mudah' ? 'selected' : '' }}>
                                Mudah</option>
                            <option value="sedang" {{ old('level', $activity->level) == 'sedang' ? 'selected' : '' }}>
                                Sedang</option>
                            <option value="tantang"
                                {{ old('level', $activity->level) == 'tantang' ? 'selected' : '' }}>Tantang</option>
                        </select>
                    </div>
                @else
                    <div class="form-group"></div>
                @endif
            </div>

            <hr class="section-divider">

            <!-- Description -->
            @if (in_array($activity->stage, ['predict', 'modified', 'make']))
                <div class="section-label">
                    @if ($activity->stage == 'predict')
                        Deskripsi Soal
                    @else
                        Pertanyaan Penjelasan
                    @endif
                </div>
                <div class="form-group">
                    <div id="desc-editor">{!! old('description', $activity->description) !!}</div>
                    <input type="hidden" name="description" id="descInput">
                </div>

                <hr class="section-divider">
            @endif

            <!-- Question Text -->
            <div class="form-group">
                <label>
                    @if ($activity->stage == 'predict')
                        Pertanyaan Prediksi *
                    @elseif($activity->stage == 'run')
                        Pertanyaan Refleksi *
                    @elseif($activity->stage == 'investigate')
                        Pertanyaan Analisis *
                    @else
                        Perintah SQL *
                    @endif
                </label>
                <textarea name="question_text" rows="3" required>{{ old('question_text', $activity->question_text) }}</textarea>
            </div>

            <!-- Code Snippet -->
            @if (in_array($activity->stage, ['predict', 'run', 'investigate']))
                <hr class="section-divider">
                <div class="form-group">
                    <label>Code Snippet SQL</label>
                    <textarea name="code_snippet" class="code-area" rows="5">{{ old('code_snippet', $activity->code_snippet) }}</textarea>
                </div>
            @endif

            <!-- Editor Default Code -->
            @if ($activity->stage == 'modified')
                <hr class="section-divider">
                <div class="form-group">
                    <label>Kode Default Editor</label>
                    <textarea name="editor_default_code" class="code-area" rows="5">{{ old('editor_default_code', $activity->editor_default_code) }}</textarea>
                    <div class="field-hint">Kode SQL yang sudah terisi di editor siswa.</div>
                </div>
            @endif

            <!-- Expected Output -->
            @if (in_array($activity->stage, ['modified', 'make']))
                <hr class="section-divider">
                <div class="form-group">
                    <label>Expected Output (JSON)</label>
                    <textarea name="expected_output" class="code-area" rows="5">{{ old('expected_output', is_array($activity->expected_output) ? json_encode($activity->expected_output, JSON_PRETTY_PRINT) : $activity->expected_output) }}</textarea>
                    <div class="field-hint">Output yang diharapkan dalam format JSON array.</div>
                </div>
            @endif

            <hr class="section-divider">

            <div class="form-actions">
                <a href="{{ route('admin.chapters.content', [$course, $chapter]) }}" class="btn-secondary"
                    style="text-decoration:none;">Batal</a>
                <button type="submit" class="btn-primary">Perbarui</button>
            </div>
        </form>
    </div>

    <x-slot:scripts>

        <style>
            .db-preview-loading {
                color: #94a3b8;
                font-size: 13px;
                padding: 12px 0;
            }

            .db-preview-table {
                background: #f8f9fc;
                border: 1px solid #e4e8f1;
                border-radius: 10px;
                padding: 14px;
                margin-bottom: 12px;
            }

            .db-preview-table h4 {
                font-size: 13px;
                font-weight: 700;
                color: #0f1b3d;
                margin-bottom: 8px;
            }

            .db-preview-table code {
                font-size: 11px;
                background: #eef2ff;
                color: #3b5bdb;
                padding: 1px 6px;
                border-radius: 4px;
            }

            .db-preview-table .mini-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
                margin-top: 8px;
            }

            .db-preview-table .mini-table th {
                background: #e4e8f1;
                padding: 6px 10px;
                text-align: left;
                font-size: 10px;
                font-weight: 700;
                color: #64748b;
                text-transform: uppercase;
            }

            .db-preview-table .mini-table td {
                padding: 5px 10px;
                border-bottom: 1px solid #e4e8f1;
                color: #1e293b;
            }

            .db-preview-table .mini-table tbody tr:last-child td {
                border-bottom: none;
            }

            .db-preview-table .row-count {
                font-size: 11px;
                color: #94a3b8;
                margin-top: 6px;
            }
        </style>

        @if (in_array($activity->stage, ['predict', 'modified', 'make']))
            <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/quill-resize-image@1.0.4/dist/quill-resize-image.min.js"></script>
            <script>
                Quill.register('modules/resize', window.QuillResizeImage);

                const descQuill = new Quill('#desc-editor', {
                    theme: 'snow',
                    modules: {
                        resize: {},
                        toolbar: [
                            [{
                                'header': [1, 2, 3, false]
                            }],
                            ['bold', 'italic', 'underline'],
                            [{
                                'list': 'ordered'
                            }, {
                                'list': 'bullet'
                            }],
                            ['blockquote', 'code-block'],
                            ['link', 'image'],
                            ['clean']
                        ]
                    }
                });

                document.getElementById('activityForm').addEventListener('submit', function() {
                    document.getElementById('descInput').value = descQuill.root.innerHTML;
                });
            </script>
        @endif

        <script>
            async function loadDbPreview() {
                const select = document.getElementById('dbSelect');
                const preview = document.getElementById('dbPreview');
                const id = select.value;
                if (!id) {
                    preview.innerHTML = '';
                    return;
                }
                preview.innerHTML = '<div class="db-preview-loading">Memuat preview database...</div>';
                try {
                    const res = await fetch('/admin/sandbox/' + id + '/preview');
                    const tables = await res.json();
                    if (!tables.length) {
                        preview.innerHTML = '<div class="db-preview-loading">Database ini belum memiliki tabel.</div>';
                        return;
                    }
                    let html = '';
                    tables.forEach(t => {
                        html += '<div class="db-preview-table"><h4>' + t.display_name + ' <code>' + t.table_name +
                            '</code></h4>';
                        if (t.columns.length) {
                            html += '<table class="mini-table"><thead><tr>';
                            t.columns.forEach(c => html += '<th>' + c + '</th>');
                            html += '</tr></thead><tbody>';
                            if (t.rows.length) {
                                t.rows.forEach(row => {
                                    html += '<tr>';
                                    t.columns.forEach(c => {
                                        html += '<td>' + (row[c] !== null ? String(row[c])
                                            .substring(0, 50) : '-') + '</td>';
                                    });
                                    html += '</tr>';
                                });
                            } else {
                                html += '<tr><td colspan="' + t.columns.length +
                                    '" style="text-align:center;color:#94a3b8;">Belum ada data</td></tr>';
                            }
                            html += '</tbody></table>';
                            html += '<div class="row-count">Total: ' + t.total + ' baris' + (t.total > 10 ?
                                ' (10 pertama)' : '') + '</div>';
                        }
                        html += '</div>';
                    });
                    preview.innerHTML = html;
                } catch (err) {
                    preview.innerHTML =
                    '<div class="db-preview-loading" style="color:#ef4444;">Gagal memuat preview.</div>';
                }
            }
            if (document.getElementById('dbSelect').value) loadDbPreview();
        </script>
    </x-slot:scripts>

</x-layouts.admin>
