<x-layouts.admin title="Kelola LKPD">
    <x-slot:styles>
        <style>
            .courses-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                gap: 16px;
            }

            .course-card {
                background: #fff;
                border: 1px solid #d0d5e0;
                border-radius: 6px;
                display: flex;
                flex-direction: column;
                box-shadow: 3px 3px 0 #c8cfdc;
                transition: box-shadow 0.15s;
            }

            .course-card:hover {
                box-shadow: 4px 4px 0 #b8c2d4;
            }

            .card-body {
                padding: 20px 22px;
                flex: 1;
            }

            .card-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 10px;
            }

            .card-order {
                font-size: 11px;
                font-weight: 700;
                color: #6b7a99;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .card-scope {
                font-size: 11px;
                font-weight: 600;
                color: #6b7a99;
                background: #f0f2f7;
                padding: 3px 8px;
                border-radius: 4px;
            }

            .card-title {
                font-size: 14px;
                font-weight: 700;
                color: #0f1b3d;
                margin-bottom: 6px;
                line-height: 1.4;
            }

            .card-desc {
                font-size: 12.5px;
                color: #6b7a99;
                line-height: 1.6;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                margin-bottom: 14px;
            }

            .card-meta {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 12px;
                color: #9aa5b8;
            }

            .card-footer {
                display: flex;
                border-top: 1px solid #f0f2f7;
            }

            .cf-btn {
                flex: 1;
                padding: 11px 0;
                text-align: center;
                font-size: 12px;
                font-weight: 700;
                font-family: inherit;
                cursor: pointer;
                border: none;
                background: none;
                text-decoration: none;
                color: #4a5568;
                display: block;
                transition: background 0.12s, color 0.12s;
            }

            .cf-btn + .cf-btn { border-left: 1px solid #f0f2f7; }

            .cf-manage { color: #0f1b3d; }
            .cf-manage:hover { background: #f0f2f7; }
            .cf-edit:hover   { background: #f7f8fa; }
            .cf-delete       { color: #dc2626; }
            .cf-delete:hover { background: #fef2f2; }

            .empty-state {
                grid-column: 1 / -1;
                background: #fff;
                border: 2px dashed #dde1ea;
                border-radius: 6px;
                padding: 56px 40px;
                text-align: center;
                color: #9aa5b8;
            }

            .empty-state p { font-size: 13px; margin: 12px 0 20px; }
        </style>
    </x-slot:styles>

    <div class="page-header" style="display:flex;justify-content:space-between;align-items:center;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <h1 style="margin:0;">Kelola LKPD</h1>
                    <button onclick="openModal('help')" style="width:20px;height:20px;border-radius:50%;border:1.5px solid #6b7a99;background:#f0f2f7;color:#4a5568;font-size:11px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.15s;flex-shrink:0;" onmouseover="this.style.background='#0f1b3d';this.style.borderColor='#0f1b3d';this.style.color='#fff'" onmouseout="this.style.background='#f0f2f7';this.style.borderColor='#6b7a99';this.style.color='#4a5568'">?</button>
                </div>
                <p>{{ $courses->count() }} LKPD tersedia</p>
            </div>
        </div>
        <button class="btn-primary" style="display:inline-flex;align-items:center;gap:7px;" onclick="openModal('create')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah LKPD
        </button>
    </div>

    <div class="courses-grid">
        @forelse($courses as $course)
            <div class="course-card">
                <!-- Cover Image -->
                <div style="height:130px;overflow:hidden;border-radius:5px 5px 0 0;background:#e8eaf0;flex-shrink:0;">
                    <img src="{{ $course->coverImageUrl() }}" alt=""
                        style="width:100%;height:100%;object-fit:cover;display:block;">
                </div>
                <div class="card-body">
                    <div class="card-top">
                        <span class="card-order">Urutan #{{ $course->order }}</span>
                        <span class="card-scope">{{ $course->kelas?->name ?? 'Umum' }}</span>
                    </div>
                    <div class="card-title">{{ $course->title }}</div>
                    <div class="card-desc">{{ $course->description ?? 'Tidak ada deskripsi.' }}</div>
                    <div class="card-meta">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                        {{ $course->chapters_count }} chapter
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.chapters.index', $course) }}" class="cf-btn cf-manage">Kelola Chapter →</a>
                    <button class="cf-btn cf-edit btn-edit-course"
                        data-id="{{ $course->id }}"
                        data-title="{{ $course->title }}"
                        data-desc="{{ $course->description ?? '' }}"
                        data-order="{{ $course->order }}"
                        data-kelas="{{ $course->kelas_id ?? '' }}"
                        data-cover="{{ $course->cover_image ? $course->coverImageUrl() : '' }}"
                        >Edit</button>
                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                        onsubmit="return confirm('Hapus LKPD ini beserta semua chapter di dalamnya?')"
                        style="flex:1;display:flex;">
                        @csrf @method('DELETE')
                        <button type="submit" class="cf-btn cf-delete" style="flex:1;">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                <p>Belum ada LKPD. Mulai dengan menambahkan LKPD pertama.</p>
                <button class="btn-primary" onclick="openModal('create')">+ Tambah LKPD</button>
            </div>
        @endforelse
    </div>

    <!-- Modal Panduan -->
    <div class="modal-backdrop" id="helpModal" style="align-items:flex-start;overflow-y:auto;padding:40px 20px;">
        <div class="modal-box" style="max-width:640px;width:100%;margin:auto;">
            <button class="modal-close" onclick="closeModal('help')">&times;</button>
            <h2>Panduan Kelola LKPD</h2>
            <div style="font-size:13px;color:#4a5568;line-height:1.7;display:flex;flex-direction:column;gap:14px;">
                <div style="background:#f7f8fa;border:1px solid #e8eaf0;border-radius:5px;padding:12px 14px;">
                    <div style="font-weight:700;color:#0f1b3d;margin-bottom:6px;">① Tambah LKPD</div>
                    Klik <strong>Tambah LKPD</strong>, isi judul, deskripsi singkat, urutan tampil, dan pilih target kelas siswa (atau biarkan <em>Umum</em> untuk semua). LKPD adalah wadah utama materi pembelajaran.
                </div>
                <div style="background:#f7f8fa;border:1px solid #e8eaf0;border-radius:5px;padding:12px 14px;">
                    <div style="font-weight:700;color:#0f1b3d;margin-bottom:6px;">② Kelola Chapter</div>
                    Setelah LKPD dibuat, klik <strong>Kelola Chapter →</strong> untuk menambahkan chapter. Setiap chapter berisi materi belajar (pendahuluan, tujuan, ringkasan, dll.) dan aktivitas PRIMM (Predict, Run, Investigate, Modify, Make).
                </div>
                <div style="background:#f7f8fa;border:1px solid #e8eaf0;border-radius:5px;padding:12px 14px;">
                    <div style="font-weight:700;color:#0f1b3d;margin-bottom:6px;">③ Atur Urutan</div>
                    Urutan menentukan posisi LKPD di halaman siswa. Jika nomor urutan bentrok, LKPD lain akan otomatis digeser — tidak perlu diubah manual satu per satu.
                </div>
                <div style="background:#f7f8fa;border:1px solid #e8eaf0;border-radius:5px;padding:12px 14px;">
                    <div style="font-weight:700;color:#0f1b3d;margin-bottom:6px;">④ Edit / Hapus</div>
                    Gunakan <strong>Edit</strong> untuk mengubah judul, deskripsi, urutan, atau target kelas siswa. Gunakan <strong>Hapus</strong> untuk menghapus LKPD beserta seluruh chapter dan aktivitas di dalamnya — <em>tindakan ini tidak dapat dibatalkan.</em>
                </div>
            </div>
            <div class="form-actions" style="margin-top:20px;padding-top:16px;">
                <button type="button" class="btn-primary" onclick="closeModal('help')">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal-backdrop" id="createModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal('create')">&times;</button>
            <h2>Tambah LKPD Baru</h2>
            @if ($errors->any() && !old('_edit'))
                <div class="form-errors">
                    @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Judul LKPD *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: DML Join Tabel">
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Deskripsi singkat LKPD...">{{ old('description') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Urutan *</label>
                    <input type="number" name="order" value="{{ old('order', $courses->count() + 1) }}" required min="1">
                </div>
                <div class="form-group">
                    <label>Target Kelas <span style="font-weight:400;color:#9aa5b8;">(kosongkan = semua kelas)</span></label>
                    <select name="kelas_id">
                        <option value="">— Umum (Semua Kelas) —</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->school->name }} — {{ $kelas->name }} ({{ $kelas->tahunAjaran->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Cover Image <span style="font-weight:400;color:#9aa5b8;">(opsional, maks. 2MB)</span></label>
                    <div id="createCoverPreviewWrap" style="display:none;margin-bottom:8px;width:100%;height:110px;border-radius:5px;overflow:hidden;background:#e8eaf0;">
                        <img id="createCoverPreview" src="" alt="" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <input type="file" name="cover_image" id="createCoverInput" accept="image/*"
                        style="width:100%;padding:7px 10px;border:1.5px solid #dde1ea;border-radius:5px;font-size:13px;font-family:inherit;background:#fff;color:#1a2332;">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('create')">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal-backdrop" id="editModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal('edit')">&times;</button>
            <h2>Edit LKPD</h2>
            @if ($errors->any() && old('_edit'))
                <div class="form-errors">
                    @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif
            <form method="POST" id="editForm" enctype="multipart/form-data">
                @csrf @method('PUT')
                <input type="hidden" name="_edit" value="1">
                <input type="hidden" name="remove_cover" id="editRemoveCover" value="0">
                <div class="form-group">
                    <label>Judul LKPD *</label>
                    <input type="text" name="title" id="editTitle" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" id="editDescription" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Urutan *</label>
                    <input type="number" name="order" id="editOrder" required min="1">
                </div>
                <div class="form-group">
                    <label>Target Kelas <span style="font-weight:400;color:#9aa5b8;">(kosongkan = semua kelas)</span></label>
                    <select name="kelas_id" id="editKelasId">
                        <option value="">— Umum (Semua Kelas) —</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->id }}">
                                {{ $kelas->school->name }} — {{ $kelas->name }} ({{ $kelas->tahunAjaran->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Cover Image <span style="font-weight:400;color:#9aa5b8;">(opsional, maks. 2MB)</span></label>
                    <div id="editCoverPreviewWrap" style="display:none;margin-bottom:8px;position:relative;width:100%;height:110px;border-radius:5px;overflow:hidden;background:#e8eaf0;">
                        <img id="editCoverPreview" src="" alt="" style="width:100%;height:100%;object-fit:cover;">
                        <button type="button" onclick="removeCover()"
                            style="position:absolute;top:6px;right:6px;background:rgba(0,0,0,0.55);color:#fff;border:none;border-radius:4px;padding:3px 8px;font-size:11px;font-weight:700;cursor:pointer;">
                            Hapus Cover
                        </button>
                    </div>
                    <input type="file" name="cover_image" id="editCoverInput" accept="image/*"
                        style="width:100%;padding:7px 10px;border:1.5px solid #dde1ea;border-radius:5px;font-size:13px;font-family:inherit;background:#fff;color:#1a2332;">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('edit')">Batal</button>
                    <button type="submit" class="btn-primary">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

    <x-slot:scripts>
        <script>
            function openModal(t)  { document.getElementById(t + 'Modal').classList.add('active'); }
            function closeModal(t) { document.getElementById(t + 'Modal').classList.remove('active'); }

            // Edit buttons via data attributes
            document.querySelectorAll('.btn-edit-course').forEach(btn => {
                btn.addEventListener('click', function () {
                    const coverUrl = this.dataset.cover;
                    document.getElementById('editForm').action = '/admin/courses/' + this.dataset.id;
                    document.getElementById('editTitle').value       = this.dataset.title;
                    document.getElementById('editDescription').value = this.dataset.desc;
                    document.getElementById('editOrder').value       = this.dataset.order;
                    document.getElementById('editKelasId').value     = this.dataset.kelas || '';
                    document.getElementById('editRemoveCover').value = '0';
                    document.getElementById('editCoverInput').value  = '';

                    const wrap = document.getElementById('editCoverPreviewWrap');
                    const img  = document.getElementById('editCoverPreview');
                    if (coverUrl) {
                        img.src = coverUrl;
                        wrap.style.display = 'block';
                    } else {
                        wrap.style.display = 'none';
                    }
                    openModal('edit');
                });
            });

            function removeCover() {
                document.getElementById('editRemoveCover').value = '1';
                document.getElementById('editCoverPreviewWrap').style.display = 'none';
                document.getElementById('editCoverInput').value = '';
            }

            // Preview cover saat pilih file — modal tambah
            document.getElementById('createCoverInput').addEventListener('change', function () {
                const wrap = document.getElementById('createCoverPreviewWrap');
                const img  = document.getElementById('createCoverPreview');
                if (this.files && this.files[0]) {
                    img.src = URL.createObjectURL(this.files[0]);
                    wrap.style.display = 'block';
                } else {
                    wrap.style.display = 'none';
                }
            });

            // Preview cover saat pilih file — modal edit
            document.getElementById('editCoverInput').addEventListener('change', function () {
                const wrap = document.getElementById('editCoverPreviewWrap');
                const img  = document.getElementById('editCoverPreview');
                if (this.files && this.files[0]) {
                    img.src = URL.createObjectURL(this.files[0]);
                    wrap.style.display = 'block';
                    document.getElementById('editRemoveCover').value = '0';
                }
            });

            document.querySelectorAll('.modal-backdrop').forEach(m => {
                m.addEventListener('click', function(e) {
                    if (e.target === this) this.classList.remove('active');
                });
            });

            @if ($errors->any() && old('_edit'))
                openModal('edit');
            @elseif ($errors->any())
                openModal('create');
            @endif
        </script>
    </x-slot:scripts>

</x-layouts.admin>
