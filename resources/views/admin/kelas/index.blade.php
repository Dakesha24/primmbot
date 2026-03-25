<x-layouts.admin title="Kelola LKPD">

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
            .action-btns { display:flex; gap:8px; }
            .btn-sm { padding:5px 12px; border-radius:6px; font-size:11.5px; font-weight:600; cursor:pointer; border:none; font-family:inherit; }
            .btn-edit { background:#f1f5f9; color:#475569; }
            .btn-edit:hover { background:#e2e8f0; }
            .btn-del { background:#fef2f2; color:#ef4444; }
            .btn-del:hover { background:#fee2e2; }
            .empty-row td { text-align:center; padding:48px; color:#94a3b8; }
            .school-badge { display:inline-block; padding:3px 10px; border-radius:6px; font-size:11px; font-weight:700; background:#f1f5f9; color:#475569; }
            .ta-badge { display:inline-block; padding:3px 10px; border-radius:6px; font-size:11px; font-weight:700; background:#eef2ff; color:#3b5bdb; }
        </style>
    </x-slot:styles>

    <div class="page-header">
        <h1>Kelola LKPD</h1>
        @if ($schools->isEmpty() || $tahunAjaranList->isEmpty())
            <span style="font-size:12px;color:#f59e0b;font-weight:600;">⚠ Tambah sekolah &amp; tahun ajaran dahulu sebelum membuat kelas.</span>
        @else
            <button class="btn-add" onclick="openModal('create')">+ Tambah Kelas</button>
        @endif
    </div>

    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Kelas</th>
                    <th>Sekolah</th>
                    <th>Tahun Ajaran</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kelasList as $kelas)
                    <tr>
                        <td style="color:#94a3b8;width:40px;">{{ $loop->iteration }}</td>
                        <td style="font-weight:700;">{{ $kelas->name }}</td>
                        <td><span class="school-badge">{{ $kelas->school->name }}</span></td>
                        <td><span class="ta-badge">{{ $kelas->tahunAjaran->name }}</span></td>
                        <td>
                            <div class="action-btns">
                                <button class="btn-sm btn-edit"
                                    onclick="openEdit({{ $kelas->id }}, '{{ addslashes($kelas->name) }}', {{ $kelas->school_id }}, {{ $kelas->tahun_ajaran_id }})">Edit</button>
                                <form method="POST" action="{{ route('admin.kelas.destroy', $kelas) }}"
                                    onsubmit="return confirm('Hapus kelas ini? Profil siswa yang terhubung akan kehilangan kelasnya.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-sm btn-del">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="5">Belum ada kelas. Tambahkan kelas pertama.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah --}}
    <div class="modal-backdrop" id="createModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal('create')">&times;</button>
            <h2>Tambah Kelas</h2>
            @if ($errors->any() && !old('_edit'))
                <div class="form-errors">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            @endif
            <form method="POST" action="{{ route('admin.kelas.store') }}">
                @csrf
                <div class="form-group">
                    <label>Sekolah *</label>
                    <select name="school_id" required>
                        <option value="" disabled selected>Pilih sekolah</option>
                        @foreach ($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Tahun Ajaran *</label>
                    <select name="tahun_ajaran_id" required>
                        <option value="" disabled selected>Pilih tahun ajaran</option>
                        @foreach ($tahunAjaranList as $ta)
                            <option value="{{ $ta->id }}" {{ old('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                                {{ $ta->name }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Kelas *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: XI PPLG 1">
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
            <h2>Edit Kelas</h2>
            @if ($errors->any() && old('_edit'))
                <div class="form-errors">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            @endif
            <form method="POST" id="editForm">
                @csrf @method('PUT')
                <input type="hidden" name="_edit" value="1">
                <div class="form-group">
                    <label>Sekolah *</label>
                    <select name="school_id" id="editSchool" required>
                        @foreach ($schools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Tahun Ajaran *</label>
                    <select name="tahun_ajaran_id" id="editTahunAjaran" required>
                        @foreach ($tahunAjaranList as $ta)
                            <option value="{{ $ta->id }}">{{ $ta->name }}{{ $ta->is_active ? ' (Aktif)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Kelas *</label>
                    <input type="text" name="name" id="editName" required>
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
            function openEdit(id, name, schoolId, tahunId) {
                document.getElementById('editForm').action = '/admin/kelas/'+id;
                document.getElementById('editName').value = name;
                document.getElementById('editSchool').value = schoolId;
                document.getElementById('editTahunAjaran').value = tahunId;
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
