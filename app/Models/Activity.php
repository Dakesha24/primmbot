<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'sandbox_database_id',
        'description',
        'stage',
        'level',
        'question_text',
        'code_snippet',
        'editor_default_code',
        'reference_sql',
        'expected_output',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'expected_output' => 'array',
        ];
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function aiInteractionLogs()
    {
        return $this->hasMany(AiInteractionLog::class);
    }

    // === Helpers ===

    public function hasLevel(): bool
    {
        return !is_null($this->level);
    }

    public function isStage(string $stage): bool
    {
        return $this->stage === $stage;
    }

    public function sandboxDatabase()
    {
        return $this->belongsTo(\App\Models\SandboxDatabase::class);
    }
}
