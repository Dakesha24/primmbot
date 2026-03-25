<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    // Halaman Daftar Kelas
    public function index()
    {
        $user    = Auth::user();
        $kelasId = $user->profile?->kelas_id;

        $courses = Course::with(['chapters.activities', 'kelas.school'])
            ->where(function ($q) use ($kelasId) {
                $q->whereNull('kelas_id');
                if ($kelasId) {
                    $q->orWhere('kelas_id', $kelasId);
                }
            })
            ->orderBy('order')
            ->get();

        $enrolledIds = $user->courseEnrollments()->pluck('course_id');

        $courses->each(function ($course) use ($enrolledIds) {
            $course->is_enrolled = $enrolledIds->contains($course->id);
            $course->sub_materi_count = $course->chapters->count();

            if ($course->is_enrolled) {
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
            } else {
                $course->progress = 0;
            }
        });

        return view('pages.courses.index', compact('courses'));
    }

    // Daftar ke course
    public function enroll(Request $request, Course $course)
    {
        $user    = Auth::user();
        $kelasId = $user->profile?->kelas_id;

        // Pastikan course memang boleh diakses siswa ini
        $allowed = is_null($course->kelas_id) || $course->kelas_id === $kelasId;
        abort_unless($allowed, 403);

        CourseEnrollment::firstOrCreate([
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ]);

        return redirect()->route('courses.show', $course)
            ->with('enroll_success', $course->title);
    }

    // Halaman Detail Kelas (Daftar Sub Materi)
    public function show(Course $course)
    {
        // Cek enrollment
        $isEnrolled = Auth::user()->courseEnrollments()->where('course_id', $course->id)->exists();
        if (!$isEnrolled) {
            return redirect()->route('courses.index')
                ->with('warning', 'Daftar ke kelas "' . $course->title . '" terlebih dahulu.');
        }

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
