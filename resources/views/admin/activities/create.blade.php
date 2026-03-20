<x-layouts.admin title="Tambah Aktivitas">
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

            .form-row {
                display: flex;
                gap: 16px;
                margin-bottom: 18px;
            }

            .form-row .form-group {
                flex: 1;
                margin-bottom: 0;
            }

            .stage-selector {
                display: flex;
                gap: 8px;
                margin-bottom: 28px;
            }

            .stage-btn {
                padding: 8px 18px;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 600;
                font-family: inherit;
                cursor: pointer;
                border: 1.5px solid #e2e8f0;
                background: #fff;
                color: #64748b;
                text-decoration: none;
                transition: all 0.15s;
            }

            .stage-btn:hover {
                border-color: #3b5bdb;
                color: #3b5bdb;
            }

            .stage-btn.active {
                background: #0f1b3d;
                color: #fff;
                border-color: #0f1b3d;
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

            /* DB Selector */
            .db-selector {
                display: flex;
                gap: 10px;
                align-items: center;
            }

            .db-selector select {
                flex: 1;
            }

            .db-link {
                padding: 10px 16px;
                border-radius: 10px;
                font-size: 12px;
                font-weight: 700;
                color: #3b5bdb;
                background: #eef2ff;
                text-decoration: none;
                white-space: nowrap;
            }

            .db-link:hover {
                background: #dbeafe;
            }

            /* DB Preview */
            .db-preview {
                margin-top: 16px;
            }

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
    </x-slot:styles>

    <div class="breadcrumb">
        <a href="{{ route('admin.courses.index') }}">Kelola Kelas</a>
        <span>›</span>
        <a href="{{ route('admin.chapters.index', $course) }}">{{ $course->title }}</a>
        <span>›</span>
        <a href="{{ route('admin.chapters.content', [$course, $chapter]) }}">{{ $chapter->title }}</a>
        <span>›</span>
        <span>Tambah Aktivitas</span>
    </div>

    <div class="form-card">
        <h2>Tambah Aktivitas PRIMM</h2>
        <p class="subtitle">Pilih tahap PRIMM lalu isi form sesuai kebutuhan tahap tersebut.</p>

        <div class="stage-selector">
            @foreach (['predict', 'run', 'investigate', 'modified', 'make'] as $s)
                <a href="{{ route('admin.activities.create', [$course, $chapter, 'stage' => $s]) }}"
                    class="stage-btn {{ $stage == $s ? 'active' : '' }}">{{ ucfirst($s) }}</a>
            @endforeach
        </div>

        @if ($errors->any())
            <div class="form-errors">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.activities.store', [$course, $chapter]) }}" id="activityForm">
            @csrf
            <input type="hidden" name="stage" value="{{ $stage }}">

            <!-- Database Sandbox -->
            <div class="form-group" style="margin-bottom: 18px;">
                <label>Database Sandbox</label>
                <div class="db-selector">
                    <select name="sandbox_database_id" id="dbSelect" onchange="loadDbPreview()">
                        <option value="">— Pilih Database —</option>
                        @foreach ($sandboxDatabases as $sdb)
                            <option value="{{ $sdb->id }}"
                                {{ old('sandbox_database_id') == $sdb->id ? 'selected' : '' }}>{{ $sdb->name }}
                                ({{ $sdb->prefix }})</option>
                        @endforeach
                    </select>
                    <a href="{{ route('admin.sandbox.index') }}" class="db-link">+ Buat Database</a>
                </div>
                <div class="field-hint">Pilih database yang digunakan siswa. Kosongkan jika tidak diperlukan.</div>
                <div class="db-preview" id="dbPreview"></div>
            </div>

            <hr class="section-divider">

            <!-- Order + Level -->
            <div class="form-row">
                <div class="form-group">
                    <label>Urutan *</label>
                    <input type="number" name="order" value="{{ old('order', 0) }}" required min="0">
                </div>

                @if ($stage == 'investigate')
                    <div class="form-group">
                        <label>Level *</label>
                        <select name="level" required>
                            <option value="atoms" {{ old('level') == 'atoms' ? 'selected' : '' }}>Atoms</option>
                            <option value="blocks" {{ old('level') == 'blocks' ? 'selected' : '' }}>Blocks</option>
                            <option value="relations" {{ old('level') == 'relations' ? 'selected' : '' }}>Relations
                            </option>
                            <option value="macro" {{ old('level') == 'macro' ? 'selected' : '' }}>Macro</option>
                        </select>
                    </div>
                @elseif(in_array($stage, ['modified', 'make']))
                    <div class="form-group">
                        <label>Level *</label>
                        <select name="level" required>
                            <option value="mudah" {{ old('level') == 'mudah' ? 'selected' : '' }}>Mudah</option>
                            <option value="sedang" {{ old('level') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="tantang" {{ old('level') == 'tantang' ? 'selected' : '' }}>Tantang</option>
                        </select>
                    </div>
                @else
                    <div class="form-group"></div>
                @endif
            </div>

            <hr class="section-divider">

            <!-- Description -->
            @if (in_array($stage, ['predict', 'modified', 'make']))
                <div class="section-label">
                    @if ($stage == 'predict')
                        Deskripsi Soal (cerita + tabel database)
                    @else
                        Pertanyaan Penjelasan
                    @endif
                </div>
                <div class="form-group">
                    <div id="desc-editor">{!! old('description') !!}</div>
                    <input type="hidden" name="description" id="descInput">
                    <div class="field-hint">
                        @if ($stage == 'predict')
                            Tulis cerita konteks dan tabel database dalam format HTML.
                        @else
                            Pertanyaan yang meminta siswa menjelaskan perubahan kode.
                        @endif
                    </div>
                </div>
                <hr class="section-divider">
            @endif

            <!-- Question Text -->
            <div class="form-group">
                <label>
                    @if ($stage == 'predict')
                        Pertanyaan Prediksi *
                    @elseif($stage == 'run')
                        Pertanyaan Refleksi *
                    @elseif($stage == 'investigate')
                        Pertanyaan Analisis *
                    @else
                        Perintah SQL *
                    @endif
                </label>
                <textarea name="question_text" rows="3" required placeholder="Tulis pertanyaan atau perintah...">{{ old('question_text') }}</textarea>
                <div class="field-hint">
                    @if ($stage == 'predict')
                        Pertanyaan yang meminta siswa memprediksi output SQL.
                    @elseif($stage == 'run')
                        Pertanyaan refleksi setelah siswa menjalankan kode.
                    @elseif($stage == 'investigate')
                        Pertanyaan analisis sesuai level.
                    @else
                        Instruksi SQL yang harus ditulis/dimodifikasi siswa.
                    @endif
                </div>
            </div>

            <!-- Code Snippet -->
            @if (in_array($stage, ['predict', 'run', 'investigate']))
                <hr class="section-divider">
                <div class="form-group">
                    <label>Code Snippet SQL</label>
                    <textarea name="code_snippet" class="code-area" rows="5" placeholder="SELECT * FROM ...">{{ old('code_snippet') }}</textarea>
                    <div class="field-hint">Kode SQL yang ditampilkan ke siswa (read-only di predict, editable di
                        run/investigate).</div>
                </div>
            @endif

            <!-- Editor Default Code -->
            @if ($stage == 'modified')
                <hr class="section-divider">
                <div class="form-group">
                    <label>Kode Default Editor</label>
                    <textarea name="editor_default_code" class="code-area" rows="5"
                        placeholder="SELECT * FROM buku JOIN penerbit ON ...">{{ old('editor_default_code') }}</textarea>
                    <div class="field-hint">Kode SQL yang sudah terisi di editor siswa. Siswa memodifikasi kode ini.
                    </div>
                </div>
            @endif

            <!-- Expected Output -->
            @if (in_array($stage, ['modified', 'make']))
                <hr class="section-divider">
                <div class="form-group">
                    <label>Expected Output (JSON)</label>
                    <textarea name="expected_output" class="code-area" rows="5" placeholder='[{"kolom1": "nilai1"}]'>{{ old('expected_output') }}</textarea>
                    <div class="field-hint">Output yang diharapkan dalam format JSON array.</div>
                </div>
            @endif

            <hr class="section-divider">

            <div class="form-actions">
                <a href="{{ route('admin.chapters.content', [$course, $chapter]) }}" class="btn-secondary"
                    style="text-decoration:none;">Batal</a>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>

    <x-slot:scripts>
        @if (in_array($stage, ['predict', 'modified', 'make']))
            <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/quill-resize-image@1.0.4/dist/quill-resize-image.min.js"></script>
            <script>
                Quill.register('modules/resize', window.QuillResizeImage);
                const descQuill = new Quill('#desc-editor', {
                    theme: 'snow',
                    placeholder: '{{ $stage == 'predict' ? 'Tulis deskripsi soal + tabel HTML...' : 'Tulis pertanyaan penjelasan...' }}',
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
                        html += '<div class="db-preview-table">';
                        html += '<h4>' + t.display_name + ' <code>' + t.table_name + '</code></h4>';

                        if (t.columns.length) {
                            html += '<table class="mini-table"><thead><tr>';
                            t.columns.forEach(c => html += '<th>' + c + '</th>');
                            html += '</tr></thead><tbody>';

                            if (t.rows.length) {
                                t.rows.forEach(row => {
                                    html += '<tr>';
                                    t.columns.forEach(c => {
                                        const val = row[c] !== null && row[c] !== undefined ? row[
                                            c] : '-';
                                        html += '<td>' + String(val).substring(0, 50) + '</td>';
                                    });
                                    html += '</tr>';
                                });
                            } else {
                                html += '<tr><td colspan="' + t.columns.length +
                                    '" style="text-align:center; color:#94a3b8;">Belum ada data</td></tr>';
                            }

                            html += '</tbody></table>';
                            html += '<div class="row-count">Total: ' + t.total + ' baris' + (t.total > 10 ?
                                ' (menampilkan 10 pertama)' : '') + '</div>';
                        }

                        html += '</div>';
                    });

                    preview.innerHTML = html;
                } catch (err) {
                    preview.innerHTML =
                    '<div class="db-preview-loading" style="color:#ef4444;">Gagal memuat preview.</div>';
                }
            }

            // Auto-load jika sudah ada value
            if (document.getElementById('dbSelect').value) {
                loadDbPreview();
            }
        </script>
    </x-slot:scripts>

</x-layouts.admin>
