<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    // Tombol CEK — Simpan draft + kembalikan feedback AI
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'activity_id' => ['required', 'exists:activities,id'],
            'answer_text' => ['nullable', 'string'],
            'answer_code' => ['nullable', 'string'],
        ]);

        $activity = Activity::findOrFail($request->activity_id);

        // Validasi: minimal salah satu jawaban harus diisi
        if (empty($request->answer_text) && empty($request->answer_code)) {
            return response()->json([
                'success' => false,
                'error' => 'Jawaban tidak boleh kosong.',
            ], 422);
        }

        // Simpan atau update draft submission
        $submission = Submission::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'activity_id' => $activity->id,
            ],
            [
                'answer_text' => $request->answer_text,
                'answer_code' => $request->answer_code,
                'is_correct' => false,
            ]
        );

        // Generate feedback berdasarkan tahap
        $feedback = $this->generateFeedback($activity, $submission);

        // Simpan feedback
        $submission->update(['ai_feedback' => $feedback]);

        return response()->json([
            'success' => true,
            'feedback' => $feedback,
            'submission_id' => $submission->id,
        ]);
    }

    // Tombol SUBMIT — Validasi + simpan jawaban final
    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'activity_id' => ['required', 'exists:activities,id'],
            'answer_text' => ['nullable', 'string'],
            'answer_code' => ['nullable', 'string'],
        ]);

        $activity = Activity::findOrFail($request->activity_id);

        if (empty($request->answer_text) && empty($request->answer_code)) {
            return response()->json([
                'success' => false,
                'error' => 'Jawaban tidak boleh kosong.',
            ], 422);
        }

        // Cek apakah sudah pernah submit final
        $existing = Submission::where('user_id', Auth::id())
            ->where('activity_id', $activity->id)
            ->where('is_correct', true)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'error' => 'Kamu sudah menyelesaikan aktivitas ini.',
            ], 422);
        }

        // Evaluasi jawaban
        $evaluation = $this->evaluateAnswer($activity, $request->answer_text, $request->answer_code);

        // Simpan submission
        $submission = Submission::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'activity_id' => $activity->id,
            ],
            [
                'answer_text' => $request->answer_text,
                'answer_code' => $request->answer_code,
                'is_correct' => $evaluation['is_correct'],
                'score' => $evaluation['score'],
                'ai_feedback' => $evaluation['feedback'],
            ]
        );

        return response()->json([
            'success' => true,
            'is_correct' => $evaluation['is_correct'],
            'score' => $evaluation['score'],
            'feedback' => $evaluation['feedback'],
            'submission_id' => $submission->id,
        ]);
    }

    // Cek progress: apakah stage sebelumnya sudah selesai
    public function checkProgress(Request $request): JsonResponse
    {
        $request->validate([
            'activity_id' => ['required', 'exists:activities,id'],
        ]);

        $activity = Activity::findOrFail($request->activity_id);
        $chapter = $activity->chapter;

        // Definisi urutan stage
        $stageOrder = ['predict', 'run', 'investigate', 'modified', 'make'];
        $currentStageIndex = array_search($activity->stage, $stageOrder);

        // Cek apakah semua stage sebelumnya sudah selesai
        $locked = false;
        $message = '';

        if ($currentStageIndex > 0) {
            for ($i = 0; $i < $currentStageIndex; $i++) {
                $prevStageActivities = $chapter->activities()
                    ->where('stage', $stageOrder[$i])
                    ->pluck('id');

                $completedCount = Submission::where('user_id', Auth::id())
                    ->whereIn('activity_id', $prevStageActivities)
                    ->where('is_correct', true)
                    ->distinct('activity_id')
                    ->count('activity_id');

                if ($completedCount < $prevStageActivities->count()) {
                    $locked = true;
                    $message = 'Selesaikan tahap ' . ucfirst($stageOrder[$i]) . ' terlebih dahulu.';
                    break;
                }
            }
        }

        // Cek level progression (Investigate & Modified)
        if (!$locked && $activity->level) {
            $levelOrder = $activity->stage === 'investigate'
                ? ['atoms', 'blocks', 'relations', 'macro']
                : ['mudah', 'sedang', 'tantang'];

            $currentLevelIndex = array_search($activity->level, $levelOrder);

            if ($currentLevelIndex > 0) {
                for ($i = 0; $i < $currentLevelIndex; $i++) {
                    $prevLevelActivities = $chapter->activities()
                        ->where('stage', $activity->stage)
                        ->where('level', $levelOrder[$i])
                        ->pluck('id');

                    $completedCount = Submission::where('user_id', Auth::id())
                        ->whereIn('activity_id', $prevLevelActivities)
                        ->where('is_correct', true)
                        ->distinct('activity_id')
                        ->count('activity_id');

                    if ($completedCount < $prevLevelActivities->count()) {
                        $locked = true;
                        $message = 'Selesaikan level ' . ucfirst($levelOrder[$i]) . ' terlebih dahulu.';
                        break;
                    }
                }
            }
        }

        return response()->json([
            'locked' => $locked,
            'message' => $message,
        ]);
    }

    // ==========================================
    // FEEDBACK & EVALUATION (Sementara tanpa AI)
    // Nanti diganti integrasi Gemini API
    // ==========================================

    private function generateFeedback(Activity $activity, Submission $submission): string
    {
        $stage = $activity->stage;

        // Feedback sementara berdasarkan tahap (nanti diganti AI)
        switch ($stage) {
            case 'predict':
                return $this->feedbackPredict($activity, $submission);
            case 'run':
                return $this->feedbackRun($activity, $submission);
            case 'investigate':
                return $this->feedbackInvestigate($activity, $submission);
            case 'modified':
                return $this->feedbackModified($activity, $submission);
            case 'make':
                return $this->feedbackMake($activity, $submission);
            default:
                return 'Feedback tidak tersedia.';
        }
    }

    private function feedbackPredict(Activity $activity, Submission $submission): string
    {
        $answer = strtolower($submission->answer_text ?? '');
        if (strlen($answer) < 20) {
            return 'Coba jelaskan lebih detail prediksimu. Apa yang akan ditampilkan oleh query tersebut? Kolom apa saja yang muncul? Data dari tabel mana?';
        }
        if (str_contains($answer, 'join') || str_contains($answer, 'gabung') || str_contains($answer, 'tabel')) {
            return 'Bagus! Kamu sudah menyebutkan konsep penggabungan tabel. Coba perinci lagi: kolom apa saja yang akan muncul di hasil output?';
        }
        return 'Coba perhatikan lagi query-nya. Kata kunci SELECT menentukan kolom apa yang ditampilkan, dan JOIN menggabungkan data dari dua tabel. Apa yang bisa kamu simpulkan?';
    }

    private function feedbackRun(Activity $activity, Submission $submission): string
    {
        $answer = strtolower($submission->answer_text ?? '');
        if (strlen($answer) < 20) {
            return 'Jelaskan lebih detail perbandinganmu. Apakah hasil output sama dengan prediksimu? Bagian mana yang berbeda?';
        }
        return 'Refleksi yang baik! Dengan membandingkan prediksi dan hasil aktual, kamu bisa memahami bagaimana SQL memproses perintah JOIN.';
    }

    private function feedbackInvestigate(Activity $activity, Submission $submission): string
    {
        $answer = strtolower($submission->answer_text ?? '');
        $level = $activity->level;

        if (strlen($answer) < 15) {
            return 'Jawabanmu masih terlalu singkat. Coba jelaskan dengan lebih detail menggunakan bahasamu sendiri.';
        }

        $hints = [
            'atoms' => 'Perhatikan setiap elemen kecil dalam query. Tanda titik (.) berfungsi untuk menunjukkan kolom milik tabel tertentu. Apakah jawabanmu sudah mencakup hal ini?',
            'blocks' => 'Coba pikirkan fungsi dari setiap blok/baris dalam query. Baris ON menentukan kondisi pencocokan antar tabel.',
            'relations' => 'Pikirkan hubungan antar tabel. Primary Key adalah identitas unik, sedangkan Foreign Key menghubungkan ke tabel lain.',
            'macro' => 'Bagus! Coba pikirkan dalam konteks yang lebih luas: di situasi nyata apa saja query seperti ini dibutuhkan?',
        ];

        return $hints[$level] ?? 'Coba analisis lebih dalam lagi.';
    }

    private function feedbackModified(Activity $activity, Submission $submission): string
    {
        $code = strtolower($submission->answer_code ?? '');

        if (empty($code)) {
            return 'Kamu belum menulis kode SQL. Coba modifikasi kode yang ada di editor sesuai instruksi.';
        }

        if (!str_contains($code, 'select')) {
            return 'Pastikan query-mu dimulai dengan SELECT. Coba periksa kembali sintaks SQL-mu.';
        }

        // Cek apakah output sesuai expected
        if ($activity->expected_output) {
            return 'Coba jalankan query-mu dengan tombol Run, lalu bandingkan hasilnya dengan yang diminta di soal. Apakah sudah sesuai?';
        }

        return 'Kode SQL-mu sudah memiliki struktur yang baik. Pastikan hasilnya sesuai dengan yang diminta di soal.';
    }

    private function feedbackMake(Activity $activity, Submission $submission): string
    {
        $code = strtolower($submission->answer_code ?? '');

        if (empty($code)) {
            return 'Kamu belum menulis kode SQL. Baca kembali soalnya, identifikasi tabel dan kolom yang diperlukan, lalu tulis query dari nol.';
        }

        if (!str_contains($code, 'join')) {
            return 'Soal ini membutuhkan penggabungan dua tabel. Coba gunakan perintah JOIN untuk menghubungkan kedua tabel tersebut.';
        }

        if (!str_contains($code, 'on')) {
            return 'Query JOIN membutuhkan kondisi ON untuk mencocokkan kolom antar tabel. Kolom apa yang menghubungkan kedua tabel tersebut?';
        }

        return 'Query-mu sudah menggunakan JOIN. Coba jalankan dan bandingkan hasilnya dengan yang diminta di soal.';
    }

    private function evaluateAnswer(Activity $activity, ?string $answerText, ?string $answerCode): array
    {
        // Evaluasi sementara (nanti diganti AI scoring)
        $stage = $activity->stage;
        $score = 0;
        $isCorrect = false;
        $feedback = '';

        switch ($stage) {
            case 'predict':
            case 'run':
                // Evaluasi narasi: minimal panjang dan relevansi kata kunci
                $answer = strtolower($answerText ?? '');
                $keywords = ['join', 'tabel', 'kolom', 'data', 'select', 'output', 'hasil'];
                $matchedKeywords = 0;
                foreach ($keywords as $keyword) {
                    if (str_contains($answer, $keyword)) {
                        $matchedKeywords++;
                    }
                }

                if (strlen($answer) < 20) {
                    $score = 20;
                    $feedback = 'Jawabanmu masih terlalu singkat. Coba klik "Cek" terlebih dahulu untuk mendapat petunjuk, lalu perbaiki jawabanmu.';
                } elseif ($matchedKeywords < 2) {
                    $score = 40;
                    $feedback = 'Jawabanmu belum cukup relevan. Coba gunakan tombol "Cek" untuk mendapat petunjuk.';
                } else {
                    $score = 70 + ($matchedKeywords * 5);
                    $score = min($score, 100);
                    $isCorrect = true;
                    $feedback = 'Jawaban diterima! Kamu sudah menunjukkan pemahaman yang baik.';
                }
                break;

            case 'investigate':
                $answer = strtolower($answerText ?? '');
                if (strlen($answer) < 15) {
                    $score = 20;
                    $feedback = 'Jawabanmu terlalu singkat untuk di-submit. Gunakan tombol "Cek" untuk mendapat petunjuk.';
                } elseif (strlen($answer) < 40) {
                    $score = 50;
                    $feedback = 'Jawabanmu masih kurang mendalam. Coba elaborasi lebih lanjut.';
                } else {
                    $score = 75;
                    $isCorrect = true;
                    $feedback = 'Jawaban diterima! Analisismu sudah cukup baik.';
                }
                break;

            case 'modified':
            case 'make':
                $code = strtolower($answerCode ?? '');

                if (empty($code)) {
                    $score = 0;
                    $feedback = 'Kamu belum menulis kode SQL.';
                } elseif (!str_contains($code, 'select')) {
                    $score = 20;
                    $feedback = 'Query harus dimulai dengan SELECT.';
                } elseif ($activity->expected_output) {
                    // Bandingkan output dengan expected
                    $comparison = $this->compareOutput($code, $activity->expected_output);
                    $score = $comparison['score'];
                    $isCorrect = $comparison['is_correct'];
                    $feedback = $comparison['feedback'];
                } else {
                    $score = 60;
                    $feedback = 'Query sudah memiliki struktur yang valid.';
                }
                break;
        }

        return [
            'is_correct' => $isCorrect,
            'score' => $score,
            'feedback' => $feedback,
        ];
    }

    private function compareOutput(string $query, array $expectedOutput): array
    {
        try {
            $results = \Illuminate\Support\Facades\DB::connection('sandbox')
                ->select($query);

            $rows = array_map(fn($row) => (array) $row, $results);

            // Bandingkan jumlah baris
            if (count($rows) !== count($expectedOutput)) {
                return [
                    'is_correct' => false,
                    'score' => 50,
                    'feedback' => 'Jumlah baris output (' . count($rows) . ') tidak sesuai dengan yang diharapkan (' . count($expectedOutput) . '). Periksa kembali query-mu.',
                ];
            }

            // Bandingkan kolom
            if (!empty($rows)) {
                $actualColumns = array_keys($rows[0]);
                $expectedColumns = array_keys($expectedOutput[0]);

                if ($actualColumns != $expectedColumns) {
                    return [
                        'is_correct' => false,
                        'score' => 60,
                        'feedback' => 'Kolom yang ditampilkan belum sesuai. Yang diharapkan: ' . implode(', ', $expectedColumns) . '. Yang kamu tampilkan: ' . implode(', ', $actualColumns) . '.',
                    ];
                }
            }

            // Bandingkan isi data
            $actualData = json_encode($rows);
            $expectedData = json_encode($expectedOutput);

            if ($actualData === $expectedData) {
                return [
                    'is_correct' => true,
                    'score' => 100,
                    'feedback' => 'Sempurna! Output query-mu sudah sesuai dengan yang diharapkan.',
                ];
            }

            return [
                'is_correct' => false,
                'score' => 70,
                'feedback' => 'Struktur query sudah benar, tapi data yang dihasilkan belum tepat. Coba periksa kondisi JOIN atau WHERE-mu.',
            ];

        } catch (\Exception $e) {
            return [
                'is_correct' => false,
                'score' => 30,
                'feedback' => 'Query menghasilkan error: ' . $e->getMessage(),
            ];
        }
    }
}