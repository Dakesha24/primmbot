<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SandboxDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SandboxDatabaseController extends Controller
{
    public function index()
    {
        $databases = SandboxDatabase::withCount('sandboxTables')->orderBy('created_at', 'desc')->get();
        return view('admin.sandbox.index', compact('databases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $prefix = Str::slug($request->name, '_');

        // Pastikan prefix unik
        $original = $prefix;
        $counter = 1;
        while (SandboxDatabase::where('prefix', $prefix)->exists()) {
            $prefix = $original . '_' . $counter++;
        }

        SandboxDatabase::create([
            'name' => $request->name,
            'prefix' => $prefix,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.sandbox.index')->with('success', 'Database "' . $request->name . '" berhasil dibuat.');
    }

    public function update(Request $request, SandboxDatabase $sandbox)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $sandbox->update($request->only('name', 'description'));

        return redirect()->route('admin.sandbox.index')->with('success', 'Database berhasil diperbarui.');
    }

    public function destroy(SandboxDatabase $sandbox)
    {
        // Hapus semua tabel sungguhan di primmbot_sandbox
        foreach ($sandbox->sandboxTables as $table) {
            DB::connection('sandbox')->statement("DROP TABLE IF EXISTS `{$table->table_name}`");
        }

        $sandbox->delete();

        return redirect()->route('admin.sandbox.index')->with('success', 'Database beserta semua tabelnya berhasil dihapus.');
    }

    public function show(SandboxDatabase $sandbox)
    {
        $sandbox->load('sandboxTables');

        // Ambil jumlah row per tabel
        $tableData = $sandbox->sandboxTables->map(function ($table) {
            try {
                $count = DB::connection('sandbox')->table($table->table_name)->count();
            } catch (\Exception $e) {
                $count = 0;
            }
            $table->row_count = $count;
            return $table;
        });

        return view('admin.sandbox.show', compact('sandbox', 'tableData'));
    }

    public function previewApi(SandboxDatabase $sandbox)
    {
        $tables = $sandbox->sandboxTables()->orderBy('order')->get();

        $result = [];
        foreach ($tables as $table) {
            try {
                $columns = DB::connection('sandbox')->getSchemaBuilder()->getColumnListing($table->table_name);
                $rows = DB::connection('sandbox')->table($table->table_name)->limit(10)->get();
                $result[] = [
                    'display_name' => $table->display_name,
                    'table_name' => $table->table_name,
                    'columns' => $columns,
                    'rows' => $rows,
                    'total' => DB::connection('sandbox')->table($table->table_name)->count(),
                ];
            } catch (\Exception $e) {
                $result[] = [
                    'display_name' => $table->display_name,
                    'table_name' => $table->table_name,
                    'columns' => [],
                    'rows' => [],
                    'total' => 0,
                ];
            }
        }

        return response()->json($result);
    }
}
