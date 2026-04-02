<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index()
    {
        $courses    = Course::with('kelas.school')->withCount('chapters')->orderBy('order')->get();
        $kelasList  = Kelas::with(['school', 'tahunAjaran'])->orderBy('school_id')->orderBy('name')->get();
        return view('admin.courses.index', compact('courses', 'kelasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'order'        => 'required|integer|min:1',
            'kelas_id'     => 'nullable|exists:kelas,id',
            'cover_image'  => 'nullable|image|max:2048',
        ]);

        $newOrder = (int) $request->order;

        // Geser semua kelas yang urutannya >= newOrder ke bawah
        Course::where('order', '>=', $newOrder)->increment('order');

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $dir = $_SERVER['DOCUMENT_ROOT'] . '/courses/covers';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $coverPath = 'courses/covers/' . $filename;
        }

        Course::create([
            'title'        => $request->title,
            'description'  => $request->description,
            'order'        => $newOrder,
            'kelas_id'     => $request->kelas_id,
            'cover_image'  => $coverPath,
        ]);

        return redirect()->route('admin.courses.index')->with('success', 'LKPD berhasil ditambahkan.');
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'order'        => 'required|integer|min:1',
            'kelas_id'     => 'nullable|exists:kelas,id',
            'cover_image'  => 'nullable|image|max:2048',
            'remove_cover' => 'nullable|boolean',
        ]);

        $newOrder = (int) $request->order;
        $oldOrder = $course->order;

        if ($newOrder !== $oldOrder) {
            if ($newOrder < $oldOrder) {
                Course::where('id', '!=', $course->id)
                    ->whereBetween('order', [$newOrder, $oldOrder - 1])
                    ->increment('order');
            } else {
                Course::where('id', '!=', $course->id)
                    ->whereBetween('order', [$oldOrder + 1, $newOrder])
                    ->decrement('order');
            }
        }

        $coverPath = $course->cover_image;

        if ($request->hasFile('cover_image')) {
            if ($coverPath) {
                $old = $_SERVER['DOCUMENT_ROOT'] . '/' . $coverPath;
                if (file_exists($old)) unlink($old);
            }
            $file = $request->file('cover_image');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $dir = $_SERVER['DOCUMENT_ROOT'] . '/courses/covers';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $coverPath = 'courses/covers/' . $filename;
        } elseif ($request->boolean('remove_cover') && $coverPath) {
            $old = $_SERVER['DOCUMENT_ROOT'] . '/' . $coverPath;
            if (file_exists($old)) unlink($old);
            $coverPath = null;
        }

        $course->update([
            'title'        => $request->title,
            'description'  => $request->description,
            'order'        => $newOrder,
            'kelas_id'     => $request->kelas_id,
            'cover_image'  => $coverPath,
        ]);

        return redirect()->route('admin.courses.index')->with('success', 'LKPD berhasil diperbarui.');
    }

    public function destroy(Course $course)
    {
        if ($course->cover_image) {
            $old = $_SERVER['DOCUMENT_ROOT'] . '/' . $course->cover_image;
            if (file_exists($old)) unlink($old);
        }
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'LKPD berhasil dihapus.');
    }
}