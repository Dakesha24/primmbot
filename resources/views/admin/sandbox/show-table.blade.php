<x-layouts.admin title="Tabel — {{ $table->display_name }}">
    <x-slot:styles>
        <style>
            .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 24px; font-size: 13px; flex-wrap: wrap; }
            .breadcrumb a { color: #3b5bdb; text-decoration: none; font-weight: 600; }
            .breadcrumb a:hover { text-decoration: underline; }
            .breadcrumb span { color: #94a3b8; }

            .tbl-header-bar {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; padding: 16px 22px;
                margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 12px;
            }
            .tbl-header-bar-left { display: flex; align-items: center; gap: 12px; }
            .tbl-header-icon {
                width: 36px; height: 36px; border-radius: 6px; background: #f0f2f7;
                display: flex; align-items: center; justify-content: center; color: #6b7a99; flex-shrink: 0;
            }
            .tbl-header-title { font-size: 15px; font-weight: 700; color: #0f1b3d; margin-bottom: 2px; }
            .tbl-header-meta { font-size: 11.5px; color: #94a3b8; }
            .tbl-header-meta code { background: #eef2ff; color: #3b5bdb; padding: 1px 6px; border-radius: 3px; font-size: 11px; font-family: 'Courier New', monospace; }

            .btn-sm {
                display: inline-flex; align-items: center; gap: 5px;
                padding: 7px 14px; border-radius: 5px; font-size: 12px; font-weight: 700;
                font-family: inherit; cursor: pointer; border: none; text-decoration: none; transition: all 0.12s;
            }
            .btn-sm-outline { color: #3b5bdb; background: #eef2ff; border: 1px solid #c7d2fe; }
            .btn-sm-outline:hover { background: #dbeafe; }

            .section-title { font-size: 13px; font-weight: 700; color: #0f1b3d; margin-bottom: 12px; }
            .section-divider { border: none; border-top: 1px dashed #e4e8f1; margin: 28px 0; }

            .data-wrap {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; overflow-x: auto; margin-bottom: 4px;
            }

            .data-table { width: 100%; border-collapse: collapse; font-size: 12.5px; min-width: 500px; }
            .data-table thead tr { background: #f8f9fc; border-bottom: 2px solid #e4e8f1; }
            .data-table th {
                padding: 10px 14px; text-align: left; font-size: 10.5px; font-weight: 700;
                color: #6b7a99; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;
            }
            .data-table td { padding: 9px 14px; color: #1e293b; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .data-table tbody tr { border-bottom: 1px solid #f0f2f7; }
            .data-table tbody tr:last-child { border-bottom: none; }
            .data-table tbody tr:hover td { background: #fafbfd; }

            .btn-row {
                background: none; border: none; cursor: pointer;
                font-size: 11.5px; font-family: inherit; font-weight: 700;
                padding: 4px 9px; border-radius: 4px; transition: all 0.12s;
            }
            .btn-row-edit { color: #3b5bdb; }
            .btn-row-edit:hover { background: #eef2ff; }
            .btn-row-del { color: #ef4444; }
            .btn-row-del:hover { background: #fef2f2; }
            .btn-row-save { color: #16a34a; }
            .btn-row-save:hover { background: #f0fdf4; }
            .btn-row-cancel { color: #64748b; }
            .btn-row-cancel:hover { background: #f1f5f9; }

            .empty-data { padding: 36px; text-align: center; color: #94a3b8; font-size: 13px; }

            .edit-input {
                padding: 5px 8px; border: 1.5px solid #dde1ea; border-radius: 5px;
                font-size: 12px; font-family: inherit; color: #1e293b;
                width: 100%; min-width: 80px; background: #fff;
            }
            .edit-input:focus { outline: none; border-color: #3b5bdb; }
            .row-edit td { background: #f8f9fc !important; }

            .insert-card {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; padding: 22px 24px;
            }
            .insert-fields { display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 18px; }
            .insert-field { display: flex; flex-direction: column; gap: 4px; }
            .insert-field label { font-size: 10.5px; font-weight: 700; color: #6b7a99; text-transform: uppercase; letter-spacing: 0.04em; }
            .insert-field input {
                padding: 7px 11px; border: 1.5px solid #dde1ea; border-radius: 5px;
                font-size: 12.5px; font-family: inherit; color: #1e293b; background: #fff; width: 150px;
            }
            .insert-field input:focus { outline: none; border-color: #0f1b3d; }
            .auto-hint { font-size: 10px; color: #94a3b8; font-style: italic; }
        </style>
    </x-slot:styles>

    <div class="breadcrumb">
        <a href="{{ route('admin.sandbox.index') }}">Kelola Database</a>
        <span>›</span>
        <a href="{{ route('admin.sandbox.show', $sandbox) }}">{{ $sandbox->name }}</a>
        <span>›</span>
        <span>{{ $table->display_name }}</span>
    </div>

    <div class="tbl-header-bar">
        <div class="tbl-header-bar-left">
            <div class="tbl-header-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            </div>
            <div>
                <div class="tbl-header-title">{{ $table->display_name }}</div>
                <div class="tbl-header-meta"><code>{{ $table->table_name }}</code> &nbsp;·&nbsp; {{ $rows->count() }} baris</div>
            </div>
        </div>
        <a href="{{ route('admin.sandbox.table.structure', [$sandbox, $table]) }}" class="btn-sm btn-sm-outline">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Struktur
        </a>
    </div>

    {{-- Data Table --}}
    <div class="section-title">Data Tabel</div>
    <div class="data-wrap">
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
                    <tr id="view-{{ $index }}">
                        @foreach ($columns as $col)
                            <td>{{ data_get((array) $row, $col, '—') }}</td>
                        @endforeach
                        <td style="white-space:nowrap;">
                            <button type="button" class="btn-row btn-row-edit" onclick="toggleEdit({{ $index }})">Edit</button>
                            <form method="POST" action="{{ route('admin.sandbox.table.deleteRow', [$sandbox, $table]) }}" style="display:inline;" onsubmit="return confirm('Hapus baris ini?')">
                                @csrf @method('DELETE')
                                <input type="hidden" name="row_index" value="{{ $index }}">
                                <button type="submit" class="btn-row btn-row-del">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <tr class="row-edit" id="edit-{{ $index }}" style="display:none;">
                        <form method="POST" action="{{ route('admin.sandbox.table.updateRow', [$sandbox, $table]) }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="row_index" value="{{ $index }}">
                            @foreach ($columns as $col)
                                <td>
                                    @if ($col === 'id')
                                        <span style="color:#94a3b8; font-size:12px;">{{ data_get((array) $row, $col) }}</span>
                                    @else
                                        <input type="text" name="edit_{{ $col }}" value="{{ data_get((array) $row, $col) }}" class="edit-input">
                                    @endif
                                </td>
                            @endforeach
                            <td style="white-space:nowrap;">
                                <button type="submit" class="btn-row btn-row-save">Simpan</button>
                                <button type="button" class="btn-row btn-row-cancel" onclick="toggleEdit({{ $index }})">Batal</button>
                            </td>
                        </form>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}" class="empty-data">Belum ada data. Gunakan form di bawah untuk menambah baris pertama.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <hr class="section-divider">

    {{-- Insert Row --}}
    <div class="section-title">Tambah Baris Data</div>
    <div class="insert-card">
        @if ($errors->any())
            <div class="form-errors" style="margin-bottom:16px;">
                @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('admin.sandbox.table.insert', [$sandbox, $table]) }}">
            @csrf
            <div class="insert-fields">
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
