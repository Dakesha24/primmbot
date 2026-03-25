<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    private string $apiKey;
    private string $model;
    private string $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key', '');
        $this->model  = config('services.groq.model', 'openai/gpt-oss-120b');
    }

    // ─── Public API ──────────────────────────────────────────────────────────────

    /** Feedback scaffolding untuk tombol Cek */
    public function getFeedback(Activity $activity, Submission $submission): string
    {
        $context = $this->loadContext($activity);
        $prompt  = $this->buildFeedbackPrompt($activity, $submission, $context);
        return $this->call($prompt) ?? $this->fallbackFeedback($activity->stage);
    }

    /** Evaluasi + skor untuk tombol Submit (tahap predict/run/investigate) */
    public function evaluateSubmission(Activity $activity, ?string $answerText, ?string $answerCode): array
    {
        $context  = $this->loadContext($activity);
        $prompt   = $this->buildEvaluationPrompt($activity, $answerText, $answerCode, $context);
        $response = $this->call($prompt);

        if (!$response) {
            return $this->fallbackEvaluation($activity->stage, $answerText);
        }

        return $this->parseEvaluation($response, $activity->stage);
    }

    /** Evaluasi modify: bandingkan output aktual siswa vs expected output dengan AI */
    public function evaluateModify(Activity $activity, ?string $answerCode, array $actualOutput, array $expectedOutput): array
    {
        $context  = $this->loadContext($activity);
        $prompt   = $this->buildModifyEvaluationPrompt($activity, $answerCode, $actualOutput, $expectedOutput, $context);
        $response = $this->call($prompt, 300);

        if (!$response) {
            return $this->fallbackEvaluation('modify', null);
        }

        return $this->parseEvaluation($response, 'modify');
    }

    /** Respons chat Virtual Assistant */
    public function chat(Activity $activity, string $userMessage, array $history = []): string
    {
        $context = $this->loadContext($activity);
        $prompt  = $this->buildChatPrompt($activity, $userMessage, $history, $context);
        return $this->call($prompt) ?? 'Maaf, asisten virtual sedang tidak tersedia. Coba beberapa saat lagi.';
    }

    // ─── Context Loader ───────────────────────────────────────────────────────────

    private function loadContext(Activity $activity): array
    {
        // Load ringkasan materi dari chapter
        $materials = $activity->chapter
            ->lessonMaterials()
            ->where('type', 'ringkasan_materi')
            ->orderBy('order')
            ->get()
            ->map(fn($m) => strip_tags($m->content))
            ->filter()
            ->implode("\n\n");

        // Load struktur tabel sandbox
        $sandboxTables = [];
        if ($activity->sandbox_database_id) {
            $activity->load('sandboxDatabase.sandboxTables');
            if ($activity->sandboxDatabase) {
                foreach ($activity->sandboxDatabase->sandboxTables as $table) {
                    try {
                        $columns = DB::connection('sandbox')->select("DESCRIBE `{$table->table_name}`");
                        $sandboxTables[$table->display_name] = array_map(fn($col) => [
                            'name' => $col->Field,
                            'type' => $col->Type,
                            'key'  => $col->Key,
                        ], $columns);
                    } catch (\Exception $e) {
                        // Abaikan jika tabel belum ada
                    }
                }
            }
        }

        return [
            'materials'     => $materials,
            'sandboxTables' => $sandboxTables,
        ];
    }

    // ─── Context Formatters ───────────────────────────────────────────────────────

    private function formatMaterials(string $materials): string
    {
        if (empty(trim($materials))) return '';
        // Potong maksimal 800 karakter agar hemat token
        $materials = mb_substr(trim($materials), 0, 800);
        return "[MATERI]\n{$materials}\n[/MATERI]";
    }

    private function formatSandboxTables(array $sandboxTables): string
    {
        if (empty($sandboxTables)) return '';

        $text = "[DB]\n";
        foreach ($sandboxTables as $displayName => $columns) {
            $cols = implode(', ', array_map(fn($c) => $c['name'] . ($c['key'] === 'PRI' ? '(PK)' : ($c['key'] === 'MUL' ? '(FK)' : '')), $columns));
            $text .= "{$displayName}: {$cols}\n";
        }
        return rtrim($text) . "\n[/DB]";
    }

    // ─── Prompt Builders ─────────────────────────────────────────────────────────

    private function buildFeedbackPrompt(Activity $activity, Submission $submission, array $context): string
    {
        $stage       = $activity->stage;
        $question    = $activity->question_text;
        $code        = $activity->code_snippet ?? '-';
        $level       = $activity->level ?? '-';
        $answerText  = $submission->answer_text ?? '-';
        $answerCode  = $submission->answer_code ?? '-';
        $description = $activity->description ? strip_tags($activity->description) : '-';

        $materialsText = $this->formatMaterials($context['materials']);
        $tablesText    = $this->formatSandboxTables($context['sandboxTables']);

        $stagePrompt = match ($stage) {
            'predict'     => "PREDICT. SQL: {$code}. Soal: {$question}. Prediksi siswa: {$answerText}. Bimbing dengan pertanyaan: kolom apa dipilih? tabel mana digabung? kondisi JOIN-nya?",
            'run'         => "RUN. SQL: {$code}. Soal: {$question}. Refleksi siswa: {$answerText}. Dorong siswa bandingkan prediksi vs hasil nyata.",
            'investigate' => "INVESTIGATE level {$level} ({$this->getLevelFocus($level)}). SQL: {$code}. Soal: {$question}. Jawaban: {$answerText}. Bimbing analisis sesuai level.",
            'modify'      => "MODIFY. Soal: {$question}. Kode siswa: {$answerCode}. Penjelasan: {$answerText}. Bimbing apakah arah modifikasi sudah benar.",
            'make'        => "MAKE. Soal: {$question}. Kode siswa: {$answerCode}. Penjelasan: {$answerText}. Bimbing cek komponen SELECT/FROM/JOIN/ON yang kurang.",
            default       => "Feedback untuk jawaban: {$answerText}",
        };

        return $this->getSystemPrompt() . "\n"
            . ($materialsText ? $materialsText . "\n" : '')
            . ($tablesText ? $tablesText . "\n" : '')
            . $stagePrompt . "\nBerikan HANYA clue/petunjuk berupa pertanyaan pemantik (2-3 kalimat). "
            . "DILARANG menyebut jawaban, kode yang benar, atau menjelaskan konsep secara langsung. "
            . "Bimbing siswa untuk menemukan sendiri:";
    }

    private function buildEvaluationPrompt(Activity $activity, ?string $answerText, ?string $answerCode, array $context): string
    {
        $stage    = $activity->stage;
        $question = $activity->question_text;
        $code     = $activity->code_snippet ?? '-';
        $level    = $activity->level ?? '-';
        $answer   = $answerText ?? '-';

        $materialsText = $this->formatMaterials($context['materials']);
        $tablesText    = $this->formatSandboxTables($context['sandboxTables']);

        $criteria = match ($stage) {
            'predict'     => 'Skor: 85-100=sebut kolom+tabel+kondisi JOIN jelas; 70-84=relevan sebagian; 50-69=kurang detail; 0-49=singkat/tidak relevan.',
            'run'         => 'Skor: 85-100=bandingkan prediksi vs hasil+alasan; 70-84=bandingkan tanpa alasan; 50-69=sebut hasil saja; 0-49=tidak relevan.',
            'investigate' => "Level {$level}: {$this->getLevelFocus($level)}. Skor: 85-100=analisis mendalam; 70-84=cukup; 50-69=dangkal; 0-49=tidak relevan.",
            default       => '',
        };

        return $this->getSystemPrompt() . "\n"
            . ($materialsText ? $materialsText . "\n" : '')
            . ($tablesText ? $tablesText . "\n" : '')
            . "Tahap: {$stage}. SQL: {$code}. Soal: {$question}. Jawaban siswa: {$answer}\n"
            . $criteria . "\n"
            . 'Balas HANYA JSON: {"score":0-100,"is_correct":true/false,"feedback":"1-2 kalimat"}. Tanpa teks lain.';
    }

    private function buildModifyEvaluationPrompt(Activity $activity, ?string $answerCode, array $actualOutput, array $expectedOutput, array $context): string
    {
        $question      = $activity->question_text;
        $defaultCode   = $activity->editor_default_code ?? '-';
        $tablesText    = $this->formatSandboxTables($context['sandboxTables']);

        $actualJson   = json_encode($actualOutput,   JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $expectedJson = json_encode($expectedOutput, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // Potong jika terlalu panjang
        $actualJson   = mb_substr($actualJson,   0, 600);
        $expectedJson = mb_substr($expectedJson, 0, 600);

        return $this->getSystemPrompt() . "\n"
            . ($tablesText ? $tablesText . "\n" : '')
            . "Tahap: MODIFY. Soal: {$question}\n"
            . "Kode awal: {$defaultCode}\n"
            . "Kode siswa:\n{$answerCode}\n"
            . "Output siswa:\n{$actualJson}\n"
            . "Output yang diharapkan:\n{$expectedJson}\n"
            . "Nilai apakah output siswa secara logika sesuai dengan yang diharapkan (kolom, jumlah baris, isi data). "
            . "Jika berbeda tapi logika benar, tetap beri skor tinggi. "
            . 'Balas HANYA JSON: {"score":0-100,"is_correct":true/false,"feedback":"1-2 kalimat"}. Tanpa teks lain.';
    }

    private function buildChatPrompt(Activity $activity, string $userMessage, array $history, array $context): string
    {
        $stage    = ucfirst($activity->stage);
        $level    = $activity->level ? " — Level {$activity->level}" : '';
        $question = $activity->question_text;
        $code     = $activity->code_snippet ?? '-';

        $materialsText = $this->formatMaterials($context['materials']);
        $tablesText    = $this->formatSandboxTables($context['sandboxTables']);

        $historyText = '';
        foreach ($history as $h) {
            $role         = $h['role'] === 'user' ? 'Siswa' : 'PRIMM Bot';
            $historyText .= "{$role}: {$h['message']}\n";
        }

        // Deteksi apakah pesan bersifat kasual (salam, perkenalan, dll)
        $isCasual = $this->isCasualMessage($userMessage);

        $contextBlock = $isCasual ? '' :
            ($materialsText ? $materialsText . "\n" : '')
            . ($tablesText ? $tablesText . "\n" : '')
            . "Konteks aktivitas — Tahap: {$stage}{$level}. Soal: {$question}. SQL: {$code}\n";

        return $this->getSystemPrompt() . "\n"
            . $contextBlock
            . ($historyText ? "Riwayat:\n{$historyText}" : '')
            . "Siswa: {$userMessage}\nJawab sebagai PRIMM Bot, 2-3 kalimat:";
    }

    private function isCasualMessage(string $message): bool
    {
        $msg = mb_strtolower(trim($message));
        $casualPatterns = ['halo', 'hai', 'hi', 'hello', 'hey', 'siapa kamu', 'siapa namamu', 'nama kamu', 'namamu', 'perkenalan', 'tes', 'test', 'coba', 'hei', 'selamat'];
        foreach ($casualPatterns as $pattern) {
            if (str_contains($msg, $pattern)) return true;
        }
        return mb_strlen($msg) <= 5; // Pesan sangat pendek dianggap kasual
    }

    // ─── System Prompt ────────────────────────────────────────────────────────────

    private function getSystemPrompt(): string
    {
        return 'Kamu adalah PRIMM Bot, asisten belajar SQL untuk siswa SMK kelas XI. '
            . 'Jika siswa menyapa atau berkenalan, balas dengan ramah dan natural. '
            . 'Jika siswa bertanya soal SQL/materi, gunakan scaffolding Socratic: ajukan pertanyaan pemantik yang mengarahkan siswa berpikir sendiri. '
            . 'LARANGAN MUTLAK: JANGAN pernah memberikan jawaban, solusi, kode SQL yang benar, atau penjelasan yang mengungkap jawaban secara langsung. '
            . 'Hanya berikan clue/petunjuk arah berupa pertanyaan atau hint tanpa menyebut solusinya. '
            . 'Maks 3 kalimat, Bahasa Indonesia ramah.';
    }

    // ─── Core API Call ────────────────────────────────────────────────────────────

    private function call(string $prompt, int $maxTokens = 256): ?string
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $response = Http::timeout(20)
                ->withToken($this->apiKey)
                ->post($this->apiUrl, [
                    'model'       => $this->model,
                    'messages'    => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                    'max_tokens'  => $maxTokens,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            Log::warning('Groq API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Groq call failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    private function parseEvaluation(string $response, string $stage): array
    {
        preg_match('/\{.*?\}/s', $response, $matches);

        if (!empty($matches[0])) {
            $data = json_decode($matches[0], true);
            if ($data && isset($data['score'], $data['is_correct'], $data['feedback'])) {
                return [
                    'score'      => (int) $data['score'],
                    'is_correct' => (bool) $data['is_correct'],
                    'feedback'   => $data['feedback'],
                ];
            }
        }

        return $this->fallbackEvaluation($stage, null);
    }

    private function getLevelFocus(string $level): string
    {
        return match ($level) {
            'atoms'     => 'elemen terkecil query: karakter, tanda baca, kata kunci individual (SELECT, FROM, JOIN, ON, titik sebagai pemisah tabel.kolom)',
            'blocks'    => 'baris/klausa query: fungsi setiap klausa, urutan penulisan, kondisi ON dalam JOIN',
            'relations' => 'relasi antar tabel: Primary Key, Foreign Key, dan cara tabel dihubungkan melalui JOIN',
            'macro'     => 'konteks keseluruhan: kapan dan mengapa query jenis ini digunakan di dunia nyata',
            default     => 'analisis query SQL secara umum',
        };
    }

    private function fallbackFeedback(string $stage): string
    {
        return match ($stage) {
            'predict'     => 'Coba perhatikan query dengan teliti. Kolom apa saja yang dipilih oleh SELECT? Tabel mana yang digabungkan oleh JOIN?',
            'run'         => 'Bandingkan hasil yang muncul dengan prediksimu sebelumnya. Apakah ada yang berbeda atau mengejutkan?',
            'investigate' => 'Coba analisis lebih dalam. Perhatikan setiap bagian query dan pikirkan fungsi masing-masing.',
            'modify'      => 'Periksa apakah query-mu sudah sesuai instruksi. Coba jalankan dan bandingkan hasilnya.',
            'make'        => 'Pastikan query-mu sudah menggunakan SELECT, FROM, JOIN, dan ON dengan benar sesuai perintah.',
            default       => 'Coba periksa kembali jawabanmu dan gunakan tombol Cek untuk mendapat petunjuk.',
        };
    }

    private function fallbackEvaluation(string $stage, ?string $answerText): array
    {
        $answer = strtolower($answerText ?? '');

        if (strlen($answer) < 15) {
            return [
                'score'      => 20,
                'is_correct' => false,
                'feedback'   => 'Jawaban terlalu singkat. Gunakan tombol Cek terlebih dahulu untuk mendapat petunjuk, lalu perbaiki jawabanmu.',
            ];
        }

        $keywords = ['join', 'tabel', 'kolom', 'data', 'select', 'output', 'hasil', 'relasi', 'primary', 'foreign', 'gabung'];
        $matched  = count(array_filter($keywords, fn($k) => str_contains($answer, $k)));
        $score    = min(65 + ($matched * 5), 100);

        return [
            'score'      => $score,
            'is_correct' => $score >= 70,
            'feedback'   => $score >= 70
                ? 'Jawaban diterima! Pemahaman kamu tentang SQL JOIN sudah cukup baik.'
                : 'Coba elaborasi lebih lanjut dengan menyebut konsep SQL yang relevan seperti tabel, kolom, atau JOIN.',
        ];
    }
}
