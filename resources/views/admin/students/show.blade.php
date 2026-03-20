<x-layouts.admin title="Detail Siswa">

    <x-slot:styles>
    <style>
        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 24px; font-size: 13px; flex-wrap: wrap;
        }
        .breadcrumb a { color: #3b5bdb; text-decoration: none; font-weight: 600; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb span { color: #94a3b8; }

        .profile-card {
            background: #fff;
            border: 1px solid #e4e8f1;
            border-radius: 16px;
            padding: 28px 32px;
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 28px;
        }

        .profile-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #0f1b3d;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .profile-avatar img {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-details h2 {
            font-size: 18px;
            font-weight: 700;
            color: #0f1b3d;
            margin-bottom: 4px;
        }

        .profile-meta {
            display: flex;
            gap: 20px;
            font-size: 13px;
            color: #64748b;
            flex-wrap: wrap;
        }

        .profile-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid #e4e8f1;
            border-radius: 12px;
            padding: 20px;
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

        .section-label {
            font-size: 15px;
            font-weight: 700;
            color: #0f1b3d;
            margin-bottom: 16px;
        }

        .course-progress-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .course-progress-item {
            background: #fff;
            border: 1px solid #e4e8f1;
            border-radius: 14px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .cpi-info { flex: 1; }

        .cpi-info h4 {
            font-size: 14px;
            font-weight: 700;
            color: #0f1b3d;
            margin-bottom: 4px;
        }

        .cpi-info .cpi-sub {
            font-size: 12px;
            color: #94a3b8;
        }

        .cpi-bar {
            width: 200px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cpi-track {
            flex: 1;
            height: 8px;
            background: #e4e8f1;
            border-radius: 4px;
            overflow: hidden;
        }

        .cpi-fill {
            height: 100%;
            border-radius: 4px;
            background: linear-gradient(90deg, #3b5bdb, #748ffc);
        }

        .cpi-percent {
            font-size: 14px;
            font-weight: 800;
            color: #0f1b3d;
            min-width: 42px;
            text-align: right;
        }
    </style>
    </x-slot:styles>

    <div class="breadcrumb">
        <a href="{{ route('admin.students.index') }}">Kelola Siswa</a>
        <span>›</span>
        <span>{{ $student->name }}</span>
    </div>

    <!-- Profile -->
    <div class="profile-card">
        <div class="profile-avatar">
            @if($student->profile?->avatar)
                <img src="{{ asset('storage/' . $student->profile->avatar) }}" alt="">
            @else
                {{ strtoupper(substr($student->name, 0, 1)) }}
            @endif
        </div>
        <div class="profile-details">
            <h2>{{ $student->name }}</h2>
            <div class="profile-meta">
                <span>{{ $student->email }}</span>
                <span>NIS: {{ $student->profile?->nim ?? '-' }}</span>
                <span>Kelas: {{ $student->profile?->kelas ?? '-' }}</span>
                <span>{{ $student->profile?->gender ?? '-' }}</span>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Total Submissions</div>
            <div class="stat-value">{{ $totalSubmissions }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Jawaban Benar</div>
            <div class="stat-value" style="color: #16a34a;">{{ $correctSubmissions }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Akurasi</div>
            <div class="stat-value">{{ $totalSubmissions > 0 ? round(($correctSubmissions / $totalSubmissions) * 100) : 0 }}%</div>
        </div>
    </div>

    <!-- Progress per Course -->
    <div class="section-label">Progress per Kelas</div>
    <div class="course-progress-list">
        @foreach($courseProgress as $cp)
            <div class="course-progress-item">
                <div class="cpi-info">
                    <h4>{{ $cp->title }}</h4>
                    <div class="cpi-sub">{{ $cp->completed }} / {{ $cp->total }} aktivitas selesai</div>
                </div>
                <div class="cpi-bar">
                    <div class="cpi-track">
                        <div class="cpi-fill" style="width: {{ $cp->percent }}%"></div>
                    </div>
                    <span class="cpi-percent">{{ $cp->percent }}%</span>
                </div>
            </div>
        @endforeach
    </div>

</x-layouts.admin>