<x-layouts.admin title="Kelola Database">

    <x-slot:styles>
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
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
            transition: all 0.2s;
        }

        .btn-add:hover { background: #1a2d5a; transform: translateY(-1px); }

        .db-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 22px;
        }

        .db-card {
            background: #fff;
            border: 1px solid #e4e8f1;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.25s;
        }

        .db-card:hover {
            border-color: #c7d0e2;
            box-shadow: 0 8px 32px rgba(15,27,61,0.07);
            transform: translateY(-2px);
        }

        .card-accent {
            height: 4px;
            background: linear-gradient(90deg, #0f1b3d, #3b5bdb);
        }

        .card-body { padding: 24px 24px 0; flex: 1; }

        .card-prefix {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            color: #3b5bdb;
            background: #eef2ff;
            padding: 3px 10px;
            border-radius: 6px;
            margin-bottom: 12px;
            font-family: 'Courier New', monospace;
        }

        .card-body h3 {
            font-size: 17px;
            font-weight: 700;
            color: #0f1b3d;
            margin-bottom: 8px;
        }

        .card-body .desc {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-stats {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .card-stats .stat {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            background: #f8f9fc;
            padding: 5px 10px;
            border-radius: 7px;
        }

        .card-footer {
            display: flex;
            border-top: 1px solid #f0f2f7;
        }

        .card-footer .cf-btn {
            flex: 1;
            padding: 13px 0;
            text-align: center;
            font-size: 12.5px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            border: none;
            background: none;
            text-decoration: none;
            transition: all 0.15s;
        }

        .card-footer .cf-btn + .cf-btn { border-left: 1px solid #f0f2f7; }

        .cf-manage { color: #3b5bdb; }
        .cf-manage:hover { background: #eef2ff; }

        .cf-edit { color: #475569; }
        .cf-edit:hover { background: #f8fafc; }

        .cf-delete { color: #ef4444; }
        .cf-delete:hover { background: #fef2f2; }

        .empty-state {
            grid-column: 1 / -1;
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

    <div class="page-header">
        <p>{{ $databases->count() }} database tersedia</p>
        <button class="btn-add" onclick="openModal('create')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Database
        </button>
    </div>

    <div class="db-grid">
        @forelse($databases as $db)
            <div class="db-card">
                <div class="card-accent"></div>
                <div class="card-body">
                    <span class="card-prefix">{{ $db->prefix }}</span>
                    <h3>{{ $db->name }}</h3>
                    <p class="desc">{{ $db->description ?? 'Tidak ada deskripsi.' }}</p>
                    <div class="card-stats">
                        <span class="stat">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                            {{ $db->sandbox_tables_count }} tabel
                        </span>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.sandbox.show', $db) }}" class="cf-btn cf-manage">Kelola Tabel →</a>
                    <button class="cf-btn cf-edit" onclick="openEditModal({{ $db->id }}, '{{ addslashes($db->name) }}', `{{ addslashes($db->description ?? '') }}`)">Edit</button>
                    <form method="POST" action="{{ route('admin.sandbox.destroy', $db) }}" onsubmit="return confirm('Hapus database ini beserta semua tabelnya?')" style="flex:1; display:flex;">
                        @csrf @method('DELETE')
                        <button type="submit" class="cf-btn cf-delete" style="flex:1;">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                <p>Belum ada database. Buat database pertama untuk digunakan di soal PRIMM.</p>
                <button class="btn-add" onclick="openModal('create')">+ Buat Database</button>
            </div>
        @endforelse
    </div>

    <!-- Modal Tambah -->
    <div class="modal-backdrop" id="createModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal('create')">&times;</button>
            <h2>Buat Database Baru</h2>
            @if($errors->any() && !old('_edit'))
                <div class="form-errors">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            @endif
            <form method="POST" action="{{ route('admin.sandbox.store') }}">
                @csrf
                <div class="form-group">
                    <label>Nama Database *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Toko Buku">
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Deskripsi singkat database...">{{ old('description') }}</textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('create')">Batal</button>
                    <button type="submit" class="btn-primary">Buat</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal-backdrop" id="editModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal('edit')">&times;</button>
            <h2>Edit Database</h2>
            @if($errors->any() && old('_edit'))
                <div class="form-errors">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            @endif
            <form method="POST" id="editForm">
                @csrf @method('PUT')
                <input type="hidden" name="_edit" value="1">
                <div class="form-group">
                    <label>Nama Database *</label>
                    <input type="text" name="name" id="editName" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" id="editDesc" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('edit')">Batal</button>
                    <button type="submit" class="btn-primary">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

    <x-slot:scripts>
    <script>
        function openModal(t) { document.getElementById(t+'Modal').classList.add('active'); }
        function closeModal(t) { document.getElementById(t+'Modal').classList.remove('active'); }
        function openEditModal(id, name, desc) {
            document.getElementById('editForm').action = '/admin/sandbox/' + id;
            document.getElementById('editName').value = name;
            document.getElementById('editDesc').value = desc;
            openModal('edit');
        }
        document.querySelectorAll('.modal-backdrop').forEach(m => {
            m.addEventListener('click', function(e) { if(e.target === this) this.classList.remove('active'); });
        });
        @if($errors->any() && old('_edit')) openModal('edit');
        @elseif($errors->any()) openModal('create');
        @endif
    </script>
    </x-slot:scripts>

</x-layouts.admin>