<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\LessonMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LessonMaterialController extends Controller
{
    public function create(Course $course, Chapter $chapter)
    {
        return view('admin.materials.create', compact('course', 'chapter'));
    }

    public function store(Request $request, Course $course, Chapter $chapter)
    {
        $request->validate([
            'type' => 'required|in:pendahuluan,petunjuk_belajar,tujuan,prasyarat,ringkasan_materi',
            'content' => 'required|string',
            'order' => 'required|integer|min:0',
        ]);

        $chapter->lessonMaterials()->create($request->only('type', 'content', 'order'));

        return redirect()
            ->route('admin.chapters.content', [$course, $chapter])
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    public function edit(Course $course, Chapter $chapter, LessonMaterial $material)
    {
        return view('admin.materials.edit', compact('course', 'chapter', 'material'));
    }

    public function update(Request $request, Course $course, Chapter $chapter, LessonMaterial $material)
    {
        $request->validate([
            'type' => 'required|in:pendahuluan,petunjuk_belajar,tujuan,prasyarat,ringkasan_materi',
            'content' => 'required|string',
            'order' => 'required|integer|min:0',
        ]);

        $material->update($request->only('type', 'content', 'order'));

        return redirect()
            ->route('admin.chapters.content', [$course, $chapter])
            ->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Course $course, Chapter $chapter, LessonMaterial $material)
    {
        $material->delete();

        return redirect()
            ->route('admin.chapters.content', [$course, $chapter])
            ->with('success', 'Materi berhasil dihapus.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ]);

        $file = $request->file('image');
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/materials';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $file->move($dir, $filename);
        $path = 'materials/' . $filename;

        return response()->json([
            'url' => asset($path),
        ]);
    }
}
