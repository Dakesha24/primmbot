<x-layouts.admin title="Tabel — {{ $table->display_name }}">
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
                margin-bottom: 24px;
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

            .data-table-wrap {
                background: #fff;
                border: 1px solid #e4e8f1;
                border-radius: 14px;
                overflow-x: auto;
                margin-bottom: 28px;
            }

            .data-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 13px;
                min-width: 500px;
            }

            .data-table thead tr {
                background: #f8f9fc;
                border-bottom: 1px solid #e4e8f1;
            }

            .data-table th {
                padding: 11px 14px;
                text-align: left;
                font-size: 11px;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                white-space: nowrap;
            }

            .data-table td {
                padding: 10px 14px;
                color: #1e293b;
                max-width: 200px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .data-table tbody tr {
                border-bottom: 1px solid #f0f2f7;
            }

            .data-table tbody tr:last-child {
                border-bottom: none;
            }

            .data-table tbody tr:hover {
                background: #fafbfd;
            }

            .btn-row {
                background: none;
                border: none;
                cursor: pointer;
                font-size: 12px;
                font-family: inherit;
                font-weight: 600;
                padding: 4px 10px;
                border-radius: 6px;
                transition: all 0.15s;
            }

            .btn-row-edit {
                color: #3b5bdb;
            }

            .btn-row-edit:hover {
                background: #eef2ff;
            }

            .btn-row-del {
                color: #ef4444;
            }

            .btn-row-del:hover {
                background: #fef2f2;
            }

            .btn-row-save {
                color: #16a34a;
            }

            .btn-row-save:hover {
                background: #f0fdf4;
            }

            .btn-row-cancel {
                color: #64748b;
            }

            .btn-row-cancel:hover {
                background: #f1f5f9;
            }

            .empty-data {
                padding: 40px;
                text-align: center;
                color: #94a3b8;
                font-size: 13px;
            }

            /* Edit mode */
            .edit-input {
                padding: 5px 8px;
                border: 1.5px solid #e2e8f0;
                border-radius: 6px;
                font-size: 12px;
                font-family: inherit;
                color: #1e293b;
                width: 100%;
                min-width: 80px;
                background: #fff;
            }

            .edit-input:focus {
                outline: none;
                border-color: #3b5bdb;
            }

            .row-view {
                display: table-row;
            }

            .row-edit {
                display: none;
            }

            .row-edit td {
                background: #f8f9fc;
            }

            /* Insert form */
            .insert-card {
                background: #fff;
                border: 1px solid #e4e8f1;
                border-radius: 14px;
                padding: 24px;
            }

            .insert-row {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-bottom: 16px;
            }

            .insert-field {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .insert-field label {
                font-size: 11px;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
            }

            .insert-field input {
                padding: 8px 12px;
                border: 1.5px solid #e2e8f0;
                border-radius: 8px;
                font-size: 13px;
                font-family: inherit;
                color: #1e293b;
                background: #fff;
                width: 160px;
            }

            .insert-field input:focus {
                outline: none;
                border-color: #0f1b3d;
                box-shadow: 0 0 0 3px rgba(15, 27, 61, 0.06);
            }

            .insert-field .auto-hint {
                font-size: 10px;
                color: #94a3b8;
                font-style: italic;
            }
        </style>
    </x-slot:styles>

    <div class="breadcrumb">
        <a href="{{ route('admin.sandbox.index') }}">Kelola Database</a>
        <span>›</span>
        <a href="{{ route('admin.sandbox.show', $sandbox) }}">{{ $sandbox->name }}</a>
        <span>›</span>
        <span>{{ $table->display_name }}</span>
    </div>

    <div class="table-banner" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h2>{{ $table->display_name }}</h2>
            <div class="meta"><code>{{ $table->table_name }}</code> — {{ $rows->count() }} baris</div>
        </div>
        <a href="{{ route('admin.sandbox.table.structure', [$sandbox, $table]) }}"
            style="padding:8px 18px; border-radius:8px; font-size:12px; font-weight:700; color:#3b5bdb; background:#eef2ff; text-decoration:none;">Edit
            Struktur</a>
    </div>

    <!-- Data Table -->
    <div class="section-label">Data Tabel</div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    @foreach ($columns as $col)
                        <th>{{ $col }}</th>
                    @endforeach
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $index => $row)
                    {{-- View mode --}}
                    <tr class="row-view" id="view-{{ $index }}">
                        @foreach ($columns as $col)
                            <td>{{ data_get((array) $row, $col, '-') }}</td>
                        @endforeach
                        <td style="white-space: nowrap;">
                            <button type="button" class="btn-row btn-row-edit"
                                onclick="toggleEdit({{ $index }})">Edit</button>
                            <form method="POST"
                                action="{{ route('admin.sandbox.table.deleteRow', [$sandbox, $table]) }}"
                                style="display:inline;" onsubmit="return confirm('Hapus baris ini?')">
                                @csrf @method('DELETE')
                                <input type="hidden" name="row_index" value="{{ $index }}">
                                <button type="submit" class="btn-row btn-row-del">Hapus</button>
                            </form>
                        </td>
                    </tr>

                    {{-- Edit mode --}}
                    <tr class="row-edit" id="edit-{{ $index }}">
                        <form method="POST" action="{{ route('admin.sandbox.table.updateRow', [$sandbox, $table]) }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="row_index" value="{{ $index }}">
                            @foreach ($columns as $col)
                                <td>
                                    @if ($col === 'id')
                                        <span style="color:#94a3b8;">{{ data_get((array) $row, $col) }}</span>
                                    @else
                                        <input type="text" name="edit_{{ $col }}"
                                            value="{{ data_get((array) $row, $col) }}" class="edit-input">
                                    @endif
                                </td>
                            @endforeach
                            <td style="white-space: nowrap;">
                                <button type="submit" class="btn-row btn-row-save">Simpan</button>
                                <button type="button" class="btn-row btn-row-cancel"
                                    onclick="toggleEdit({{ $index }})">Batal</button>
                            </td>
                        </form>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}" class="empty-data">Belum ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Insert Row -->
    <div class="section-label">Tambah Data</div>
    <div class="insert-card">
        @if ($errors->any())
            <div class="form-errors" style="margin-bottom:16px;">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.sandbox.table.insert', [$sandbox, $table]) }}">
            @csrf
            <div class="insert-row">
                @foreach ($columns as $col)
                    <div class="insert-field">
                        <label>{{ $col }}</label>
                        @if (in_array($col, ['id', 'created_at', 'updated_at']))
                            <input type="text" name="col_{{ $col }}" placeholder="Otomatis">
                            <span class="auto-hint">Kosongkan = otomatis</span>
                        @else
                            <input type="text" name="col_{{ $col }}" placeholder="{{ $col }}">
                        @endif
                    </div>
                @endforeach
            </div>
            <button type="submit" class="btn-primary">+ Tambah Baris</button>
        </form>
    </div>

    <x-slot:scripts>
        <script>
            function toggleEdit(index) {
                const view = document.getElementById('view-' + index);
                const edit = document.getElementById('edit-' + index);

                if (edit.style.display === 'table-row') {
                    edit.style.display = 'none';
                    view.style.display = 'table-row';
                } else {
                    edit.style.display = 'table-row';
                    view.style.display = 'none';
                }
            }
        </script>
    </x-slot:scripts>

</x-layouts.admin>
