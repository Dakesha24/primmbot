<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SandboxDatabase;
use App\Models\SandboxTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SandboxTableController extends Controller
{
    public function create(SandboxDatabase $sandbox)
    {
        // Ambil semua tabel di database ini untuk referensi FK
        $existingTables = $sandbox->sandboxTables()->get();

        // Ambil kolom per tabel untuk dropdown FK
        $tableColumns = [];
        foreach ($existingTables as $tbl) {
            try {
                $tableColumns[$tbl->table_name] = DB::connection('sandbox')
                    ->getSchemaBuilder()
                    ->getColumnListing($tbl->table_name);
            } catch (\Exception $e) {
                $tableColumns[$tbl->table_name] = [];
            }
        }

        return view('admin.sandbox.create-table', compact('sandbox', 'existingTables', 'tableColumns'));
    }

    public function store(Request $request, SandboxDatabase $sandbox)
    {
        $request->validate([
            'display_name' => 'required|string|max:100',
            'columns' => 'required|array|min:1',
            'columns.*.name' => 'required|string|max:64',
            'columns.*.type' => 'required|string',
            'columns.*.length' => 'nullable|integer|min:1',
            'columns.*.nullable' => 'nullable|boolean',
            'columns.*.primary' => 'nullable|boolean',
            'columns.*.fk_table' => 'nullable|string',
            'columns.*.fk_column' => 'nullable|string',
        ]);

        $displayName = Str::slug($request->display_name, '_');
        $tableName = $sandbox->prefix . '__' . $displayName;

        if (SandboxTable::where('table_name', $tableName)->exists()) {
            return back()->withErrors(['display_name' => 'Tabel "' . $displayName . '" sudah ada di database ini.'])->withInput();
        }

        $columnDefs = [];
        $fkDefs = [];

        foreach ($request->columns as $col) {
            $type = $col['type'];
            if (!empty($col['length']) && in_array($type, ['VARCHAR', 'CHAR', 'INT'])) {
                $type .= '(' . $col['length'] . ')';
            } elseif (in_array($type, ['VARCHAR', 'CHAR']) && empty($col['length'])) {
                $type .= '(255)';
            }

            $def = "`{$col['name']}` {$type}";

            if (!empty($col['primary'])) {
                if ($col['type'] === 'INT') {
                    $def .= ' AUTO_INCREMENT PRIMARY KEY';
                } else {
                    $def .= ' PRIMARY KEY';
                }
            } elseif (empty($col['nullable'])) {
                $def .= ' NOT NULL';
            } else {
                $def .= ' NULL';
            }

            $columnDefs[] = $def;

            // Foreign Key
            if (!empty($col['fk_table']) && !empty($col['fk_column'])) {
                $fkDefs[] = "FOREIGN KEY (`{$col['name']}`) REFERENCES `{$col['fk_table']}`(`{$col['fk_column']}`)";
            }
        }

        $allDefs = array_merge($columnDefs, $fkDefs);
        $sql = "CREATE TABLE `{$tableName}` (" . implode(', ', $allDefs) . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        try {
            DB::connection('sandbox')->statement($sql);
        } catch (\Exception $e) {
            return back()->withErrors(['sql' => 'Gagal membuat tabel: ' . $e->getMessage()])->withInput();
        }

        SandboxTable::create([
            'sandbox_database_id' => $sandbox->id,
            'table_name' => $tableName,
            'display_name' => $request->display_name,
            'order' => $sandbox->sandboxTables()->count(),
        ]);

        return redirect()->route('admin.sandbox.show', $sandbox)->with('success', 'Tabel "' . $request->display_name . '" berhasil dibuat.');
    }

    public function show(SandboxDatabase $sandbox, SandboxTable $table)
    {
        // Ambil kolom
        $columns = DB::connection('sandbox')->getSchemaBuilder()->getColumnListing($table->table_name);

        // Ambil data
        $rows = DB::connection('sandbox')->table($table->table_name)->limit(200)->get();

        return view('admin.sandbox.show-table', compact('sandbox', 'table', 'columns', 'rows'));
    }

    public function insertRow(Request $request, SandboxDatabase $sandbox, SandboxTable $table)
    {
        $columns = DB::connection('sandbox')->getSchemaBuilder()->getColumnListing($table->table_name);
        $autoColumns = ['id', 'created_at', 'updated_at'];

        $data = [];
        foreach ($columns as $col) {
            $value = $request->input('col_' . $col);

            if (in_array($col, $autoColumns) && ($value === null || $value === '')) {
                if (in_array($col, ['created_at', 'updated_at'])) {
                    $data[$col] = now();
                }
                continue;
            }

            if ($value !== null && $value !== '') {
                $data[$col] = $value;
            }
        }

        if (empty($data)) {
            return back()->withErrors(['row' => 'Minimal isi 1 kolom.']);
        }

        try {
            DB::connection('sandbox')->table($table->table_name)->insert($data);
        } catch (\Exception $e) {
            return back()->withErrors(['row' => 'Gagal insert: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.sandbox.table.show', [$sandbox, $table])->with('success', 'Data berhasil ditambahkan.');
    }

    public function updateRow(Request $request, SandboxDatabase $sandbox, SandboxTable $table)
    {
        $columns = DB::connection('sandbox')->getSchemaBuilder()->getColumnListing($table->table_name);
        $rowIndex = $request->input('row_index');

        try {
            $rows = DB::connection('sandbox')->table($table->table_name)->get();
            $oldRow = $rows[$rowIndex] ?? null;

            if (!$oldRow) {
                return back()->withErrors(['row' => 'Baris tidak ditemukan.']);
            }

            $data = [];
            foreach ($columns as $col) {
                if ($col === 'id') continue;
                $value = $request->input('edit_' . $col);
                $data[$col] = ($value !== null && $value !== '') ? $value : null;
            }

            // Auto-update updated_at
            if (in_array('updated_at', $columns)) {
                $data['updated_at'] = now();
            }

            $query = DB::connection('sandbox')->table($table->table_name);
            foreach ((array) $oldRow as $col => $val) {
                $query->where($col, $val);
            }
            $query->limit(1)->update($data);
        } catch (\Exception $e) {
            return back()->withErrors(['row' => 'Gagal update: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.sandbox.table.show', [$sandbox, $table])->with('success', 'Data berhasil diperbarui.');
    }

    public function deleteRow(Request $request, SandboxDatabase $sandbox, SandboxTable $table)
    {
        $rowIndex = $request->input('row_index');

        try {
            $rows = DB::connection('sandbox')->table($table->table_name)->get();
            $row = $rows[$rowIndex] ?? null;

            if ($row) {
                $query = DB::connection('sandbox')->table($table->table_name);
                foreach ((array) $row as $col => $val) {
                    $query->where($col, $val);
                }
                $query->limit(1)->delete();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['row' => 'Gagal hapus: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.sandbox.table.show', [$sandbox, $table])->with('success', 'Data berhasil dihapus.');
    }

    public function destroy(SandboxDatabase $sandbox, SandboxTable $table)
    {
        try {
            DB::connection('sandbox')->statement("DROP TABLE IF EXISTS `{$table->table_name}`");
        } catch (\Exception $e) {
            return back()->withErrors(['table' => 'Gagal hapus tabel: ' . $e->getMessage()]);
        }

        $table->delete();

        return redirect()->route('admin.sandbox.show', $sandbox)->with('success', 'Tabel berhasil dihapus.');
    }

    public function editStructure(SandboxDatabase $sandbox, SandboxTable $table)
    {
        // Ambil detail kolom
        $columns = DB::connection('sandbox')
            ->select("SHOW COLUMNS FROM `{$table->table_name}`");

        // Ambil foreign keys
        $fks = DB::connection('sandbox')
            ->select(
                "SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                      FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                      WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL",
                [config('database.connections.sandbox.database'), $table->table_name]
            );

        $fkMap = [];
        foreach ($fks as $fk) {
            $fkMap[$fk->COLUMN_NAME] = $fk->REFERENCED_TABLE_NAME . '.' . $fk->REFERENCED_COLUMN_NAME;
        }

        // Tabel lain untuk FK
        $existingTables = $sandbox->sandboxTables()->where('id', '!=', $table->id)->get();
        $tableColumns = [];
        foreach ($existingTables as $tbl) {
            try {
                $tableColumns[$tbl->table_name] = DB::connection('sandbox')
                    ->getSchemaBuilder()
                    ->getColumnListing($tbl->table_name);
            } catch (\Exception $e) {
                $tableColumns[$tbl->table_name] = [];
            }
        }

        return view('admin.sandbox.edit-structure', compact('sandbox', 'table', 'columns', 'fkMap', 'existingTables', 'tableColumns'));
    }

    public function addColumn(Request $request, SandboxDatabase $sandbox, SandboxTable $table)
    {
        $request->validate([
            'name' => 'required|string|max:64',
            'type' => 'required|string',
            'length' => 'nullable|integer|min:1',
            'nullable' => 'nullable|boolean',
            'fk_table' => 'nullable|string',
            'fk_column' => 'nullable|string',
        ]);

        $type = $request->type;
        if (!empty($request->length) && in_array($type, ['VARCHAR', 'CHAR', 'INT'])) {
            $type .= '(' . $request->length . ')';
        } elseif (in_array($type, ['VARCHAR', 'CHAR']) && empty($request->length)) {
            $type .= '(255)';
        }

        $nullable = $request->nullable ? 'NULL' : 'NOT NULL';

        $sql = "ALTER TABLE `{$table->table_name}` ADD COLUMN `{$request->name}` {$type} {$nullable}";

        try {
            DB::connection('sandbox')->statement($sql);

            // FK
            if (!empty($request->fk_table) && !empty($request->fk_column)) {
                $fkName = 'fk_' . $table->table_name . '_' . $request->name;
                $fkSql = "ALTER TABLE `{$table->table_name}` ADD CONSTRAINT `{$fkName}` FOREIGN KEY (`{$request->name}`) REFERENCES `{$request->fk_table}`(`{$request->fk_column}`)";
                DB::connection('sandbox')->statement($fkSql);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['sql' => 'Gagal: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('admin.sandbox.table.structure', [$sandbox, $table])->with('success', 'Kolom "' . $request->name . '" berhasil ditambahkan.');
    }

    public function dropColumn(Request $request, SandboxDatabase $sandbox, SandboxTable $table)
    {
        $column = $request->input('column');

        try {
            // Hapus FK dulu jika ada
            $fks = DB::connection('sandbox')
                ->select(
                    "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                          WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL",
                    [config('database.connections.sandbox.database'), $table->table_name, $column]
                );

            foreach ($fks as $fk) {
                DB::connection('sandbox')->statement("ALTER TABLE `{$table->table_name}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            }

            DB::connection('sandbox')->statement("ALTER TABLE `{$table->table_name}` DROP COLUMN `{$column}`");
        } catch (\Exception $e) {
            return back()->withErrors(['sql' => 'Gagal hapus kolom: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.sandbox.table.structure', [$sandbox, $table])->with('success', 'Kolom "' . $column . '" berhasil dihapus.');
    }

    public function modifyColumn(Request $request, SandboxDatabase $sandbox, SandboxTable $table)
    {
        $request->validate([
            'old_name' => 'required|string',
            'new_name' => 'required|string|max:64',
            'type' => 'required|string',
            'length' => 'nullable|integer|min:1',
            'nullable' => 'nullable|boolean',
        ]);

        $type = $request->type;
        if (!empty($request->length) && in_array($type, ['VARCHAR', 'CHAR', 'INT'])) {
            $type .= '(' . $request->length . ')';
        } elseif (in_array($type, ['VARCHAR', 'CHAR']) && empty($request->length)) {
            $type .= '(255)';
        }

        $nullable = $request->nullable ? 'NULL' : 'NOT NULL';

        try {
            $sql = "ALTER TABLE `{$table->table_name}` CHANGE `{$request->old_name}` `{$request->new_name}` {$type} {$nullable}";
            DB::connection('sandbox')->statement($sql);
        } catch (\Exception $e) {
            return back()->withErrors(['sql' => 'Gagal: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.sandbox.table.structure', [$sandbox, $table])->with('success', 'Kolom berhasil diubah.');
    }
}
