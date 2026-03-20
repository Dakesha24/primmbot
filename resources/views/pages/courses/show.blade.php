<x-layouts.app title="{{ $course->title }} - PRIMMBOT">

    <div class="page-header fade-up">
        <div style="margin-bottom:0.5rem;">
            <a href="{{ route('courses.index') }}" style="color:#64748b;text-decoration:none;font-size:0.85rem;display:inline-flex;align-items:center;gap:0.3rem;transition:color 0.2s;"
                onmouseover="this.style.color='#cbd5e1'" onmouseout="this.style.color='#64748b'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Daftar Kelas
            </a>
        </div>
        <h1 class="page-title">{{ $course->title }}</h1>
        <p class="page-subtitle" style="margin-top:0.4rem;">{{ $course->description }}</p>
    </div>

    <div class="card fade-up" style="margin-bottom:1.5rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">
            <span style="font-size:0.85rem;font-weight:600;color:#94a3b8;">Progress Keseluruhan</span>
            <span style="font-size:0.85rem;font-weight:700;color:var(--cyan-400);">{{ $courseProgress }}%</span>
        </div>
        <div style="background:rgba(255,255,255,0.08);border-radius:99px;height:8px;">
            <div style="background:linear-gradient(90deg,var(--blue-600),var(--cyan-400));height:8px;border-radius:99px;width:{{ $courseProgress }}%;transition:width 0.3s;"></div>
        </div>
    </div>

    <div class="card fade-up fade-up-d1">
        <h3 style="font-size:1.05rem;font-weight:700;color:#fff;margin-bottom:1.2rem;">Konten Materi</h3>

        @foreach($course->chapters as $chapter)
            @php
                $firstMaterial = $chapter->lessonMaterials->sortBy('order')->first();
                $entryUrl = $firstMaterial
                    ? route('learning.material', [$chapter, $firstMaterial->type])
                    : '#';
            @endphp
            <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 0;{{ !$loop->last ? 'border-bottom:1px solid rgba(255,255,255,0.06);' : '' }}">
                <div style="display:flex;align-items:center;gap:0.8rem;flex:1;min-width:0;">
                    @if($chapter->progress === 100)
                        <div style="width:28px;height:28px;border-radius:50%;background:rgba(74,222,128,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="14" height="14" fill="#4ade80" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </div>
                    @else
                        <div style="width:28px;height:28px;border-radius:50%;border:2px solid rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="font-size:0.7rem;font-weight:700;color:#475569;">{{ $loop->iteration }}</span>
                        </div>
                    @endif
                    <div style="min-width:0;">
                        <p style="font-size:0.95rem;font-weight:600;color:#fff;">{{ $chapter->title }}</p>
                        <div style="display:flex;align-items:center;gap:0.6rem;margin-top:0.3rem;">
                            <span style="font-size:0.75rem;color:#64748b;">{{ $chapter->activities->count() }} aktivitas</span>
                            @if($chapter->progress > 0 && $chapter->progress < 100)
                                <span style="font-size:0.7rem;color:var(--blue-400);font-weight:600;">{{ $chapter->progress }}%</span>
                            @endif
                        </div>
                    </div>
                </div>

                <button onclick="openConfirm('{{ $chapter->title }}', '{{ $entryUrl }}')"
                    style="padding:0.45rem 1rem;border-radius:8px;border:1px solid rgba(255,255,255,0.12);background:transparent;color:#cbd5e1;font-size:0.8rem;font-weight:600;cursor:pointer;transition:all 0.2s;flex-shrink:0;font-family:inherit;"
                    onmouseover="this.style.background='rgba(255,255,255,0.06)';this.style.borderColor='rgba(255,255,255,0.25)';this.style.color='#fff'"
                    onmouseout="this.style.background='transparent';this.style.borderColor='rgba(255,255,255,0.12)';this.style.color='#cbd5e1'">
                    Koridor Kelas
                </button>
            </div>
        @endforeach
    </div>

    {{-- Modal Konfirmasi --}}
    <div id="confirmModal" style="display:none;position:fixed;inset:0;z-index:100;background:rgba(0,0,0,0.6);backdrop-filter:blur(6px);align-items:center;justify-content:center;">
        <div style="background:linear-gradient(135deg,#0f2044,#142c5c);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:2rem;max-width:400px;width:90%;text-align:center;box-shadow:0 24px 64px rgba(0,0,0,0.5);">
            <div style="width:56px;height:56px;border-radius:50%;background:rgba(37,99,235,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 1.2rem;">
                <svg width="24" height="24" fill="none" stroke="#60a5fa" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h3 style="font-size:1.15rem;font-weight:700;color:#fff;margin-bottom:0.4rem;">Mulai Kelas?</h3>
            <p id="confirmText" style="font-size:0.85rem;color:#94a3b8;margin-bottom:1.5rem;line-height:1.5;"></p>
            <div style="display:flex;gap:0.75rem;justify-content:center;">
                <button onclick="closeConfirm()" style="padding:0.6rem 1.5rem;border-radius:10px;border:1px solid rgba(255,255,255,0.12);background:transparent;color:#94a3b8;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:inherit;transition:all 0.2s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='#fff'"
                    onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                    Batal
                </button>
                <a id="confirmLink" href="#" style="padding:0.6rem 1.5rem;border-radius:10px;background:linear-gradient(135deg,#2563eb,#4f46e5);color:#fff;font-size:0.85rem;font-weight:600;text-decoration:none;box-shadow:0 4px 16px rgba(37,99,235,0.3);transition:all 0.2s;"
                    onmouseover="this.style.transform='translateY(-1px)'"
                    onmouseout="this.style.transform='translateY(0)'">
                    Ya, Mulai!
                </a>
            </div>
        </div>
    </div>

    <script>
        function openConfirm(title, url) {
            document.getElementById('confirmText').textContent = 'Kamu akan memasuki koridor kelas "' + title + '". Pastikan kamu sudah siap untuk belajar.';
            document.getElementById('confirmLink').href = url;
            document.getElementById('confirmModal').style.display = 'flex';
        }
        function closeConfirm() { document.getElementById('confirmModal').style.display = 'none'; }
        document.getElementById('confirmModal').addEventListener('click', function(e) { if (e.target === this) closeConfirm(); });
    </script>

</x-layouts.app>