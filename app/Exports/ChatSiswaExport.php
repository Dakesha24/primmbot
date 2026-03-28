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

class ChatSiswaExport implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    private array $stageOrder = ['predict', 'run', 'investigate', 'modify', 'make'];

    public function __construct(
        private Course     $course,
        private Collection $chatLogs,   // grouped by activity_id
        private array      $identity = [],
    ) {}

    public function title(): string
    {
        return substr('Chat ' . $this->identity['nama'], 0, 31);
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = ['RIWAYAT PERCAKAPAN SISWA — ' . strtoupper($this->course->title)];
        $rows[] = [];

        $rows[] = ['IDENTITAS SISWA'];
        $rows[] = ['Nama',              $this->identity['nama']      ?? '-'];
        $rows[] = ['NIS',               $this->identity['nis']       ?? '-'];
        $rows[] = ['Kelas',             $this->identity['kelas']     ?? '-'];
        $rows[] = ['Dicetak oleh Guru', $this->identity['tgl_cetak'] ?? '-'];
        $rows[] = [];

        $rows[] = ['Bab', 'Tahap', 'No. Soal', 'Waktu', 'Peran', 'Pesan'];

        $allActivities = $this->course->chapters->flatMap(fn($ch) => $ch->activities);
        $activityMap   = $allActivities->keyBy('id');

        foreach ($this->course->chapters as $chapterIdx => $chapter) {
            $byStage = $chapter->activities->groupBy('stage');

            foreach ($this->stageOrder as $stage) {
                if (!$byStage->has($stage)) continue;

                foreach ($byStage[$stage]->values() as $soalIdx => $activity) {
                    $logs = $this->chatLogs->get($activity->id, collect());

                    if ($logs->isEmpty()) continue;

                    foreach ($logs as $log) {
                        $rows[] = [
                            $chapter->title,
                            ucfirst($stage),
                            'Soal ' . ($soalIdx + 1),
                            $log->created_at->format('d/m/Y H:i'),
                            'Siswa',
                            $log->prompt_sent,
                        ];
                        $rows[] = [
                            $chapter->title,
                            ucfirst($stage),
                            'Soal ' . ($soalIdx + 1),
                            $log->created_at->format('d/m/Y H:i'),
                            'AI Bot',
                            $log->response_received,
                        ];
                    }
                }
            }
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = 'F';

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        foreach ($sheet->getRowIterator() as $row) {
            $idx   = $row->getRowIndex();
            $value = $sheet->getCell('A' . $idx)->getValue();

            if ($value === 'IDENTITAS SISWA') {
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

            // Warna baris AI Bot berbeda
            $role = $sheet->getCell('E' . $idx)->getValue();
            if ($role === 'AI Bot') {
                $sheet->getStyle("A{$idx}:{$lastCol}{$idx}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F4FF']],
                ]);
            }
        }

        $sheet->getStyle('F:F')->getAlignment()->setWrapText(true);
        $sheet->getColumnDimension('F')->setWidth(70);
        $sheet->getColumnDimension('D')->setWidth(16);

        return [];
    }
}
