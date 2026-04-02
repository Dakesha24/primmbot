<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;

/**
 * Mengurai respons JSON dari AI menjadi EvaluationResult.
 *
 * AI diharapkan merespons dalam format:
 * { "keruntutan": 80, "berargumen": 75, "kesimpulan": 70, "feedback": "..." }
 *
 * Jika parsing gagal (AI tidak merespons atau format salah), digunakan
 * fallback berbasis keyword agar siswa tetap mendapat feedback.
 */
class ResponseParser
{
    /**
     * Parse respons JSON dari AI.
     * Jika gagal, otomatis jatuh ke fallback() dengan log warning.
     */
    public function parseEvaluation(string $response, int $kkm = 70): EvaluationResult
    {
        // Cari blok JSON di dalam respons (AI kadang menambahkan teks di luar JSON)
        preg_match('/\{.*?\}/s', $response, $matches);

        if (!empty($matches[0])) {
            $data = json_decode($matches[0], true);

            if (
                $data &&
                isset($data['keruntutan'], $data['berargumen'], $data['kesimpulan'], $data['feedback'])
            ) {
                // Hitung skor total sebagai rata-rata tiga indikator logical thinking
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

        // JSON tidak ditemukan atau field yang diharapkan tidak lengkap
        // Catat ke log agar bisa dimonitor — bisa jadi AI mengubah format respons
        Log::warning('ResponseParser: JSON AI tidak valid, menggunakan fallback scoring', [
            'response_preview' => mb_substr($response, 0, 200),
            'kkm'              => $kkm,
        ]);

        return $this->fallback($kkm);
    }

    /**
     * Fallback scoring berbasis keyword saat AI tidak bisa digunakan.
     *
     * PENTING: Ini bukan evaluasi yang ideal — hanya pengganti darurat.
     * Jika sering aktif, periksa log untuk mengetahui penyebabnya.
     *
     * Skor dihitung dari jumlah kata kunci relevan yang ditemukan dalam jawaban.
     */
    public function fallback(int $kkm = 70, ?string $answerText = null): EvaluationResult
    {
        $answer   = mb_strtolower($answerText ?? '');
        $tooShort = mb_strlen($answer) < config('ai.min_answer_length_fallback', 15);

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

        // Keyword relevan materi SQL JOIN — setiap kata yang cocok menambah skor
        $keywords = ['join', 'tabel', 'kolom', 'data', 'select', 'output', 'hasil', 'relasi',
                     'primary', 'foreign', 'gabung', 'karena', 'sehingga', 'jadi'];
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
