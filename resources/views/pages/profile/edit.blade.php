<x-layouts.app title="Profil - PRIMMBOT">

    <div class="page-header fade-up">
        <h1 class="page-title">Profil Saya</h1>
        <p class="page-subtitle">Lengkapi data diri untuk mulai belajar</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success fade-up">{{ session('success') }}</div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning fade-up">{{ session('warning') }}</div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start;">

            {{-- Kolom Kiri: Form --}}
            <div class="card fade-up fade-up-d1">
                <h3 style="font-size:1.05rem;font-weight:700;color:#fff;margin-bottom:1.5rem;">Data Diri</h3>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label" for="full_name">Nama Lengkap *</label>
                        <input class="form-input" id="full_name" type="text" name="full_name"
                            value="{{ old('full_name', $profile->full_name) }}" required
                            placeholder="Masukkan nama lengkap">
                        @error('full_name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="nim">NIS (Nomor Induk Siswa) *</label>
                        <input class="form-input" id="nim" type="text" name="nim"
                            value="{{ old('nim', $profile->nim) }}" required
                            placeholder="Masukkan NIS">
                        @error('nim')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="gender">Jenis Kelamin *</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="" disabled {{ old('gender', $profile->gender) ? '' : 'selected' }}>Pilih</option>
                            <option value="Laki-laki" {{ old('gender', $profile->gender) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender', $profile->gender) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="school_name">Nama Sekolah *</label>
                        <select class="form-select" id="school_name" name="school_name" required>
                            <option value="SMKN 4 Bandung" {{ old('school_name', $profile->school_name) === 'SMKN 4 Bandung' ? 'selected' : '' }}>SMKN 4 Bandung</option>
                        </select>
                        @error('school_name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="kelas">Kelas *</label>
                        <select class="form-select" id="kelas" name="kelas" required>
                            <option value="" disabled {{ old('kelas', $profile->kelas) ? '' : 'selected' }}>Pilih kelas</option>
                            <option value="XI PPLG 1" {{ old('kelas', $profile->kelas) === 'XI PPLG 1' ? 'selected' : '' }}>XI PPLG 1</option>
                            <option value="XI PPLG 2" {{ old('kelas', $profile->kelas) === 'XI PPLG 2' ? 'selected' : '' }}>XI PPLG 2</option>
                            <option value="XI PPLG 3" {{ old('kelas', $profile->kelas) === 'XI PPLG 3' ? 'selected' : '' }}>XI PPLG 3</option>
                        </select>
                        @error('kelas')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:0.75rem;margin-top:1.5rem;">
                    <button type="submit" class="btn btn-primary">Simpan Profil</button>
                    @if($profile->isComplete())
                        <a href="{{ route('dashboard') }}" class="btn btn-outline">Kembali</a>
                    @endif
                </div>
            </div>

            {{-- Kolom Kanan: Avatar + Info Akun --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;">

                {{-- Avatar Card --}}
                <div class="card fade-up fade-up-d2" style="text-align:center;">
                    <div id="avatar-preview" style="width:120px;height:120px;border-radius:50%;overflow:hidden;margin:0 auto 1rem;border:3px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;">
                        @if($profile->avatar)
                            <img src="{{ asset('storage/' . $profile->avatar) }}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <span style="font-size:2.5rem;font-weight:700;color:#475569;">
                                {{ strtoupper(substr($profile->full_name ?? '?', 0, 1)) }}
                            </span>
                        @endif
                    </div>

                    <p style="font-size:1.05rem;font-weight:700;color:#fff;margin-bottom:0.15rem;">
                        {{ $profile->full_name ?? $user->username }}
                    </p>
                    <p style="font-size:0.8rem;color:#64748b;margin-bottom:1rem;">
                        {{ $profile->kelas ? $profile->kelas . ' — ' : '' }}{{ $profile->school_name ?? '' }}
                    </p>

                    <label for="avatar" style="display:inline-block;padding:0.5rem 1.2rem;border-radius:8px;border:1px solid rgba(255,255,255,0.15);color:#cbd5e1;font-size:0.8rem;font-weight:500;cursor:pointer;transition:all 0.2s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.borderColor='rgba(255,255,255,0.3)'"
                        onmouseout="this.style.background='transparent';this.style.borderColor='rgba(255,255,255,0.15)'">
                        Ganti Foto
                    </label>
                    <input type="file" id="avatar" name="avatar" accept="image/jpg,image/jpeg,image/png,image/webp" style="display:none;"
                        onchange="previewAvatar(this)">
                    <p style="font-size:0.65rem;color:#475569;margin-top:0.5rem;">Opsional. JPG, PNG, WebP. Maks 2MB.</p>
                    @error('avatar')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info Akun Card --}}
                <div class="card fade-up fade-up-d3">
                    <h3 style="font-size:0.95rem;font-weight:700;color:#fff;margin-bottom:1rem;">Informasi Akun</h3>

                    <div style="margin-bottom:0.9rem;">
                        <p style="font-size:0.7rem;color:#475569;font-weight:600;margin-bottom:0.15rem;text-transform:uppercase;letter-spacing:0.5px;">Username</p>
                        <p style="font-size:0.85rem;color:#cbd5e1;">{{ $user->username }}</p>
                    </div>
                    <div style="margin-bottom:0.9rem;">
                        <p style="font-size:0.7rem;color:#475569;font-weight:600;margin-bottom:0.15rem;text-transform:uppercase;letter-spacing:0.5px;">Email</p>
                        <p style="font-size:0.85rem;color:#cbd5e1;">{{ $user->email }}</p>
                    </div>
                    <div style="margin-bottom:0.9rem;">
                        <p style="font-size:0.7rem;color:#475569;font-weight:600;margin-bottom:0.15rem;text-transform:uppercase;letter-spacing:0.5px;">Role</p>
                        <p style="font-size:0.85rem;color:#cbd5e1;">{{ ucfirst($user->role) }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.7rem;color:#475569;font-weight:600;margin-bottom:0.15rem;text-transform:uppercase;letter-spacing:0.5px;">Google</p>
                        <div style="display:flex;align-items:center;gap:0.4rem;">
                            @if($user->google_id)
                                <div style="width:8px;height:8px;border-radius:50%;background:#4ade80;"></div>
                                <span style="font-size:0.85rem;color:#4ade80;">Terhubung</span>
                            @else
                                <div style="width:8px;height:8px;border-radius:50%;background:#64748b;"></div>
                                <span style="font-size:0.85rem;color:#64748b;">Tidak terhubung</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file maksimal 2MB.');
                    input.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatar-preview');
                    preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>

    <style>
        @media (max-width: 768px) {
            [style*="grid-template-columns:1fr 320px"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

</x-layouts.app>