<?php

namespace App\Services\AI;

class EvaluationResult
{
    public function __construct(
        public int    $keruntutan,
        public int    $berargumen,
        public int    $kesimpulan,
        public int    $total,
        public bool   $isCorrect,
        public string $feedback,
    ) {}
}
