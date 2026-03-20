<x-layouts.admin title="Dashboard">

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 32px;">

        <div style="background: #fff; border: 1px solid #e8ecf3; border-radius: 14px; padding: 24px;">
            <div style="font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px;">Total Siswa</div>
            <div style="font-size: 36px; font-weight: 800; color: #0f1b3d;">{{ $stats['total_students'] }}</div>
        </div>

        <div style="background: #fff; border: 1px solid #e8ecf3; border-radius: 14px; padding: 24px;">
            <div style="font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px;">Total Kelas</div>
            <div style="font-size: 36px; font-weight: 800; color: #0f1b3d;">{{ $stats['total_courses'] }}</div>
        </div>

        <div style="background: #fff; border: 1px solid #e8ecf3; border-radius: 14px; padding: 24px;">
            <div style="font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px;">Total Aktivitas</div>
            <div style="font-size: 36px; font-weight: 800; color: #0f1b3d;">{{ $stats['total_activities'] }}</div>
        </div>

        <div style="background: #fff; border: 1px solid #e8ecf3; border-radius: 14px; padding: 24px;">
            <div style="font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px;">Submissions</div>
            <div style="font-size: 36px; font-weight: 800; color: #0f1b3d;">{{ $stats['total_submissions'] }}</div>
        </div>

    </div>

    <div style="background: #fff; border: 1px solid #e8ecf3; border-radius: 14px; padding: 40px; text-align: center; color: #94a3b8;">
        <p style="font-size: 14px;">Grafik dan monitoring detail akan ditambahkan di tahap selanjutnya.</p>
    </div>

</x-layouts.admin>