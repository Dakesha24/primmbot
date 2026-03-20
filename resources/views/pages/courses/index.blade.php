<x-layouts.app title="Kelas - PRIMMBOT">

    <div class="page-header fade-up">
        <h1 class="page-title">Daftar Kelas</h1>
        <p class="page-subtitle">Pilih kelas untuk mulai belajar</p>
    </div>

    <div class="grid-3">
        @foreach($courses as $course)
            <div class="card fade-up fade-up-d{{ $loop->index + 1 }}" style="display:flex;flex-direction:column;justify-content:space-between;">
                <div>
                    <span style="font-size:0.7rem;font-weight:700;color:var(--cyan-400);text-transform:uppercase;letter-spacing:1px;">DML</span>
                    <h3 style="font-size:1.05rem;font-weight:700;color:#fff;margin:0.4rem 0 0.3rem;">{{ $course->title }}</h3>
                    <p style="font-size:0.8rem;color:#64748b;">{{ $course->sub_materi_count }} sub materi</p>

                    {{-- Progress Bar --}}
                    <div style="display:flex;align-items:center;gap:0.75rem;margin:1rem 0;">
                        <div style="flex:1;background:rgba(255,255,255,0.08);border-radius:99px;height:6px;">
                            <div style="background:linear-gradient(90deg,var(--blue-600),var(--cyan-400));height:6px;border-radius:99px;width:{{ $course->progress }}%;transition:width 0.3s;"></div>
                        </div>
                        <span style="font-size:0.75rem;color:#64748b;font-weight:600;">{{ $course->progress }}%</span>
                    </div>
                </div>

                <a href="{{ route('courses.show', $course) }}" style="display:block;text-align:center;padding:0.55rem;border-radius:8px;border:1px solid rgba(255,255,255,0.12);color:#cbd5e1;font-size:0.8rem;font-weight:600;text-decoration:none;transition:all 0.2s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.06)';this.style.borderColor='rgba(255,255,255,0.25)';this.style.color='#fff'"
                    onmouseout="this.style.background='transparent';this.style.borderColor='rgba(255,255,255,0.12)';this.style.color='#cbd5e1'">
                    Mulai
                </a>
            </div>
        @endforeach
    </div>

</x-layouts.app>