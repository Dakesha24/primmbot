<x-layouts.admin title="Tahun Ajaran">

    <x-slot:styles>
        <style>
            .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; }
            .page-header h1 { font-size:22px; font-weight:800; color:#0f1b3d; }
            .btn-add {
                display:inline-flex; align-items:center; gap:8px;
                background:#0f1b3d; color:#fff; padding:10px 22px;
                border-radius:10px; font-size:13px; font-weight:700;
                border:none; cursor:pointer; font-family:inherit;
            }
            .btn-add:hover { background:#1a2d5a; }
            .data-table-wrap { background:#fff; border:1px solid #e4e8f1; border-radius:14px; overflow:hidden; }
            .data-table { width:100%; border-collapse:collapse; font-size:13px; }
            .data-table thead tr { background:#f8f9fc; border-bottom:1px solid #e4e8f1; }
            .data-table th { padding:12px 16px; text-align:left; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.04em; }
            .data-table td { padding:14px 16px; color:#1e293b; border-bottom:1px solid #f0f2f7; }
            .data-table tbody tr:last-child td { border-bottom:none; }
            .data-table tbody tr:hover { background:#fafbfd; }
            .badge-active { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; background:#d1fae5; color:#065f46; }
            .badge-inactive { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; background:#f1f5f9; color:#94a3b8; }
            .badge-kelas { display:inline-block; padding:3px 10px; border-radius:6px; font-size:11px; font-weight:700; background:#eef2ff; color:#3b5bdb; }
            .action-btns { display:flex; gap:8px; }
            .btn-sm { padding:5px 12px; border-radius:6px; font-size:11.5px; font-weight:600; cursor:pointer; border:none; font-family:inherit; }
            .btn-edit { background:#f1f5f9; color:#475569; }
            .btn-edit:hover { background:#e2e8f0; }
            .btn-del { background:#fef2f2; color:#ef4444; }
            .btn-del:hover { background:#fee2e2; }
            .btn-activate { background:#d1fae5; color:#065f46; }
            .btn-activate:hover { background:#a7f3d0; }
            .empty-row td { text-align:center; padding:48px; color:#94a3b8; }
        </style>
    </x-slot:styles>

    <div class="page-header">
        <h1>Tahun Ajaran</h1>
        <button class="btn-add" onclick="openModal('create')">+ Tambah Tahun Ajaran</button>
    </div>

    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tahun Ajaran</th>
                    <th>Status</th>
                    <th>Jumlah Kelas</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tahunAjaranList as $ta)
                    <tr>
                        <td style="color:#94a3b8;width:40px;">{{ $loop->iteration }}</td>
                        <td style="font-weight:600;">{{ $ta->name }}</td>
                        <td>
                            @if ($ta->is_active)
                                <span class="badge-active">
                                    <span style="width:6px;height:6px;border-radius:50%;background:#10b981;"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="badge-inactive">Tidak Aktif</span>
                            @endif
                        </td>
                        <td><span class="badge-kelas">{{ $ta->kelas_count }} kelas</span></td>
                        <td>
                            <div class="action-btns">
                                @if (!$ta->is_active)
                                    <form method="POST" action="{{ route('admin.tahun-ajaran.activate', $ta) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn-sm btn-activate">Aktifkan</button>
                                    </form>
                                @endif
                                <button class="btn-sm btn-edit"
                                    onclick="openEdit({{ $ta->id }}, '{{ addslashes($ta->name) }}', {{ $ta->is_active ? 'true' : 'false' }})">Edit</button>
                                <form method="POST" action="{{ route('admin.tahun-ajaran.destroy', $ta) }}"
                                    onsubmit="return confirm('Hapus tahun ajaran ini? Semua kelas yang terhubung akan ikut terhapus.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-sm btn-del">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="5">Belum ada tahun ajaran. Tambahkan tahun ajaran pertama.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah --}}
    <div class="modal-backdrop" id="createModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal('create')">&times;</button>
            <h2>Tambah Tahun Ajaran</h2>
            @if ($errors->any() && !old('_edit'))
                <div class="form-errors">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            @endif
            <form method="POST" action="{{ route('admin.tahun-ajaran.store') }}">
                @csrf
                <div class="form-group">
                    <label>Tahun Ajaran *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: 2025/2026">
                </div>
                <div class="form-group" style="display:flex;align-items:center;gap:10px;">
                    <input type="checkbox" name="is_active" id="isActiveCreate" value="1" style="width:auto;">
                    <label for="isActiveCreate" style="margin:0;text-transform:none;font-size:13px;font-weight:500;color:#1e293b;">Jadikan tahun ajaran aktif</label>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('create')">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal-backdrop" id="editModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal('edit')">&times;</button>
            <h2>Edit Tahun Ajaran</h2>
            @if ($errors->any() && old('_edit'))
                <div class="form-errors">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            @endif
            <form method="POST" id="editForm">
                @csrf @method('PUT')
                <input type="hidden" name="_edit" value="1">
                <div class="form-group">
                    <label>Tahun Ajaran *</label>
                    <input type="text" name="name" id="editName" required>
                </div>
                <div class="form-group" style="display:flex;align-items:center;gap:10px;">
                    <input type="checkbox" name="is_active" id="isActiveEdit" value="1" style="width:auto;">
                    <label for="isActiveEdit" style="margin:0;text-transform:none;font-size:13px;font-weight:500;color:#1e293b;">Jadikan tahun ajaran aktif</label>
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
            function openEdit(id, name, isActive) {
                document.getElementById('editForm').action = '/admin/tahun-ajaran/'+id;
                document.getElementById('editName').value = name;
                document.getElementById('isActiveEdit').checked = isActive;
                openModal('edit');
            }
            document.querySelectorAll('.modal-backdrop').forEach(m => {
                m.addEventListener('click', e => { if (e.target === m) m.classList.remove('active'); });
            });
            @if ($errors->any() && old('_edit')) openModal('edit');
            @elseif ($errors->any()) openModal('create');
            @endif
        </script>
    </x-slot:scripts>

</x-layouts.admin>
