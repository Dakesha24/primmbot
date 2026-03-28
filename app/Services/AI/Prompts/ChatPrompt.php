<?php

namespace App\Services\AI\Prompts;

use App\Models\Activity;
use App\Models\Submission;

class ChatPrompt
{
    public function build(
        Activity    $activity,
        string      $message,
        array       $history,
        array       $context,
        ?Submission $latestSubmission = null,
    ): string {
        $stage    = ucfirst($activity->stage);
        $level    = $activity->level ? " — Level {$activity->level}" : '';
        $question = $activity->question_text;
        $code     = $activity->code_snippet ?? '-';
        $materials = $context['materialsFormatted'];
        $tables    = $context['tablesFormatted'];

        $historyText = '';
        foreach ($history as $h) {
            $role         = $h['role'] === 'user' ? 'Siswa' : 'PRIMM Bot';
            $historyText .= "{$role}: {$h['message']}\n";
        }

        $submissionContext = '';
        if ($latestSubmission) {
            $submissionContext = "Riwayat submit terakhir siswa: "
                . "percobaan ke-{$latestSubmission->attempt}, "
                . "skor {$latestSubmission->score}/100"
                . ($latestSubmission->is_correct ? ' (sudah lulus)' : ' (belum lulus KKM)')
                . ".\n";
        }

        $isCasual = $this->isCasualMessage($message);

        $contextBlock = $isCasual ? '' :
            ($materials ? $materials . "\n" : '')
            . ($tables   ? $tables   . "\n" : '')
            . "Konteks aktivitas — Tahap: {$stage}{$level}. Soal: {$question}. SQL: {$code}\n"
            . $submissionContext;

        return SystemPrompt::get() . "\n"
            . $contextBlock
            . ($historyText ? "Riwayat percakapan:\n{$historyText}" : '')
            . "Siswa: {$message}\n"
            . 'Jawab sebagai PRIMM Bot, maksimal 3 kalimat:';
    }

    private function isCasualMessage(string $message): bool
    {
        $msg     = mb_strtolower(trim($message));
        $casual  = ['halo', 'hai', 'hi', 'hello', 'hey', 'siapa kamu', 'siapa namamu',
                    'nama kamu', 'namamu', 'perkenalan', 'tes', 'test', 'coba', 'hei', 'selamat'];

        foreach ($casual as $pattern) {
            if (str_contains($msg, $pattern)) return true;
        }

        return mb_strlen($msg) <= 5;
    }
}
