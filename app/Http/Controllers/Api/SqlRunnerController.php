<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SandboxTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class SqlRunnerController extends Controller
{
    public function run(Request $request): JsonResponse
    {
        // Validasi wajib menyertakan database_id
        $request->validate([
            'query' => ['required', 'string', 'max:5000'],
            'database_id' => ['required', 'integer'],
        ]);

        $query = trim($request->input('query'));
        $queryType = $this->getQueryType($query);

        // --- VALIDASI KEAMANAN: Cek Hak Akses Semua Tabel ---
        $tableNames = $this->extractTableNames($query);
        $tableName = $tableNames[0] ?? null; // untuk keperluan SELECT * setelah DML
        if (!empty($tableNames)) {
            $allowedTables = SandboxTable::where('sandbox_database_id', $request->database_id)
                ->pluck('table_name')
                ->map(fn($t) => strtolower($t))
                ->toArray();

            foreach ($tableNames as $tbl) {
                $tblLower = strtolower(str_replace('`', '', $tbl));
                if (!in_array($tblLower, $allowedTables)) {
                    return response()->json([
                        'success' => false,
                        'error' => "Akses ditolak: Tabel '{$tbl}' tidak diizinkan di aktivitas ini."
                    ], 403);
                }
            }
        }

        try {
            if ($queryType === 'select') {
                $results = DB::connection('sandbox')->select($query);
                $rows = array_map(fn($row) => (array) $row, $results);
                $columns = !empty($rows) ? array_keys($rows[0]) : [];

                return response()->json([
                    'success' => true,
                    'type' => 'select',
                    'columns' => $columns,
                    'rows' => $rows,
                    'row_count' => count($rows),
                    'message' => null,
                ]);
            }

            $db = DB::connection('sandbox');
            $db->beginTransaction();

            try {
                $affected = $db->affectingStatement($query);
                $rows = [];
                $columns = [];

                if ($tableName) {
                    try {
                        $results = $db->select("SELECT * FROM `{$tableName}` LIMIT 50");
                        $rows = array_map(fn($row) => (array) $row, $results);
                        $columns = !empty($rows) ? array_keys($rows[0]) : [];
                    } catch (\Exception $e) {
                    }
                }

                $db->rollBack();

                $typeLabel = strtoupper($queryType);
                $message = "{$typeLabel} berhasil dijalankan ({$affected} baris terpengaruh). Data direset otomatis.";

                return response()->json([
                    'success' => true,
                    'type' => $queryType,
                    'columns' => $columns,
                    'rows' => $rows,
                    'row_count' => count($rows),
                    'affected' => $affected,
                    'message' => $message,
                ]);
            } catch (\Exception $e) {
                $db->rollBack();
                return response()->json([
                    'success' => false,
                    'error' => 'SQL Error: ' . $this->sanitizeError($e->getMessage()),
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'SQL Error: ' . $this->sanitizeError($e->getMessage()),
            ], 422);
        }
    }

    // tables() dibiarkan jika sewaktu-waktu dibutuhkan admin/fitur lain, 
    // tapi sisi siswa sudah tidak memakai endpoint ini.
    public function tables(): JsonResponse
    {
        try {
            $tables = DB::connection('sandbox')->select('SHOW TABLES');
            $tableNames = array_map(fn($t) => array_values((array) $t)[0], $tables);

            $tablesWithColumns = [];
            foreach ($tableNames as $tableName) {
                if ($tableName === 'migrations') continue;
                $columns = DB::connection('sandbox')->select("DESCRIBE `{$tableName}`");
                $tablesWithColumns[$tableName] = array_map(fn($col) => [
                    'name' => $col->Field,
                    'type' => $col->Type,
                    'key' => $col->Key,
                ], $columns);
            }

            return response()->json(['success' => true, 'tables' => $tablesWithColumns]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Gagal mengambil struktur tabel.'], 500);
        }
    }

    private function getQueryType(string $query): string
    {
        $trimmed = strtoupper(ltrim($query));
        if (str_starts_with($trimmed, 'SELECT') || str_starts_with($trimmed, 'SHOW') || str_starts_with($trimmed, 'DESCRIBE') || str_starts_with($trimmed, 'EXPLAIN')) return 'select';
        if (str_starts_with($trimmed, 'INSERT')) return 'insert';
        if (str_starts_with($trimmed, 'UPDATE')) return 'update';
        if (str_starts_with($trimmed, 'DELETE')) return 'delete';
        if (str_starts_with($trimmed, 'CREATE')) return 'create';
        if (str_starts_with($trimmed, 'ALTER')) return 'alter';
        if (str_starts_with($trimmed, 'DROP')) return 'drop';
        if (str_starts_with($trimmed, 'TRUNCATE')) return 'truncate';
        return 'other';
    }

    private function extractTableNames(string $query): array
    {
        $tables = [];

        // INSERT INTO, UPDATE, DELETE FROM, CREATE/DROP/ALTER TABLE
        $singlePatterns = [
            '/\bINSERT\s+INTO\s+`?(\w+)`?/i',
            '/\bUPDATE\s+`?(\w+)`?/i',
            '/\bDELETE\s+FROM\s+`?(\w+)`?/i',
            '/\bTABLE\s+`?(\w+)`?/i',
        ];
        foreach ($singlePatterns as $pattern) {
            if (preg_match($pattern, $query, $m)) {
                $tables[] = $m[1];
            }
        }

        // FROM tabel1, tabel2 (comma-separated)
        if (preg_match('/\bFROM\s+([\w\s`,]+?)(?:\bWHERE\b|\bJOIN\b|\bORDER\b|\bGROUP\b|\bLIMIT\b|\bHAVING\b|$)/i', $query, $m)) {
            foreach (preg_split('/\s*,\s*/', trim($m[1])) as $part) {
                $tbl = preg_replace('/\s+\w+$/', '', trim($part)); // hapus alias
                $tbl = trim($tbl, '` ');
                if ($tbl !== '') $tables[] = $tbl;
            }
        }

        // JOIN tabel (semua jenis JOIN)
        preg_match_all('/\bJOIN\s+`?(\w+)`?/i', $query, $joinMatches);
        $tables = array_merge($tables, $joinMatches[1]);

        return array_unique(array_filter($tables));
    }

    private function sanitizeError(string $message): string
    {
        $message = preg_replace('/SQLSTATE\[\w+\]:?\s*/', '', $message);
        return str_replace(['primmbot_sandbox.', '`'], '', $message);
    }
}
