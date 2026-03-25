<x-layouts.admin title="Tambah Materi">
    <x-slot:styles>
        <script src="https://cdn.jsdelivr.net/npm/quill-resize-image@1.0.4/dist/quill-resize-image.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
        <style>
            .breadcrumb {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 24px;
                font-size: 13px;
                flex-wrap: wrap;
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

            .form-card {
                background: #fff;
                border: 1px solid #e4e8f1;
                border-radius: 16px;
                padding: 32px;
                max-width: 900px;
            }

            .form-card h2 {
                font-size: 18px;
                font-weight: 700;
                color: #0f1b3d;
                margin-bottom: 24px;
            }

            .form-row {
                display: flex;
                gap: 16px;
                margin-bottom: 18px;
            }

            .form-row .form-group {
                flex: 1;
                margin-bottom: 0;
            }

            #editor-container {
                height: 400px;
                background: #fff;
                border-radius: 0 0 10px 10px;
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: 14px;
            }

            .ql-toolbar.ql-snow {
                border-radius: 10px 10px 0 0;
                border-color: #e2e8f0;
                background: #f8f9fc;
            }

            .ql-container.ql-snow {
                border-color: #e2e8f0;
                border-radius: 0 0 10px 10px;
            }

            .ql-editor img {
                max-width: 100%;
                border-radius: 8px;
            }

            .editor-hint {
                font-size: 11px;
                color: #94a3b8;
                margin-top: 8px;
            }

            .ql-editor img {
                cursor: pointer;
            }
        </style>
    </x-slot:styles>

    <div class="breadcrumb">
        <a href="{{ route('admin.courses.index') }}">Kelola LKPD</a>
        <span>›</span>
        <a href="{{ route('admin.chapters.index', $course) }}">{{ $course->title }}</a>
        <span>›</span>
        <a href="{{ route('admin.chapters.content', [$course, $chapter]) }}">{{ $chapter->title }}</a>
        <span>›</span>
        <span>Tambah Materi</span>
    </div>

    <div class="form-card">
        <h2>Tambah Materi Baru</h2>

        @if ($errors->any())
            <div class="form-errors">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.materials.store', [$course, $chapter]) }}" id="materialForm">
            @csrf

            <div class="form-group">
                <label>Tipe Materi *</label>
                <select name="type" required>
                    <option value="">— Pilih Tipe —</option>
                    <option value="pendahuluan" {{ old('type', request('type')) == 'pendahuluan' ? 'selected' : '' }}>Pendahuluan</option>
                    <option value="petunjuk_belajar" {{ old('type', request('type')) == 'petunjuk_belajar' ? 'selected' : '' }}>Petunjuk Belajar</option>
                    <option value="tujuan" {{ old('type', request('type')) == 'tujuan' ? 'selected' : '' }}>Tujuan Pembelajaran</option>
                    <option value="prasyarat" {{ old('type', request('type')) == 'prasyarat' ? 'selected' : '' }}>Prasyarat</option>
                    <option value="ringkasan_materi" {{ old('type', request('type')) == 'ringkasan_materi' ? 'selected' : '' }}>Ringkasan Materi</option>
                </select>
            </div>

            <div class="form-group">
                <label>Konten *</label>
                <div id="editor-container">{!! old('content') !!}</div>
                <input type="hidden" name="content" id="contentInput">
                <div class="editor-hint">Gunakan toolbar untuk format teks, masukkan gambar, tabel, dan kode.</div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.chapters.content', [$course, $chapter]) }}" class="btn-secondary"
                    style="text-decoration:none;">Batal</a>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>

    <x-slot:scripts>
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
        <script>
            Quill.register('modules/resize', window.QuillResizeImage);
            const quill = new Quill('#editor-container', {
                theme: 'snow',
                placeholder: 'Tulis konten materi di sini...',
                modules: {
                    resize: {},
                    toolbar: {
                        container: [
                            [{
                                'header': [1, 2, 3, false]
                            }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{
                                'color': []
                            }, {
                                'background': []
                            }],
                            [{
                                'list': 'ordered'
                            }, {
                                'list': 'bullet'
                            }],
                            [{
                                'align': []
                            }],
                            ['blockquote', 'code-block'],
                            ['link', 'image'],
                            ['clean']
                        ],
                        handlers: {
                            image: imageHandler
                        }
                    }
                }
            });

            function imageHandler() {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = async () => {
                    const file = input.files[0];
                    if (!file) return;

                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran gambar maksimal 2MB.');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('image', file);

                    try {
                        const res = await fetch('{{ route('admin.upload.image') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: formData
                        });

                        const data = await res.json();
                        const range = quill.getSelection(true);
                        quill.insertEmbed(range.index, 'image', data.url);
                        quill.setSelection(range.index + 1);
                    } catch (err) {
                        alert('Gagal upload gambar.');
                    }
                };
            }

            document.getElementById('materialForm').addEventListener('submit', function() {
                document.getElementById('contentInput').value = quill.root.innerHTML;
            });
        </script>
    </x-slot:scripts>

</x-layouts.admin>
