<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Course;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function index(Course $course)
    {
        $chapters = $course->chapters()
            ->withCount(['lessonMaterials', 'activities'])
            ->orderBy('order')
            ->get();

        return view('admin.chapters.index', compact('course', 'chapters'));
    }

    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
        ]);

        $course->chapters()->create($request->only('title', 'description', 'order'));

        return redirect()->route('admin.chapters.index', $course)->with('success', 'Chapter berhasil ditambahkan.');
    }

    public function update(Request $request, Course $course, Chapter $chapter)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
        ]);

        $chapter->update($request->only('title', 'description', 'order'));

        return redirect()->route('admin.chapters.index', $course)->with('success', 'Chapter berhasil diperbarui.');
    }

    public function destroy(Course $course, Chapter $chapter)
    {
        $chapter->delete();
        return redirect()->route('admin.chapters.index', $course)->with('success', 'Chapter berhasil dihapus.');
    }
}