<?php

namespace App\Services\AI\Prompts\Stages;

use App\Models\Activity;
use App\Models\Submission;
use App\Services\AI\Prompts\SystemPrompt;

class RunPrompt
{
    public function buildEvaluationPrompt(
        Activity   $activity,
        Submission $submission,
        array      $context,
        ?string    $predictAnswer = null,
    ): string {
        $question   = $activity->question_text;
        $code       = $activity->code_snippet ?? '-';
        $answerText = $submission->answer_text ?? '-';
        $materials  = $context['materialsFormatted'];
        $tables     = $context['tablesFormatted'];

        $predictBlock = $predictAnswer
            ? "Jawaban Predict siswa sebelumnya: {$predictAnswer}\n"
            : '';

        return SystemPrompt::get() . "\n"
            . ($materials ? $materials . "\n" : '')
            . ($tables    ? $tables    . "\n" : '')
            . "Tahap: RUN\n"
            . "Query SQL yang dijalankan:\n{$code}\n"
            . $predictBlock
            . "Pertanyaan refleksi: {$question}\n"
            . "Jawaban refleksi siswa: {$answerText}\n\n"
            . $this->getRubrik() . "\n\n"
            . 'Balas HANYA JSON (tanpa teks lain): '
            . '{"keruntutan":0-100,"berargumen":0-100,"kesimpulan":0-100,'
            . '"feedback":"scaffolding 2-3 kalimat — pertanyaan pemantik mengarah ke indikator terendah, '
            . 'TANPA menyebut jawaban yang benar"}';
    }

    private function getRubrik(): string
    {
        return "Nilai tiga indikator Logical Thinking (0-100):\n"
            . "- keruntutan: apakah refleksi urut dari prediksi awal → hasil nyata yang dilihat "
            .   "→ penjelasan perbedaan atau kesamaannya?\n"
            . "- berargumen: apakah siswa menjelaskan MENGAPA prediksinya berbeda atau sama "
            .   "dengan output yang sebenarnya?\n"
            . "- kesimpulan: apakah siswa menyimpulkan apa yang dipelajari dari "
            .   "perbandingan prediksi vs hasil nyata ini?";
    }
}
