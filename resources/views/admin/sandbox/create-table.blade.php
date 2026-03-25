<x-layouts.admin title="Buat Tabel">
    <x-slot:styles>
        <style>
            .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 24px; font-size: 13px; flex-wrap: wrap; }
            .breadcrumb a { color: #3b5bdb; text-decoration: none; font-weight: 600; }
            .breadcrumb a:hover { text-decoration: underline; }
            .breadcrumb span { color: #94a3b8; }

            .page-grid { display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start; }

            .form-card {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; padding: 28px 32px;
            }
            .form-card-title { font-size: 15px; font-weight: 700; color: #0f1b3d; margin-bottom: 4px; }
            .form-card-sub { font-size: 12px; color: #64748b; margin-bottom: 24px; }
            .form-card-sub code { background: #eef2ff; color: #3b5bdb; padding: 1px 7px; border-radius: 4px; font-size: 11px; font-family: 'Courier New', monospace; }

            .section-title { font-size: 13px; font-weight: 700; color: #0f1b3d; margin-bottom: 12px; }
            .section-divider { border: none; border-top: 1px dashed #e4e8f1; margin: 22px 0; }

            .info-box {
                background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 5px;
                padding: 10px 14px; font-size: 12px; color: #3b5bdb; margin-bottom: 20px; line-height: 1.6;
            }

            .columns-container { display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px; }

            .col-row {
                background: #f8f9fc; padding: 14px 16px; border-radius: 5px;
                border: 1px solid #e4e8f1;
            }
            .col-row-main { display: flex; gap: 8px; align-items: center; margin-bottom: 8px; flex-wrap: wrap; }
            .col-row-fk {
                display: flex; gap: 8px; align-items: center; flex-wrap: wrap;
                padding-top: 8px; border-top: 1px dashed #e2e8f0;
            }

            .col-row input, .col-row select {
                padding: 7px 10px; border: 1.5px solid #dde1ea; border-radius: 5px;
                font-size: 12.5px; font-family: inherit; color: #1e293b; background: #fff;
            }
            .col-row input:focus, .col-row select:focus { outline: none; border-color: #0f1b3d; }

            .col-name { flex: 2; min-width: 120px; }
            .col-type { flex: 1.2; min-width: 90px; }
            .col-length { width: 76px; }
            .col-fk-select { flex: 1; min-width: 100px; }

            .col-checks { display: flex; gap: 10px; align-items: center; font-size: 12px; color: #64748b; white-space: nowrap; }
            .col-checks label { display: flex; align-items: center; gap: 4px; cursor: pointer; }

            .fk-label { font-size: 11px; font-weight: 700; color: #3b5bdb; white-space: nowrap; }

            .btn-remove-col { background: none; border: none; color: #ef4444; cursor: pointer; font-size: 17px; padding: 0 3px; line-height: 1; }
            .btn-remove-col:hover { color: #dc2626; }

            .btn-add-col {
                display: inline-flex; align-items: center; gap: 6px;
                background: #f0f2f7; color: #475569; padding: 7px 15px; border-radius: 5px;
                font-size: 12.5px; font-weight: 600; border: 1px solid #e4e8f1;
                cursor: pointer; font-family: inherit; margin-bottom: 4px;
            }
            .btn-add-col:hover { background: #e4e8f1; }

            /* SQL Preview panel */
            .preview-card {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; overflow: hidden;
                position: sticky; top: 86px;
            }
            .preview-card-head {
                padding: 12px 18px; border-bottom: 1px solid #e4e8f1;
                background: #f8f9fc; display: flex; align-items: center; justify-content: space-between;
            }
            .preview-card-head span { font-size: 11px; font-weight: 700; color: #6b7a99; text-transform: uppercase; letter-spacing: 0.06em; }
            .preview-card-head .preview-name-badge { font-size: 11px; color: #3b5bdb; background: #eef2ff; padding: 2px 8px; border-radius: 4px; font-family: 'Courier New', monospace; }
            .preview-sql {
                background: #0f1b3d; color: #a5f3fc; padding: 16px 18px;
                font-family: 'Courier New', monospace; font-size: 12px; line-height: 1.7;
                white-space: pre-wrap; max-height: 380px; overflow-y: auto; margin: 0;
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

    <div class="page-grid">
        {{-- Form --}}
        <div class="form-card">
            <div class="form-card-title">Buat Tabel Baru</div>
            <div class="form-card-sub">Database: {{ $sandbox->name }} — Prefix: <code>{{ $sandbox->prefix }}__</code></div>

            @if ($existingTables->count())
                <div class="info-box">
                    Tabel yang sudah ada:
                    @foreach ($existingTables as $et)
                        <strong>{{ $et->display_name }}</strong>{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                    — dapat dijadikan referensi Foreign Key.
                </div>
            @endif

            @if ($errors->any())
                <div class="form-errors">
                    @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.sandbox.table.store', $sandbox) }}" id="tableForm">
                @csrf

                <div class="form-group">
                    <label>Nama Tabel *</label>
                    <input type="text" name="display_name" value="{{ old('display_name') }}" required
                        placeholder="Contoh: penerbit" oninput="updatePreview()">
                    <div style="font-size:11px; color:#94a3b8; margin-top:4px;">
                        Akan disimpan sebagai: <code style="background:#f0f2f7; color:#3b5bdb; padding:1px 6px; border-radius:3px; font-family:'Courier New',monospace; font-size:11px;">{{ $sandbox->prefix }}__<span id="previewName">...</span></code>
                    </div>
                </div>

                <hr class="section-divider">
                <div class="section-title">Definisi Kolom</div>

                <div class="columns-container" id="columnsContainer">
                    <div class="col-row">
                        <div class="col-row-main">
                            <input type="text" name="columns[0][name]" class="col-name" placeholder="Nama kolom" value="id" required oninput="updatePreview()">
                            <select name="columns[0][type]" class="col-type" onchange="updatePreview()">
                                <option value="INT" selected>INT</option>
                                <option value="VARCHAR">VARCHAR</option>
                                <option value="TEXT">TEXT</option>
                                <option value="DATE">DATE</option>
                                <option value="DATETIME">DATETIME</option>
                                <option value="DECIMAL">DECIMAL</option>
                                <option value="BOOLEAN">BOOLEAN</option>
                            </select>
                            <input type="number" name="columns[0][length]" class="col-length" placeholder="Length" min="1" oninput="updatePreview()">
                            <div class="col-checks">
                                <label><input type="checkbox" name="columns[0][primary]" value="1" checked onchange="updatePreview()"> PK</label>
                                <label><input type="checkbox" name="columns[0][nullable]" value="1" onchange="updatePreview()"> Null</label>
                            </div>
                            <button type="button" class="btn-remove-col" onclick="removeColumn(this)" title="Hapus kolom">✕</button>
                        </div>
                        <div class="col-row-fk">
                            <span class="fk-label">FK →</span>
                            <select name="columns[0][fk_table]" class="col-fk-select" onchange="loadFkColumns(this, 0); updatePreview()">
                                <option value="">Tidak ada</option>
                                @foreach ($existingTables as $et)
                                    <option value="{{ $et->table_name }}">{{ $et->display_name }}</option>
                                @endforeach
                            </select>
                            <select name="columns[0][fk_column]" class="col-fk-select" id="fkCol0" onchange="updatePreview()">
                                <option value="">— Kolom —</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn-add-col" onclick="addColumn()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tambah Kolom
                </button>

                <hr class="section-divider">

                <div class="form-actions">
                    <a href="{{ route('admin.sandbox.show', $sandbox) }}" class="btn-secondary" style="text-decoration:none;">Batal</a>
                    <button type="submit" class="btn-primary">Buat Tabel</button>
                </div>
            </form>
        </div>

        {{-- SQL Preview --}}
        <div class="preview-card">
            <div class="preview-card-head">
                <span>Preview SQL</span>
                <span class="preview-name-badge" id="previewBadge">{{ $sandbox->prefix }}__...</span>
            </div>
            <pre class="preview-sql" id="sqlPreview">CREATE TABLE ...</pre>
        </div>
    </div>

    <x-slot:scripts>
        <script>
            let colIndex = 1;
            const prefix = '{{ $sandbox->prefix }}';
            const tableColumns = @json($tableColumns);
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
                        <button type="button" class="btn-remove-col" onclick="removeColumn(this)">✕</button>
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
                document.getElementById('previewBadge').textContent = prefix + '__' + (slug || '...');

                const tableName = prefix + '__' + (slug || 'nama_tabel');
                const rows = document.querySelectorAll('.col-row');
                let cols = [], fks = [];

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
                        def += type === 'INT' ? ' AUTO_INCREMENT PRIMARY KEY' : ' PRIMARY KEY';
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

                document.getElementById('sqlPreview').textContent =
                    'CREATE TABLE `' + tableName + '` (\n' + cols.concat(fks).join(',\n') + '\n);';
            }

            document.querySelectorAll('.col-row input, .col-row select').forEach(el => {
                el.addEventListener('input', updatePreview);
                el.addEventListener('change', updatePreview);
            });

            updatePreview();
        </script>
    </x-slot:scripts>

</x-layouts.admin>
