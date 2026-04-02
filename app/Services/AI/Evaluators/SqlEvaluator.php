<?php

namespace App\Services\AI\Evaluators;

use App\Models\Activity;
use App\Models\Submission;
use App\Models\SandboxTable;
use App\Services\AI\EvaluationResult;
use App\Services\AI\GroqClient;
use App\Services\AI\ResponseParser;
use App\Services\AI\TableNameRewriter;
use App\Services\AI\Prompts\Stages\ModifyPrompt;
use App\Services\AI\Prompts\Stages\MakePrompt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Evaluator untuk jawaban SQL (Modify, Make).
 * Menjalankan query siswa ke sandbox, membandingkan output, lalu mengirim ke AI untuk dinilai.
 */
class SqlEvaluator
{
    public function __construct(
        private GroqClient     $client,
        private ResponseParser $parser,
    ) {}

    public function evaluate(Activity $activity, Submission $submission, array $context): EvaluationResult
    {
        $answerCode = trim($submission->answer_code ?? '');

        // Validasi: kode SQL wajib diisi
        if (empty($answerCode)) {
            return new EvaluationResult(
                keruntutan: 0,
                berargumen: 0,
                kesimpulan: 0,
                total:      0,
                isCorrect:  false,
                feedback:   'Kamu belum menulis kode SQL. Coba pikirkan dulu — tabel apa yang perlu dilibatkan untuk menjawab soal ini?',
            );
        }

        // ── Rewrite nama tabel pendek → nama lengkap di sandbox ──────────────
        // Menggunakan TableNameRewriter yang sama dengan SqlRunnerController
        // agar perilaku konsisten antara "Run" dan "Submit"
        if ($activity->sandbox_database_id) {
            $sandboxTables = SandboxTable::where('sandbox_database_id', $activity->sandbox_database_id)->get();
            $tableMap      = TableNameRewriter::buildMap($sandboxTables);
            $answerCode    = TableNameRewriter::rewrite($answerCode, $tableMap);
        }

        // ── Jalankan query siswa ke sandbox (READ-ONLY) ───────────────────────
        // Tidak dibungkus transaction karena ini SELECT saja
        try {
            $results      = DB::connection('sandbox')->select($answerCode);
            $actualOutput = array_map(fn($row) => (array) $row, $results);
        } catch (\Exception $e) {
            // Query siswa menghasilkan SQL error — kembalikan feedback scaffolding
            return new EvaluationResult(
                keruntutan: 20,
                berargumen: 20,
                kesimpulan: 20,
                total:      20,
                isCorrect:  false,
                feedback:   'Query-mu menghasilkan error: ' . $e->getMessage()
                    . ' Coba periksa kembali — apakah nama tabel dan kolom sudah sesuai dengan yang tersedia?',
            );
        }

        // Ambil expected output dari aktivitas (output benar yang sudah ditetapkan guru)
        $expectedOutput = $activity->expected_output ?? [];
        if (is_string($expectedOutput)) {
            $expectedOutput = json_decode($expectedOutput, true) ?? [];
        }

        // ── Bangun prompt evaluasi sesuai stage ───────────────────────────────
        $prompt = match ($activity->stage) {
            'modify' => (new ModifyPrompt())->buildEvaluationPrompt(
                            $activity, $submission, $context, $actualOutput, $expectedOutput),
            'make'   => (new MakePrompt())->buildEvaluationPrompt(
                            $activity, $submission, $context, $actualOutput, $expectedOutput),
            // Fallback ke Make prompt jika stage tidak dikenal (seharusnya tidak terjadi)
            default  => (new MakePrompt())->buildEvaluationPrompt(
                            $activity, $submission, $context, $actualOutput, $expectedOutput),
        };

        $groqResponse = $this->client->call($prompt, config('ai.eval_max_tokens', 300));

        // ── Fallback: API tidak merespons ─────────────────────────────────────
        if (!$groqResponse) {
            Log::warning('SqlEvaluator: Groq API tidak merespons, menggunakan fallback scoring', [
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
}
