<?php

namespace App\Exports;

use App\Models\Course;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class HasilBelajarExport implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(
        private Course $course,
        private Collection $submissions,
        private array $identity = [],
        private array $stageOrder = ['predict', 'run', 'investigate', 'modify', 'make']
    ) {}

    public function title(): string
    {
        return substr($this->course->title, 0, 31);
    }

    public function array(): array
    {
        $rows = [];

        // ── Judul ──────────────────────────────────────────────
        $rows[] = ['LAPORAN HASIL BELAJAR — ' . strtoupper($this->course->title)];
        $rows[] = [];

        // ── Identitas ──────────────────────────────────────────
        $rows[] = ['IDENTITAS SISWA'];
        $rows[] = ['Nama',                $this->identity['nama'] ?? '-'];
        $rows[] = ['NIS',                 $this->identity['nis']  ?? '-'];
        $rows[] = ['Kelas',               $this->identity['kelas'] ?? '-'];
        $rows[] = ['Sekolah',             $this->identity['sekolah'] ?? '-'];
        $rows[] = ['Terakhir Mengerjakan',$this->identity['tgl_terakhir'] ?? '-'];
        $rows[] = ['Dicetak',             $this->identity['tgl_cetak'] ?? '-'];
        $rows[] = [];

        // ── Ringkasan ──────────────────────────────────────────
        $allActivities = $this->course->chapters->flatMap(fn($ch) => $ch->activities);
        $total         = $allActivities->count();
        $correctIds    = $this->submissions->where('is_correct', true)->pluck('activity_id');
        $completed     = $correctIds->count();
        $percent       = $total > 0 ? round($completed / $total * 100) : 0;
        $avgScore      = $this->submissions->whereNotNull('score')->avg('score');

        $rows[] = ['RINGKASAN'];
        $rows[] = ['Progress', "{$percent}%"];
        $rows[] = ['Soal Selesai', "{$completed}/{$total}"];
        if ($avgScore !== null) {
            $rows[] = ['Rata-rata Skor', round($avgScore) . '/100'];
        }
        $rows[] = [];

        // ── Detail per aktivitas ───────────────────────────────
        $rows[] = ['DETAIL HASIL PER AKTIVITAS'];
        $rows[] = ['Bab', 'Tahap', 'Level', 'Pertanyaan', 'Status', 'Skor', 'Jawaban', 'Kode SQL', 'Feedback AI', 'Tanggal'];

        foreach ($this->course->chapters as $chapter) {
            $byStage = $chapter->activities->groupBy('stage');

            foreach ($this->stageOrder as $stage) {
                if (!$byStage->has($stage)) continue;

                foreach ($byStage[$stage] as $activity) {
                    $sub = $this->submissions->get($activity->id);

                    $rows[] = [
                        $chapter->title,
                        ucfirst($stage),
                        $activity->level ?? '-',
                        strip_tags($activity->question_text),
                        $sub ? ($sub->is_correct ? 'Benar' : 'Belum Benar') : 'Belum Dikerjakan',
                        $sub?->score ?? '-',
                        $sub?->answer_text ?? '-',
                        $sub?->answer_code ?? '-',
                        $sub?->ai_feedback ?? '-',
                        $sub ? $sub->updated_at->format('d/m/Y H:i') : '-',
                    ];
                }
            }
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        // Judul utama (baris 1)
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Tandai baris section header dan baris header tabel
        $sectionLabels  = ['IDENTITAS SISWA', 'RINGKASAN', 'DETAIL HASIL PER AKTIVITAS'];
        $tableHeaderVal = 'Bab';

        foreach ($sheet->getRowIterator() as $row) {
            $idx   = $row->getRowIndex();
            $value = $sheet->getCell('A' . $idx)->getValue();

            if (in_array($value, $sectionLabels)) {
                $sheet->getStyle('A' . $idx)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1E3A5F']],
                ]);
            }

            if ($value === $tableHeaderVal) {
                $sheet->getStyle('A' . $idx . ':J' . $idx)->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }
        }

        // Wrap text untuk kolom pertanyaan (D) dan feedback (I)
        $sheet->getStyle('D:D')->getAlignment()->setWrapText(true);
        $sheet->getStyle('G:G')->getAlignment()->setWrapText(true);
        $sheet->getStyle('I:I')->getAlignment()->setWrapText(true);

        // Lebar kolom khusus
        $sheet->getColumnDimension('D')->setWidth(45);
        $sheet->getColumnDimension('G')->setWidth(35);
        $sheet->getColumnDimension('H')->setWidth(35);
        $sheet->getColumnDimension('I')->setWidth(45);

        return [];
    }
}
