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
    <h1 class="content-title">{{ $title }}</h1>
    <div class="content-card">
        <div class="prose">{!! $material->content !!}</div>
    </div>
@endsection

@section('nav_prev')
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
@endsection

@section('nav_next')
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
            Selanjutnya
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    @else
        <div></div>
    @endif
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
                body: JSON.stringify({
                    lesson_material_id: materialId
                }),
            });
        }
    </script>
@endpush
