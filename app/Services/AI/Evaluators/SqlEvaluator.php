<?php

namespace App\Services\AI\Evaluators;

use App\Models\Activity;
use App\Models\Submission;
use App\Models\SandboxTable;
use App\Services\AI\EvaluationResult;
use App\Services\AI\GroqClient;
use App\Services\AI\ResponseParser;
use App\Services\AI\Prompts\Stages\ModifyPrompt;
use App\Services\AI\Prompts\Stages\MakePrompt;
use Illuminate\Support\Facades\DB;

class SqlEvaluator
{
    public function __construct(
        private GroqClient     $client,
        private ResponseParser $parser,
    ) {}

    public function evaluate(Activity $activity, Submission $submission, array $context): EvaluationResult
    {
        $answerCode = trim($submission->answer_code ?? '');

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

        // Rewrite nama pendek → nama lengkap (sama seperti SqlRunnerController)
        if ($activity->sandbox_database_id) {
            $sandboxTables = SandboxTable::where('sandbox_database_id', $activity->sandbox_database_id)->get();
            $tableMap = [];
            foreach ($sandboxTables as $t) {
                $tableMap[strtolower($t->display_name)] = $t->table_name;
                $parts = explode('__', $t->table_name, 2);
                if (isset($parts[1])) {
                    $tableMap[strtolower($parts[1])] = $t->table_name;
                }
            }
            uksort($tableMap, fn($a, $b) => strlen($b) - strlen($a));
            foreach ($tableMap as $shortName => $fullName) {
                if (strtolower($shortName) === strtolower($fullName)) continue;
                $answerCode = preg_replace('/`' . preg_quote($shortName, '/') . '`/i', "`{$fullName}`", $answerCode);
                $answerCode = preg_replace('/\b' . preg_quote($shortName, '/') . '\b/i', $fullName, $answerCode);
            }
        }

        // Jalankan query siswa ke sandbox (SELECT only)
        try {
            $results      = DB::connection('sandbox')->select($answerCode);
            $actualOutput = array_map(fn($row) => (array) $row, $results);
        } catch (\Exception $e) {
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

        $expectedOutput = $activity->expected_output ?? [];
        if (is_string($expectedOutput)) {
            $expectedOutput = json_decode($expectedOutput, true) ?? [];
        }

        $prompt = match ($activity->stage) {
            'modify' => (new ModifyPrompt())->buildEvaluationPrompt(
                            $activity, $submission, $context, $actualOutput, $expectedOutput),
            'make'   => (new MakePrompt())->buildEvaluationPrompt(
                            $activity, $submission, $context, $actualOutput, $expectedOutput),
            default  => (new MakePrompt())->buildEvaluationPrompt(
                            $activity, $submission, $context, $actualOutput, $expectedOutput),
        };

        $response = $this->client->call($prompt, 300);

        if (!$response) {
            return $this->parser->fallback($activity->kkm, $submission->answer_text);
        }

        return $this->parser->parseEvaluation($response, $activity->kkm);
    }
}
