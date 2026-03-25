<x-layouts.admin title="Hasil — {{ $course->title }}">
    <x-slot:styles>
        <style>
            .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 24px; font-size: 13px; flex-wrap: wrap; }
            .breadcrumb a { color: #3b5bdb; text-decoration: none; font-weight: 600; }
            .breadcrumb a:hover { text-decoration: underline; }
            .breadcrumb span { color: #94a3b8; }

            .page-head { margin-bottom: 22px; }
            .page-head h1 { font-size: 20px; font-weight: 800; color: #0f1b3d; margin-bottom: 4px; }
            .page-head p  { font-size: 13px; color: #64748b; }

            /* Stat Cards */
            .stat-row { display: flex; gap: 14px; margin-bottom: 24px; flex-wrap: wrap; }
            .stat-card {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; padding: 14px 20px; min-width: 130px;
                display: flex; flex-direction: column; gap: 4px;
            }
            .stat-card .s-label { font-size: 10.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
            .stat-card .s-value { font-size: 26px; font-weight: 800; color: #0f1b3d; line-height: 1.1; }
            .stat-card .s-sub   { font-size: 11px; color: #94a3b8; }

            /* Toolbar */
            .toolbar { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; }
            .search-input {
                flex: 1; min-width: 180px; max-width: 260px;
                padding: 8px 12px; border: 1.5px solid #dde1ea; border-radius: 5px;
                font-size: 13px; color: #1e293b; font-family: inherit; outline: none;
            }
            .search-input:focus { border-color: #3b5bdb; }
            .filter-select {
                padding: 8px 10px; border: 1.5px solid #dde1ea; border-radius: 5px;
                font-size: 13px; color: #1e293b; background: #fff; font-family: inherit;
                outline: none; cursor: pointer;
            }
            .filter-select:focus { border-color: #3b5bdb; }
            .btn-filter {
                padding: 8px 16px; border-radius: 5px; font-size: 12.5px; font-weight: 700;
                background: #0f1b3d; color: #fff; border: none; cursor: pointer; font-family: inherit;
            }
            .btn-filter:hover { background: #1a2d5a; }
            .btn-reset {
                padding: 8px 12px; border-radius: 5px; font-size: 12.5px; font-weight: 600;
                color: #64748b; background: #f1f5f9; border: 1px solid #e4e8f1;
                cursor: pointer; text-decoration: none; display: inline-flex; align-items: center;
                font-family: inherit;
            }
            .btn-reset:hover { background: #e2e8f0; }

            /* Table */
            .table-wrap {
                background: #fff; border: 1px solid #e4e8f1; border-radius: 6px;
                box-shadow: 3px 3px 0 #c8cfdc; overflow-x: auto;
            }
            .result-table { width: 100%; border-collapse: collapse; font-size: 12.5px; min-width: 700px; }
            .result-table thead tr { background: #f8f9fc; border-bottom: 2px solid #e4e8f1; }
            .result-table th {
                padding: 11px 14px; text-align: left;
                font-size: 10.5px; font-weight: 700; color: #6b7a99;
                text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;
            }
            .result-table th.center { text-align: center; }
            .result-table td { padding: 12px 14px; color: #1e293b; border-bottom: 1px solid #f0f2f7; vertical-align: middle; }
            .result-table tbody tr:last-child td { border-bottom: none; }
            .result-table tbody tr:hover td { background: #fafbfd; }

            .student-info { display: flex; align-items: center; gap: 10px; }
            .student-avatar {
                width: 32px; height: 32px; border-radius: 50%;
                background: #0f1b3d; color: #fff;
                display: flex; align-items: center; justify-content: center;
                font-size: 11px; font-weight: 800; flex-shrink: 0;
            }
            .student-avatar img { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }
            .student-name { font-size: 13px; font-weight: 700; color: #0f1b3d; }
            .student-sub  { font-size: 11px; color: #94a3b8; margin-top: 1px; }

            .ch-badge {
                display: inline-flex; align-items: center; justify-content: center;
                padding: 2px 7px; border-radius: 4px; font-size: 10.5px; font-weight: 700; white-space: nowrap;
            }
            .ch-done    { background: #d1fae5; color: #065f46; }
            .ch-partial { background: #fef3c7; color: #92400e; }
            .ch-none    { background: #f1f5f9; color: #94a3b8; }

            .prog-wrap { display: flex; align-items: center; gap: 8px; min-width: 130px; }
            .prog-track { flex: 1; height: 5px; background: #e4e8f1; border-radius: 3px; overflow: hidden; }
            .prog-fill  { height: 100%; border-radius: 3px; background: linear-gradient(90deg, #3b5bdb, #748ffc); }
            .prog-text  { font-size: 11.5px; font-weight: 700; color: #0f1b3d; white-space: nowrap; }

            .score-cell { text-align: center; font-size: 13px; font-weight: 700; color: #3b5bdb; }
            .score-cell.no-score { color: #cbd5e1; font-weight: 400; font-size: 12px; }

            .btn-detail { padding: 5px 11px; border-radius: 5px; font-size: 11.5px; font-weight: 700; color: #3b5bdb; background: #eef2ff; text-decoration: none; }
            .btn-detail:hover { background: #dbeafe; }

            .empty-state { padding: 56px 20px; text-align: center; color: #94a3b8; font-size: 13px; }
        </style>
    </x-slot:styles>

    <div class="breadcrumb">
        <a href="{{ route('admin.hasil-kelas.index') }}">Hasil LKPD</a>
        <span>›</span>
        <span>{{ $course->title }}</span>
    </div>

    <div class="page-head">
        <h1>{{ $course->title }}</h1>
        <p>
            @if ($course->kelas)
                {{ $course->kelas->school->name }} · {{ $course->kelas->name }} · {{ $course->kelas->tahunAjaran->name }}
            @else
                LKPD Umum
            @endif
        </p>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-row">
        <div class="stat-card">
            <div class="s-label">Terdaftar</div>
            <div class="s-value">{{ $students->count() }}</div>
            <div class="s-sub">Siswa</div>
        </div>
        <div class="stat-card">
            <div class="s-label">Rata-rata Progress</div>
            <div class="s-value">{{ $students->isEmpty() ? 0 : round($students->avg('progress_percent')) }}%</div>
        </div>
        <div class="stat-card">
            <div class="s-label">Selesai</div>
            <div class="s-value">{{ $students->where('progress_percent', 100)->count() }}</div>
            <div class="s-sub">Siswa</div>
        </div>
        <div class="stat-card">
            <div class="s-label">Belum Mulai</div>
            <div class="s-value">{{ $students->where('progress_percent', 0)->count() }}</div>
            <div class="s-sub">Siswa</div>
        </div>
        @php $avgScore = $students->whereNotNull('avg_score')->avg('avg_score'); @endphp
        @if ($avgScore !== null)
            <div class="stat-card">
                <div class="s-label">Rata-rata Skor</div>
                <div class="s-value">{{ round($avgScore) }}</div>
            </div>
        @endif
        <div class="stat-card">
            <div class="s-label">Total Aktivitas</div>
            <div class="s-value">{{ $totalActivities }}</div>
        </div>
    </div>

    {{-- Toolbar --}}
    <form method="GET" action="{{ route('admin.hasil-kelas.show', $course) }}" class="toolbar">
        <input type="text" name="search" class="search-input" placeholder="Cari nama siswa..." value="{{ $search }}">
        @if ($kelasList->isNotEmpty())
            <select name="kelas_id" class="filter-select">
                <option value="">Semua Kelas</option>
                @foreach ($kelasList as $kelas)
                    <option value="{{ $kelas->id }}" {{ $filterKelasId == $kelas->id ? 'selected' : '' }}>
                        {{ $kelas->name }} — {{ $kelas->school->name }}
                    </option>
                @endforeach
            </select>
        @endif
        <button type="submit" class="btn-filter">Cari</button>
        @if ($search || $filterKelasId)
            <a href="{{ route('admin.hasil-kelas.show', $course) }}" class="btn-reset">Reset</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="table-wrap">
        <table class="result-table">
            <thead>
                <tr>
                    <th style="min-width:200px;">Siswa</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                    @foreach ($course->chapters as $chapter)
                        <th class="center" title="{{ $chapter->title }}">Ch.{{ $loop->iteration }}</th>
                    @endforeach
                    <th class="center">Skor</th>
                    <th style="min-width:150px;">Progress</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $student)
                    <tr>
                        <td>
                            <div class="student-info">
                                <div class="student-avatar">
                                    @if ($student->profile?->avatar)
                                        <img src="{{ $student->profile->avatarUrl() }}" alt="">
                                    @else
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="student-name">{{ $student->profile?->full_name ?? $student->username }}</div>
                                    <div class="student-sub">{{ $student->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="color:#64748b; font-size:11.5px;">{{ $student->profile?->nim ?? '—' }}</td>
                        <td style="font-size:11.5px; color:#64748b;">{{ $student->profile?->kelas?->name ?? '—' }}</td>

                        @foreach ($student->chapter_progress as $cp)
                            <td style="text-align:center;">
                                @if ($cp['total'] === 0)
                                    <span class="ch-badge ch-none">—</span>
                                @elseif ($cp['completed'] === $cp['total'])
                                    <span class="ch-badge ch-done" title="{{ $cp['completed'] }}/{{ $cp['total'] }}">✓</span>
                                @elseif ($cp['completed'] > 0)
                                    <span class="ch-badge ch-partial" title="{{ $cp['completed'] }}/{{ $cp['total'] }}">{{ $cp['completed'] }}/{{ $cp['total'] }}</span>
                                @else
                                    <span class="ch-badge ch-none">—</span>
                                @endif
                            </td>
                        @endforeach

                        <td class="score-cell {{ $student->avg_score === null ? 'no-score' : '' }}">
                            {{ $student->avg_score !== null ? $student->avg_score : '—' }}
                        </td>

                        <td>
                            <div class="prog-wrap">
                                <div class="prog-track">
                                    <div class="prog-fill" style="width:{{ $student->progress_percent }}%"></div>
                                </div>
                                <span class="prog-text">{{ $student->completed_count }}/{{ $student->total_count }}</span>
                            </div>
                        </td>

                        <td>
                            <a href="{{ route('admin.hasil-kelas.student', [$course, $student]) }}" class="btn-detail">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 5 + $course->chapters->count() }}">
                            <div class="empty-state">
                                {{ ($search || $filterKelasId) ? 'Tidak ada siswa yang sesuai filter.' : 'Belum ada siswa yang terdaftar di LKPD ini.' }}
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-layouts.admin>
