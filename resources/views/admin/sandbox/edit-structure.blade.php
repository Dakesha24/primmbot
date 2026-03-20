<x-layouts.admin title="Struktur — {{ $table->display_name }}">
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

            .table-banner {
                background: #fff;
                border: 1px solid #e4e8f1;
                border-radius: 14px;
                padding: 18px 24px;
                margin-bottom: 28px;
            }

            .table-banner h2 {
                font-size: 16px;
                font-weight: 700;
                color: #0f1b3d;
                margin-bottom: 3px;
            }

            .table-banner .meta {
                font-size: 12px;
                color: #94a3b8;
            }

            .table-banner code {
                background: #eef2ff;
                color: #3b5bdb;
                padding: 2px 8px;
                border-radius: 4px;
                font-size: 11px;
            }

            .section-label {
                font-size: 14px;
                font-weight: 700;
                color: #0f1b3d;
                margin-bottom: 14px;
            }

            /* Column list */
            .col-list {
                background: #fff;
                border: 1px solid #e4e8f1;
                border-radius: 14px;
                overflow: hidden;
                margin-bottom: 28px;
            }

            .col-list table {
                width: 100%;
                border-collapse: collapse;
                font-size: 13px;
            }

            .col-list thead tr {
                background: #f8f9fc;
                border-bottom: 1px solid #e4e8f1;
            }

            .col-list th {
                padding: 11px 16px;
                text-align: left;
                font-size: 11px;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 0.04em;
            }

            .col-list td {
                padding: 12px 16px;
                color: #1e293b;
            }

            .col-list tbody tr {
                border-bottom: 1px solid #f0f2f7;
            }

            .col-list tbody tr:last-child {
                border-bottom: none;
            }

            .col-list tbody tr:hover {
                background: #fafbfd;
            }

            .badge-pk {
                font-size: 10px;
                font-weight: 700;
                color: #fff;
                background: #0f1b3d;
                padding: 2px 8px;
                border-radius: 4px;
            }

            .badge-fk {
                font-size: 10px;
                font-weight: 700;
                color: #3b5bdb;
                background: #eef2ff;
                padding: 2px 8px;
                border-radius: 4px;
            }

            .badge-null {
                font-size: 10px;
                font-weight: 700;
                color: #94a3b8;
                background: #f0f2f7;
                padding: 2px 8px;
                border-radius: 4px;
            }

            .col-actions {
                display: flex;
                gap: 4px;
            }

            .btn-xs {
                padding: 5px 11px;
                border-radius: 6px;
                font-size: 11.5px;
                font-weight: 600;
                font-family: inherit;
                cursor: pointer;
                border: none;
                text-decoration: none;
                transition: all 0.15s;
            }

            .btn-xs-edit {
                color: #475569;
                background: #f1f5f9;
            }

            .btn-xs-edit:hover {
                background: #e2e8f0;
            }

            .btn-xs-del {
                color: #ef4444;
                background: #fff;
            }

            .btn-xs-del:hover {
                background: #fef2f2;
            }

            /* Add column form */
            .add-col-card {
                background: #fff;
                border: 1px solid #e4e8f1;
                border-radius: 14px;
                padding: 24px;
                margin-bottom: 28px;
            }

            .add-col-row {
                display: flex;
                gap: 10px;
                align-items: flex-end;
                flex-wrap: wrap;
                margin-bottom: 12px;
            }

            .add-col-row .field {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .add-col-row .field label {
                font-size: 11px;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
            }

            .add-col-row .field input,
            .add-col-row .field select {
                padding: 8px 10px;
                border: 1.5px solid #e2e8f0;
                border-radius: 8px;
                font-size: 13px;
                font-family: inherit;
                color: #1e293b;
                background: #fff;
            }

            .add-col-row .field input:focus,
            .add-col-row .field select:focus {
                outline: none;
                border-color: #0f1b3d;
            }

            .fk-row {
                display: flex;
                gap: 10px;
                align-items: flex-end;
                flex-wrap: wrap;
                padding-top: 12px;
                border-top: 1px dashed #e4e8f1;
                margin-bottom: 16px;
            }

            .fk-row .fk-label {
                font-size: 12px;
                font-weight: 700;
                color: #3b5bdb;
                padding-bottom: 8px;
            }
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

    <div class="table-banner">
        <h2>Struktur Tabel: {{ $table->display_name }}</h2>
        <div class="meta"><code>{{ $table->table_name }}</code></div>
    </div>

    <!-- Current Columns -->
    <div class="section-label">Kolom Saat Ini</div>
    <div class="col-list">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
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
                        <td style="font-weight:600;">{{ $col->Field }}</td>
                        <td><code
                                style="background:#f0f2f7; padding:2px 8px; border-radius:4px; font-size:12px;">{{ $col->Type }}</code>
                        </td>
                        <td>
                            @if ($col->Key === 'PRI')
                                <span class="badge-pk">PRIMARY KEY</span>
                            @endif
                            @if ($col->Null === 'YES')
                                <span class="badge-null">NULLABLE</span>
                            @endif
                            @if (str_contains($col->Extra, 'auto_increment'))
                                <span class="badge-null">AUTO_INCREMENT</span>
                            @endif
                        </td>
                        <td>
                            @if (isset($fkMap[$col->Field]))
                                <span class="badge-fk">→ {{ $fkMap[$col->Field] }}</span>
                            @else
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="col-actions">
                                @if ($col->Key !== 'PRI')
                                    <button type="button" class="btn-xs btn-xs-edit"
                                        onclick="toggleEditRow('{{ $col->Field }}')">Edit</button>
                                    <form method="POST"
                                        action="{{ route('admin.sandbox.table.dropColumn', [$sandbox, $table]) }}"
                                        onsubmit="return confirm('Hapus kolom {{ $col->Field }}? Data di kolom ini akan hilang.')"
                                        style="display:inline;">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="column" value="{{ $col->Field }}">
                                        <button type="submit" class="btn-xs btn-xs-del">Hapus</button>
                                    </form>
                                @else
                                    <span style="color:#cbd5e1; font-size:12px;">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- Edit row --}}
                    @if ($col->Key !== 'PRI')
                        <tr id="editRow-{{ $col->Field }}" style="display:none; background:#f8f9fc;">
                            <td colspan="5">
                                <form method="POST"
                                    action="{{ route('admin.sandbox.table.modifyColumn', [$sandbox, $table]) }}"
                                    style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="old_name" value="{{ $col->Field }}">
                                    <div class="field" style="display:flex; flex-direction:column; gap:3px;">
                                        <label style="font-size:10px; color:#94a3b8; font-weight:700;">NAMA</label>
                                        <input type="text" name="new_name" value="{{ $col->Field }}" required
                                            style="padding:7px 10px; border:1.5px solid #e2e8f0; border-radius:7px; font-size:13px; font-family:inherit; width:140px;">
                                    </div>
                                    <div class="field" style="display:flex; flex-direction:column; gap:3px;">
                                        <label style="font-size:10px; color:#94a3b8; font-weight:700;">TIPE</label>
                                        <select name="type"
                                            style="padding:7px 10px; border:1.5px solid #e2e8f0; border-radius:7px; font-size:13px; font-family:inherit;">
                                            @foreach (['INT', 'VARCHAR', 'TEXT', 'DATE', 'DATETIME', 'DECIMAL', 'BOOLEAN'] as $t)
                                                <option value="{{ $t }}"
                                                    {{ str_starts_with(strtoupper($col->Type), strtolower($t)) ? 'selected' : '' }}>
                                                    {{ $t }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field" style="display:flex; flex-direction:column; gap:3px;">
                                        <label style="font-size:10px; color:#94a3b8; font-weight:700;">LENGTH</label>
                                        <input type="number" name="length" min="1"
                                            style="padding:7px 10px; border:1.5px solid #e2e8f0; border-radius:7px; font-size:13px; font-family:inherit; width:80px;"
                                            value="{{ preg_match('/\((\d+)\)/', $col->Type, $m) ? $m[1] : '' }}">
                                    </div>
                                    <div style="display:flex; align-items:center; gap:4px; padding-top:16px;">
                                        <label
                                            style="font-size:12px; color:#64748b; cursor:pointer; display:flex; align-items:center; gap:4px;">
                                            <input type="checkbox" name="nullable" value="1"
                                                {{ $col->Null === 'YES' ? 'checked' : '' }}> Null
                                        </label>
                                    </div>
                                    <div style="display:flex; gap:6px; padding-top:16px;">
                                        <button type="submit" class="btn-xs"
                                            style="color:#16a34a; background:#f0fdf4;">Simpan</button>
                                        <button type="button" class="btn-xs" style="color:#64748b; background:#f1f5f9;"
                                            onclick="toggleEditRow('{{ $col->Field }}')">Batal</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add Column -->
    <div class="section-label">Tambah Kolom Baru</div>
    <div class="add-col-card">
        @if ($errors->any())
            <div class="form-errors" style="margin-bottom:16px;">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.sandbox.table.addColumn', [$sandbox, $table]) }}">
            @csrf
            <div class="add-col-row">
                <div class="field">
                    <label>Nama Kolom *</label>
                    <input type="text" name="name" required placeholder="nama_kolom" style="width:160px;">
                </div>
                <div class="field">
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
                <div class="field">
                    <label>Length</label>
                    <input type="number" name="length" min="1" placeholder="255" style="width:80px;">
                </div>
                <div style="display:flex; align-items:center; gap:4px; padding-bottom:2px;">
                    <label
                        style="font-size:12px; color:#64748b; cursor:pointer; display:flex; align-items:center; gap:4px;">
                        <input type="checkbox" name="nullable" value="1" checked> Null
                    </label>
                </div>
            </div>

            @if ($existingTables->count())
                <div class="fk-row">
                    <span class="fk-label">Foreign Key (opsional):</span>
                    <div class="field">
                        <label>Tabel Referensi</label>
                        <select name="fk_table" id="addFkTable" onchange="loadAddFkColumns()">
                            <option value="">Tidak ada</option>
                            @foreach ($existingTables as $et)
                                <option value="{{ $et->table_name }}">{{ $et->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Kolom Referensi</label>
                        <select name="fk_column" id="addFkColumn">
                            <option value="">— Kolom —</option>
                        </select>
                    </div>
                </div>
            @endif

            <button type="submit" class="btn-primary">+ Tambah Kolom</button>
        </form>
    </div>

    <div style="margin-top:16px;">
        <a href="{{ route('admin.sandbox.table.show', [$sandbox, $table]) }}"
            style="color:#3b5bdb; text-decoration:none; font-size:14px; font-weight:600;">← Kembali ke Data Tabel</a>
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
