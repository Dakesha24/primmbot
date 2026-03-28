<?php

namespace App\Services\AI;

class ResponseParser
{
    public function parseEvaluation(string $response, int $kkm = 70): EvaluationResult
    {
        preg_match('/\{.*?\}/s', $response, $matches);

        if (!empty($matches[0])) {
            $data = json_decode($matches[0], true);

            if (
                $data &&
                isset($data['keruntutan'], $data['berargumen'], $data['kesimpulan'], $data['feedback'])
            ) {
                $keruntutan = (int) $data['keruntutan'];
                $berargumen = (int) $data['berargumen'];
                $kesimpulan = (int) $data['kesimpulan'];
                $total      = (int) round(($keruntutan + $berargumen + $kesimpulan) / 3);

                return new EvaluationResult(
                    keruntutan: $keruntutan,
                    berargumen: $berargumen,
                    kesimpulan: $kesimpulan,
                    total:      $total,
                    isCorrect:  $total >= $kkm,
                    feedback:   $data['feedback'],
                );
            }
        }

        return $this->fallback($kkm);
    }

    public function fallback(int $kkm = 70, ?string $answerText = null): EvaluationResult
    {
        $answer  = mb_strtolower($answerText ?? '');
        $tooShort = mb_strlen($answer) < 15;

        if ($tooShort) {
            return new EvaluationResult(
                keruntutan: 20,
                berargumen: 20,
                kesimpulan: 20,
                total:      20,
                isCorrect:  false,
                feedback:   'Jawaban terlalu singkat. Coba elaborasi lebih — jelaskan dengan urut, berikan alasan, dan tutup dengan kesimpulan.',
            );
        }

        $keywords = ['join', 'tabel', 'kolom', 'data', 'select', 'output', 'hasil', 'relasi', 'primary', 'foreign', 'gabung', 'karena', 'sehingga', 'jadi'];
        $matched  = count(array_filter($keywords, fn($k) => str_contains($answer, $k)));
        $score    = min(60 + ($matched * 4), 85);

        return new EvaluationResult(
            keruntutan: $score,
            berargumen: $score,
            kesimpulan: $score,
            total:      $score,
            isCorrect:  $score >= $kkm,
            feedback:   $score >= $kkm
                ? 'Jawabanmu sudah mengarah dengan baik! Coba perhatikan apakah sudah ada urutan berpikir, alasan, dan kesimpulan yang jelas.'
                : 'Coba elaborasi lebih lanjut — jelaskan secara urut, berikan alasan mengapa, lalu tutup dengan kesimpulan.',
        );
    }
}
