<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\AiInteractionLog;
use App\Models\Submission;
use App\Services\GroqService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function __construct(private GroqService $groq) {}
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

        // Generate feedback via Groq
        $feedback = $this->groq->getFeedback($activity, $submission);

        // Untuk tahap make: evaluasi SQL juga agar frontend tahu skor
        $score = null;
        $isCorrect = false;
        if ($activity->stage === 'make') {
            $evaluation = $this->evaluateAnswer($activity, $request->answer_text, $request->answer_code);
            $score      = $evaluation['score'];
            $isCorrect  = $evaluation['is_correct'];
        }

        // Simpan feedback
        $submission->update(['ai_feedback' => $feedback]);

        // Bangun pesan visual seperti yang ditampilkan di chat widget
        $cekParts = ['Cek jawaban:'];
        if (!empty($request->answer_code)) $cekParts[] = $request->answer_code;
        if (!empty($request->answer_text)) $cekParts[] = $request->answer_text;
        $responseDisplay = $feedback . ($score !== null ? " (Skor: {$score}/100)" : '');

        AiInteractionLog::create([
            'user_id'           => Auth::id(),
            'activity_id'       => $activity->id,
            'prompt_sent'       => implode("\n", $cekParts),
            'response_received' => $responseDisplay,
        ]);

        return response()->json([
            'success'    => true,
            'feedback'   => $feedback,
            'score'      => $score,
            'is_correct' => $isCorrect,
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

        $submitParts = ['Submit jawaban:'];
        if (!empty($request->answer_code)) $submitParts[] = $request->answer_code;
        if (!empty($request->answer_text)) $submitParts[] = $request->answer_text;
        $icon = $evaluation['is_correct'] ? '✅ ' : '⚠️ ';

        AiInteractionLog::create([
            'user_id'           => Auth::id(),
            'activity_id'       => $activity->id,
            'prompt_sent'       => implode("\n", $submitParts),
            'response_received' => $icon . $evaluation['feedback'] . " (Skor: {$evaluation['score']}/100)",
        ]);

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
        $stageOrder = ['predict', 'run', 'investigate', 'modify', 'make'];
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

    // Virtual Assistant Chat
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'activity_id' => ['required', 'exists:activities,id'],
            'message'     => ['required', 'string', 'max:1000'],
            'history'     => ['nullable', 'array'],
        ]);

        $activity = Activity::findOrFail($request->activity_id);
        $history  = $request->input('history', []);

        $response = $this->groq->chat($activity, $request->message, $history);

        AiInteractionLog::create([
            'user_id'           => Auth::id(),
            'activity_id'       => $activity->id,
            'prompt_sent'       => $request->message,
            'response_received' => $response,
        ]);

        return response()->json([
            'success'  => true,
            'response' => $response,
        ]);
    }

    private function evaluateAnswer(Activity $activity, ?string $answerText, ?string $answerCode): array
    {
        $stage = $activity->stage;

        // Tahap berbasis narasi: scoring via Groq
        if (in_array($stage, ['predict', 'run', 'investigate'])) {
            return $this->groq->evaluateSubmission($activity, $answerText, $answerCode);
        }

        // Modify / Make: jalankan query siswa → bandingkan output aktual vs expected via AI
        if ($stage === 'modify' || $stage === 'make') {
            if (empty(trim($answerCode ?? ''))) {
                return ['is_correct' => false, 'score' => 0, 'feedback' => 'Kamu belum menulis kode SQL.'];
            }
            try {
                $results = \Illuminate\Support\Facades\DB::connection('sandbox')->select($answerCode);
                $actualOutput = array_map(fn($row) => (array) $row, $results);
            } catch (\Exception $e) {
                return ['is_correct' => false, 'score' => 20, 'feedback' => 'Query menghasilkan error: ' . $e->getMessage()];
            }
            $expectedOutput = $activity->expected_output;
            if (is_string($expectedOutput)) {
                $expectedOutput = json_decode($expectedOutput, true) ?? [];
            }
            return $this->groq->evaluateModify($activity, $answerCode, $actualOutput, $expectedOutput ?? []);
        }

        // Tahap berbasis kode SQL: jalankan query dan bandingkan output
        $code = strtolower($answerCode ?? '');

        if (empty($code)) {
            return ['is_correct' => false, 'score' => 0, 'feedback' => 'Kamu belum menulis kode SQL.'];
        }

        if (!str_contains($code, 'select')) {
            return ['is_correct' => false, 'score' => 20, 'feedback' => 'Query harus dimulai dengan SELECT.'];
        }

        $expectedOutput = $activity->expected_output;
        if (is_string($expectedOutput)) {
            $expectedOutput = json_decode($expectedOutput, true);
        }
        if (!empty($expectedOutput)) {
            return $this->compareOutput($answerCode, $expectedOutput);
        }

        return ['is_correct' => false, 'score' => 60, 'feedback' => 'Query sudah memiliki struktur yang valid. Pastikan hasilnya sesuai dengan yang diminta di soal.'];
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