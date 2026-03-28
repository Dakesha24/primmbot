<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }
        .page { padding: 28px 32px; }

        .doc-header { background: #1e3a5f; color: #fff; padding: 16px 20px; border-radius: 8px; margin-bottom: 14px; }
        .doc-header h1 { font-size: 14px; font-weight: 700; }
        .doc-header p  { font-size: 9px; color: #93c5fd; margin-top: 3px; }

        .identity-box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 14px; background: #f8fafc; }
        .section-title { font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
        .id-tbl { display: table; width: 100%; }
        .id-row { display: table-row; }
        .id-key { display: table-cell; font-size: 10px; color: #64748b; width: 160px; padding: 2px 0; }
        .id-sep { display: table-cell; font-size: 10px; color: #94a3b8; width: 14px; }
        .id-val { display: table-cell; font-size: 10px; font-weight: 600; color: #1e293b; }

        .stats-box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 14px; }
        .stat-cell { display: table-cell; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; width: 33%; }
        .stat-label { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; }
        .stat-value { font-size: 18px; font-weight: 700; color: #1e3a5f; margin-top: 2px; }
        .stat-unit  { font-size: 10px; color: #94a3b8; font-weight: 400; }

        .prog-box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 18px; }
        .prog-top { display: table; width: 100%; margin-bottom: 6px; }
        .prog-lbl { display: table-cell; font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; }
        .prog-pct { display: table-cell; text-align: right; font-size: 9px; font-weight: 700; color: #1e3a5f; }
        .prog-track { height: 8px; background: #e2e8f0; border-radius: 4px; }
        .prog-fill  { height: 8px; background: #2563eb; border-radius: 4px; }

        .chapter-block { margin-bottom: 18px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
        .chapter-hdr   { background: #1e3a5f; color: #fff; font-size: 11px; font-weight: 700; padding: 8px 14px; }
        .stage-hdr     { background: #e8eef7; color: #1e3a5f; font-size: 9px; font-weight: 700;
                         text-transform: uppercase; letter-spacing: 0.5px; padding: 5px 14px; border-bottom: 1px solid #dce8f5; }

        .act-item { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; }
        .act-item:last-child { border-bottom: none; }

        .act-meta { display: table; width: 100%; margin-bottom: 5px; }
        .act-meta-left  { display: table-cell; }
        .act-meta-right { display: table-cell; text-align: right; }

        .level-tag    { background: #f1f5f9; color: #64748b; font-size: 8px; padding: 1px 5px; border-radius: 3px; }
        .status-benar { color: #16a34a; font-weight: 700; font-size: 9px; }
        .status-salah { color: #dc2626; font-weight: 700; font-size: 9px; }
        .status-empty { color: #94a3b8; font-size: 9px; }
        .score-ai  { font-size: 11px; color: #64748b; }
        .score-guru { font-size: 12px; font-weight: 700; color: #1e3a5f; }

        .question { font-size: 10px; color: #334155; line-height: 1.55; margin-bottom: 6px; }

        .sub-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 5px; padding: 8px 10px; margin-top: 6px; }
        .sub-lbl  { font-size: 8px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 2px; }
        .sub-txt  { font-size: 10px; color: #334155; line-height: 1.5; }
        .sub-code { font-family: Courier New, monospace; font-size: 9px; color: #1d4ed8;
                    background: #eff6ff; padding: 5px 8px; border-radius: 4px; margin-top: 4px; line-height: 1.5; }

        .ai-box  { background: #f0f7ff; border-left: 3px solid #93c5fd; padding: 6px 9px; margin-top: 6px; border-radius: 0 4px 4px 0; }
        .ai-lbl  { font-size: 8px; font-weight: 700; color: #3b82f6; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 2px; }
        .ai-txt  { font-size: 10px; color: #334155; line-height: 1.5; }

        .guru-box { background: #f8fafc; border-left: 3px solid #0f1b3d; padding: 6px 9px; margin-top: 5px; border-radius: 0 4px 4px 0; }
        .guru-lbl { font-size: 8px; font-weight: 700; color: #0f1b3d; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 2px; }
        .guru-txt { font-size: 10px; color: #1e293b; line-height: 1.5; }

        .footer { text-align: center; font-size: 8px; color: #94a3b8; padding: 10px 0 0; border-top: 1px solid #e2e8f0; margin-top: 6px; }
    </style>
</head>
<body>
<div class="page">

<div class="doc-header">
    <h1>Laporan Hasil Belajar — {{ $course->title }}</h1>
    <p>PRIMMBOT · Dicetak oleh Guru · {{ $identity['tgl_cetak'] }}</p>
</div>

<div class="identity-box">
    <div class="section-title">Identitas Siswa</div>
    <div class="id-tbl">
        <div class="id-row"><span class="id-key">Nama</span><span class="id-sep">:</span><span class="id-val">{{ $identity['nama'] }}</span></div>
        <div class="id-row"><span class="id-key">NIS</span><span class="id-sep">:</span><span class="id-val">{{ $identity['nis'] }}</span></div>
        <div class="id-row"><span class="id-key">Kelas</span><span class="id-sep">:</span><span class="id-val">{{ $identity['kelas'] }}</span></div>
        <div class="id-row"><span class="id-key">Sekolah</span><span class="id-sep">:</span><span class="id-val">{{ $identity['sekolah'] }}</span></div>
        <div class="id-row"><span class="id-key">Terakhir Mengerjakan</span><span class="id-sep">:</span><span class="id-val">{{ $identity['tgl_terakhir'] }}</span></div>
    </div>
</div>

@php
    $total     = $stats['total'];
    $completed = $stats['completed'];
    $percent   = $stats['percent'];
    $avgScore  = $stats['avg_score'];
@endphp

<div class="stats-box">
    <div class="section-title">Ringkasan</div>
    <table style="width:100%;border-collapse:separate;border-spacing:8px 0;">
        <tr>
            <td class="stat-cell">
                <div class="stat-label">Progress</div>
                <div class="stat-value">{{ $percent }}<span class="stat-unit">%</span></div>
            </td>
            <td class="stat-cell">
                <div class="stat-label">Soal Selesai</div>
                <div class="stat-value">{{ $completed }}<span class="stat-unit">/{{ $total }}</span></div>
            </td>
            @if ($avgScore !== null)
            <td class="stat-cell">
                <div class="stat-label">Rata-rata Skor</div>
                <div class="stat-value">{{ $avgScore }}<span class="stat-unit">/100</span></div>
            </td>
            @endif
        </tr>
    </table>
</div>

<div class="prog-box">
    <div class="prog-top">
        <span class="prog-lbl">Progress Keseluruhan</span>
        <span class="prog-pct">{{ $completed }}/{{ $total }} soal · {{ $percent }}%</span>
    </div>
    <div class="prog-track"><div class="prog-fill" style="width:{{ $percent }}%;"></div></div>
</div>

@php $stageOrder = ['predict','run','investigate','modify','make']; @endphp
@foreach ($course->chapters as $chapter)
    <div class="chapter-block">
        <div class="chapter-hdr">{{ $chapter->title }}</div>

        @php $byStage = $chapter->activities->groupBy('stage'); @endphp

        @foreach ($stageOrder as $stageName)
            @if ($byStage->has($stageName))
                <div class="stage-hdr">{{ ucfirst($stageName) }}</div>

                @foreach ($byStage[$stageName] as $activity)
                    @php
                        $sub    = $activity->student_submission;
                        $review = $sub?->teacherReview;
                    @endphp
                    <div class="act-item">
                        <div class="act-meta">
                            <div class="act-meta-left">
                                @if ($activity->level)<span class="level-tag">{{ $activity->level }}</span> @endif
                                @if ($sub && $sub->is_correct)
                                    <span class="status-benar">✓ Benar</span>
                                @elseif ($sub)
                                    <span class="status-salah">✗ Belum Benar</span>
                                @else
                                    <span class="status-empty">Belum Dikerjakan</span>
                                @endif
                            </div>
                            @if ($sub && $sub->score !== null)
                            <div class="act-meta-right">
                                <span class="score-ai">Skor AI: {{ $sub->score }}</span>
                                @if ($review && $review->score !== null)
                                    &nbsp;<span class="score-guru">Guru: {{ $review->score }}/100</span>
                                @else
                                    <span class="score-guru">/100</span>
                                @endif
                            </div>
                            @endif
                        </div>

                        <div class="question">{{ Str::limit(strip_tags($activity->question_text), 220) }}</div>

                        @if ($sub)
                            <div class="sub-box">
                                @if ($sub->answer_text)
                                    <div class="sub-lbl">Jawaban</div>
                                    <div class="sub-txt">{{ $sub->answer_text }}</div>
                                @endif
                                @if ($sub->answer_code)
                                    <div class="sub-lbl" style="margin-top:5px;">Kode SQL</div>
                                    <div class="sub-code">{{ $sub->answer_code }}</div>
                                @endif
                            </div>
                            @if ($sub->ai_feedback)
                                <div class="ai-box">
                                    <div class="ai-lbl">Feedback AI</div>
                                    <div class="ai-txt">{{ $sub->ai_feedback }}</div>
                                </div>
                            @endif
                            @if ($review && $review->feedback)
                                <div class="guru-box">
                                    <div class="guru-lbl">Koreksi Guru</div>
                                    <div class="guru-txt">{{ $review->feedback }}</div>
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            @endif
        @endforeach
    </div>
@endforeach

<div class="footer">PRIMMBOT — Platform e-LKPD Basis Data · {{ $identity['sekolah'] }}</div>

</div>
</body>
</html>
