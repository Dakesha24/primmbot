<?php

namespace App\Services\AI\Prompts\Stages;

use App\Models\Activity;
use App\Models\Submission;
use App\Services\AI\Prompts\SystemPrompt;

class PredictPrompt
{
    public function buildEvaluationPrompt(
        Activity   $activity,
        Submission $submission,
        array      $context,
    ): string {
        $question   = $activity->question_text;
        $code       = $activity->code_snippet ?? '-';
        $answerText = $submission->answer_text ?? '-';
        $materials  = $context['materialsFormatted'];
        $tables     = $context['tablesFormatted'];

        return SystemPrompt::get() . "\n"
            . ($materials ? $materials . "\n" : '')
            . ($tables    ? $tables    . "\n" : '')
            . "Tahap: PREDICT\n"
            . "Query SQL yang diberikan:\n{$code}\n"
            . "Pertanyaan: {$question}\n"
            . "Jawaban siswa: {$answerText}\n\n"
            . $this->getRubrik() . "\n\n"
            . 'Balas HANYA JSON (tanpa teks lain): '
            . '{"keruntutan":0-100,"berargumen":0-100,"kesimpulan":0-100,'
            . '"feedback":"scaffolding 2-3 kalimat — pertanyaan pemantik mengarah ke indikator terendah, '
            . 'TANPA menyebut jawaban yang benar"}';
    }

    private function getRubrik(): string
    {
        return "Nilai tiga indikator Logical Thinking (0-100):\n"
            . "- keruntutan: apakah penjelasan urut dari struktur query "
            .   "(SELECT → tabel di-JOIN → kondisi ON → output yang terbentuk)?\n"
            . "- berargumen: apakah siswa menjelaskan MENGAPA output akan seperti itu, "
            .   "bukan sekadar menyebutkan apa outputnya?\n"
            . "- kesimpulan: apakah siswa menarik kesimpulan tentang output yang akan "
            .   "dihasilkan berdasarkan analisis query-nya?";
    }
}
