<?php

namespace App\Services\AI;

use App\Models\Activity;
use App\Models\Submission;
use App\Services\AI\Evaluators\NarrativeEvaluator;
use App\Services\AI\Evaluators\SqlEvaluator;
use App\Services\AI\Prompts\ChatPrompt;

/**
 * Pintu masuk utama dari Controller ke sistem AI.
 *
 * Tanggung jawab:
 *   - evaluateSubmission(): arahkan ke evaluator yang tepat (Narrative atau SQL)
 *   - chat(): kirim pesan siswa ke Virtual Assistant
 *
 * Controller tidak perlu tahu detail evaluator mana yang digunakan —
 * cukup panggil AIService.
 */
class AIService
{
    public function __construct(
        private ContextLoader       $contextLoader,
        private NarrativeEvaluator  $narrativeEvaluator,
        private SqlEvaluator        $sqlEvaluator,
        private ChatPrompt          $chatPrompt,
        private GroqClient          $client,
    ) {}

    /**
     * Evaluasi jawaban siswa.
     *
     * - Tahap predict/run/investigate → NarrativeEvaluator (jawaban teks)
     * - Tahap modify/make             → SqlEvaluator (jawaban SQL)
     */
    public function evaluateSubmission(Activity $activity, Submission $submission): EvaluationResult
    {
        $context = $this->loadFormattedContext($activity);

        if (in_array($activity->stage, ['modify', 'make'])) {
            return $this->sqlEvaluator->evaluate($activity, $submission, $context);
        }

        return $this->narrativeEvaluator->evaluate($activity, $submission, $context);
    }

    /**
     * Kirim pesan chat siswa ke Virtual Assistant.
     *
     * Mengembalikan array dengan:
     *   - 'text'         → teks respons AI (atau pesan fallback jika API gagal)
     *   - 'tokensUsed'   → jumlah token yang dikonsumsi (0 jika gagal)
     *   - 'responseTime' → waktu respons dalam detik (0.0 jika gagal)
     */
    public function chat(
        Activity    $activity,
        string      $message,
        array       $history,
        ?Submission $latestSubmission = null,
    ): array {
        $context      = $this->loadFormattedContext($activity);
        $prompt       = $this->chatPrompt->build($activity, $message, $history, $context, $latestSubmission);

        // Temperature lebih tinggi untuk chat agar respons terasa lebih natural
        $groqResponse = $this->client->call(
            $prompt,
            config('ai.chat_max_tokens', 300),
            temperature: config('ai.chat_temperature', 0.7),
        );

        return [
            'text'         => $groqResponse?->content ?? 'Maaf, asisten virtual sedang tidak tersedia. Coba beberapa saat lagi.',
            'tokensUsed'   => $groqResponse?->tokensUsed   ?? 0,
            'responseTime' => $groqResponse?->responseTime ?? 0.0,
        ];
    }

    /**
     * Muat konteks aktivitas dalam format mentah dan format siap-prompt.
     *
     * @return array{materials: string, sandboxTables: array, materialsFormatted: string, tablesFormatted: string}
     */
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
