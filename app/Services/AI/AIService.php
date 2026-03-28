<?php

namespace App\Services\AI;

use App\Models\Activity;
use App\Models\Submission;
use App\Services\AI\Evaluators\NarrativeEvaluator;
use App\Services\AI\Evaluators\SqlEvaluator;
use App\Services\AI\Prompts\ChatPrompt;

class AIService
{
    public function __construct(
        private ContextLoader       $contextLoader,
        private NarrativeEvaluator  $narrativeEvaluator,
        private SqlEvaluator        $sqlEvaluator,
        private ChatPrompt          $chatPrompt,
        private GroqClient          $client,
    ) {}

    public function evaluateSubmission(Activity $activity, Submission $submission): EvaluationResult
    {
        $context = $this->loadFormattedContext($activity);

        if (in_array($activity->stage, ['modify', 'make'])) {
            return $this->sqlEvaluator->evaluate($activity, $submission, $context);
        }

        return $this->narrativeEvaluator->evaluate($activity, $submission, $context);
    }

    public function chat(
        Activity    $activity,
        string      $message,
        array       $history,
        ?Submission $latestSubmission = null,
    ): string {
        $context = $this->loadFormattedContext($activity);
        $prompt  = $this->chatPrompt->build($activity, $message, $history, $context, $latestSubmission);

        return $this->client->call($prompt, 300, temperature: 0.7)
            ?? 'Maaf, asisten virtual sedang tidak tersedia. Coba beberapa saat lagi.';
    }

    private function loadFormattedContext(Activity $activity): array
    {
        $raw = $this->contextLoader->load($activity);

        return [
            'materials'          => $raw['materials'],
            'sandboxTables'      => $raw['sandboxTables'],
            'materialsFormatted' => $this->contextLoader->formatMaterials($raw['materials']),
            'tablesFormatted'    => $this->contextLoader->formatSandboxTables($raw['sandboxTables']),
        ];
    }
}
