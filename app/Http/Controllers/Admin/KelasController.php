<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\School;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelasList    = Kelas::with(['school', 'tahunAjaran'])->orderBy('school_id')->orderBy('tahun_ajaran_id')->orderBy('name')->get();
        $schools      = School::orderBy('name')->get();
        $tahunAjaranList = TahunAjaran::orderByDesc('name')->get();
        return view('admin.kelas.index', compact('kelasList', 'schools', 'tahunAjaranList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_id'       => 'required|exists:schools,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'name'            => 'required|string|max:100',
        ]);

        Kelas::create($request->only('school_id', 'tahun_ajaran_id', 'name'));
        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'school_id'       => 'required|exists:schools,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'name'            => 'required|string|max:100',
        ]);

        $kela->update($request->only('school_id', 'tahun_ajaran_id', 'name'));
        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        $kela->delete();
        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
