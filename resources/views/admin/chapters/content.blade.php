<x-layouts.admin title="Kelola Kegiatan">

    <x-slot:styles>
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

            .chapter-banner {
                background: #fff;
                border: 1px solid #e4e8f1;
                border-radius: 14px;
                padding: 20px 24px;
                margin-bottom: 32px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .chapter-banner h2 {
                font-size: 16px;
                font-weight: 700;
                color: #0f1b3d;
                margin-bottom: 4px;
            }

            .chapter-banner p {
                font-size: 13px;
                color: #94a3b8;
            }

            .section-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 18px;
            }

            .section-header h3 {
                font-size: 15px;
                font-weight: 700;
                color: #0f1b3d;
            }

            .section-header .count {
                font-size: 12px;
                font-weight: 600;
                color: #94a3b8;
                background: #f0f2f7;
                padding: 3px 10px;
                border-radius: 6px;
                margin-left: 10px;
            }

            .btn-add-sm {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: #0f1b3d;
                color: #fff;
                padding: 8px 18px;
                border-radius: 8px;
                font-size: 12px;
                font-weight: 700;
                border: none;
                cursor: pointer;
                font-family: inherit;
                text-decoration: none;
                transition: all 0.15s;
            }

            .btn-add-sm:hover {
                background: #1a2d5a;
            }

            .content-section {
                margin-bottom: 40px;
            }

            /* Item Row */
            .item-list {
                background: #fff;
                border: 1px solid #e4e8f1;
                border-radius: 14px;
                overflow: hidden;
            }

            .item-row {
                display: flex;
                align-items: center;
                padding: 16px 20px;
                gap: 14px;
                transition: background 0.1s;
            }

            .item-row:not(:last-child) {
                border-bottom: 1px solid #f0f2f7;
            }

            .item-row:hover {
                background: #fafbfd;
            }

            .item-order {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                background: #f0f2f7;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 700;
                color: #64748b;
                flex-shrink: 0;
            }

            .item-info {
                flex: 1;
                min-width: 0;
            }

            .item-info .item-title {
                font-size: 14px;
                font-weight: 600;
                color: #1e293b;
                margin-bottom: 3px;
            }

            .item-info .item-sub {
                font-size: 12px;
                color: #94a3b8;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .item-badge {
                font-size: 11px;
                font-weight: 700;
                padding: 4px 10px;
                border-radius: 6px;
                flex-shrink: 0;
            }

            .badge-material {
                background: #eef2ff;
                color: #3b5bdb;
            }

            .badge-predict {
                background: #fef3c7;
                color: #92400e;
            }

            .badge-run {
                background: #d1fae5;
                color: #065f46;
            }

            .badge-investigate {
                background: #dbeafe;
                color: #1e40af;
            }

            .badge-modified {
                background: #f3e8ff;
                color: #6b21a8;
            }

            .badge-make {
                background: #ffe4e6;
                color: #9f1239;
            }

            .item-actions {
                display: flex;
                gap: 6px;
                flex-shrink: 0;
            }

            .item-actions a,
            .item-actions button {
                padding: 6px 14px;
                border-radius: 7px;
                font-size: 12px;
                font-weight: 600;
                font-family: inherit;
                cursor: pointer;
                border: none;
                background: none;
                text-decoration: none;
                transition: all 0.15s;
            }

            .act-edit {
                color: #475569;
                background: #f1f5f9;
            }

            .act-edit:hover {
                background: #e2e8f0;
            }

            .act-delete {
                color: #ef4444;
            }

            .act-delete:hover {
                background: #fef2f2;
            }

            .empty-row {
                padding: 40px 20px;
                text-align: center;
                color: #94a3b8;
                font-size: 13px;
            }

            .divider {
                border: none;
                border-top: 2px dashed #e8ecf3;
                margin: 8px 0 32px;
            }
        </style>
    </x-slot:styles>

    <div class="breadcrumb">
        <a href="{{ route('admin.courses.index') }}">Kelola Kelas</a>
        <span>›</span>
        <a href="{{ route('admin.chapters.index', $course) }}">{{ $course->title }}</a>
        <span>›</span>
        <span>{{ $chapter->title }}</span>
    </div>

    <div class="chapter-banner">
        <div>
            <h2>{{ $chapter->title }}</h2>
            <p>{{ $chapter->description ?? 'Tidak ada deskripsi.' }}</p>
        </div>
    </div>

    <!-- Materi Pendahuluan -->
    <div class="content-section">
        <div class="section-header">
            <h3>Materi Pendahuluan <span class="count">{{ $materials->count() }}</span></h3>
            <a href="{{ route('admin.materials.create', [$course, $chapter]) }}" class="btn-add-sm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Materi
            </a>
        </div>

        <div class="item-list">
            @forelse($materials as $mat)
                <div class="item-row">
                    <div class="item-order">{{ $mat->order }}</div>
                    <div class="item-info">
                        <div class="item-title">{{ ucwords(str_replace('_', ' ', $mat->type)) }}</div>
                        <div class="item-sub">{{ Str::limit(strip_tags($mat->content), 80) }}</div>
                    </div>
                    <span class="item-badge badge-material">Materi</span>
                    <div class="item-actions">
                        <a href="{{ route('admin.materials.edit', [$course, $chapter, $mat]) }}"
                            class="act-edit">Edit</a>
                        <form method="POST" action="{{ route('admin.materials.destroy', [$course, $chapter, $mat]) }}"
                            onsubmit="return confirm('Hapus materi ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="act-delete">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-row">Belum ada materi pendahuluan.</div>
            @endforelse
        </div>
    </div>

    <hr class="divider">

    <!-- Kegiatan Inti (PRIMM) -->
    <div class="content-section">
        <div class="section-header">
            <h3>Kegiatan Inti — PRIMM <span class="count">{{ $activities->count() }}</span></h3>
            <a href="{{ route('admin.activities.create', [$course, $chapter]) }}" class="btn-add-sm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Aktivitas
            </a>
        </div>

        <div class="item-list">
            @forelse($activities as $act)
                <div class="item-row">
                    <div class="item-order">{{ $act->order }}</div>
                    <div class="item-info">
                        <div class="item-title">{{ Str::limit($act->question_text, 60) }}</div>
                        <div class="item-sub">
                            {{ ucfirst($act->stage) }}
                            @if ($act->level)
                                · Level: {{ ucfirst($act->level) }}
                            @endif
                            @if ($act->sandboxDatabase)
                                · DB: {{ $act->sandboxDatabase->name }}
                            @endif
                        </div>
                    </div>
                    <span class="item-badge badge-{{ $act->stage }}">{{ ucfirst($act->stage) }}</span>
                    <div class="item-actions">
                        <a href="{{ route('admin.activities.edit', [$course, $chapter, $act]) }}"
                            class="act-edit">Edit</a>
                        <form method="POST"
                            action="{{ route('admin.activities.destroy', [$course, $chapter, $act]) }}"
                            onsubmit="return confirm('Hapus aktivitas ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="act-delete">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-row">Belum ada aktivitas PRIMM.</div>
            @endforelse
        </div>
    </div>

</x-layouts.admin>
