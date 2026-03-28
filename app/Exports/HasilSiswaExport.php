<?php

namespace App\Exports;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class HasilSiswaExport implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    private array $stageOrder = ['predict', 'run', 'investigate', 'modify', 'make'];

    public function __construct(
        private Course     $course,
        private Collection $submissions,
        private array      $identity = [],
    ) {}

    public function title(): string
    {
        return substr('Hasil ' . $this->identity['nama'], 0, 31);
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = ['LAPORAN HASIL BELAJAR SISWA — ' . strtoupper($this->course->title)];
        $rows[] = [];

        $rows[] = ['IDENTITAS SISWA'];
        $rows[] = ['Nama',                 $this->identity['nama']         ?? '-'];
        $rows[] = ['NIS',                  $this->identity['nis']          ?? '-'];
        $rows[] = ['Kelas',                $this->identity['kelas']        ?? '-'];
        $rows[] = ['Sekolah',              $this->identity['sekolah']      ?? '-'];
        $rows[] = ['Terakhir Mengerjakan', $this->identity['tgl_terakhir'] ?? '-'];
        $rows[] = ['Dicetak oleh Guru',    $this->identity['tgl_cetak']    ?? '-'];
        $rows[] = [];

        $allActivities = $this->course->chapters->flatMap(fn($ch) => $ch->activities);
        $total         = $allActivities->count();
        $correctIds    = $this->submissions->where('is_correct', true)->pluck('activity_id');
        $completed     = $correctIds->count();
        $percent       = $total > 0 ? round($completed / $total * 100) : 0;
        $avgScore      = $this->submissions->whereNotNull('score')->avg('score');

        $rows[] = ['RINGKASAN'];
        $rows[] = ['Progress',       "{$percent}%"];
        $rows[] = ['Soal Selesai',   "{$completed}/{$total}"];
        if ($avgScore !== null) {
            $rows[] = ['Rata-rata Skor', round($avgScore) . '/100'];
        }
        $rows[] = [];

        $rows[] = ['DETAIL HASIL PER AKTIVITAS'];
        $rows[] = [
            'Bab', 'Tahap', 'Level', 'Pertanyaan', 'Status',
            'Skor AI', 'Skor Guru', 'Jawaban', 'Kode SQL',
            'Feedback AI', 'Feedback Guru', 'Tanggal Submit',
        ];

        foreach ($this->course->chapters as $chapter) {
            $byStage = $chapter->activities->groupBy('stage');

            foreach ($this->stageOrder as $stage) {
                if (!$byStage->has($stage)) continue;

                foreach ($byStage[$stage] as $activity) {
                    $sub    = $this->submissions->get($activity->id);
                    $review = $sub?->teacherReview;

                    $rows[] = [
                        $chapter->title,
                        ucfirst($stage),
                        $activity->level ?? '-',
                        strip_tags($activity->question_text),
                        $sub ? ($sub->is_correct ? 'Benar' : 'Belum Benar') : 'Belum Dikerjakan',
                        $sub?->score          ?? '-',
                        $review?->score       ?? '-',
                        $sub?->answer_text    ?? '-',
                        $sub?->answer_code    ?? '-',
                        $sub?->ai_feedback    ?? '-',
                        $review?->feedback    ?? '-',
                        $sub ? $sub->updated_at->format('d/m/Y H:i') : '-',
                    ];
                }
            }
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = 'L';

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sectionLabels = ['IDENTITAS SISWA', 'RINGKASAN', 'DETAIL HASIL PER AKTIVITAS'];

        foreach ($sheet->getRowIterator() as $row) {
            $idx   = $row->getRowIndex();
            $value = $sheet->getCell('A' . $idx)->getValue();

            if (in_array($value, $sectionLabels)) {
                $sheet->getStyle('A' . $idx)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1E3A5F']],
                ]);
            }

            if ($value === 'Bab') {
                $sheet->getStyle("A{$idx}:{$lastCol}{$idx}")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F1B3D']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }
        }

        foreach (['D', 'H', 'I', 'J', 'K'] as $col) {
            $sheet->getStyle("{$col}:{$col}")->getAlignment()->setWrapText(true);
        }

        $sheet->getColumnDimension('D')->setWidth(42);
        $sheet->getColumnDimension('H')->setWidth(35);
        $sheet->getColumnDimension('I')->setWidth(35);
        $sheet->getColumnDimension('J')->setWidth(42);
        $sheet->getColumnDimension('K')->setWidth(35);

        return [];
    }
}
