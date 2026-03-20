<x-layouts.admin title="Database — {{ $sandbox->name }}">

    <x-slot:styles>
    <style>
        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 24px; font-size: 13px;
        }
        .breadcrumb a { color: #3b5bdb; text-decoration: none; font-weight: 600; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb span { color: #94a3b8; }

        .db-banner {
            background: #fff;
            border: 1px solid #e4e8f1;
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .db-banner h2 { font-size: 16px; font-weight: 700; color: #0f1b3d; margin-bottom: 4px; }
        .db-banner p { font-size: 13px; color: #94a3b8; }
        .db-banner code { background: #eef2ff; color: #3b5bdb; padding: 2px 8px; border-radius: 4px; font-size: 12px; }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-header p { color: #94a3b8; font-size: 14px; }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #0f1b3d;
            color: #fff;
            padding: 10px 22px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            font-family: inherit;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-add:hover { background: #1a2d5a; transform: translateY(-1px); }

        .table-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .table-item {
            background: #fff;
            border: 1px solid #e4e8f1;
            border-radius: 14px;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.2s;
        }

        .table-item:hover {
            border-color: #c7d0e2;
            box-shadow: 0 4px 16px rgba(15,27,61,0.05);
        }

        .table-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #eef2ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b5bdb;
            flex-shrink: 0;
        }

        .table-info { flex: 1; }
        .table-info h4 { font-size: 14px; font-weight: 700; color: #0f1b3d; margin-bottom: 3px; }
        .table-info .meta { font-size: 12px; color: #94a3b8; }
        .table-info .meta code { background: #f0f2f7; padding: 1px 6px; border-radius: 4px; font-size: 11px; }

        .table-actions {
            display: flex;
            gap: 6px;
            flex-shrink: 0;
        }

        .table-actions a,
        .table-actions button {
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.15s;
        }

        .act-view { color: #3b5bdb; background: #eef2ff; }
        .act-view:hover { background: #dbeafe; }

        .act-delete { color: #ef4444; background: #fff; }
        .act-delete:hover { background: #fef2f2; }

        .empty-state {
            background: #fff;
            border: 2px dashed #d5dbe8;
            border-radius: 16px;
            padding: 64px 40px;
            text-align: center;
        }

        .empty-state svg { margin-bottom: 16px; color: #c7d0e2; }
        .empty-state p { font-size: 14px; color: #94a3b8; margin-bottom: 20px; }
    </style>
    </x-slot:styles>

    <div class="breadcrumb">
        <a href="{{ route('admin.sandbox.index') }}">Kelola Database</a>
        <span>›</span>
        <span>{{ $sandbox->name }}</span>
    </div>

    <div class="db-banner">
        <div>
            <h2>{{ $sandbox->name }}</h2>
            <p>{{ $sandbox->description ?? 'Tidak ada deskripsi.' }} — Prefix: <code>{{ $sandbox->prefix }}</code></p>
        </div>
    </div>

    <div class="page-header">
        <p>{{ $tableData->count() }} tabel</p>
        <a href="{{ route('admin.sandbox.table.create', $sandbox) }}" class="btn-add">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Tabel
        </a>
    </div>

    @if($tableData->count())
        <div class="table-list">
            @foreach($tableData as $tbl)
                <div class="table-item">
                    <div class="table-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    </div>
                    <div class="table-info">
                        <h4>{{ $tbl->display_name }}</h4>
                        <div class="meta">
                            <code>{{ $tbl->table_name }}</code> — {{ $tbl->row_count }} baris data
                        </div>
                    </div>
                    <div class="table-actions">
                        <a href="{{ route('admin.sandbox.table.show', [$sandbox, $tbl]) }}" class="act-view">Lihat & Isi Data</a>
                        <form method="POST" action="{{ route('admin.sandbox.table.destroy', [$sandbox, $tbl]) }}" onsubmit="return confirm('Hapus tabel {{ $tbl->display_name }}? Semua data akan hilang.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="act-delete">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            <p>Belum ada tabel. Buat tabel pertama untuk database ini.</p>
            <a href="{{ route('admin.sandbox.table.create', $sandbox) }}" class="btn-add">+ Buat Tabel</a>
        </div>
    @endif

</x-layouts.admin>