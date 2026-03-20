<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;

class ChapterController extends Controller
{
    public function show(Course $course, Chapter $chapter)
    {
        // 1. Validasi: Pastikan bab (chapter) ini memang milik kursus (course) yang sedang diakses
        if ($chapter->course_id !== $course->id) {
            abort(404);
        }

        // 2. Eager Loading: Muat relasi materi dan aktivitas untuk efisiensi database (hindari N+1 query)
        $chapter->load(['lessonMaterials', 'activities']);

        // 3. Ambil ID aktivitas yang sudah dikerjakan dengan benar oleh user saat ini
        $completedActivityIds = Submission::where('user_id', Auth::id())
            ->whereIn('activity_id', $chapter->activities->pluck('id'))
            ->where('is_correct', true)
            ->pluck('activity_id')
            ->toArray();

        // 4. Hitung persentase progres belajar di bab ini
        $totalActivities = $chapter->activities->count();
        $completedCount = count(array_unique($completedActivityIds));
        $progress = $totalActivities > 0
            ? round(($completedCount / $totalActivities) * 100)
            : 0;

        // 5. Kirim data ke tampilan (view) chapters.show
        return view('chapters.show', compact('course', 'chapter', 'completedActivityIds', 'progress'));
    }
}