@extends('layouts.learning')

@php
    $matLabels = [
        'pendahuluan' => 'Pendahuluan Kelas',
        'petunjuk_belajar' => 'Petunjuk Belajar',
        'tujuan' => 'Tujuan Pembelajaran',
        'prasyarat' => 'Prasyarat Tools',
    ];
    $title = $matLabels[$material->type] ?? ucfirst($material->type);

    $allMaterials = $chapter->lessonMaterials
        ->whereIn('type', ['pendahuluan', 'petunjuk_belajar', 'tujuan', 'prasyarat'])
        ->sortBy('order');
    $materialKeys = $allMaterials->pluck('type')->values();
    $currentIndex = $materialKeys->search($material->type);
    $prevType = $currentIndex > 0 ? $materialKeys[$currentIndex - 1] : null;
    $nextType = $currentIndex < $materialKeys->count() - 1 ? $materialKeys[$currentIndex + 1] : null;
    $isLast = $material->type === 'prasyarat';
@endphp

@section('content')
    <style>
        .main-inner {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            height: calc(100vh - 56px);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .mat-content {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
            min-height: 0;
        }
        .mat-content-inner {
            max-width: 1100px;
            margin: 0 auto;
        }
        .mat-content::-webkit-scrollbar { width: 7px; }
        .mat-content::-webkit-scrollbar-track { background: rgba(255,255,255,0.03); }
        .mat-content::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.18); border-radius: 6px; }
        .mat-content::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.32); }

        .mat-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.6rem 2rem;
            border-top: 1px solid rgba(255,255,255,0.07);
            background: rgba(10,22,40,0.5);
            backdrop-filter: blur(8px);
            flex-shrink: 0;
        }
        .mat-nav .nav-btn {
            padding: 0.45rem 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 8px;
        }
        .mat-nav .nav-btn-prev {
            color: #94a3b8;
            border: 1px solid rgba(255,255,255,0.18);
            background: transparent;
        }
        .mat-nav .nav-btn-prev:hover {
            color: #fff;
            border-color: rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.05);
        }
        .mat-nav .nav-btn-next {
            color: #cbd5e1;
            background: rgba(100,116,139,0.25);
            border: 1px solid rgba(148,163,184,0.3);
            box-shadow: none;
        }
        .mat-nav .nav-btn-next:hover {
            color: #fff;
            background: rgba(100,116,139,0.35);
        }
    </style>

    <div class="mat-content">
        <div class="mat-content-inner">
            <h1 class="content-title">{{ $title }}</h1>
            <div class="content-card">
                <div class="prose">{!! $material->content !!}</div>
            </div>
        </div>
    </div>

    <div class="mat-nav">
        @if ($prevType)
            <a href="{{ route('learning.material', [$chapter, $prevType]) }}" class="nav-btn nav-btn-prev">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Sebelumnya
            </a>
        @else
            <div></div>
        @endif

        @if ($nextType)
            <a href="{{ route('learning.material', [$chapter, $nextType]) }}" class="nav-btn nav-btn-next"
                onclick="markComplete({{ $material->id }})">
                Selanjutnya
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @elseif($isLast)
            <a href="{{ route('learning.summary', $chapter) }}" class="nav-btn nav-btn-next"
                onclick="markComplete({{ $material->id }})">
                Ringkasan Materi
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @else
            <div></div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function markComplete(materialId) {
            fetch('{{ route('learning.completeMaterial', $chapter) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ lesson_material_id: materialId }),
            });
        }
    </script>
@endpush
