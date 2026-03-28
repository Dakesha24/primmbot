<?php

namespace App\Services\AI\Prompts\Stages;

use App\Models\Activity;
use App\Models\Submission;
use App\Services\AI\Prompts\SystemPrompt;

class MakePrompt
{
    public function buildEvaluationPrompt(
        Activity   $activity,
        Submission $submission,
        array      $context,
        array      $actualOutput   = [],
        array      $expectedOutput = [],
    ): string {
        $question   = $activity->question_text;
        $answerCode = $submission->answer_code ?? '-';
        $answerText = $submission->answer_text ?? '-';
        $tables     = $context['tablesFormatted'];

        $actualJson   = mb_substr(json_encode($actualOutput,   JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), 0, 600);
        $expectedJson = mb_substr(json_encode($expectedOutput, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), 0, 600);

        return SystemPrompt::get() . "\n"
            . ($tables ? $tables . "\n" : '')
            . "Tahap: MAKE\n"
            . "Perintah soal: {$question}\n"
            . "Kode SQL buatan siswa (dari nol):\n{$answerCode}\n"
            . "Penjelasan siswa: {$answerText}\n"
            . "Output aktual query siswa:\n{$actualJson}\n"
            . "Output yang diharapkan:\n{$expectedJson}\n\n"
            . $this->getRubrik() . "\n\n"
            . "Nilai output: jika output siswa secara logika sesuai (kolom, data, jumlah baris), "
            . "tetap beri skor tinggi meski menggunakan cara yang sedikit berbeda.\n"
            . 'Balas HANYA JSON (tanpa teks lain): '
            . '{"keruntutan":0-100,"berargumen":0-100,"kesimpulan":0-100,'
            . '"feedback":"scaffolding 2-3 kalimat — pertanyaan pemantik mengarah ke indikator terendah, '
            . 'TANPA menyebut jawaban yang benar"}';
    }

    private function getRubrik(): string
    {
        return "Nilai tiga indikator Logical Thinking (0-100):\n"
            . "- keruntutan: apakah siswa membangun query secara urut dan logis "
            .   "(kebutuhan soal → pilih tabel → tentukan JOIN → tulis SELECT)?\n"
            . "- berargumen: apakah siswa menjelaskan MENGAPA memilih tabel dan "
            .   "kondisi JOIN tersebut dalam penjelasannya?\n"
            . "- kesimpulan: apakah siswa menyimpulkan apakah query yang dibuatnya "
            .   "sudah menjawab kebutuhan soal?";
    }
}
