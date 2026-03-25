<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiInteractionLog;
use App\Models\Course;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;

class HasilKelasController extends Controller
{
    private array $stages = ['predict', 'run', 'investigate', 'modify', 'make'];

    // ── Index: satu card per course ──────────────────────────────────────────
    public function index()
    {
        $courses = Course::with(['kelas.school', 'kelas.tahunAjaran', 'chapters.activities'])
            ->orderBy('order')
            ->get();

        $courses->each(function ($course) {
            $allActivities   = $course->chapters->flatMap(fn($ch) => $ch->activities);
            $totalActivities = $allActivities->count();

            $students = User::whereHas('courseEnrollments', fn($q) => $q->where('course_id', $course->id))
                ->with(['submissions' => fn($q) => $q->where('is_correct', true)])
                ->get();

            $course->enrolled_count = $students->count();

            if ($students->isEmpty() || $totalActivities === 0) {
                $course->avg_progress  = 0;
                $course->chapter_stats = $course->chapters->map(fn($ch) => ['title' => $ch->title, 'avg_percent' => 0]);
                $course->avg_score     = null;
                return;
            }

            $students->each(function ($s) use ($totalActivities) {
                $done = $s->submissions->pluck('activity_id')->unique()->count();
                $s->progress_percent = round($done / $totalActivities * 100);
            });

            $course->avg_progress = round($students->avg('progress_percent'));

            $course->chapter_stats = $course->chapters->map(function ($chapter) use ($students) {
                $total = $chapter->activities->count();
                if ($total === 0) return ['title' => $chapter->title, 'avg_percent' => 0];
                $avgCompleted = $students->avg(function ($s) use ($chapter) {
                    $correctIds = $s->submissions->pluck('activity_id');
                    return $chapter->activities->filter(fn($a) => $correctIds->contains($a->id))->count();
                });
                return ['title' => $chapter->title, 'avg_percent' => round($avgCompleted / $total * 100)];
            });

            $scoredStudents = $students->filter(fn($s) => $s->submissions->whereNotNull('score')->isNotEmpty());
            $course->avg_score = $scoredStudents->isNotEmpty()
                ? round($scoredStudents->avg(fn($s) => $s->submissions->whereNotNull('score')->avg('score')))
                : null;
        });

        return view('admin.hasil-kelas.index', compact('courses'));
    }

    // ── Show: daftar siswa terdaftar di satu course ───────────────────────────
    public function show(Request $request, Course $course)
    {
        $course->load(['kelas.school', 'kelas.tahunAjaran', 'chapters.activities']);

        $allActivities   = $course->chapters->flatMap(fn($ch) => $ch->activities);
        $totalActivities = $allActivities->count();
        $activityIds     = $allActivities->pluck('id');

        $query = User::whereHas('courseEnrollments', fn($q) => $q->where('course_id', $course->id))
            ->with([
                'profile.kelas.school',
                'submissions' => fn($q) => $q->whereIn('activity_id', $activityIds),
            ]);

        // Filter by kelas
        $filterKelasId = $request->query('kelas_id');
        if ($filterKelasId) {
            $query->whereHas('profile', fn($q) => $q->where('kelas_id', $filterKelasId));
        }

        // Filter by name search
        $search = trim($request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhereHas('profile', fn($q2) => $q2->where('full_name', 'like', "%{$search}%"));
            });
        }

        $students = $query->get()
            ->sortBy(fn($s) => $s->profile?->full_name ?? $s->username)
            ->values();

        // Kelas list for filter dropdown (only kelas represented among enrollees)
        $kelasList = Kelas::with('school')
            ->whereHas('profiles.user.courseEnrollments', fn($q) => $q->where('course_id', $course->id))
            ->orderBy('name')
            ->get();

        $students->each(function ($student) use ($course, $allActivities, $totalActivities, $activityIds) {
            $correctSubs   = $student->submissions->where('is_correct', true);
            $correctIds    = $correctSubs->pluck('activity_id');
            $allSubs       = $student->submissions;

            $student->completed_count  = $correctIds->unique()->count();
            $student->total_count      = $totalActivities;
            $student->progress_percent = $totalActivities > 0
                ? round($correctIds->unique()->count() / $totalActivities * 100)
                : 0;

            // Avg score from all submissions that have a score
            $scoredSubs = $allSubs->whereNotNull('score');
            $student->avg_score = $scoredSubs->isNotEmpty()
                ? round($scoredSubs->avg('score'))
                : null;

            // Progress per chapter
            $student->chapter_progress = $course->chapters->map(function ($chapter) use ($correctIds) {
                $total     = $chapter->activities->count();
                $completed = $chapter->activities->filter(fn($a) => $correctIds->contains($a->id))->count();
                return [
                    'title'     => $chapter->title,
                    'total'     => $total,
                    'completed' => $completed,
                    'percent'   => $total > 0 ? round($completed / $total * 100) : 0,
                ];
            });
        });

        return view('admin.hasil-kelas.show', compact('course', 'students', 'totalActivities', 'kelasList', 'filterKelasId', 'search'));
    }

    // ── Student: jawaban + riwayat chat per siswa ────────────────────────────
    public function student(Course $course, User $student)
    {
        $course->load(['kelas.school', 'kelas.tahunAjaran', 'chapters' => function ($q) {
            $q->orderBy('order')->with(['activities' => fn($q) => $q->orderBy('order')]);
        }]);

        $student->load('profile.kelas.school');

        $activityIds = $course->chapters->flatMap(fn($ch) => $ch->activities->pluck('id'));

        // Semua submission siswa untuk course ini
        $submissions = $student->submissions()
            ->whereIn('activity_id', $activityIds)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->keyBy('activity_id');

        // Semua AI interaction logs siswa untuk course ini
        $aiLogs = AiInteractionLog::where('user_id', $student->id)
            ->whereIn('activity_id', $activityIds)
            ->orderBy('created_at')
            ->get()
            ->groupBy('activity_id');

        // Gabungkan ke activities
        $course->chapters->each(function ($chapter) use ($submissions, $aiLogs) {
            $chapter->activities->each(function ($activity) use ($submissions, $aiLogs) {
                $activity->student_submission = $submissions->get($activity->id);

                $logs = $aiLogs->get($activity->id, collect());

                // Pisahkan: chat vs check/submit
                $activity->chat_logs = $logs->filter(
                    fn($l) => !str_starts_with($l->prompt_sent, 'check:') && !str_starts_with($l->prompt_sent, 'submit:')
                )->values();

                $activity->check_logs = $logs->filter(
                    fn($l) => str_starts_with($l->prompt_sent, 'check:') || str_starts_with($l->prompt_sent, 'submit:')
                )->values();
            });
        });

        return view('admin.hasil-kelas.student', compact('course', 'student'));
    }
}
