<?php

namespace App\Services\AI;

/**
 * Value object yang membungkus respons dari Groq API.
 *
 * Menyimpan konten teks beserta metadata penggunaan (token & waktu respons)
 * agar data monitoring bisa diteruskan ke AiInteractionLog.
 */
class GroqResponse
{
    public function __construct(
        public readonly string $content,
        public readonly int    $tokensUsed,
        public readonly float  $responseTime,
    ) {}
}
