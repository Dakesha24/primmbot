<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Chapter;

class ChapterContentController extends Controller
{
    public function index(Course $course, Chapter $chapter)
    {
        $materials = $chapter->lessonMaterials()->orderBy('order')->get();
        $activities = $chapter->activities()->with('sandboxDatabase')->orderBy('order')->get();

        return view('admin.chapters.content', compact('course', 'chapter', 'materials', 'activities'));
    }
}
