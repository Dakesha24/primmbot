<x-layouts.admin title="Database — {{ $sandbox->name }}">
    <x-slot:styles>
    <style>
        .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 24px; font-size: 13px; flex-wrap: wrap; }
        .breadcrumb a { color: #3b5bdb; text-decoration: none; font-weight: 600; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb span { color: #94a3b8; }

        .db-info-bar {
            background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
            box-shadow: 3px 3px 0 #c8cfdc; padding: 18px 24px;
            margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px;
        }
        .db-info-bar-left { display: flex; align-items: center; gap: 14px; }
        .db-info-icon {
            width: 40px; height: 40px; border-radius: 6px; background: #eef2ff;
            display: flex; align-items: center; justify-content: center; color: #3b5bdb; flex-shrink: 0;
        }
        .db-info-title { font-size: 15px; font-weight: 700; color: #0f1b3d; margin-bottom: 3px; }
        .db-info-meta { font-size: 12px; color: #64748b; }
        .db-info-meta code { background: #eef2ff; color: #3b5bdb; padding: 1px 7px; border-radius: 4px; font-size: 11px; font-family: 'Courier New', monospace; }

        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .section-header h2 { font-size: 14px; font-weight: 700; color: #0f1b3d; }
        .section-header span { font-size: 12px; color: #94a3b8; }

        .btn-primary-nav {
            display: inline-flex; align-items: center; gap: 7px;
            background: #0f1b3d; color: #fff; padding: 8px 18px;
            border-radius: 6px; font-size: 12.5px; font-weight: 700;
            border: none; cursor: pointer; font-family: inherit; text-decoration: none;
            transition: background 0.15s;
        }
        .btn-primary-nav:hover { background: #1a2d5a; }

        .table-list-wrap {
            background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
            box-shadow: 3px 3px 0 #c8cfdc; overflow: hidden;
        }

        .table-list-item {
            display: flex; align-items: center; gap: 14px;
            padding: 14px 20px; border-bottom: 1px solid #f0f2f7;
            transition: background 0.1s;
        }
        .table-list-item:last-child { border-bottom: none; }
        .table-list-item:hover { background: #fafbfd; }

        .tbl-icon {
            width: 36px; height: 36px; border-radius: 6px; background: #f0f2f7;
            display: flex; align-items: center; justify-content: center; color: #6b7a99; flex-shrink: 0;
        }
        .tbl-info { flex: 1; min-width: 0; }
        .tbl-info h4 { font-size: 13.5px; font-weight: 700; color: #0f1b3d; margin-bottom: 2px; }
        .tbl-info .tbl-meta { font-size: 11.5px; color: #94a3b8; }
        .tbl-info .tbl-meta code { background: #f0f2f7; padding: 1px 5px; border-radius: 3px; font-size: 10.5px; font-family: 'Courier New', monospace; }

        .tbl-actions { display: flex; gap: 6px; flex-shrink: 0; }
        .btn-tbl {
            padding: 6px 13px; border-radius: 5px; font-size: 12px; font-weight: 600;
            font-family: inherit; cursor: pointer; border: none; text-decoration: none; transition: all 0.12s;
        }
        .btn-tbl-view { color: #3b5bdb; background: #eef2ff; }
        .btn-tbl-view:hover { background: #dbeafe; }
        .btn-tbl-del { color: #ef4444; background: #fff; border: 1px solid #f0f2f7; }
        .btn-tbl-del:hover { background: #fef2f2; border-color: #fecaca; }

        .empty-state {
            background: #fff; border: 2px dashed #d5dbe8; border-radius: 6px;
            padding: 56px 40px; text-align: center;
        }
        .empty-state svg { margin-bottom: 14px; color: #c7d0e2; }
        .empty-state p { font-size: 13px; color: #94a3b8; margin-bottom: 18px; }
    </style>
    </x-slot:styles>

    <div class="breadcrumb">
        <a href="{{ route('admin.sandbox.index') }}">Kelola Database</a>
        <span>›</span>
        <span>{{ $sandbox->name }}</span>
    </div>

    <div class="db-info-bar">
        <div class="db-info-bar-left">
            <div class="db-info-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
            </div>
            <div>
                <div class="db-info-title">{{ $sandbox->name }}</div>
                <div class="db-info-meta">
                    Prefix: <code>{{ $sandbox->prefix }}</code>
                    @if($sandbox->description) — {{ $sandbox->description }} @endif
                </div>
            </div>
        </div>
    </div>

    <div class="section-header">
        <div>
            <h2>Daftar Tabel</h2>
        </div>
        <div style="display:flex; align-items:center; gap:10px;">
            <span>{{ $tableData->count() }} tabel</span>
            <a href="{{ route('admin.sandbox.table.create', $sandbox) }}" class="btn-primary-nav">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Buat Tabel
            </a>
        </div>
    </div>

    @if($tableData->count())
        <div class="table-list-wrap">
            @foreach($tableData as $tbl)
                <div class="table-list-item">
                    <div class="tbl-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    </div>
                    <div class="tbl-info">
                        <h4>{{ $tbl->display_name }}</h4>
                        <div class="tbl-meta">
                            <code>{{ $tbl->table_name }}</code> &nbsp;·&nbsp; {{ $tbl->row_count }} baris data
                        </div>
                    </div>
                    <div class="tbl-actions">
                        <a href="{{ route('admin.sandbox.table.show', [$sandbox, $tbl]) }}" class="btn-tbl btn-tbl-view">Lihat & Isi Data</a>
                        <form method="POST" action="{{ route('admin.sandbox.table.destroy', [$sandbox, $tbl]) }}" onsubmit="return confirm('Hapus tabel {{ $tbl->display_name }}? Semua data akan hilang.')" style="display:contents;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-tbl btn-tbl-del">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            <p>Belum ada tabel. Buat tabel pertama untuk database ini.</p>
            <a href="{{ route('admin.sandbox.table.create', $sandbox) }}" class="btn-primary-nav">+ Buat Tabel</a>
        </div>
    @endif

</x-layouts.admin>
