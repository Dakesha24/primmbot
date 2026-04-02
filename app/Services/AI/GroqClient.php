<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * HTTP client untuk Groq API dengan mekanisme failover otomatis.
 *
 * Cara kerja:
 *   1. Coba key-1 (utama) terlebih dahulu.
 *   2. Jika gagal (rate limit, error server, atau exception jaringan),
 *      otomatis beralih ke key-2, key-3, dst.
 *   3. Jika semua key habis dicoba dan gagal, kembalikan null
 *      (caller bertanggung jawab menangani kondisi ini).
 *
 * Menambah key baru: cukup tambah GROQ_API_KEY_4 di .env
 * dan daftarkan di config/services.php — tidak perlu ubah file ini.
 */
class GroqClient
{
    private array  $apiKeys;
    private string $model;
    private string $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKeys = config('services.groq.api_keys', []);
        $this->model   = config('services.groq.model', 'llama-3.1-70b-versatile');
    }

    /**
     * Kirim prompt ke Groq dan kembalikan GroqResponse (konten + token + waktu).
     * Failover otomatis ke key berikutnya jika key saat ini gagal.
     *
     * @return GroqResponse|null  Respons AI, atau null jika semua key gagal.
     */
    public function call(string $prompt, int $maxTokens = 300, float $temperature = 0.2): ?GroqResponse
    {
        if (empty($this->apiKeys)) {
            Log::error('GroqClient: tidak ada API key yang dikonfigurasi di services.groq.api_keys.');
            return null;
        }

        foreach ($this->apiKeys as $index => $apiKey) {
            $keyLabel = 'key-' . ($index + 1);

            $response = $this->attemptCall($apiKey, $prompt, $maxTokens, $temperature);

            if ($response !== null) {
                return $response;
            }

            // Key ini gagal — catat dan coba key berikutnya (jika ada)
            $hasNextKey = isset($this->apiKeys[$index + 1]);
            Log::warning("GroqClient: {$keyLabel} gagal." . ($hasNextKey ? ' Beralih ke key berikutnya.' : ' Tidak ada key cadangan lagi.'));
        }

        Log::error('GroqClient: semua API key telah dicoba dan gagal.');
        return null;
    }

    /**
     * Satu percobaan request ke Groq menggunakan key tertentu.
     * Kembalikan GroqResponse jika sukses (berisi konten, token, waktu), null jika gagal.
     */
    private function attemptCall(
        string $apiKey,
        string $prompt,
        int    $maxTokens,
        float  $temperature,
    ): ?GroqResponse {
        $startTime = microtime(true);

        try {
            $httpResponse = Http::timeout(config('ai.timeout', 20))
                ->withToken($apiKey)
                ->post($this->apiUrl, [
                    'model'       => $this->model,
                    'messages'    => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => $temperature,
                    'max_tokens'  => $maxTokens,
                ]);

            $responseTime = round(microtime(true) - $startTime, 2);

            if ($httpResponse->successful()) {
                return new GroqResponse(
                    content:      $httpResponse->json('choices.0.message.content') ?? '',
                    tokensUsed:   $httpResponse->json('usage.total_tokens') ?? 0,
                    responseTime: $responseTime,
                );
            }

            Log::warning('GroqClient: API mengembalikan error.', [
                'http_status' => $httpResponse->status(),
                'body'        => $httpResponse->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('GroqClient: exception saat request.', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
