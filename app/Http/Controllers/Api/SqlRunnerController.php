<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use App\Models\SandboxTable;
use App\Services\AI\TableNameRewriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class SqlRunnerController extends Controller
{
    public function run(Request $request): JsonResponse
    {
        $request->validate([
            'query'       => ['required', 'string', 'max:5000'],
            'database_id' => ['required', 'integer', 'exists:sandbox_databases,id'],
        ]);

        // ── Validasi Akses: siswa hanya boleh query database yang ada di course-nya ──
        // Cegah siswa mengirim database_id sembarang dan mengakses sandbox database lain.
        $enrolledCourseIds = CourseEnrollment::where('user_id', Auth::id())
            ->pluck('course_id');

        $hasAccess = \App\Models\Activity::where('sandbox_database_id', $request->database_id)
            ->whereHas('chapter', fn($q) => $q->whereIn('course_id', $enrolledCourseIds))
            ->exists();

        if (!$hasAccess) {
            Log::warning('Akses sandbox database ditolak', [
                'user_id'     => Auth::id(),
                'database_id' => $request->database_id,
            ]);
            return response()->json(['success' => false, 'error' => 'Akses ditolak.'], 403);
        }
        // ─────────────────────────────────────────────────────────────────────────────

        $query = trim($request->input('query'));

        // Load semua tabel yang diizinkan untuk database ini
        $sandboxTables = SandboxTable::where('sandbox_database_id', $request->database_id)->get();

        // Ganti nama pendek siswa dengan nama lengkap di sandbox
        // Contoh: "products" → "toko_ayam__products"
        $tableMap = TableNameRewriter::buildMap($sandboxTables);
        $query    = TableNameRewriter::rewrite($query, $tableMap);

        $queryType = $this->getQueryType($query);

        // ── Validasi Keamanan: Cek semua tabel dalam query ada di daftar yang diizinkan ──
        $tableNames = $this->extractTableNames($query);
        $tableName  = $tableNames[0] ?? null; // untuk SELECT * preview setelah DML

        if (!empty($tableNames)) {
            $allowedTables = $sandboxTables->pluck('table_name')
                ->map(fn($t) => strtolower($t))
                ->toArray();

            foreach ($tableNames as $tbl) {
                $tblLower = strtolower(str_replace('`', '', $tbl));
                if (!in_array($tblLower, $allowedTables)) {
                    return response()->json([
                        'success' => false,
                        'error'   => "Akses ditolak: Tabel '{$tbl}' tidak diizinkan di aktivitas ini.",
                    ], 403);
                }
            }
        }

        try {
            // ── SELECT / SHOW / DESCRIBE / EXPLAIN: langsung jalankan, tidak perlu rollback ──
            if ($queryType === 'select') {
                $results = DB::connection('sandbox')->select($query);
                $rows    = array_map(fn($row) => (array) $row, $results);
                $columns = !empty($rows) ? array_keys($rows[0]) : [];

                return response()->json([
                    'success'   => true,
                    'type'      => 'select',
                    'columns'   => $columns,
                    'rows'      => $rows,
                    'row_count' => count($rows),
                    'message'   => null,
                ]);
            }

            // ── INSERT / UPDATE / DELETE / dll: jalankan dalam transaction lalu rollback ──
            // Data siswa tidak pernah benar-benar tersimpan — ini mode "latihan"
            $db = DB::connection('sandbox');
            $db->beginTransaction();

            try {
                $affected = $db->affectingStatement($query);
                $rows     = [];
                $columns  = [];

                // Tampilkan isi tabel setelah operasi sebagai preview (SELECT *)
                if ($tableName) {
                    try {
                        $limit   = config('ai.sandbox_preview_limit', 50);
                        $results = $db->select("SELECT * FROM `{$tableName}` LIMIT {$limit}");
                        $rows    = array_map(fn($row) => (array) $row, $results);
                        $columns = !empty($rows) ? array_keys($rows[0]) : [];
                    } catch (\Exception $e) {
                        // Preview gagal — bukan error kritis, tabel mungkin baru dihapus/rename
                        Log::warning('Gagal preview tabel setelah DML', [
                            'table' => $tableName,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Rollback setelah preview — data tidak disimpan permanen
                $db->rollBack();

                $typeLabel = strtoupper($queryType);
                $message   = "{$typeLabel} berhasil dijalankan ({$affected} baris terpengaruh). Data direset otomatis.";

                return response()->json([
                    'success'   => true,
                    'type'      => $queryType,
                    'columns'   => $columns,
                    'rows'      => $rows,
                    'row_count' => count($rows),
                    'affected'  => $affected,
                    'message'   => $message,
                ]);
            } catch (\Exception $e) {
                $db->rollBack();
                return response()->json([
                    'success' => false,
                    'error'   => 'SQL Error: ' . $this->sanitizeError($e->getMessage()),
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => 'SQL Error: ' . $this->sanitizeError($e->getMessage()),
            ], 422);
        }
    }

    // tables() — endpoint opsional untuk admin/debugging, bukan dipakai sisi siswa
    public function tables(): JsonResponse
    {
        try {
            $tables     = DB::connection('sandbox')->select('SHOW TABLES');
            $tableNames = array_map(fn($t) => array_values((array) $t)[0], $tables);

            $tablesWithColumns = [];
            foreach ($tableNames as $tableName) {
                if ($tableName === 'migrations') continue;
                $columns = DB::connection('sandbox')->select("DESCRIBE `{$tableName}`");
                $tablesWithColumns[$tableName] = array_map(fn($col) => [
                    'name' => $col->Field,
                    'type' => $col->Type,
                    'key'  => $col->Key,
                ], $columns);
            }

            return response()->json(['success' => true, 'tables' => $tablesWithColumns]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Gagal mengambil struktur tabel.'], 500);
        }
    }

    /**
     * Deteksi jenis query dari kata kunci pertama (SELECT, INSERT, dll).
     */
    private function getQueryType(string $query): string
    {
        $trimmed = strtoupper(ltrim($query));

        if (str_starts_with($trimmed, 'SELECT')   ||
            str_starts_with($trimmed, 'SHOW')      ||
            str_starts_with($trimmed, 'DESCRIBE')  ||
            str_starts_with($trimmed, 'EXPLAIN'))  return 'select';

        if (str_starts_with($trimmed, 'INSERT'))   return 'insert';
        if (str_starts_with($trimmed, 'UPDATE'))   return 'update';
        if (str_starts_with($trimmed, 'DELETE'))   return 'delete';
        if (str_starts_with($trimmed, 'CREATE'))   return 'create';
        if (str_starts_with($trimmed, 'ALTER'))    return 'alter';
        if (str_starts_with($trimmed, 'DROP'))     return 'drop';
        if (str_starts_with($trimmed, 'TRUNCATE')) return 'truncate';

        return 'other';
    }

    /**
     * Ekstrak semua nama tabel yang digunakan dalam query.
     * Digunakan untuk memvalidasi bahwa siswa hanya mengakses tabel yang diizinkan.
     */
    private function extractTableNames(string $query): array
    {
        $tables = [];

        // Pola satu tabel: INSERT INTO, UPDATE, DELETE FROM, CREATE/DROP/ALTER TABLE
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

        // Pola multi-tabel: FROM tabel1, tabel2
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

    /**
     * Bersihkan pesan error SQL sebelum ditampilkan ke siswa.
     * Hapus prefix teknis (SQLSTATE) dan nama database internal.
     */
    private function sanitizeError(string $message): string
    {
        // Hapus prefix SQLSTATE yang tidak relevan bagi siswa
        $message = preg_replace('/SQLSTATE\[\w+\]:?\s*/', '', $message);

        // Hapus nama database sandbox dari pesan error (jangan expose nama internal)
        $sandboxDb = config('database.connections.sandbox.database', 'primmbot_sandbox');
        return str_replace(["{$sandboxDb}.", '`'], '', $message);
    }
}
