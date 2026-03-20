<x-layouts.admin title="Buat Tabel">
    <x-slot:styles>
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
                max-width: 1000px;
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

            .section-label {
                font-size: 14px;
                font-weight: 700;
                color: #0f1b3d;
                margin-bottom: 14px;
            }

            .columns-container {
                display: flex;
                flex-direction: column;
                gap: 12px;
                margin-bottom: 24px;
            }

            .col-row {
                background: #f8f9fc;
                padding: 14px;
                border-radius: 10px;
                border: 1px solid #e4e8f1;
            }

            .col-row-main {
                display: flex;
                gap: 10px;
                align-items: center;
                margin-bottom: 8px;
            }

            .col-row-fk {
                display: flex;
                gap: 10px;
                align-items: center;
                padding-top: 8px;
                border-top: 1px dashed #e2e8f0;
            }

            .col-row input,
            .col-row select {
                padding: 8px 10px;
                border: 1.5px solid #e2e8f0;
                border-radius: 8px;
                font-size: 13px;
                font-family: inherit;
                color: #1e293b;
                background: #fff;
            }

            .col-row input:focus,
            .col-row select:focus {
                outline: none;
                border-color: #0f1b3d;
            }

            .col-name {
                flex: 2;
            }

            .col-type {
                flex: 1.2;
            }

            .col-length {
                width: 80px;
            }

            .col-fk-select {
                flex: 1;
            }

            .col-checks {
                display: flex;
                gap: 12px;
                align-items: center;
                font-size: 12px;
                color: #64748b;
                white-space: nowrap;
            }

            .col-checks label {
                display: flex;
                align-items: center;
                gap: 4px;
                cursor: pointer;
            }

            .fk-label {
                font-size: 11px;
                font-weight: 700;
                color: #3b5bdb;
                white-space: nowrap;
            }

            .btn-remove-col {
                background: none;
                border: none;
                color: #ef4444;
                cursor: pointer;
                font-size: 18px;
                padding: 0 4px;
                line-height: 1;
            }

            .btn-remove-col:hover {
                color: #dc2626;
            }

            .btn-add-col {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: #eef2ff;
                color: #3b5bdb;
                padding: 8px 16px;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 600;
                border: none;
                cursor: pointer;
                font-family: inherit;
                margin-bottom: 24px;
            }

            .btn-add-col:hover {
                background: #dbeafe;
            }

            .divider {
                border: none;
                border-top: 1px dashed #e4e8f1;
                margin: 20px 0;
            }

            .preview-sql {
                background: #0f1b3d;
                color: #a5f3fc;
                padding: 16px;
                border-radius: 10px;
                font-family: 'Courier New', monospace;
                font-size: 13px;
                line-height: 1.6;
                white-space: pre-wrap;
                margin-bottom: 24px;
                max-height: 250px;
                overflow-y: auto;
            }

            .info-box {
                background: #eef2ff;
                border: 1px solid #c7d2fe;
                border-radius: 10px;
                padding: 12px 16px;
                font-size: 12px;
                color: #3b5bdb;
                margin-bottom: 20px;
                line-height: 1.5;
            }
        </style>
    </x-slot:styles>

    <div class="breadcrumb">
        <a href="{{ route('admin.sandbox.index') }}">Kelola Database</a>
        <span>›</span>
        <a href="{{ route('admin.sandbox.show', $sandbox) }}">{{ $sandbox->name }}</a>
        <span>›</span>
        <span>Buat Tabel</span>
    </div>

    <div class="form-card">
        <h2>Buat Tabel Baru</h2>
        <p class="subtitle">Database: {{ $sandbox->name }} — Prefix: <code>{{ $sandbox->prefix }}__</code></p>

        @if ($existingTables->count())
            <div class="info-box">
                Tabel yang sudah ada:
                @foreach ($existingTables as $et)
                    <strong>{{ $et->display_name }}</strong> ({{ $et->table_name }}){{ !$loop->last ? ', ' : '' }}
                @endforeach
                — bisa digunakan sebagai referensi Foreign Key.
            </div>
        @endif

        @if ($errors->any())
            <div class="form-errors">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.sandbox.table.store', $sandbox) }}" id="tableForm">
            @csrf

            <div class="form-group">
                <label>Nama Tabel *</label>
                <input type="text" name="display_name" value="{{ old('display_name') }}" required
                    placeholder="Contoh: penerbit" oninput="updatePreview()">
                <div style="font-size:11px; color:#94a3b8; margin-top:4px;">Akan disimpan sebagai:
                    <code>{{ $sandbox->prefix }}__<span id="previewName">...</span></code></div>
            </div>

            <hr class="divider">

            <div class="section-label">Definisi Kolom</div>

            <div class="columns-container" id="columnsContainer">
                <div class="col-row">
                    <div class="col-row-main">
                        <input type="text" name="columns[0][name]" class="col-name" placeholder="Nama kolom"
                            value="id" required oninput="updatePreview()">
                        <select name="columns[0][type]" class="col-type" onchange="updatePreview()">
                            <option value="INT" selected>INT</option>
                            <option value="VARCHAR">VARCHAR</option>
                            <option value="TEXT">TEXT</option>
                            <option value="DATE">DATE</option>
                            <option value="DATETIME">DATETIME</option>
                            <option value="DECIMAL">DECIMAL</option>
                            <option value="BOOLEAN">BOOLEAN</option>
                        </select>
                        <input type="number" name="columns[0][length]" class="col-length" placeholder="Length"
                            min="1" oninput="updatePreview()">
                        <div class="col-checks">
                            <label><input type="checkbox" name="columns[0][primary]" value="1" checked
                                    onchange="updatePreview()"> PK</label>
                            <label><input type="checkbox" name="columns[0][nullable]" value="1"
                                    onchange="updatePreview()"> Null</label>
                        </div>
                        <button type="button" class="btn-remove-col" onclick="removeColumn(this)"
                            title="Hapus">&times;</button>
                    </div>
                    <div class="col-row-fk">
                        <span class="fk-label">FK →</span>
                        <select name="columns[0][fk_table]" class="col-fk-select"
                            onchange="loadFkColumns(this, 0); updatePreview()">
                            <option value="">Tidak ada</option>
                            @foreach ($existingTables as $et)
                                <option value="{{ $et->table_name }}">{{ $et->display_name }}</option>
                            @endforeach
                        </select>
                        <select name="columns[0][fk_column]" class="col-fk-select" id="fkCol0"
                            onchange="updatePreview()">
                            <option value="">— Kolom —</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="button" class="btn-add-col" onclick="addColumn()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Kolom
            </button>

            <hr class="divider">

            <div class="section-label">Preview SQL</div>
            <div class="preview-sql" id="sqlPreview">CREATE TABLE ...</div>

            <div class="form-actions">
                <a href="{{ route('admin.sandbox.show', $sandbox) }}" class="btn-secondary"
                    style="text-decoration:none;">Batal</a>
                <button type="submit" class="btn-primary">Buat Tabel</button>
            </div>
        </form>
    </div>

    <x-slot:scripts>
        <script>
            let colIndex = 1;
            const prefix = '{{ $sandbox->prefix }}';

            // Data kolom per tabel untuk FK
            const tableColumns = @json($tableColumns);

            // Daftar tabel untuk dropdown FK
            const existingTables = @json($existingTables->map(fn($t) => ['table_name' => $t->table_name, 'display_name' => $t->display_name]));

            function buildFkOptions() {
                let html = '<option value="">Tidak ada</option>';
                existingTables.forEach(t => {
                    html += `<option value="${t.table_name}">${t.display_name}</option>`;
                });
                return html;
            }

            function loadFkColumns(select, idx) {
                const fkColSelect = document.getElementById('fkCol' + idx);
                const tableName = select.value;
                fkColSelect.innerHTML = '<option value="">— Kolom —</option>';

                if (tableName && tableColumns[tableName]) {
                    tableColumns[tableName].forEach(col => {
                        fkColSelect.innerHTML += `<option value="${col}">${col}</option>`;
                    });
                }
            }

            function addColumn() {
                const container = document.getElementById('columnsContainer');
                const row = document.createElement('div');
                row.className = 'col-row';
                row.innerHTML = `
                <div class="col-row-main">
                    <input type="text" name="columns[${colIndex}][name]" class="col-name" placeholder="Nama kolom" required oninput="updatePreview()">
                    <select name="columns[${colIndex}][type]" class="col-type" onchange="updatePreview()">
                        <option value="INT">INT</option>
                        <option value="VARCHAR" selected>VARCHAR</option>
                        <option value="TEXT">TEXT</option>
                        <option value="DATE">DATE</option>
                        <option value="DATETIME">DATETIME</option>
                        <option value="DECIMAL">DECIMAL</option>
                        <option value="BOOLEAN">BOOLEAN</option>
                    </select>
                    <input type="number" name="columns[${colIndex}][length]" class="col-length" placeholder="Length" min="1" oninput="updatePreview()">
                    <div class="col-checks">
                        <label><input type="checkbox" name="columns[${colIndex}][primary]" value="1" onchange="updatePreview()"> PK</label>
                        <label><input type="checkbox" name="columns[${colIndex}][nullable]" value="1" onchange="updatePreview()"> Null</label>
                    </div>
                    <button type="button" class="btn-remove-col" onclick="removeColumn(this)">&times;</button>
                </div>
                <div class="col-row-fk">
                    <span class="fk-label">FK →</span>
                    <select name="columns[${colIndex}][fk_table]" class="col-fk-select" onchange="loadFkColumns(this, ${colIndex}); updatePreview()">
                        ${buildFkOptions()}
                    </select>
                    <select name="columns[${colIndex}][fk_column]" class="col-fk-select" id="fkCol${colIndex}" onchange="updatePreview()">
                        <option value="">— Kolom —</option>
                    </select>
                </div>
            `;
                container.appendChild(row);
                colIndex++;
                updatePreview();
            }

            function removeColumn(btn) {
                const container = document.getElementById('columnsContainer');
                if (container.children.length > 1) {
                    btn.closest('.col-row').remove();
                    updatePreview();
                }
            }

            function updatePreview() {
                const nameInput = document.querySelector('input[name="display_name"]');
                const slug = nameInput.value.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
                document.getElementById('previewName').textContent = slug || '...';

                const tableName = prefix + '__' + (slug || 'nama_tabel');
                const rows = document.querySelectorAll('.col-row');
                let cols = [];
                let fks = [];

                rows.forEach(row => {
                    const name = row.querySelector('.col-name')?.value || 'kolom';
                    const type = row.querySelector('.col-type')?.value || 'VARCHAR';
                    const length = row.querySelector('.col-length')?.value;
                    const pk = row.querySelector('input[value="1"][name*="primary"]')?.checked;
                    const nullable = row.querySelector('input[value="1"][name*="nullable"]')?.checked;
                    const fkTable = row.querySelector('select[name*="fk_table"]')?.value;
                    const fkColumn = row.querySelector('select[name*="fk_column"]')?.value;

                    let def = '  `' + name + '` ' + type;
                    if (length && ['VARCHAR', 'CHAR', 'INT'].includes(type)) def += '(' + length + ')';
                    else if (['VARCHAR', 'CHAR'].includes(type) && !length) def += '(255)';

                    if (pk) {
                        if (type === 'INT') def += ' AUTO_INCREMENT PRIMARY KEY';
                        else def += ' PRIMARY KEY';
                    } else if (!nullable) {
                        def += ' NOT NULL';
                    } else {
                        def += ' NULL';
                    }
                    cols.push(def);

                    if (fkTable && fkColumn) {
                        fks.push('  FOREIGN KEY (`' + name + '`) REFERENCES `' + fkTable + '`(`' + fkColumn + '`)');
                    }
                });

                const allDefs = cols.concat(fks);
                document.getElementById('sqlPreview').textContent =
                    'CREATE TABLE `' + tableName + '` (\n' + allDefs.join(',\n') + '\n);';
            }

            document.querySelectorAll('.col-row input, .col-row select').forEach(el => {
                el.addEventListener('input', updatePreview);
                el.addEventListener('change', updatePreview);
            });

            updatePreview();
        </script>
    </x-slot:scripts>

</x-layouts.admin>
