<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    // ── Konstanta Stage & Level ───────────────────────────────────────────────
    // Didefinisikan di sini agar tidak tersebar sebagai magic string di banyak file.
    // Gunakan Activity::STAGE_ORDER, Activity::LEVEL_INVESTIGATE, dst.

    /** Urutan tahap PRIMM — urutan ini menentukan alur progress siswa */
    const STAGE_ORDER = ['predict', 'run', 'investigate', 'modify', 'make'];

    /** Urutan level untuk tahap Investigate */
    const LEVEL_INVESTIGATE = ['atoms', 'blocks', 'relations', 'macro'];

    /** Urutan level untuk tahap Modify dan Make */
    const LEVEL_TASK = ['mudah', 'sedang', 'tantang'];

    /** Nilai KKM default jika tidak diatur per aktivitas */
    const DEFAULT_KKM = 70;

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
        'kkm',
        'reference_answer',
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
