<x-layouts.admin title="Dashboard">

    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            <p>Selamat datang, {{ Auth::user()->name }}</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin-bottom: 28px;">

        <a href="{{ route('admin.students.index') }}" class="card" style="padding: 20px 24px; display: flex; align-items: center; gap: 16px; text-decoration: none; transition: box-shadow 0.15s, border-color 0.15s;" onmouseover="this.style.borderColor='#c5cde0';this.style.boxShadow='0 2px 8px rgba(15,27,61,0.08)'" onmouseout="this.style.borderColor='';this.style.boxShadow=''">
            <div style="width: 42px; height: 42px; background: #f0f2f7; border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4a5568" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: #6b7a99; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px;">Total Siswa</div>
                <div style="font-size: 30px; font-weight: 800; color: #0f1b3d; line-height: 1;">{{ $stats['total_students'] }}</div>
            </div>
        </a>

        <a href="{{ route('admin.courses.index') }}" class="card" style="padding: 20px 24px; display: flex; align-items: center; gap: 16px; text-decoration: none; transition: box-shadow 0.15s, border-color 0.15s;" onmouseover="this.style.borderColor='#c5cde0';this.style.boxShadow='0 2px 8px rgba(15,27,61,0.08)'" onmouseout="this.style.borderColor='';this.style.boxShadow=''">
            <div style="width: 42px; height: 42px; background: #f0f2f7; border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4a5568" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                </svg>
            </div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: #6b7a99; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px;">Total LKPD</div>
                <div style="font-size: 30px; font-weight: 800; color: #0f1b3d; line-height: 1;">{{ $stats['total_courses'] }}</div>
            </div>
        </a>

        <a href="{{ route('admin.courses.index') }}" class="card" style="padding: 20px 24px; display: flex; align-items: center; gap: 16px; text-decoration: none; transition: box-shadow 0.15s, border-color 0.15s;" onmouseover="this.style.borderColor='#c5cde0';this.style.boxShadow='0 2px 8px rgba(15,27,61,0.08)'" onmouseout="this.style.borderColor='';this.style.boxShadow=''">
            <div style="width: 42px; height: 42px; background: #f0f2f7; border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4a5568" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                </svg>
            </div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: #6b7a99; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px;">Total Aktivitas</div>
                <div style="font-size: 30px; font-weight: 800; color: #0f1b3d; line-height: 1;">{{ $stats['total_activities'] }}</div>
            </div>
        </a>

        <a href="{{ route('admin.students.index') }}" class="card" style="padding: 20px 24px; display: flex; align-items: center; gap: 16px; text-decoration: none; transition: box-shadow 0.15s, border-color 0.15s;" onmouseover="this.style.borderColor='#c5cde0';this.style.boxShadow='0 2px 8px rgba(15,27,61,0.08)'" onmouseout="this.style.borderColor='';this.style.boxShadow=''">
            <div style="width: 42px; height: 42px; background: #f0f2f7; border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4a5568" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: #6b7a99; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px;">Submissions</div>
                <div style="font-size: 30px; font-weight: 800; color: #0f1b3d; line-height: 1;">{{ $stats['total_submissions'] }}</div>
            </div>
        </a>

    </div>

    <div class="card" style="padding: 24px;">
        <div style="font-size: 13px; font-weight: 700; color: #1a2332; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #e8ecf3;">Aktivitas Terbaru</div>
        <p style="font-size: 13px; color: #64748b; margin: 0;">Grafik dan monitoring detail akan ditambahkan di tahap selanjutnya.</p>
    </div>

</x-layouts.admin>
