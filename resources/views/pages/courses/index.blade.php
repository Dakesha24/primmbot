<x-layouts.app title="LKPD - PRIMMBOT">

    <div class="page-header fade-up">
        <h1 class="page-title">Daftar LKPD</h1>
        <p class="page-subtitle">Pilih LKPD untuk mulai belajar</p>
    </div>

    @if(session('warning'))
        <div style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.3);color:#fbbf24;padding:0.75rem 1rem;border-radius:10px;font-size:0.85rem;font-weight:600;margin-bottom:1.5rem;">
            {{ session('warning') }}
        </div>
    @endif

    <div class="grid-3">
        @foreach($courses as $course)
            <div class="card fade-up fade-up-d{{ $loop->index + 1 }}" style="display:flex;flex-direction:column;justify-content:space-between;padding:0;overflow:hidden;">
                {{-- Cover Image --}}
                <div style="height:120px;overflow:hidden;flex-shrink:0;">
                    <img src="{{ $course->coverImageUrl() }}" alt=""
                        style="width:100%;height:100%;object-fit:cover;display:block;">
                </div>
                <div style="padding:1rem 1.2rem;flex:1;display:flex;flex-direction:column;justify-content:space-between;">
                <div>
                    {{-- Badge kelas target --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">
                        <span style="font-size:0.7rem;font-weight:700;color:var(--cyan-400);text-transform:uppercase;letter-spacing:1px;">DML</span>
                        @if($course->kelas)
                            <span style="font-size:0.65rem;font-weight:700;color:#6366f1;background:rgba(99,102,241,0.12);padding:2px 8px;border-radius:20px;">
                                {{ $course->kelas->school->name }} · {{ $course->kelas->name }}
                            </span>
                        @else
                            <span style="font-size:0.65rem;font-weight:700;color:#64748b;background:rgba(255,255,255,0.05);padding:2px 8px;border-radius:20px;">
                                Umum
                            </span>
                        @endif
                    </div>

                    <h3 style="font-size:1.05rem;font-weight:700;color:#fff;margin:0.3rem 0 0.3rem;">{{ $course->title }}</h3>
                    <p style="font-size:0.8rem;color:#64748b;">{{ $course->sub_materi_count }} sub materi</p>

                    {{-- Progress Bar — hanya tampil jika sudah enrolled --}}
                    @if($course->is_enrolled)
                        <div style="display:flex;align-items:center;gap:0.75rem;margin:1rem 0;">
                            <div style="flex:1;background:rgba(255,255,255,0.08);border-radius:99px;height:6px;">
                                <div style="background:linear-gradient(90deg,var(--blue-600),var(--cyan-400));height:6px;border-radius:99px;width:{{ $course->progress }}%;transition:width 0.3s;"></div>
                            </div>
                            <span style="font-size:0.75rem;color:#64748b;font-weight:600;">{{ $course->progress }}%</span>
                        </div>
                    @else
                        <div style="margin:1rem 0;height:6px;background:rgba(255,255,255,0.04);border-radius:99px;"></div>
                    @endif
                </div>

                {{-- Button berbeda berdasarkan enrollment --}}
                @if($course->is_enrolled)
                    <a href="{{ route('courses.show', $course) }}"
                        style="display:block;text-align:center;padding:0.6rem;border-radius:8px;border:1px solid rgba(37,99,235,0.5);background:rgba(37,99,235,0.12);color:#93c5fd;font-size:0.8rem;font-weight:700;text-decoration:none;transition:all 0.2s;"
                        onmouseover="this.style.background='rgba(37,99,235,0.22)';this.style.borderColor='rgba(37,99,235,0.7)';this.style.color='#fff'"
                        onmouseout="this.style.background='rgba(37,99,235,0.12)';this.style.borderColor='rgba(37,99,235,0.5)';this.style.color='#93c5fd'">
                        Lanjutkan →
                    </a>
                @else
                    <button onclick="openEnroll({{ $course->id }}, '{{ addslashes($course->title) }}')"
                        style="display:block;width:100%;text-align:center;padding:0.6rem;border-radius:8px;border:none;background:linear-gradient(135deg,#2563eb,#4f46e5);color:#fff;font-size:0.8rem;font-weight:700;cursor:pointer;font-family:inherit;transition:all 0.2s;box-shadow:0 4px 14px rgba(37,99,235,0.3);"
                        onmouseover="this.style.opacity='0.88';this.style.transform='translateY(-1px)'"
                        onmouseout="this.style.opacity='1';this.style.transform='translateY(0)'">
                        Daftar LKPD
                    </button>
                @endif
                </div>{{-- end padding wrapper --}}
            </div>
        @endforeach
    </div>

    {{-- Modal Konfirmasi Daftar --}}
    <div id="enrollModal" style="display:none;position:fixed;inset:0;z-index:100;background:rgba(0,0,0,0.6);backdrop-filter:blur(6px);align-items:center;justify-content:center;">
        <div style="background:linear-gradient(135deg,#0f2044,#142c5c);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:2rem;max-width:400px;width:90%;text-align:center;box-shadow:0 24px 64px rgba(0,0,0,0.5);">
            <div style="width:56px;height:56px;border-radius:50%;background:rgba(37,99,235,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 1.2rem;">
                <svg width="24" height="24" fill="none" stroke="#60a5fa" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h3 style="font-size:1.15rem;font-weight:700;color:#fff;margin-bottom:0.4rem;">Daftar ke LKPD Ini?</h3>
            <p id="enrollText" style="font-size:0.85rem;color:#94a3b8;margin-bottom:1.5rem;line-height:1.5;"></p>
            <div style="display:flex;gap:0.75rem;justify-content:center;">
                <button onclick="closeEnroll()" style="padding:0.6rem 1.5rem;border-radius:10px;border:1px solid rgba(255,255,255,0.12);background:transparent;color:#94a3b8;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:inherit;transition:all 0.2s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='#fff'"
                    onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                    Batal
                </button>
                <form id="enrollForm" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" style="padding:0.6rem 1.5rem;border-radius:10px;background:linear-gradient(135deg,#2563eb,#4f46e5);color:#fff;font-size:0.85rem;font-weight:700;border:none;cursor:pointer;font-family:inherit;box-shadow:0 4px 16px rgba(37,99,235,0.3);transition:all 0.2s;"
                        onmouseover="this.style.transform='translateY(-1px)'"
                        onmouseout="this.style.transform='translateY(0)'">
                        Ya, Daftar!
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEnroll(courseId, courseTitle) {
            document.getElementById('enrollText').textContent =
                'Kamu akan mendaftar ke LKPD "' + courseTitle + '". Setelah mendaftar, kamu bisa mulai belajar.';
            document.getElementById('enrollForm').action = '/kelas/' + courseId + '/enroll';
            document.getElementById('enrollModal').style.display = 'flex';
        }
        function closeEnroll() { document.getElementById('enrollModal').style.display = 'none'; }
        document.getElementById('enrollModal').addEventListener('click', function(e) {
            if (e.target === this) closeEnroll();
        });
    </script>

</x-layouts.app>
