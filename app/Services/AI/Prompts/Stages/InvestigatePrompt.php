<?php

namespace App\Services\AI\Prompts\Stages;

use App\Models\Activity;
use App\Models\Submission;
use App\Services\AI\Prompts\SystemPrompt;

class InvestigatePrompt
{
    public function buildEvaluationPrompt(
        Activity   $activity,
        Submission $submission,
        array      $context,
    ): string {
        $question   = $activity->question_text;
        $code       = $activity->code_snippet ?? '-';
        $level      = $activity->level ?? '-';
        $answerText = $submission->answer_text ?? '-';
        $materials  = $context['materialsFormatted'];
        $tables     = $context['tablesFormatted'];

        $refBlock = $activity->reference_answer
            ? "Contoh jawaban ideal (gunakan sebagai acuan kualitas berpikir, bukan kunci jawaban):\n{$activity->reference_answer}\n\n"
            : '';

        return SystemPrompt::get() . "\n"
            . ($materials ? $materials . "\n" : '')
            . ($tables    ? $tables    . "\n" : '')
            . "Tahap: INVESTIGATE — Level: {$level} ({$this->getLevelFocus($level)})\n"
            . "Query SQL yang dianalisis:\n{$code}\n"
            . "Pertanyaan analisis: {$question}\n"
            . "Jawaban analisis siswa: {$answerText}\n\n"
            . $refBlock
            . $this->getRubrik($level) . "\n\n"
            . 'Balas HANYA JSON (tanpa teks lain): '
            . '{"keruntutan":0-100,"berargumen":0-100,"kesimpulan":0-100,'
            . '"feedback":"scaffolding 2-3 kalimat — pertanyaan pemantik mengarah ke indikator terendah, '
            . 'TANPA menyebut jawaban yang benar"}';
    }

    private function getRubrik(string $level): string
    {
        $fokus = $this->getLevelFocus($level);

        return "Nilai tiga indikator Logical Thinking (0-100) — fokus level {$level}: {$fokus}\n"
            . "- keruntutan: apakah analisis urut dan sistematis sesuai fokus level ini? "
            .   "Apakah siswa mengurai elemen-elemen secara terstruktur?\n"
            . "- berargumen: apakah siswa menjelaskan FUNGSI atau ALASAN dari setiap "
            .   "elemen yang dianalisis, bukan sekadar menyebutkan namanya?\n"
            . "- kesimpulan: apakah siswa menarik kesimpulan dari analisisnya, "
            .   "misalnya tentang fungsi, relasi, atau konteks penggunaan query ini?";
    }

    private function getLevelFocus(string $level): string
    {
        return match ($level) {
            'atoms'     => 'elemen terkecil query: karakter, tanda baca, kata kunci individual (SELECT, FROM, JOIN, ON, titik pemisah tabel.kolom)',
            'blocks'    => 'baris/klausa query: fungsi setiap klausa, urutan penulisan, kondisi ON dalam JOIN',
            'relations' => 'relasi antar tabel: Primary Key, Foreign Key, cara tabel dihubungkan melalui JOIN',
            'macro'     => 'konteks keseluruhan: kapan dan mengapa query jenis ini digunakan di dunia nyata',
            default     => 'analisis query SQL secara umum',
        };
    }
}
