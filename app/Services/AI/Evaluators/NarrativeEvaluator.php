<?php

namespace App\Services\AI\Evaluators;

use App\Models\Activity;
use App\Models\Submission;
use App\Services\AI\ContextLoader;
use App\Services\AI\EvaluationResult;
use App\Services\AI\GroqClient;
use App\Services\AI\ResponseParser;
use App\Services\AI\Prompts\Stages\PredictPrompt;
use App\Services\AI\Prompts\Stages\RunPrompt;
use App\Services\AI\Prompts\Stages\InvestigatePrompt;
use Illuminate\Support\Facades\Log;

/**
 * Evaluator untuk jawaban teks (Predict, Run, Investigate).
 * Mengirim jawaban siswa ke AI untuk dinilai berdasarkan rubrik logical thinking.
 */
class NarrativeEvaluator
{
    public function __construct(
        private GroqClient     $client,
        private ResponseParser $parser,
    ) {}

    public function evaluate(Activity $activity, Submission $submission, array $context): EvaluationResult
    {
        $answer = trim($submission->answer_text ?? '');

        // ── Pre-check 1: Jawaban terlalu singkat ─────────────────────────────
        // Tidak perlu kirim ke AI — langsung tolak dengan feedback scaffolding
        $minLength = config('ai.min_answer_length', 20);
        if (mb_strlen($answer) < $minLength) {
            return new EvaluationResult(
                keruntutan: 0,
                berargumen: 0,
                kesimpulan: 0,
                total:      0,
                isCorrect:  false,
                feedback:   'Jawaban terlalu singkat. Coba jelaskan pemikiranmu dengan lebih lengkap — uraikan secara urut, berikan alasan, dan tutup dengan kesimpulan.',
            );
        }

        // ── Pre-check 2: Rasio huruf terlalu rendah (jawaban acak/spam) ──────
        $letterCount = preg_match_all('/[a-zA-Z]/u', $answer);
        $ratio       = $letterCount / mb_strlen($answer);
        $wordCount   = str_word_count($answer);

        if ($wordCount < config('ai.min_word_count', 3) || $ratio < config('ai.min_letter_ratio', 0.5)) {
            return new EvaluationResult(
                keruntutan: 0,
                berargumen: 0,
                kesimpulan: 0,
                total:      0,
                isCorrect:  false,
                feedback:   'Jawaban tidak dapat dipahami. Tuliskan penjelasanmu dalam kalimat yang jelas dan bermakna.',
            );
        }

        // ── Bangun prompt sesuai stage ────────────────────────────────────────
        $prompt = match ($activity->stage) {
            'predict'     => $this->buildPredictPrompt($activity, $submission, $context),
            'run'         => $this->buildRunPrompt($activity, $submission, $context),
            'investigate' => $this->buildInvestigatePrompt($activity, $submission, $context),
            // Fallback ke predict jika stage tidak dikenal (seharusnya tidak terjadi)
            default       => $this->buildPredictPrompt($activity, $submission, $context),
        };

        $groqResponse = $this->client->call($prompt, config('ai.eval_max_tokens', 300));

        // ── Fallback: API tidak merespons ────────────────────────────────────
        // Catat ke log agar admin bisa mendeteksi jika Groq API sedang bermasalah
        if (!$groqResponse) {
            Log::warning('NarrativeEvaluator: Groq API tidak merespons, menggunakan fallback scoring', [
                'activity_id' => $activity->id,
                'user_id'     => $submission->user_id,
                'stage'       => $activity->stage,
                'attempt'     => $submission->attempt,
            ]);
            return $this->parser->fallback($activity->kkm, $submission->answer_text);
        }

        $result               = $this->parser->parseEvaluation($groqResponse->content, $activity->kkm);
        $result->tokensUsed   = $groqResponse->tokensUsed;
        $result->responseTime = $groqResponse->responseTime;

        return $result;
    }

    private function buildPredictPrompt(Activity $activity, Submission $submission, array $context): string
    {
        return (new PredictPrompt())->buildEvaluationPrompt($activity, $submission, $context);
    }

    private function buildRunPrompt(Activity $activity, Submission $submission, array $context): string
    {
        // Inject jawaban Predict sebelumnya ke prompt Run
        // agar AI tahu apakah prediksi siswa sesuai dengan hasil eksekusi
        $predictSubmission = Submission::where('user_id', $submission->user_id)
            ->whereHas('activity', fn($q) => $q
                ->where('chapter_id', $activity->chapter_id)
                ->where('stage', 'predict')
            )
            ->orderBy('attempt', 'desc')
            ->first();

        $predictAnswer = $predictSubmission?->answer_text;

        return (new RunPrompt())->buildEvaluationPrompt($activity, $submission, $context, $predictAnswer);
    }

    private function buildInvestigatePrompt(Activity $activity, Submission $submission, array $context): string
    {
        return (new InvestigatePrompt())->buildEvaluationPrompt($activity, $submission, $context);
    }
}
