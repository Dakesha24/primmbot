<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\AiInteractionLog;
use App\Models\Submission;
use App\Services\AI\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function __construct(private AIService $ai) {}

    /**
     * Submit jawaban final siswa — evaluasi via AI, simpan skor, beri feedback scaffolding.
     */
    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'activity_id' => ['required', 'exists:activities,id'],
            'answer_text' => ['nullable', 'string', 'max:10000'],
            'answer_code' => ['nullable', 'string', 'max:5000'],
        ]);

        $activity = Activity::findOrFail($request->activity_id);

        // Validasi: minimal salah satu dari answer_text atau answer_code harus diisi
        if (empty($request->answer_text) && empty($request->answer_code)) {
            return response()->json([
                'success' => false,
                'error'   => 'Jawaban tidak boleh kosong.',
            ], 422);
        }

        // Hitung attempt ke-berapa untuk siswa ini di aktivitas ini
        $lastAttempt = Submission::where('user_id', Auth::id())
            ->where('activity_id', $activity->id)
            ->max('attempt') ?? 0;
        $attempt = $lastAttempt + 1;

        // Buat record submission baru — SELALU insert, tidak update yang lama
        // Setiap attempt tersimpan sebagai riwayat (untuk keperluan penelitian)
        $submission = Submission::create([
            'user_id'     => Auth::id(),
            'activity_id' => $activity->id,
            'answer_text' => $request->answer_text,
            'answer_code' => $request->answer_code,
            'is_correct'  => false,
            'attempt'     => $attempt,
        ]);

        // Evaluasi via AIService (NarrativeEvaluator atau SqlEvaluator tergantung stage)
        $result = $this->ai->evaluateSubmission($activity, $submission);

        // Update submission dengan hasil evaluasi AI
        $submission->update([
            'score_keruntutan' => $result->keruntutan,
            'score_berargumen' => $result->berargumen,
            'score_kesimpulan' => $result->kesimpulan,
            'score'            => $result->total,      // skor AI — tidak pernah ditimpa guru
            'is_correct'       => $result->isCorrect,
            'ai_feedback'      => $result->feedback,
        ]);

        // Catat interaksi ke log (type='submit') untuk keperluan analisis
        AiInteractionLog::create([
            'user_id'           => Auth::id(),
            'activity_id'       => $activity->id,
            'type'              => 'submit',
            'prompt_sent'       => "Submit percobaan ke-{$attempt}: " . ($request->answer_code ?? $request->answer_text),
            'response_received' => "Skor: {$result->total}/100 | {$result->feedback}",
            'tokens_used'       => $result->tokensUsed   ?: null,
            'response_time'     => $result->responseTime ?: null,
        ]);

        return response()->json([
            'success'    => true,
            'is_correct' => $result->isCorrect,
            'score'      => $result->total,
            'score_detail' => [
                'keruntutan' => $result->keruntutan,
                'berargumen' => $result->berargumen,
                'kesimpulan' => $result->kesimpulan,
            ],
            'feedback'      => $result->feedback,
            'attempt'       => $attempt,
            'submission_id' => $submission->id,
        ]);
    }

    /**
     * Cek apakah activity yang dituju sudah boleh diakses (stage & level sebelumnya selesai).
     *
     * Optimasi: dulu ada N+1 query karena query DB dijalankan per iterasi loop.
     * Sekarang: load semua activities + completed IDs sekali → filter di PHP.
     * Total query: 2 (bukan N_stages × 2 + N_levels × 2).
     */
    public function checkProgress(Request $request): JsonResponse
    {
        $request->validate([
            'activity_id' => ['required', 'exists:activities,id'],
        ]);

        $activity = Activity::findOrFail($request->activity_id);
        $chapter  = $activity->chapter;

        $stageOrder        = Activity::STAGE_ORDER;
        $currentStageIndex = array_search($activity->stage, $stageOrder);
        $locked            = false;
        $message           = '';

        // ── Load semua data sekaligus (2 query total) ────────────────────────
        // Query 1: semua activities chapter ini, dikelompokkan per stage
        $allActivities = $chapter->activities()
            ->get(['id', 'stage', 'level'])
            ->groupBy('stage');

        // Query 2: semua activity_id yang sudah diselesaikan (is_correct = true)
        $allActivityIds   = $allActivities->flatten()->pluck('id');
        $completedIds     = Submission::where('user_id', Auth::id())
            ->whereIn('activity_id', $allActivityIds)
            ->where('is_correct', true)
            ->distinct('activity_id')
            ->pluck('activity_id')
            ->toArray();
        // ─────────────────────────────────────────────────────────────────────

        // Cek: apakah semua stage sebelumnya sudah selesai?
        if ($currentStageIndex > 0) {
            for ($i = 0; $i < $currentStageIndex; $i++) {
                $prevActivities = $allActivities->get($stageOrder[$i], collect());
                $prevIds        = $prevActivities->pluck('id')->toArray();

                // Bandingkan di PHP — tidak perlu query lagi
                $completedCount = count(array_intersect($completedIds, $prevIds));

                if ($completedCount < count($prevIds)) {
                    $locked  = true;
                    $message = 'Selesaikan tahap ' . ucfirst($stageOrder[$i]) . ' terlebih dahulu.';
                    break;
                }
            }
        }

        // Cek: apakah level sebelumnya dalam stage yang sama sudah selesai?
        if (!$locked && $activity->level) {
            $levelOrder        = $activity->stage === 'investigate'
                ? Activity::LEVEL_INVESTIGATE
                : Activity::LEVEL_TASK;
            $currentLevelIndex = array_search($activity->level, $levelOrder);

            if ($currentLevelIndex > 0) {
                // Kelompokkan activities stage ini per level.
                // Aktivitas tanpa level (null) dikecualikan dari cek prasyarat —
                // mereka tidak bisa jadi "level yang harus diselesaikan dulu".
                $stageActivities = $allActivities
                    ->get($activity->stage, collect())
                    ->filter(fn($a) => $a->level !== null)   // abaikan aktivitas tanpa level
                    ->groupBy('level');

                for ($i = 0; $i < $currentLevelIndex; $i++) {
                    $prevLevelActivities = $stageActivities->get($levelOrder[$i], collect());
                    $prevIds             = $prevLevelActivities->pluck('id')->toArray();
                    $completedCount      = count(array_intersect($completedIds, $prevIds));

                    if ($completedCount < count($prevIds)) {
                        $locked  = true;
                        $message = 'Selesaikan level ' . ucfirst($levelOrder[$i]) . ' terlebih dahulu.';
                        break;
                    }
                }
            }
        }

        return response()->json(['locked' => $locked, 'message' => $message]);
    }
}
