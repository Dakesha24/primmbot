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

class NarrativeEvaluator
{
    public function __construct(
        private GroqClient     $client,
        private ResponseParser $parser,
    ) {}

    public function evaluate(Activity $activity, Submission $submission, array $context): EvaluationResult
    {
        $answer = trim($submission->answer_text ?? '');

        // Pre-check: jawaban terlalu pendek atau tidak bermakna
        if (mb_strlen($answer) < 20) {
            return new EvaluationResult(
                keruntutan: 0,
                berargumen: 0,
                kesimpulan: 0,
                total:      0,
                isCorrect:  false,
                feedback:   'Jawaban terlalu singkat. Coba jelaskan pemikiranmu dengan lebih lengkap — uraikan secara urut, berikan alasan, dan tutup dengan kesimpulan.',
            );
        }

        // Pre-check: rasio huruf terlalu rendah (jawaban acak/tidak bermakna)
        $letterCount = preg_match_all('/[a-zA-Z]/u', $answer);
        $ratio = $letterCount / mb_strlen($answer);
        $wordCount = str_word_count($answer);
        if ($wordCount < 3 || $ratio < 0.5) {
            return new EvaluationResult(
                keruntutan: 0,
                berargumen: 0,
                kesimpulan: 0,
                total:      0,
                isCorrect:  false,
                feedback:   'Jawaban tidak dapat dipahami. Tuliskan penjelasanmu dalam kalimat yang jelas dan bermakna.',
            );
        }

        $prompt = match ($activity->stage) {
            'predict'     => $this->buildPredictPrompt($activity, $submission, $context),
            'run'         => $this->buildRunPrompt($activity, $submission, $context),
            'investigate' => $this->buildInvestigatePrompt($activity, $submission, $context),
            default       => $this->buildPredictPrompt($activity, $submission, $context),
        };

        $response = $this->client->call($prompt, 300);

        if (!$response) {
            return $this->parser->fallback($activity->kkm, $submission->answer_text);
        }

        return $this->parser->parseEvaluation($response, $activity->kkm);
    }

    private function buildPredictPrompt(Activity $activity, Submission $submission, array $context): string
    {
        return (new PredictPrompt())->buildEvaluationPrompt($activity, $submission, $context);
    }

    private function buildRunPrompt(Activity $activity, Submission $submission, array $context): string
    {
        // Ambil jawaban Predict sebelumnya untuk diinjeksi ke prompt Run
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
