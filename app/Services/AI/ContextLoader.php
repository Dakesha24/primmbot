<?php

namespace App\Services\AI;

use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Memuat konteks yang akan disertakan dalam prompt AI:
 *   1. Ringkasan materi dari chapter aktivitas (teks, dipotong agar hemat token)
 *   2. Skema tabel sandbox yang digunakan aktivitas (nama kolom, tipe, key)
 */
class ContextLoader
{
    public function load(Activity $activity): array
    {
        // ── 1. Ringkasan materi ───────────────────────────────────────────────
        $materials = $activity->chapter
            ->lessonMaterials()
            ->where('type', 'ringkasan_materi')
            ->orderBy('order')
            ->get()
            ->map(fn($m) => strip_tags($m->content))
            ->filter()
            ->implode("\n\n");

        // Potong agar tidak melebihi batas token yang dikirim ke AI
        $materials = mb_substr(trim($materials), 0, config('ai.material_context_limit', 800));

        // ── 2. Struktur tabel sandbox ─────────────────────────────────────────
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
                        // Tabel fisik belum dibuat di sandbox, atau ada masalah koneksi.
                        // Log warning agar admin bisa mendeteksi jika terjadi berulang.
                        Log::warning('ContextLoader: Gagal DESCRIBE tabel sandbox', [
                            'table' => $table->table_name,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        return [
            'materials'     => $materials,
            'sandboxTables' => $sandboxTables,
        ];
    }

    /**
     * Format ringkasan materi ke dalam blok teks untuk prompt.
     * Contoh output: "[MATERI]\n...\n[/MATERI]"
     */
    public function formatMaterials(string $materials): string
    {
        if (empty(trim($materials))) return '';
        return "[MATERI]\n{$materials}\n[/MATERI]";
    }

    /**
     * Format struktur tabel sandbox ke dalam blok teks untuk prompt.
     * Contoh output: "[DB]\nproducts: id(PK), name, price\n[/DB]"
     */
    public function formatSandboxTables(array $sandboxTables): string
    {
        if (empty($sandboxTables)) return '';

        $text = "[DB]\n";
        foreach ($sandboxTables as $displayName => $columns) {
            $cols = implode(', ', array_map(function ($c) {
                $suffix = match ($c['key']) {
                    'PRI'   => '(PK)',
                    'MUL'   => '(FK)',
                    default => '',
                };
                return $c['name'] . $suffix;
            }, $columns));
            $text .= "{$displayName}: {$cols}\n";
        }

        return rtrim($text) . "\n[/DB]";
    }
}
