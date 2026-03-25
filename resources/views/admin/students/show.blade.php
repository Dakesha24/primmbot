<x-layouts.admin title="Detail Siswa">

    <x-slot:styles>
    <style>
        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 20px; font-size: 13px;
        }
        .breadcrumb a { color: #3b5bdb; text-decoration: none; font-weight: 600; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb span { color: #9aa5b8; }

        .top-row {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 20px;
            margin-bottom: 20px;
            align-items: start;
        }

        .avatar-card {
            background: #fff;
            border: 1px solid #e8eaf0;
            border-radius: 6px;
            padding: 28px 20px;
            text-align: center;
        }

        .avatar-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-name {
            font-size: 15px;
            font-weight: 700;
            color: #0f1b3d;
            margin-bottom: 4px;
        }

        .avatar-email {
            font-size: 12px;
            color: #6b7a99;
            word-break: break-all;
        }

        .badge-status {
            display: inline-block;
            margin-top: 12px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 4px;
        }

        .badge-active   { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }

        .info-card {
            background: #fff;
            border: 1px solid #e8eaf0;
            border-radius: 6px;
            padding: 24px;
        }

        .info-card-title {
            font-size: 12px;
            font-weight: 700;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f2f7;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px 28px;
        }

        .info-item {}
        .info-label {
            font-size: 11px;
            font-weight: 700;
            color: #6b7a99;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 13.5px;
            font-weight: 600;
            color: #0f1b3d;
        }
        .info-value.muted { color: #9aa5b8; font-weight: 400; }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #0f1b3d;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 14px;
        }

        .course-progress-item {
            background: #fff;
            border: 1px solid #e8eaf0;
            border-radius: 6px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 10px;
        }

        .cpi-info { flex: 1; }
        .cpi-info h4 { font-size: 13.5px; font-weight: 700; color: #0f1b3d; margin-bottom: 2px; }
        .cpi-info .cpi-sub { font-size: 12px; color: #6b7a99; }

        .cpi-bar { width: 200px; display: flex; align-items: center; gap: 10px; }
        .cpi-track { flex: 1; height: 5px; background: #e8eaf0; border-radius: 3px; overflow: hidden; }
        .cpi-fill  { height: 100%; background: #0f1b3d; border-radius: 3px; }
        .cpi-pct   { font-size: 13px; font-weight: 800; color: #0f1b3d; min-width: 38px; text-align: right; }
    </style>
    </x-slot:styles>

    <div class="breadcrumb">
        <a href="{{ route('admin.students.index') }}">Kelola Siswa</a>
        <span>›</span>
        <span>{{ $student->name }}</span>
    </div>

    <!-- Atas: Avatar + Info -->
    <div class="top-row">

        <!-- Avatar Card -->
        <div class="avatar-card">
            <div class="avatar-circle">
                @if($student->profile?->avatar)
                    <img src="{{ $student->profile->avatarUrl() }}" alt="">
                @else
                    <svg width="36" height="36" fill="none" stroke="#fff" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                @endif
            </div>
            <div class="avatar-name">{{ $student->profile?->full_name ?? $student->username }}</div>
            <div class="avatar-email">{{ $student->email }}</div>
            <span class="badge-status {{ $student->is_active ? 'badge-active' : 'badge-inactive' }}">
                {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>

        <!-- Info Lengkap -->
        <div class="info-card">
            <div class="info-card-title">Informasi Siswa</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nama Lengkap</div>
                    <div class="info-value {{ $student->profile?->full_name ? '' : 'muted' }}">
                        {{ $student->profile?->full_name ?? 'Belum diisi' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Username</div>
                    <div class="info-value">{{ $student->username }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $student->email }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">NIS</div>
                    <div class="info-value {{ $student->profile?->nim ? '' : 'muted' }}">
                        {{ $student->profile?->nim ?? 'Belum diisi' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jenis Kelamin</div>
                    <div class="info-value {{ $student->profile?->gender ? '' : 'muted' }}">
                        {{ $student->profile?->gender ?? 'Belum diisi' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kelas</div>
                    <div class="info-value {{ $student->profile?->kelas ? '' : 'muted' }}">
                        {{ $student->profile?->kelas?->name ?? 'Belum dipilih' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Sekolah</div>
                    <div class="info-value {{ $student->profile?->kelas?->school ? '' : 'muted' }}">
                        {{ $student->profile?->kelas?->school?->name ?? '-' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tahun Ajaran</div>
                    <div class="info-value {{ $student->profile?->kelas?->tahunAjaran ? '' : 'muted' }}">
                        {{ $student->profile?->kelas?->tahunAjaran?->name ?? '-' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Login Metode</div>
                    <div class="info-value">
                        {{ $student->google_id ? 'Google OAuth' : '' }}
                        {{ $student->google_id && $student->password ? ' + ' : '' }}
                        {{ $student->password ? 'Email & Password' : '' }}
                        {{ !$student->google_id && !$student->password ? '-' : '' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Terdaftar Sejak</div>
                    <div class="info-value">{{ $student->created_at->format('d M Y, H:i') }}</div>
                </div>
            </div>
        </div>

    </div>

    <!-- Progress per Kelas -->
    <div class="section-title">Progress per Kelas</div>
    @foreach($courseProgress as $cp)
        <div class="course-progress-item">
            <div class="cpi-info">
                <h4>{{ $cp->title }}</h4>
                <div class="cpi-sub">{{ $cp->completed }} / {{ $cp->total }} aktivitas selesai</div>
            </div>
            <div class="cpi-bar">
                <div class="cpi-track">
                    <div class="cpi-fill" style="width:{{ $cp->percent }}%"></div>
                </div>
                <span class="cpi-pct">{{ $cp->percent }}%</span>
            </div>
        </div>
    @endforeach

</x-layouts.admin>
