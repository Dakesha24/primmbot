<?php

namespace App\Services\AI\Prompts\Stages;

use App\Models\Activity;
use App\Models\Submission;
use App\Services\AI\Prompts\SystemPrompt;

class ModifyPrompt
{
    public function buildEvaluationPrompt(
        Activity   $activity,
        Submission $submission,
        array      $context,
        array      $actualOutput   = [],
        array      $expectedOutput = [],
    ): string {
        $question    = $activity->question_text;
        $defaultCode = $activity->editor_default_code ?? '-';
        $answerCode  = $submission->answer_code ?? '-';
        $answerText  = $submission->answer_text ?? '-';
        $tables      = $context['tablesFormatted'];

        $actualJson   = mb_substr(json_encode($actualOutput,   JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), 0, 600);
        $expectedJson = mb_substr(json_encode($expectedOutput, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), 0, 600);

        return SystemPrompt::get() . "\n"
            . ($tables ? $tables . "\n" : '')
            . "Tahap: MODIFY\n"
            . "Perintah soal: {$question}\n"
            . "Kode awal yang diberikan:\n{$defaultCode}\n"
            . "Kode modifikasi siswa:\n{$answerCode}\n"
            . "Penjelasan siswa: {$answerText}\n"
            . "Output aktual query siswa:\n{$actualJson}\n"
            . "Output yang diharapkan:\n{$expectedJson}\n\n"
            . $this->getRubrik() . "\n\n"
            . "Nilai output: jika output siswa secara logika sesuai (kolom, data, jumlah baris), "
            . "tetap beri skor tinggi meski format sedikit berbeda.\n"
            . 'Balas HANYA JSON (tanpa teks lain): '
            . '{"keruntutan":0-100,"berargumen":0-100,"kesimpulan":0-100,'
            . '"feedback":"scaffolding 2-3 kalimat — pertanyaan pemantik mengarah ke indikator terendah, '
            . 'TANPA menyebut jawaban yang benar"}';
    }

    private function getRubrik(): string
    {
        return "Nilai tiga indikator Logical Thinking (0-100):\n"
            . "- keruntutan: apakah siswa memahami kode asal → mengidentifikasi bagian yang "
            .   "perlu diubah → menerapkan perubahan secara logis dan urut?\n"
            . "- berargumen: apakah siswa menjelaskan MENGAPA memodifikasi bagian tersebut "
            .   "dalam penjelasannya?\n"
            . "- kesimpulan: apakah siswa menyimpulkan apakah modifikasinya sudah "
            .   "menjawab perintah soal, berdasarkan output yang dihasilkan?";
    }
}
