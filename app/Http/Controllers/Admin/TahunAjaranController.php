<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahunAjaranList = TahunAjaran::withCount('kelas')->orderByDesc('name')->get();
        return view('admin.tahun-ajaran.index', compact('tahunAjaranList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:20|unique:tahun_ajaran,name',
            'is_active' => 'boolean',
        ]);
        TahunAjaran::create(['name' => $request->name, 'is_active' => $request->boolean('is_active')]);
        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $request->validate([
            'name'      => 'required|string|max:20|unique:tahun_ajaran,name,' . $tahunAjaran->id,
            'is_active' => 'boolean',
        ]);
        $tahunAjaran->update(['name' => $request->name, 'is_active' => $request->boolean('is_active')]);
        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->delete();
        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    public function toggleActive(TahunAjaran $tahunAjaran)
    {
        // Nonaktifkan semua dulu, lalu aktifkan yang dipilih
        TahunAjaran::query()->update(['is_active' => false]);
        $tahunAjaran->update(['is_active' => true]);
        return redirect()->route('admin.tahun-ajaran.index')->with('success', "Tahun ajaran {$tahunAjaran->name} diaktifkan.");
    }
}
