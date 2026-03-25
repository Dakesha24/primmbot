<x-layouts.admin title="Chapters — {{ $course->title }}">
    <x-slot:styles>
        <style>
            .breadcrumb {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 20px;
                font-size: 13px;
            }
            .breadcrumb a { color: #3b5bdb; text-decoration: none; font-weight: 600; }
            .breadcrumb a:hover { text-decoration: underline; }
            .breadcrumb span { color: #9aa5b8; }

            .course-banner {
                background: #fff;
                border: 1px solid #d0d5e0;
                border-radius: 6px;
                padding: 16px 20px;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 3px 3px 0 #c8cfdc;
            }

            .course-banner h2 {
                font-size: 14px;
                font-weight: 700;
                color: #0f1b3d;
                margin-bottom: 3px;
            }

            .course-banner p {
                font-size: 12.5px;
                color: #9aa5b8;
            }

            .chapters-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                gap: 16px;
            }

            .chapter-card {
                background: #fff;
                border: 1px solid #d0d5e0;
                border-radius: 6px;
                display: flex;
                flex-direction: column;
                box-shadow: 3px 3px 0 #c8cfdc;
                transition: box-shadow 0.15s;
            }

            .chapter-card:hover {
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
                gap: 8px;
            }

            .card-meta .meta-item {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                font-size: 12px;
                font-weight: 600;
                color: #4a5568;
                background: #f0f2f7;
                border: 1px solid #d0d5e0;
                padding: 4px 9px;
                border-radius: 4px;
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

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('admin.courses.index') }}">Kelola LKPD</a>
        <span>›</span>
        <span>{{ $course->title }}</span>
    </div>

    <!-- Course Banner -->
    <div class="course-banner">
        <div>
            <h2>{{ $course->title }}</h2>
            <p>{{ $course->description ?? 'Tidak ada deskripsi.' }}</p>
        </div>
    </div>

    <!-- Page Header -->
    <div class="page-header" style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <div style="display:flex;align-items:center;gap:8px;">
                <h1 style="margin:0;">Kelola Chapter</h1>
                <button onclick="openModal('help')" style="width:20px;height:20px;border-radius:50%;border:1.5px solid #6b7a99;background:#f0f2f7;color:#4a5568;font-size:11px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.15s;flex-shrink:0;"
                    onmouseover="this.style.background='#0f1b3d';this.style.borderColor='#0f1b3d';this.style.color='#fff'"
                    onmouseout="this.style.background='#f0f2f7';this.style.borderColor='#6b7a99';this.style.color='#4a5568'">?</button>
            </div>
            <p>{{ $chapters->count() }} chapter tersedia</p>
        </div>
        <button class="btn-primary" style="display:inline-flex;align-items:center;gap:7px;" onclick="openModal('create')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Chapter
        </button>
    </div>

    <div class="chapters-grid">
        @forelse($chapters as $chapter)
            <div class="chapter-card">
                <div class="card-body">
                    <div class="card-top">
                        <span class="card-order">Chapter #{{ $chapter->order }}</span>
                    </div>
                    <div class="card-title">{{ $chapter->title }}</div>
                    <div class="card-desc">{{ $chapter->description ?? 'Tidak ada deskripsi.' }}</div>
                    <div class="card-meta">
                        <span class="meta-item">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                            {{ $chapter->lesson_materials_count }} materi
                        </span>
                        <span class="meta-item">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            {{ $chapter->activities_count }} aktivitas
                        </span>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.chapters.content', [$course, $chapter]) }}" class="cf-btn cf-manage">Kelola Kegiatan →</a>
                    <button class="cf-btn cf-edit"
                        onclick="openEditModal({{ $chapter->id }}, '{{ addslashes($chapter->title) }}', `{{ addslashes($chapter->description ?? '') }}`, {{ $chapter->order }})">Edit</button>
                    <form method="POST" action="{{ route('admin.chapters.destroy', [$course, $chapter]) }}"
                        onsubmit="return confirm('Hapus chapter ini beserta semua materi & aktivitas?')"
                        style="flex:1;display:flex;">
                        @csrf @method('DELETE')
                        <button type="submit" class="cf-btn cf-delete" style="flex:1;">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <p>Belum ada chapter di LKPD ini.</p>
                <button class="btn-primary" onclick="openModal('create')">+ Tambah Chapter</button>
            </div>
        @endforelse
    </div>

    <!-- Modal Panduan -->
    <div class="modal-backdrop" id="helpModal" style="align-items:flex-start;overflow-y:auto;padding:40px 20px;">
        <div class="modal-box" style="max-width:640px;width:100%;margin:auto;">
            <button class="modal-close" onclick="closeModal('help')">&times;</button>
            <h2>Panduan Kelola Chapter</h2>
            <div style="font-size:13px;color:#4a5568;line-height:1.7;display:flex;flex-direction:column;gap:14px;">
                <div style="background:#f7f8fa;border:1px solid #e8eaf0;border-radius:5px;padding:12px 14px;">
                    <div style="font-weight:700;color:#0f1b3d;margin-bottom:6px;">① Tambah Chapter</div>
                    Klik <strong>Tambah Chapter</strong>, isi judul, deskripsi singkat, dan urutan tampil. Chapter adalah pembagian topik di dalam sebuah LKPD — misalnya <em>Equi Join</em>, <em>Non-Equi Join</em>, dan sebagainya.
                </div>
                <div style="background:#f7f8fa;border:1px solid #e8eaf0;border-radius:5px;padding:12px 14px;">
                    <div style="font-weight:700;color:#0f1b3d;margin-bottom:6px;">② Kelola Kegiatan</div>
                    Setelah chapter dibuat, klik <strong>Kelola Kegiatan →</strong> untuk menambahkan konten di dalamnya. Setiap chapter memiliki dua jenis konten:
                    <ul style="margin:8px 0 0 16px;display:flex;flex-direction:column;gap:4px;">
                        <li><strong>Materi Belajar</strong> — pendahuluan, petunjuk belajar, tujuan, prasyarat, dan ringkasan materi.</li>
                        <li><strong>Aktivitas PRIMM</strong> — Predict, Run, Investigate, Modify, Make.</li>
                    </ul>
                </div>
                <div style="background:#f7f8fa;border:1px solid #e8eaf0;border-radius:5px;padding:12px 14px;">
                    <div style="font-weight:700;color:#0f1b3d;margin-bottom:6px;">③ Atur Urutan</div>
                    Urutan menentukan posisi chapter saat siswa membuka LKPD. Gunakan angka yang berbeda agar urutan tidak bertabrakan — mulai dari 1, 2, 3, dan seterusnya.
                </div>
                <div style="background:#f7f8fa;border:1px solid #e8eaf0;border-radius:5px;padding:12px 14px;">
                    <div style="font-weight:700;color:#0f1b3d;margin-bottom:6px;">④ Edit / Hapus</div>
                    Gunakan <strong>Edit</strong> untuk mengubah judul, deskripsi, atau urutan chapter. Gunakan <strong>Hapus</strong> untuk menghapus chapter beserta seluruh materi dan aktivitas di dalamnya — <em>tindakan ini tidak dapat dibatalkan.</em>
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
            <h2>Tambah Chapter Baru</h2>
            @if ($errors->any() && !old('_edit'))
                <div class="form-errors">
                    @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('admin.chapters.store', $course) }}">
                @csrf
                <div class="form-group">
                    <label>Judul Chapter *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Equi Join">
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Deskripsi singkat chapter...">{{ old('description') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Urutan *</label>
                    <input type="number" name="order" value="{{ old('order', $chapters->count() + 1) }}" required min="0">
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
            <h2>Edit Chapter</h2>
            @if ($errors->any() && old('_edit'))
                <div class="form-errors">
                    @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif
            <form method="POST" id="editForm">
                @csrf @method('PUT')
                <input type="hidden" name="_edit" value="1">
                <div class="form-group">
                    <label>Judul Chapter *</label>
                    <input type="text" name="title" id="editTitle" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" id="editDescription" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Urutan *</label>
                    <input type="number" name="order" id="editOrder" required min="0">
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
            function openModal(t) {
                document.getElementById(t + 'Modal').classList.add('active');
            }

            function closeModal(t) {
                document.getElementById(t + 'Modal').classList.remove('active');
            }

            function openEditModal(id, title, desc, order) {
                document.getElementById('editForm').action = '/admin/courses/{{ $course->id }}/chapters/' + id;
                document.getElementById('editTitle').value = title;
                document.getElementById('editDescription').value = desc;
                document.getElementById('editOrder').value = order;
                openModal('edit');
            }

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
