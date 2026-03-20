<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class SqlRunnerController extends Controller
{
    public function run(Request $request): JsonResponse
    {
        $request->validate([
            'query' => ['required', 'string', 'max:5000'],
        ]);

        $query = trim($request->input('query'));
        $queryType = $this->getQueryType($query);

        try {
            if ($queryType === 'select') {
                // SELECT langsung tanpa transaction
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

            // Query modifikasi: jalankan dalam transaction lalu rollback
            $db = DB::connection('sandbox');
            $db->beginTransaction();

            try {
                $affected = $db->affectingStatement($query);

                // Coba ambil data yang relevan setelah modifikasi
                $tableName = $this->extractTableName($query);
                $rows = [];
                $columns = [];

                if ($tableName) {
                    try {
                        $results = $db->select("SELECT * FROM `{$tableName}` LIMIT 50");
                        $rows = array_map(fn($row) => (array) $row, $results);
                        $columns = !empty($rows) ? array_keys($rows[0]) : [];
                    } catch (\Exception $e) {
                        // Tabel mungkin di-drop, tidak apa-apa
                    }
                }

                // Rollback agar data kembali seperti semula
                $db->rollBack();

                $typeLabel = strtoupper($queryType);
                $message = "{$typeLabel} berhasil dijalankan ({$affected} baris terpengaruh). Data telah direset otomatis.";

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

            return response()->json([
                'success' => true,
                'tables' => $tablesWithColumns,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengambil struktur tabel.',
            ], 500);
        }
    }

    private function getQueryType(string $query): string
    {
        $trimmed = strtoupper(ltrim($query));

        if (str_starts_with($trimmed, 'SELECT') || str_starts_with($trimmed, 'SHOW') || str_starts_with($trimmed, 'DESCRIBE') || str_starts_with($trimmed, 'EXPLAIN')) {
            return 'select';
        }
        if (str_starts_with($trimmed, 'INSERT')) return 'insert';
        if (str_starts_with($trimmed, 'UPDATE')) return 'update';
        if (str_starts_with($trimmed, 'DELETE')) return 'delete';
        if (str_starts_with($trimmed, 'CREATE')) return 'create';
        if (str_starts_with($trimmed, 'ALTER')) return 'alter';
        if (str_starts_with($trimmed, 'DROP')) return 'drop';
        if (str_starts_with($trimmed, 'TRUNCATE')) return 'truncate';

        return 'other';
    }

    private function extractTableName(string $query): ?string
    {
        $patterns = [
            '/\bINSERT\s+INTO\s+`?(\w+)`?/i',
            '/\bUPDATE\s+`?(\w+)`?/i',
            '/\bDELETE\s+FROM\s+`?(\w+)`?/i',
            '/\bFROM\s+`?(\w+)`?/i',
            '/\bTABLE\s+`?(\w+)`?/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $query, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    private function sanitizeError(string $message): string
    {
        $message = preg_replace('/SQLSTATE\[\w+\]:?\s*/', '', $message);
        return str_replace(['primmbot_sandbox.', '`'], '', $message);
    }
}