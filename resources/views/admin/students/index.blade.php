<x-layouts.admin title="Kelola Siswa">

    <x-slot:styles>
        <style>
            .stats-row {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 16px;
                margin-bottom: 24px;
            }

            .stat-card {
                background: #fff;
                border: 1px solid #e4e8f1;
                border-radius: 12px;
                padding: 18px 20px;
            }

            .stat-card .stat-label {
                font-size: 11px;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                margin-bottom: 6px;
            }

            .stat-card .stat-value {
                font-size: 28px;
                font-weight: 800;
                color: #0f1b3d;
            }

            .toolbar {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 24px;
                flex-wrap: wrap;
            }

            .search-box {
                flex: 1;
                min-width: 220px;
                position: relative;
            }

            .search-box input {
                width: 100%;
                padding: 9px 14px 9px 38px;
                border: 1.5px solid #e2e8f0;
                border-radius: 10px;
                font-size: 13px;
                font-family: inherit;
                color: #1e293b;
                background: #fff;
            }

            .search-box input:focus {
                outline: none;
                border-color: #0f1b3d;
                box-shadow: 0 0 0 3px rgba(15, 27, 61, 0.06);
            }

            .search-box svg {
                position: absolute;
                left: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: #94a3b8;
            }

            .toolbar select {
                padding: 9px 14px;
                border: 1.5px solid #e2e8f0;
                border-radius: 10px;
                font-size: 13px;
                font-family: inherit;
                color: #1e293b;
                background: #fff;
                cursor: pointer;
            }

            .toolbar select:focus {
                outline: none;
                border-color: #0f1b3d;
            }

            .students-table {
                background: #fff;
                border: 1px solid #e4e8f1;
                border-radius: 14px;
                overflow-x: auto;
            }

            .students-table table {
                width: 100%;
                border-collapse: collapse;
                font-size: 13px;
                min-width: 900px;
            }

            .students-table thead tr {
                border-bottom: 1px solid #e4e8f1;
                background: #f8f9fc;
            }

            .students-table th {
                padding: 12px 16px;
                text-align: left;
                font-size: 11px;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                white-space: nowrap;
            }

            .students-table td {
                padding: 13px 16px;
                color: #1e293b;
            }

            .students-table tbody tr {
                border-bottom: 1px solid #f0f2f7;
                transition: background 0.1s;
            }

            .students-table tbody tr:last-child {
                border-bottom: none;
            }

            .students-table tbody tr:hover {
                background: #fafbfd;
            }

            .student-info {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .student-avatar {
                width: 34px;
                height: 34px;
                border-radius: 50%;
                background: #0f1b3d;
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 700;
                flex-shrink: 0;
            }

            .student-avatar img {
                width: 34px;
                height: 34px;
                border-radius: 50%;
                object-fit: cover;
            }

            .student-name {
                font-weight: 600;
                color: #0f1b3d;
                font-size: 13px;
            }

            .student-email {
                font-size: 11.5px;
                color: #94a3b8;
            }

            .badge-sm {
                font-size: 11px;
                font-weight: 700;
                padding: 3px 9px;
                border-radius: 6px;
                white-space: nowrap;
            }

            .badge-kelas {
                background: #eef2ff;
                color: #3b5bdb;
            }

            .badge-active {
                background: #d1fae5;
                color: #065f46;
            }

            .badge-inactive {
                background: #fee2e2;
                color: #991b1b;
            }

            .progress-bar-wrap {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .progress-track {
                width: 80px;
                height: 5px;
                background: #e4e8f1;
                border-radius: 3px;
                overflow: hidden;
            }

            .progress-fill {
                height: 100%;
                border-radius: 3px;
                background: linear-gradient(90deg, #3b5bdb, #748ffc);
            }

            .progress-text {
                font-size: 12px;
                font-weight: 700;
                color: #0f1b3d;
            }

            .actions-cell {
                display: flex;
                gap: 4px;
                align-items: center;
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
                white-space: nowrap;
            }

            .btn-detail {
                color: #3b5bdb;
                background: #eef2ff;
            }

            .btn-detail:hover {
                background: #dbeafe;
            }

            .btn-edit {
                color: #475569;
                background: #f1f5f9;
            }

            .btn-edit:hover {
                background: #e2e8f0;
            }

            .btn-toggle-off {
                color: #ef4444;
                background: #fff;
                border: 1px solid #fecaca;
            }

            .btn-toggle-off:hover {
                background: #fef2f2;
            }

            .btn-toggle-on {
                color: #16a34a;
                background: #fff;
                border: 1px solid #bbf7d0;
            }

            .btn-toggle-on:hover {
                background: #f0fdf4;
            }

            .date-cell {
                font-size: 12px;
                color: #64748b;
                white-space: nowrap;
            }

            .empty-row td {
                padding: 48px 16px;
                text-align: center;
                color: #94a3b8;
                font-size: 13px;
            }
        </style>
    </x-slot:styles>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Total Siswa</div>
            <div class="stat-value">{{ $students->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">XI PPLG 1</div>
            <div class="stat-value">{{ $students->filter(fn($s) => $s->profile?->kelas == 'XI PPLG 1')->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">XI PPLG 2</div>
            <div class="stat-value">{{ $students->filter(fn($s) => $s->profile?->kelas == 'XI PPLG 2')->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">XI PPLG 3</div>
            <div class="stat-value">{{ $students->filter(fn($s) => $s->profile?->kelas == 'XI PPLG 3')->count() }}</div>
        </div>
    </div>

    <!-- Toolbar -->
    <form method="GET" action="{{ route('admin.students.index') }}" class="toolbar" id="filterForm">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NIS, email...">
        </div>

        <select name="kelas" onchange="document.getElementById('filterForm').submit()">
            <option value="">Semua Kelas</option>
            @foreach ($kelasList as $k)
                <option value="{{ $k }}" {{ $kelas == $k ? 'selected' : '' }}>{{ $k }}</option>
            @endforeach
        </select>

        <select name="sort" onchange="document.getElementById('filterForm').submit()">
            <option value="terbaru" {{ $sort == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
            <option value="terlama" {{ $sort == 'terlama' ? 'selected' : '' }}>Terlama</option>
            <option value="abjad" {{ $sort == 'abjad' ? 'selected' : '' }}>A — Z</option>
        </select>
    </form>

    <!-- Table -->
    <div class="students-table">
        <table>
            <thead>
                <tr>
                    <th>Siswa</th>
                    <th>NIS</th>
                    <th>L/P</th>
                    <th>Kelas</th>
                    <th>Tahun Ajaran</th>
                    <th>Terdaftar</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>
                            <div class="student-info">
                                <div class="student-avatar">
                                    @if ($student->profile?->avatar)
                                        <img src="{{ asset('storage/' . $student->profile->avatar) }}" alt="">
                                    @else
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="student-name">{{ $student->name }}</div>
                                    <div class="student-email">{{ $student->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="color: #64748b;">{{ $student->profile?->nim ?? '-' }}</td>
                        <td style="color: #64748b;">
                            {{ $student->profile?->gender == 'Laki-laki' ? 'L' : ($student->profile?->gender == 'Perempuan' ? 'P' : '-') }}
                        </td>
                        <td>
                            @if ($student->profile?->kelas)
                                <span class="badge-sm badge-kelas">{{ $student->profile->kelas }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td style="color: #64748b; font-size: 12px;">{{ $student->profile?->tahun_ajaran ?? '-' }}</td>
                        <td class="date-cell">{{ $student->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="progress-bar-wrap">
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: {{ $student->progress_percent }}%"></div>
                                </div>
                                <span class="progress-text">{{ $student->progress_percent }}%</span>
                            </div>
                        </td>
                        <td>
                            @if ($student->is_active)
                                <span class="badge-sm badge-active">Aktif</span>
                            @else
                                <span class="badge-sm badge-inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions-cell">
                                <a href="{{ route('admin.students.show', $student) }}"
                                    class="btn-xs btn-detail">Detail</a>
                                <button class="btn-xs btn-edit"
                                    onclick="openEditModal({{ json_encode([
                                        'id' => $student->id,
                                        'full_name' => $student->profile?->full_name ?? '',
                                        'nim' => $student->profile?->nim ?? '',
                                        'kelas' => $student->profile?->kelas ?? '',
                                        'gender' => $student->profile?->gender ?? '',
                                        'tahun_ajaran' => $student->profile?->tahun_ajaran ?? '',
                                    ]) }})">Edit</button>
                                <form method="POST" action="{{ route('admin.students.toggleActive', $student) }}">
                                    @csrf @method('PATCH')
                                    @if ($student->is_active)
                                        <button type="submit" class="btn-xs btn-toggle-off"
                                            onclick="return confirm('Nonaktifkan siswa ini?')">Nonaktifkan</button>
                                    @else
                                        <button type="submit" class="btn-xs btn-toggle-on">Aktifkan</button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="9">Tidak ada siswa ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Edit -->
    <div class="modal-backdrop" id="editModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeEditModal()">&times;</button>
            <h2>Edit Data Siswa</h2>

            <form method="POST" id="editForm">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Nama Lengkap *</label>
                    <input type="text" name="full_name" id="edFullName" required>
                </div>

                <div style="display: flex; gap: 14px;">
                    <div class="form-group" style="flex:1;">
                        <label>NIS</label>
                        <input type="text" name="nim" id="edNim">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Jenis Kelamin</label>
                        <select name="gender" id="edGender">
                            <option value="">— Pilih —</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 14px;">
                    <div class="form-group" style="flex:1;">
                        <label>Kelas</label>
                        <select name="kelas" id="edKelas">
                            <option value="">— Pilih —</option>
                            <option value="XI PPLG 1">XI PPLG 1</option>
                            <option value="XI PPLG 2">XI PPLG 2</option>
                            <option value="XI PPLG 3">XI PPLG 3</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Tahun Ajaran</label>
                        <input type="text" name="tahun_ajaran" id="edTahunAjaran" placeholder="2025/2026">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <x-slot:scripts>
        <script>
            function openEditModal(data) {
                document.getElementById('editForm').action = '/admin/students/' + data.id;
                document.getElementById('edFullName').value = data.full_name;
                document.getElementById('edNim').value = data.nim;
                document.getElementById('edGender').value = data.gender;
                document.getElementById('edKelas').value = data.kelas;
                document.getElementById('edTahunAjaran').value = data.tahun_ajaran;
                document.getElementById('editModal').classList.add('active');
            }

            function closeEditModal() {
                document.getElementById('editModal').classList.remove('active');
            }

            document.getElementById('editModal').addEventListener('click', function(e) {
                if (e.target === this) closeEditModal();
            });

            // Enter key on search
            document.querySelector('.search-box input').addEventListener('keydown', function(e) {
                if (e.key === 'Enter') document.getElementById('filterForm').submit();
            });
        </script>
    </x-slot:scripts>

</x-layouts.admin>
