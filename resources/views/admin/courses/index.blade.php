<x-layouts.admin title="Kelola Kelas">
    <x-slot:styles>
        <style>
            .page-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 28px;
            }

            .page-header p {
                color: #94a3b8;
                font-size: 14px;
            }

            .btn-add {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #0f1b3d;
                color: #fff;
                padding: 10px 22px;
                border-radius: 10px;
                font-size: 13px;
                font-weight: 700;
                border: none;
                cursor: pointer;
                font-family: inherit;
                transition: all 0.2s;
            }

            .btn-add:hover {
                background: #1a2d5a;
                transform: translateY(-1px);
            }

            .courses-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
                gap: 22px;
            }

            .course-card {
                background: #fff;
                border: 1px solid #e4e8f1;
                border-radius: 16px;
                padding: 0;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                transition: all 0.25s;
            }

            .course-card:hover {
                border-color: #c7d0e2;
                box-shadow: 0 8px 32px rgba(15, 27, 61, 0.07);
                transform: translateY(-2px);
            }

            .card-accent {
                height: 4px;
                background: linear-gradient(90deg, #0f1b3d, #3b5bdb);
            }

            .card-body {
                padding: 24px 24px 0;
                flex: 1;
            }

            .card-order {
                display: inline-block;
                font-size: 11px;
                font-weight: 700;
                color: #3b5bdb;
                background: #eef2ff;
                padding: 3px 10px;
                border-radius: 6px;
                margin-bottom: 12px;
            }

            .card-body h3 {
                font-size: 17px;
                font-weight: 700;
                color: #0f1b3d;
                margin-bottom: 8px;
                line-height: 1.3;
            }

            .card-body .desc {
                font-size: 13px;
                color: #64748b;
                line-height: 1.6;
                margin-bottom: 18px;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .card-stat {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 12px;
                font-weight: 600;
                color: #94a3b8;
                background: #f8f9fc;
                padding: 6px 12px;
                border-radius: 8px;
                margin-bottom: 20px;
            }

            .card-footer {
                display: flex;
                border-top: 1px solid #f0f2f7;
            }

            .card-footer .cf-btn {
                flex: 1;
                padding: 13px 0;
                text-align: center;
                font-size: 12.5px;
                font-weight: 700;
                font-family: inherit;
                cursor: pointer;
                border: none;
                background: none;
                text-decoration: none;
                transition: all 0.15s;
            }

            .card-footer .cf-btn+.cf-btn {
                border-left: 1px solid #f0f2f7;
            }

            .cf-manage {
                color: #3b5bdb;
            }

            .cf-manage:hover {
                background: #eef2ff;
            }

            .cf-edit {
                color: #475569;
            }

            .cf-edit:hover {
                background: #f8fafc;
            }

            .cf-delete {
                color: #ef4444;
            }

            .cf-delete:hover {
                background: #fef2f2;
            }

            .empty-state {
                grid-column: 1 / -1;
                background: #fff;
                border: 2px dashed #d5dbe8;
                border-radius: 16px;
                padding: 64px 40px;
                text-align: center;
            }

            .empty-state svg {
                margin-bottom: 16px;
                color: #c7d0e2;
            }

            .empty-state p {
                font-size: 14px;
                color: #94a3b8;
                margin-bottom: 20px;
            }
        </style>
    </x-slot:styles>

    <div class="page-header">
        <p>{{ $courses->count() }} kelas tersedia</p>
        <button class="btn-add" onclick="openModal('create')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Tambah Kelas
        </button>
    </div>

    <div class="courses-grid">
        @forelse($courses as $course)
            <div class="course-card">
                <div class="card-accent"></div>
                <div class="card-body">
                    <span class="card-order">Urutan #{{ $course->order }}</span>
                    <h3>{{ $course->title }}</h3>
                    <p class="desc">{{ $course->description ?? 'Tidak ada deskripsi.' }}</p>
                    <div class="card-stat">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                        {{ $course->chapters_count }} chapters
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.chapters.index', $course) }}" class="cf-btn cf-manage">Kelola Chapters
                        →</a>
                    <button class="cf-btn cf-edit"
                        onclick="openEditModal({{ $course->id }}, '{{ addslashes($course->title) }}', `{{ addslashes($course->description ?? '') }}`, {{ $course->order }})">Edit</button>
                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                        onsubmit="return confirm('Hapus kelas ini beserta semua chapter di dalamnya?')"
                        style="flex:1; display:flex;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="cf-btn cf-delete" style="flex:1;">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.5">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                </svg>
                <p>Belum ada kelas. Mulai dengan menambahkan kelas pertama.</p>
                <button class="btn-add" onclick="openModal('create')">+ Tambah Kelas</button>
            </div>
        @endforelse
    </div>

    <!-- Modal Tambah -->
    <div class="modal-backdrop" id="createModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal('create')">&times;</button>
            <h2>Tambah Kelas Baru</h2>
            @if ($errors->any() && !old('_edit'))
                <div class="form-errors">
                    @foreach ($errors->all() as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('admin.courses.store') }}">
                @csrf
                <div class="form-group">
                    <label>Judul Kelas *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        placeholder="Contoh: DML Join Tabel">
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Deskripsi singkat kelas...">{{ old('description') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Urutan *</label>
                    <input type="number" name="order" value="{{ old('order', $courses->count() + 1) }}" required
                        min="0">
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
            <h2>Edit Kelas</h2>
            @if ($errors->any() && old('_edit'))
                <div class="form-errors">
                    @foreach ($errors->all() as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            @endif
            <form method="POST" id="editForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="_edit" value="1">
                <div class="form-group">
                    <label>Judul Kelas *</label>
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
                document.getElementById('editForm').action = '/admin/courses/' + id;
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
            @elseif ($errors->any()) openModal('create');
            @endif
        </script>
    </x-slot:scripts>

</x-layouts.admin>
