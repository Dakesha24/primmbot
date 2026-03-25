<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::withCount('kelas')->orderBy('name')->get();
        return view('admin.schools.index', compact('schools'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:schools,name']);
        School::create($request->only('name'));
        return redirect()->route('admin.schools.index')->with('success', 'Sekolah berhasil ditambahkan.');
    }

    public function update(Request $request, School $school)
    {
        $request->validate(['name' => 'required|string|max:255|unique:schools,name,' . $school->id]);
        $school->update($request->only('name'));
        return redirect()->route('admin.schools.index')->with('success', 'Sekolah berhasil diperbarui.');
    }

    public function destroy(School $school)
    {
        $school->delete();
        return redirect()->route('admin.schools.index')->with('success', 'Sekolah berhasil dihapus.');
    }
}
