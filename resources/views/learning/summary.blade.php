@extends('layouts.learning')

@php
    $firstActivity = $chapter->activities->sortBy('order')->first();
    $ringkasanMat = $chapter->lessonMaterials->where('type', 'ringkasan_materi')->first();
@endphp

@section('content')
    <h1 class="content-title">Ringkasan Materi</h1>
    @foreach($materials as $material)
        <div class="content-card">
            <div class="prose">{!! $material->content !!}</div>
        </div>
    @endforeach
@endsection

@section('nav_prev')
    @php
        $lastMat = $chapter->lessonMaterials->whereIn('type', ['pendahuluan', 'tujuan', 'prasyarat'])->sortByDesc('order')->first();
    @endphp
    @if($lastMat)
        <a href="{{ route('learning.material', [$chapter, $lastMat->type]) }}" class="nav-btn nav-btn-prev">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Sebelumnya
        </a>
    @else
        <div></div>
    @endif
@endsection

@section('nav_next')
    @if($firstActivity)
        <a href="{{ route('learning.activity', [$chapter, $firstActivity]) }}" class="nav-btn nav-btn-next"
            onclick="markComplete({{ $ringkasanMat->id ?? 0 }})">
            Mulai Predict
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    @else
        <div></div>
    @endif
@endsection

@push('scripts')
<script>
function markComplete(materialId) {
    if (!materialId) return;
    fetch('{{ route("learning.completeMaterial", $chapter) }}', {
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