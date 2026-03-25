<x-layouts.admin title="Kelola Siswa">

    <x-slot:styles>
        <style>
            .toolbar {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 20px;
                flex-wrap: wrap;
            }

            .search-wrap {
                flex: 1;
                min-width: 220px;
                position: relative;
            }

            .search-wrap svg {
                position: absolute;
                left: 11px;
                top: 50%;
                transform: translateY(-50%);
                color: #9aa5b8;
                pointer-events: none;
            }

            .search-wrap input {
                width: 100%;
                padding: 8px 12px 8px 34px;
                border: 1.5px solid #dde1ea;
                border-radius: 5px;
                font-size: 13px;
                font-family: inherit;
                color: #1a2332;
                background: #fff;
                transition: border-color 0.15s;
            }

            .search-wrap input:focus {
                outline: none;
                border-color: #0f1b3d;
                box-shadow: 0 0 0 3px rgba(15,27,61,0.07);
            }

            .toolbar select {
                padding: 8px 12px;
                border: 1.5px solid #dde1ea;
                border-radius: 5px;
                font-size: 13px;
                font-family: inherit;
                color: #1a2332;
                background: #fff;
                cursor: pointer;
                transition: border-color 0.15s;
            }

            .toolbar select:focus {
                outline: none;
                border-color: #0f1b3d;
            }

            /* Table */
            .table-wrap {
                background: #fff;
                border: 1px solid #e8eaf0;
                border-radius: 6px;
                overflow-x: auto;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 13px;
                min-width: 900px;
            }

            thead tr {
                border-bottom: 1px solid #e8eaf0;
                background: #f7f8fa;
            }

            th {
                padding: 11px 16px;
                text-align: left;
                font-size: 11px;
                font-weight: 700;
                color: #4a5568;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                white-space: nowrap;
            }

            td {
                padding: 12px 16px;
                color: #1a2332;
                border-bottom: 1px solid #f0f2f7;
            }

            tbody tr:last-child td {
                border-bottom: none;
            }

            tbody tr:hover {
                background: #fafbfd;
            }

            /* Student info cell */
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
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                overflow: hidden;
            }

            .student-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .student-name {
                font-weight: 600;
                color: #0f1b3d;
                font-size: 13px;
                line-height: 1.3;
            }

            .student-email {
                font-size: 11.5px;
                color: #6b7a99;
                margin-top: 1px;
            }

            /* Badges */
            .badge {
                font-size: 11px;
                font-weight: 700;
                padding: 3px 8px;
                border-radius: 4px;
                white-space: nowrap;
                display: inline-block;
            }

            .badge-kelas   { background: #f0f2f7; color: #4a5568; }
            .badge-active  { background: #f0fdf4; color: #166534; }
            .badge-inactive{ background: #fef2f2; color: #991b1b; }

            /* Progress */
            .progress-wrap {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .progress-track {
                width: 72px;
                height: 4px;
                background: #e8eaf0;
                border-radius: 2px;
                overflow: hidden;
            }

            .progress-fill {
                height: 100%;
                background: #0f1b3d;
                border-radius: 2px;
            }

            .progress-pct {
                font-size: 12px;
                font-weight: 700;
                color: #4a5568;
                min-width: 30px;
            }

            /* Action buttons */
            .actions {
                display: flex;
                gap: 4px;
                align-items: center;
            }

            .btn-xs {
                padding: 5px 11px;
                border-radius: 4px;
                font-size: 11.5px;
                font-weight: 600;
                font-family: inherit;
                cursor: pointer;
                border: none;
                text-decoration: none;
                display: inline-block;
                transition: all 0.12s;
                white-space: nowrap;
            }

            .btn-detail  { background: #f0f2f7; color: #0f1b3d; }
            .btn-detail:hover { background: #e4e8f1; }

            .btn-edit    { background: #f0f2f7; color: #4a5568; }
            .btn-edit:hover { background: #e4e8f1; }

            .btn-off     { background: #fff; color: #dc2626; border: 1px solid #fecaca; }
            .btn-off:hover { background: #fef2f2; }

            .btn-on      { background: #fff; color: #16a34a; border: 1px solid #bbf7d0; }
            .btn-on:hover  { background: #f0fdf4; }

            .muted { color: #6b7a99; font-size: 12.5px; }
            .empty-cell { padding: 48px 16px; text-align: center; color: #9aa5b8; font-size: 13px; }
        </style>
    </x-slot:styles>

    <div class="page-header">
        <div>
            <h1>Kelola Siswa</h1>
            <p>Daftar seluruh siswa yang terdaftar di platform</p>
        </div>
    </div>

    <!-- Stats -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:22px;">
        <div class="card" style="padding:16px 20px;">
            <div style="font-size:11px;font-weight:700;color:#6b7a99;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">Total Siswa</div>
            <div style="font-size:28px;font-weight:800;color:#0f1b3d;line-height:1;">{{ $students->count() }}</div>
        </div>
        @foreach ($kelasList as $k)
        <div class="card" style="padding:16px 20px;">
            <div style="font-size:11px;font-weight:700;color:#6b7a99;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">{{ $k->name }}</div>
            <div style="font-size:28px;font-weight:800;color:#0f1b3d;line-height:1;">{{ $students->filter(fn($s) => $s->profile?->kelas_id == $k->id)->count() }}</div>
        </div>
        @endforeach
    </div>

    <!-- Toolbar -->
    <form method="GET" action="{{ route('admin.students.index') }}" class="toolbar" id="filterForm">
        <div class="search-wrap">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NIS, email...">
        </div>

        <select name="kelas" onchange="document.getElementById('filterForm').submit()">
            <option value="">Semua Kelas</option>
            @foreach ($kelasList as $k)
                <option value="{{ $k->id }}" {{ $kelas == $k->id ? 'selected' : '' }}>{{ $k->name }} — {{ $k->school->name }}</option>
            @endforeach
        </select>

        <select name="sort" onchange="document.getElementById('filterForm').submit()">
            <option value="terbaru"  {{ $sort == 'terbaru'  ? 'selected' : '' }}>Terbaru</option>
            <option value="terlama"  {{ $sort == 'terlama'  ? 'selected' : '' }}>Terlama</option>
            <option value="abjad"    {{ $sort == 'abjad'    ? 'selected' : '' }}>A — Z</option>
        </select>
    </form>

    <!-- Table -->
    <div class="table-wrap">
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
                                        <img src="{{ $student->profile->avatarUrl() }}" alt="">
                                    @else
                                        <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <div class="student-name">{{ $student->name }}</div>
                                    <div class="student-email">{{ $student->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="muted">{{ $student->profile?->nim ?? '-' }}</td>
                        <td class="muted">
                            {{ $student->profile?->gender == 'Laki-laki' ? 'L' : ($student->profile?->gender == 'Perempuan' ? 'P' : '-') }}
                        </td>
                        <td>
                            @if ($student->profile?->kelas)
                                <span class="badge badge-kelas">{{ $student->profile->kelas->name }}</span>
                            @else
                                <span class="muted">-</span>
                            @endif
                        </td>
                        <td class="muted">{{ $student->profile?->kelas?->tahunAjaran?->name ?? '-' }}</td>
                        <td class="muted">{{ $student->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="progress-wrap">
                                <div class="progress-track">
                                    <div class="progress-fill" style="width:{{ $student->progress_percent }}%"></div>
                                </div>
                                <span class="progress-pct">{{ $student->progress_percent }}%</span>
                            </div>
                        </td>
                        <td>
                            @if ($student->is_active)
                                <span class="badge badge-active">Aktif</span>
                            @else
                                <span class="badge badge-inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.students.show', $student) }}" class="btn-xs btn-detail">Detail</a>
                                <button class="btn-xs btn-edit"
                                    onclick="openEditModal({{ json_encode([
                                        'id'        => $student->id,
                                        'full_name' => $student->profile?->full_name ?? '',
                                        'nim'       => $student->profile?->nim ?? '',
                                        'kelas_id'  => $student->profile?->kelas_id ?? '',
                                        'gender'    => $student->profile?->gender ?? '',
                                    ]) }})">Edit</button>
                                <form method="POST" action="{{ route('admin.students.toggleActive', $student) }}">
                                    @csrf @method('PATCH')
                                    @if ($student->is_active)
                                        <button type="submit" class="btn-xs btn-off"
                                            onclick="return confirm('Nonaktifkan siswa ini?')">Nonaktifkan</button>
                                    @else
                                        <button type="submit" class="btn-xs btn-on">Aktifkan</button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-cell">Tidak ada siswa ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Edit -->
    <div class="modal-backdrop" id="editModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeEditModal()">&times;</button>
            <h2 id="editModalTitle">Edit Data Siswa</h2>

            <!-- Tab -->
            <div style="display:flex;gap:0;border-bottom:1.5px solid #e8eaf0;margin-bottom:20px;">
                <button type="button" id="tabData" onclick="switchTab('data')"
                    style="padding:8px 18px;font-size:12.5px;font-weight:700;font-family:inherit;border:none;background:none;cursor:pointer;border-bottom:2px solid #0f1b3d;color:#0f1b3d;margin-bottom:-1.5px;">
                    Data Diri
                </button>
                <button type="button" id="tabPass" onclick="switchTab('pass')"
                    style="padding:8px 18px;font-size:12.5px;font-weight:700;font-family:inherit;border:none;background:none;cursor:pointer;border-bottom:2px solid transparent;color:#9aa5b8;margin-bottom:-1.5px;">
                    Password
                </button>
            </div>

            <!-- Panel Data Diri -->
            <div id="panelData">
                <form method="POST" id="editForm">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Nama Lengkap *</label>
                        <input type="text" name="full_name" id="edFullName" required>
                    </div>

                    <div style="display:flex;gap:14px;">
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

                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="kelas_id" id="edKelas">
                            <option value="">— Pilih —</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k->id }}">{{ $k->name }} — {{ $k->school->name }} ({{ $k->tahunAjaran->name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="closeEditModal()">Batal</button>
                        <button type="submit" class="btn-primary">Simpan</button>
                    </div>
                </form>
            </div>

            <!-- Panel Password -->
            <div id="panelPass" style="display:none;">
                <form method="POST" id="passwordForm">
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label>Password Baru *</label>
                        <input type="password" name="new_password" id="edNewPass" required minlength="8" placeholder="Minimal 8 karakter">
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password *</label>
                        <input type="password" name="new_password_confirmation" id="edConfPass" required minlength="8" placeholder="Ulangi password baru">
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="closeEditModal()">Batal</button>
                        <button type="submit" class="btn-primary">Simpan Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-slot:scripts>
        <script>
            function openEditModal(data) {
                document.getElementById('editForm').action = '/admin/students/' + data.id;
                document.getElementById('passwordForm').action = '/admin/students/' + data.id + '/password';
                document.getElementById('edFullName').value = data.full_name;
                document.getElementById('edNim').value = data.nim;
                document.getElementById('edGender').value = data.gender;
                document.getElementById('edKelas').value = data.kelas_id;
                document.getElementById('edNewPass').value = '';
                document.getElementById('edConfPass').value = '';
                switchTab('data');
                document.getElementById('editModal').classList.add('active');
            }

            function closeEditModal() {
                document.getElementById('editModal').classList.remove('active');
            }

            function switchTab(tab) {
                const isData = tab === 'data';
                document.getElementById('panelData').style.display = isData ? 'block' : 'none';
                document.getElementById('panelPass').style.display = isData ? 'none' : 'block';
                document.getElementById('tabData').style.borderBottomColor = isData ? '#0f1b3d' : 'transparent';
                document.getElementById('tabData').style.color = isData ? '#0f1b3d' : '#9aa5b8';
                document.getElementById('tabPass').style.borderBottomColor = isData ? 'transparent' : '#0f1b3d';
                document.getElementById('tabPass').style.color = isData ? '#9aa5b8' : '#0f1b3d';
            }

            document.getElementById('editModal').addEventListener('click', function(e) {
                if (e.target === this) closeEditModal();
            });

            document.querySelector('.search-wrap input').addEventListener('keydown', function(e) {
                if (e.key === 'Enter') document.getElementById('filterForm').submit();
            });
        </script>
    </x-slot:scripts>

</x-layouts.admin>
