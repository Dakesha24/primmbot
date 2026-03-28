<?php

namespace App\Services\AI;

use App\Models\Activity;
use Illuminate\Support\Facades\DB;

class ContextLoader
{
    public function load(Activity $activity): array
    {
        $materials = $activity->chapter
            ->lessonMaterials()
            ->where('type', 'ringkasan_materi')
            ->orderBy('order')
            ->get()
            ->map(fn($m) => strip_tags($m->content))
            ->filter()
            ->implode("\n\n");

        // Potong maksimal 800 karakter agar hemat token
        $materials = mb_substr(trim($materials), 0, 800);

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
                    } catch (\Exception) {
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

    public function formatMaterials(string $materials): string
    {
        if (empty(trim($materials))) return '';
        return "[MATERI]\n{$materials}\n[/MATERI]";
    }

    public function formatSandboxTables(array $sandboxTables): string
    {
        if (empty($sandboxTables)) return '';

        $text = "[DB]\n";
        foreach ($sandboxTables as $displayName => $columns) {
            $cols = implode(', ', array_map(function ($c) {
                $suffix = match($c['key']) {
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
