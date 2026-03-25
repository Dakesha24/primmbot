<x-layouts.app title="{{ $course->title }} - PRIMMBOT">

    @if (session('enroll_success'))
        <style>
            @keyframes toastIn { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const toast = document.createElement('div');
                toast.id = 'enroll-toast';
                toast.style.cssText = 'position:fixed;top:5rem;left:50%;transform:translateX(-50%);z-index:9999;background:#0f2a1a;border:1px solid rgba(74,222,128,0.35);border-radius:10px;padding:0.65rem 1rem;box-shadow:0 8px 24px rgba(0,0,0,0.4);display:flex;align-items:center;gap:0.6rem;white-space:nowrap;animation:toastIn 0.3s ease;';
                toast.innerHTML = `
                    <svg width="16" height="16" fill="none" stroke="#4ade80" stroke-width="2.5" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span style="font-size:0.82rem;font-weight:600;color:#4ade80;">Berhasil mendaftar</span>
                    <span style="font-size:0.82rem;color:#94a3b8;">—</span>
                    <span style="font-size:0.82rem;color:#e2e8f0;font-weight:600;">{{ session('enroll_success') }}</span>
                    <button onclick="this.closest('#enroll-toast').remove()" style="background:none;border:none;color:#475569;cursor:pointer;padding:0 0 0 4px;font-size:0.9rem;line-height:1;flex-shrink:0;">✕</button>
                `;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.style.transition = 'opacity 0.4s';
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 400);
                }, 5000);
            });
        </script>
    @endif

    <div class="page-header fade-up">
        <div style="margin-bottom:0.5rem;">
            <a href="{{ route('courses.index') }}" style="color:#64748b;text-decoration:none;font-size:0.85rem;display:inline-flex;align-items:center;gap:0.3rem;transition:color 0.2s;"
                onmouseover="this.style.color='#cbd5e1'" onmouseout="this.style.color='#64748b'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Daftar LKPD
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

                <a href="{{ $entryUrl }}"
                    style="padding:0.45rem 1rem;border-radius:8px;border:1px solid rgba(255,255,255,0.12);background:transparent;color:#cbd5e1;font-size:0.8rem;font-weight:600;text-decoration:none;transition:all 0.2s;flex-shrink:0;white-space:nowrap;"
                    onmouseover="this.style.background='rgba(255,255,255,0.06)';this.style.borderColor='rgba(255,255,255,0.25)';this.style.color='#fff'"
                    onmouseout="this.style.background='transparent';this.style.borderColor='rgba(255,255,255,0.12)';this.style.color='#cbd5e1'">
                    Koridor LKPD
                </a>
            </div>
        @endforeach
    </div>


</x-layouts.app>