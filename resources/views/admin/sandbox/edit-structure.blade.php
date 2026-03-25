<x-layouts.admin title="Struktur — {{ $table->display_name }}">
    <x-slot:styles>
        <style>
            .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 24px; font-size: 13px; flex-wrap: wrap; }
            .breadcrumb a { color: #3b5bdb; text-decoration: none; font-weight: 600; }
            .breadcrumb a:hover { text-decoration: underline; }
            .breadcrumb span { color: #94a3b8; }

            .tbl-header-bar {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; padding: 16px 22px;
                margin-bottom: 24px; display: flex; align-items: center; gap: 12px;
            }
            .tbl-header-icon {
                width: 36px; height: 36px; border-radius: 6px; background: #f0f2f7;
                display: flex; align-items: center; justify-content: center; color: #6b7a99; flex-shrink: 0;
            }
            .tbl-header-title { font-size: 15px; font-weight: 700; color: #0f1b3d; margin-bottom: 2px; }
            .tbl-header-meta { font-size: 11.5px; color: #94a3b8; }
            .tbl-header-meta code { background: #eef2ff; color: #3b5bdb; padding: 1px 6px; border-radius: 3px; font-size: 11px; font-family: 'Courier New', monospace; }

            .section-title { font-size: 13px; font-weight: 700; color: #0f1b3d; margin-bottom: 12px; }
            .section-divider { border: none; border-top: 1px dashed #e4e8f1; margin: 28px 0; }

            .struct-wrap {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; overflow: hidden; margin-bottom: 4px;
            }
            .struct-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
            .struct-table thead tr { background: #f8f9fc; border-bottom: 2px solid #e4e8f1; }
            .struct-table th {
                padding: 10px 16px; text-align: left; font-size: 10.5px; font-weight: 700;
                color: #6b7a99; text-transform: uppercase; letter-spacing: 0.05em;
            }
            .struct-table td { padding: 11px 16px; color: #1e293b; }
            .struct-table tbody tr { border-bottom: 1px solid #f0f2f7; }
            .struct-table tbody tr:last-child { border-bottom: none; }
            .struct-table tbody tr:hover td { background: #fafbfd; }
            .struct-table .edit-row td { background: #f8f9fc !important; }

            .badge { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 4px; }
            .badge-pk { color: #fff; background: #0f1b3d; }
            .badge-fk { color: #3b5bdb; background: #eef2ff; }
            .badge-null { color: #6b7a99; background: #f0f2f7; }

            .col-actions { display: flex; gap: 4px; }
            .btn-xs {
                padding: 5px 11px; border-radius: 5px; font-size: 11.5px; font-weight: 600;
                font-family: inherit; cursor: pointer; border: none; text-decoration: none; transition: all 0.12s;
            }
            .btn-xs-edit { color: #475569; background: #f1f5f9; }
            .btn-xs-edit:hover { background: #e2e8f0; }
            .btn-xs-del { color: #ef4444; background: #fff; border: 1px solid #f0f2f7; }
            .btn-xs-del:hover { background: #fef2f2; border-color: #fecaca; }
            .btn-xs-save { color: #16a34a; background: #f0fdf4; }
            .btn-xs-save:hover { background: #dcfce7; }
            .btn-xs-cancel { color: #64748b; background: #f1f5f9; }
            .btn-xs-cancel:hover { background: #e2e8f0; }

            /* Inline edit form inside table row */
            .inline-edit-form { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; padding: 2px 0; }
            .inline-field { display: flex; flex-direction: column; gap: 3px; }
            .inline-field label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
            .inline-field input, .inline-field select {
                padding: 6px 9px; border: 1.5px solid #dde1ea; border-radius: 5px;
                font-size: 12.5px; font-family: inherit; color: #1e293b; background: #fff;
            }
            .inline-field input:focus, .inline-field select:focus { outline: none; border-color: #0f1b3d; }
            .inline-field input[type="number"] { width: 76px; }
            .inline-field .null-check { display: flex; align-items: center; gap: 5px; font-size: 12px; color: #64748b; cursor: pointer; padding-top: 14px; }

            /* Add column card */
            .add-col-card {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; padding: 22px 24px;
            }
            .add-col-row { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 14px; }
            .add-col-field { display: flex; flex-direction: column; gap: 4px; }
            .add-col-field label { font-size: 10.5px; font-weight: 700; color: #6b7a99; text-transform: uppercase; letter-spacing: 0.04em; }
            .add-col-field input, .add-col-field select {
                padding: 7px 10px; border: 1.5px solid #dde1ea; border-radius: 5px;
                font-size: 12.5px; font-family: inherit; color: #1e293b; background: #fff;
            }
            .add-col-field input:focus, .add-col-field select:focus { outline: none; border-color: #0f1b3d; }
            .add-col-field .null-check { display: flex; align-items: center; gap: 5px; font-size: 12px; color: #64748b; cursor: pointer; padding-top: 16px; }

            .fk-section { padding-top: 14px; border-top: 1px dashed #e4e8f1; margin-bottom: 16px; }
            .fk-section-label { font-size: 11px; font-weight: 700; color: #3b5bdb; margin-bottom: 10px; }
            .fk-row { display: flex; gap: 10px; flex-wrap: wrap; }
        </style>
    </x-slot:styles>

    <div class="breadcrumb">
        <a href="{{ route('admin.sandbox.index') }}">Kelola Database</a>
        <span>›</span>
        <a href="{{ route('admin.sandbox.show', $sandbox) }}">{{ $sandbox->name }}</a>
        <span>›</span>
        <a href="{{ route('admin.sandbox.table.show', [$sandbox, $table]) }}">{{ $table->display_name }}</a>
        <span>›</span>
        <span>Edit Struktur</span>
    </div>

    <div class="tbl-header-bar">
        <div class="tbl-header-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
        </div>
        <div>
            <div class="tbl-header-title">Struktur: {{ $table->display_name }}</div>
            <div class="tbl-header-meta"><code>{{ $table->table_name }}</code></div>
        </div>
    </div>

    {{-- Current Columns --}}
    <div class="section-title">Kolom Saat Ini</div>
    <div class="struct-wrap">
        <table class="struct-table">
            <thead>
                <tr>
                    <th>Nama Kolom</th>
                    <th>Tipe</th>
                    <th>Atribut</th>
                    <th>Relasi FK</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($columns as $col)
                    {{-- View row --}}
                    <tr id="viewRow-{{ $col->Field }}">
                        <td style="font-weight:600; font-family:'Courier New',monospace; font-size:12px;">{{ $col->Field }}</td>
                        <td>
                            <code style="background:#f0f2f7; color:#1e293b; padding:2px 7px; border-radius:4px; font-size:11.5px; font-family:'Courier New',monospace;">{{ $col->Type }}</code>
                        </td>
                        <td style="display:flex; gap:4px; flex-wrap:wrap; align-items:center; min-height:38px;">
                            @if ($col->Key === 'PRI')<span class="badge badge-pk">PK</span>@endif
                            @if ($col->Null === 'YES')<span class="badge badge-null">NULL</span>@endif
                            @if (str_contains($col->Extra, 'auto_increment'))<span class="badge badge-null">AI</span>@endif
                        </td>
                        <td>
                            @if (isset($fkMap[$col->Field]))
                                <span class="badge badge-fk">→ {{ $fkMap[$col->Field] }}</span>
                            @else
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>
                        <td>
                            @if ($col->Key !== 'PRI')
                                <div class="col-actions">
                                    <button type="button" class="btn-xs btn-xs-edit" onclick="toggleEditRow('{{ $col->Field }}')">Edit</button>
                                    <form method="POST" action="{{ route('admin.sandbox.table.dropColumn', [$sandbox, $table]) }}" onsubmit="return confirm('Hapus kolom {{ $col->Field }}? Data di kolom ini akan hilang.')" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="column" value="{{ $col->Field }}">
                                        <button type="submit" class="btn-xs btn-xs-del">Hapus</button>
                                    </form>
                                </div>
                            @else
                                <span style="color:#cbd5e1; font-size:12px;">—</span>
                            @endif
                        </td>
                    </tr>

                    {{-- Inline edit row --}}
                    @if ($col->Key !== 'PRI')
                        <tr class="edit-row" id="editRow-{{ $col->Field }}" style="display:none;">
                            <td colspan="5" style="padding:14px 16px;">
                                <form method="POST" action="{{ route('admin.sandbox.table.modifyColumn', [$sandbox, $table]) }}" class="inline-edit-form">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="old_name" value="{{ $col->Field }}">
                                    <div class="inline-field">
                                        <label>Nama</label>
                                        <input type="text" name="new_name" value="{{ $col->Field }}" required style="width:140px;">
                                    </div>
                                    <div class="inline-field">
                                        <label>Tipe</label>
                                        <select name="type">
                                            @foreach (['INT', 'VARCHAR', 'TEXT', 'DATE', 'DATETIME', 'DECIMAL', 'BOOLEAN'] as $t)
                                                <option value="{{ $t }}" {{ str_starts_with(strtoupper($col->Type), $t) ? 'selected' : '' }}>{{ $t }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="inline-field">
                                        <label>Length</label>
                                        <input type="number" name="length" min="1" value="{{ preg_match('/\((\d+)\)/', $col->Type, $m) ? $m[1] : '' }}">
                                    </div>
                                    <label class="inline-field null-check">
                                        <input type="checkbox" name="nullable" value="1" {{ $col->Null === 'YES' ? 'checked' : '' }}> Null
                                    </label>
                                    <div style="display:flex; gap:6px; padding-top:14px;">
                                        <button type="submit" class="btn-xs btn-xs-save">Simpan</button>
                                        <button type="button" class="btn-xs btn-xs-cancel" onclick="toggleEditRow('{{ $col->Field }}')">Batal</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <hr class="section-divider">

    {{-- Add Column --}}
    <div class="section-title">Tambah Kolom Baru</div>
    <div class="add-col-card">
        @if ($errors->any())
            <div class="form-errors" style="margin-bottom:16px;">
                @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.sandbox.table.addColumn', [$sandbox, $table]) }}">
            @csrf
            <div class="add-col-row">
                <div class="add-col-field">
                    <label>Nama Kolom *</label>
                    <input type="text" name="name" required placeholder="nama_kolom" style="width:160px;">
                </div>
                <div class="add-col-field">
                    <label>Tipe *</label>
                    <select name="type">
                        <option value="INT">INT</option>
                        <option value="VARCHAR" selected>VARCHAR</option>
                        <option value="TEXT">TEXT</option>
                        <option value="DATE">DATE</option>
                        <option value="DATETIME">DATETIME</option>
                        <option value="DECIMAL">DECIMAL</option>
                        <option value="BOOLEAN">BOOLEAN</option>
                    </select>
                </div>
                <div class="add-col-field">
                    <label>Length</label>
                    <input type="number" name="length" min="1" placeholder="255" style="width:80px;">
                </div>
                <label class="add-col-field null-check">
                    <input type="checkbox" name="nullable" value="1" checked> Null
                </label>
            </div>

            @if ($existingTables->count())
                <div class="fk-section">
                    <div class="fk-section-label">Foreign Key (opsional)</div>
                    <div class="fk-row">
                        <div class="add-col-field">
                            <label>Tabel Referensi</label>
                            <select name="fk_table" id="addFkTable" onchange="loadAddFkColumns()">
                                <option value="">Tidak ada</option>
                                @foreach ($existingTables as $et)
                                    <option value="{{ $et->table_name }}">{{ $et->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="add-col-field">
                            <label>Kolom Referensi</label>
                            <select name="fk_column" id="addFkColumn">
                                <option value="">— Kolom —</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            <button type="submit" class="btn-primary">+ Tambah Kolom</button>
        </form>
    </div>

    <div style="margin-top:20px;">
        <a href="{{ route('admin.sandbox.table.show', [$sandbox, $table]) }}" style="color:#3b5bdb; text-decoration:none; font-size:13px; font-weight:600;">
            ← Kembali ke Data Tabel
        </a>
    </div>

    <x-slot:scripts>
        <script>
            const tableColumns = @json($tableColumns);

            function toggleEditRow(field) {
                const view = document.getElementById('viewRow-' + field);
                const edit = document.getElementById('editRow-' + field);
                if (edit.style.display === 'table-row') {
                    edit.style.display = 'none';
                    view.style.display = 'table-row';
                } else {
                    edit.style.display = 'table-row';
                    view.style.display = 'none';
                }
            }

            function loadAddFkColumns() {
                const table = document.getElementById('addFkTable').value;
                const select = document.getElementById('addFkColumn');
                select.innerHTML = '<option value="">— Kolom —</option>';
                if (table && tableColumns[table]) {
                    tableColumns[table].forEach(col => {
                        select.innerHTML += `<option value="${col}">${col}</option>`;
                    });
                }
            }
        </script>
    </x-slot:scripts>

</x-layouts.admin>
