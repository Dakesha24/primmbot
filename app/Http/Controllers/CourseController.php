<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    // Halaman Daftar Kelas
    public function index()
    {
        $courses = Course::with('chapters.activities')->orderBy('order')->get();

        $courses->each(function ($course) {
            $totalActivities = 0;
            $completedActivities = 0;

            $course->chapters->each(function ($chapter) use (&$totalActivities, &$completedActivities) {
                $activityIds = $chapter->activities->pluck('id');
                $totalActivities += $activityIds->count();
                $completedActivities += Submission::where('user_id', Auth::id())
                    ->whereIn('activity_id', $activityIds)
                    ->where('is_correct', true)
                    ->distinct('activity_id')
                    ->count('activity_id');
            });

            $course->progress = $totalActivities > 0
                ? round(($completedActivities / $totalActivities) * 100)
                : 0;
            $course->sub_materi_count = $course->chapters->count();
        });

        return view('pages.courses.index', compact('courses'));
    }

    // Halaman Detail Kelas (Daftar Sub Materi)
    public function show(Course $course)
    {
        $course->load('chapters.activities');

        // Hitung progress per chapter
        $course->chapters->each(function ($chapter) {
            $activityIds = $chapter->activities->pluck('id');
            $totalActivities = $activityIds->count();
            $completedActivities = Submission::where('user_id', Auth::id())
                ->whereIn('activity_id', $activityIds)
                ->where('is_correct', true)
                ->distinct('activity_id')
                ->count('activity_id');

            $chapter->progress = $totalActivities > 0
                ? round(($completedActivities / $totalActivities) * 100)
                : 0;
        });

        // Progress total course
        $totalAll = $course->chapters->sum(fn($ch) => $ch->activities->count());
        $completedAll = 0;
        $course->chapters->each(function ($chapter) use (&$completedAll) {
            $completedAll += Submission::where('user_id', Auth::id())
                ->whereIn('activity_id', $chapter->activities->pluck('id'))
                ->where('is_correct', true)
                ->distinct('activity_id')
                ->count('activity_id');
        });
        $courseProgress = $totalAll > 0 ? round(($completedAll / $totalAll) * 100) : 0;

        return view('pages.courses.show', compact('course', 'courseProgress'));
    }
}
