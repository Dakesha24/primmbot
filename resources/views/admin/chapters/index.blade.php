<x-layouts.admin title="Chapters — {{ $course->title }}">
    <x-slot:styles>
        <style>
            .breadcrumb {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 24px;
                font-size: 13px;
            }

            .breadcrumb a {
                color: #3b5bdb;
                text-decoration: none;
                font-weight: 600;
            }

            .breadcrumb a:hover {
                text-decoration: underline;
            }

            .breadcrumb span {
                color: #94a3b8;
            }

            .course-banner {
                background: #fff;
                border: 1px solid #e4e8f1;
                border-radius: 14px;
                padding: 20px 24px;
                margin-bottom: 28px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .course-banner .cb-info h2 {
                font-size: 16px;
                font-weight: 700;
                color: #0f1b3d;
                margin-bottom: 4px;
            }

            .course-banner .cb-info p {
                font-size: 13px;
                color: #94a3b8;
            }

            .page-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 24px;
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

            .chapters-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
                gap: 22px;
            }

            .chapter-card {
                background: #fff;
                border: 1px solid #e4e8f1;
                border-radius: 16px;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                transition: all 0.25s;
            }

            .chapter-card:hover {
                border-color: #c7d0e2;
                box-shadow: 0 8px 32px rgba(15, 27, 61, 0.07);
                transform: translateY(-2px);
            }

            .card-accent {
                height: 4px;
                background: linear-gradient(90deg, #3b5bdb, #748ffc);
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
                font-size: 16px;
                font-weight: 700;
                color: #0f1b3d;
                margin-bottom: 8px;
                line-height: 1.3;
            }

            .card-body .desc {
                font-size: 13px;
                color: #64748b;
                line-height: 1.6;
                margin-bottom: 16px;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .card-stats {
                display: flex;
                gap: 10px;
                margin-bottom: 20px;
            }

            .card-stats .stat {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                font-size: 12px;
                font-weight: 600;
                color: #94a3b8;
                background: #f8f9fc;
                padding: 5px 10px;
                border-radius: 7px;
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

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('admin.courses.index') }}">Kelola Kelas</a>
        <span>›</span>
        <span>{{ $course->title }}</span>
    </div>

    <!-- Course Banner -->
    <div class="course-banner">
        <div class="cb-info">
            <h2>{{ $course->title }}</h2>
            <p>{{ $course->description ?? 'Tidak ada deskripsi.' }}</p>
        </div>
    </div>

    <div class="page-header">
        <p>{{ $chapters->count() }} chapter</p>
        <button class="btn-add" onclick="openModal('create')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Tambah Chapter
        </button>
    </div>

    <div class="chapters-grid">
        @forelse($chapters as $chapter)
            <div class="chapter-card">
                <div class="card-accent"></div>
                <div class="card-body">
                    <span class="card-order">Chapter {{ $chapter->order }}</span>
                    <h3>{{ $chapter->title }}</h3>
                    <p class="desc">{{ $chapter->description ?? 'Tidak ada deskripsi.' }}</p>
                    <div class="card-stats">
                        <span class="stat">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                            </svg>
                            {{ $chapter->lesson_materials_count }} materi
                        </span>
                        <span class="stat">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            {{ $chapter->activities_count }} aktivitas
                        </span>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.chapters.content', [$course, $chapter]) }}"
                        class="cf-btn cf-manage">Kelola Kegiatan →</a>
                    <button class="cf-btn cf-edit"
                        onclick="openEditModal({{ $chapter->id }}, '{{ addslashes($chapter->title) }}', `{{ addslashes($chapter->description ?? '') }}`, {{ $chapter->order }})">Edit</button>
                    <form method="POST" action="{{ route('admin.chapters.destroy', [$course, $chapter]) }}"
                        onsubmit="return confirm('Hapus chapter ini beserta semua materi & aktivitas?')"
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
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                </svg>
                <p>Belum ada chapter di kelas ini.</p>
                <button class="btn-add" onclick="openModal('create')">+ Tambah Chapter</button>
            </div>
        @endforelse
    </div>

    <!-- Modal Tambah -->
    <div class="modal-backdrop" id="createModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal('create')">&times;</button>
            <h2>Tambah Chapter Baru</h2>
            @if ($errors->any() && !old('_edit'))
                <div class="form-errors">
                    @foreach ($errors->all() as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('admin.chapters.store', $course) }}">
                @csrf
                <div class="form-group">
                    <label>Judul Chapter *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        placeholder="Contoh: Equi Join">
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Deskripsi singkat chapter...">{{ old('description') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Urutan *</label>
                    <input type="number" name="order" value="{{ old('order', $chapters->count() + 1) }}" required
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
            <h2>Edit Chapter</h2>
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
            @elseif ($errors->any()) openModal('create');
            @endif
        </script>
    </x-slot:scripts>

</x-layouts.admin>
