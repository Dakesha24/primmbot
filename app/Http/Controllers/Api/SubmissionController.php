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

    // Submit jawaban final — evaluasi + skor + feedback scaffolding
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
                'error'   => 'Jawaban tidak boleh kosong.',
            ], 422);
        }

        // Hitung attempt ke-berapa
        $lastAttempt = Submission::where('user_id', Auth::id())
            ->where('activity_id', $activity->id)
            ->max('attempt') ?? 0;
        $attempt = $lastAttempt + 1;

        // Buat record submission baru (selalu insert, tidak update)
        $submission = Submission::create([
            'user_id'     => Auth::id(),
            'activity_id' => $activity->id,
            'answer_text' => $request->answer_text,
            'answer_code' => $request->answer_code,
            'is_correct'  => false,
            'attempt'     => $attempt,
        ]);

        // Evaluasi via AIService
        $result = $this->ai->evaluateSubmission($activity, $submission);

        // Update submission dengan hasil evaluasi
        $submission->update([
            'score_keruntutan' => $result->keruntutan,
            'score_berargumen' => $result->berargumen,
            'score_kesimpulan' => $result->kesimpulan,
            'score'            => $result->total,
            'is_correct'       => $result->isCorrect,
            'ai_feedback'      => $result->feedback,
        ]);

        // Log interaksi
        AiInteractionLog::create([
            'user_id'           => Auth::id(),
            'activity_id'       => $activity->id,
            'type'              => 'submit',
            'prompt_sent'       => "Submit percobaan ke-{$attempt}: " . ($request->answer_code ?? $request->answer_text),
            'response_received' => "Skor: {$result->total}/100 | {$result->feedback}",
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

    // Cek progress: apakah stage sebelumnya sudah selesai
    public function checkProgress(Request $request): JsonResponse
    {
        $request->validate([
            'activity_id' => ['required', 'exists:activities,id'],
        ]);

        $activity = Activity::findOrFail($request->activity_id);
        $chapter  = $activity->chapter;

        $stageOrder         = ['predict', 'run', 'investigate', 'modify', 'make'];
        $currentStageIndex  = array_search($activity->stage, $stageOrder);
        $locked             = false;
        $message            = '';

        if ($currentStageIndex > 0) {
            for ($i = 0; $i < $currentStageIndex; $i++) {
                $prevActivities = $chapter->activities()
                    ->where('stage', $stageOrder[$i])
                    ->pluck('id');

                $completedCount = Submission::where('user_id', Auth::id())
                    ->whereIn('activity_id', $prevActivities)
                    ->where('is_correct', true)
                    ->distinct('activity_id')
                    ->count('activity_id');

                if ($completedCount < $prevActivities->count()) {
                    $locked  = true;
                    $message = 'Selesaikan tahap ' . ucfirst($stageOrder[$i]) . ' terlebih dahulu.';
                    break;
                }
            }
        }

        if (!$locked && $activity->level) {
            $levelOrder        = $activity->stage === 'investigate'
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
