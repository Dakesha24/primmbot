<?php

namespace App\Http\Controllers;

use App\Exports\HasilBelajarExport;
use App\Models\AiInteractionLog;
use App\Models\Course;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class HasilBelajarController extends Controller
{
    private array $stages = ['predict', 'run', 'investigate', 'modify', 'make'];

    public function index()
    {
        $user = Auth::user();

        $courses = Course::with(['kelas.school', 'chapters.activities'])
            ->whereHas('enrollments', fn($q) => $q->where('user_id', $user->id))
            ->orderBy('order')
            ->get();

        $activityIds = $courses->flatMap(fn($c) => $c->chapters->flatMap(fn($ch) => $ch->activities->pluck('id')));

        $submissions = $user->submissions()
            ->whereIn('activity_id', $activityIds)
            ->get();

        $courses->each(function ($course) use ($submissions) {
            $allActivities = $course->chapters->flatMap(fn($ch) => $ch->activities);
            $total         = $allActivities->count();
            $courseSubs    = $submissions->whereIn('activity_id', $allActivities->pluck('id'));
            $correctIds    = $courseSubs->where('is_correct', true)->pluck('activity_id')->unique();

            $course->total_count      = $total;
            $course->completed_count  = $correctIds->count();
            $course->progress_percent = $total > 0 ? round($correctIds->count() / $total * 100) : 0;

            // Per-chapter
            $course->chapter_stats = $course->chapters->map(function ($chapter) use ($correctIds) {
                $total     = $chapter->activities->count();
                $completed = $chapter->activities->filter(fn($a) => $correctIds->contains($a->id))->count();
                return [
                    'title'     => $chapter->title,
                    'total'     => $total,
                    'completed' => $completed,
                    'percent'   => $total > 0 ? round($completed / $total * 100) : 0,
                ];
            });

            // Avg score
            $scored = $courseSubs->whereNotNull('score');
            $course->avg_score = $scored->isNotEmpty() ? round($scored->avg('score')) : null;
        });

        return view('pages.hasil-belajar.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $user = Auth::user();

        // Pastikan siswa terdaftar
        abort_unless(
            $course->enrollments()->where('user_id', $user->id)->exists(),
            403
        );

        $course->load(['kelas.school', 'chapters' => function ($q) {
            $q->orderBy('order')->with(['activities' => fn($q) => $q->orderBy('order')]);
        }]);

        $activityIds = $course->chapters->flatMap(fn($ch) => $ch->activities->pluck('id'));

        $submissions = $user->submissions()
            ->whereIn('activity_id', $activityIds)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->keyBy('activity_id');

        $chatLogs = AiInteractionLog::where('user_id', $user->id)
            ->whereIn('activity_id', $activityIds)
            ->orderBy('created_at')
            ->get()
            ->groupBy('activity_id');

        $course->chapters->each(function ($chapter) use ($submissions, $chatLogs) {
            $chapter->activities->each(function ($activity) use ($submissions, $chatLogs) {
                $activity->my_submission = $submissions->get($activity->id);
                $activity->chat_logs     = $chatLogs->get($activity->id, collect());
            });
        });

        $allActivities = $course->chapters->flatMap(fn($ch) => $ch->activities);
        $total         = $allActivities->count();
        $correctIds    = $submissions->where('is_correct', true)->pluck('activity_id')->unique();
        $completed     = $correctIds->count();

        $stats = [
            'total'     => $total,
            'completed' => $completed,
            'percent'   => $total > 0 ? round($completed / $total * 100) : 0,
            'avg_score' => $submissions->whereNotNull('score')->isNotEmpty()
                ? round($submissions->whereNotNull('score')->avg('score'))
                : null,
        ];

        $stageStats = collect($this->stages)->map(function ($stage) use ($allActivities, $submissions) {
            $stageActs  = $allActivities->where('stage', $stage);
            $total      = $stageActs->count();
            $correctIds = $submissions->where('is_correct', true)->pluck('activity_id');
            $completed  = $stageActs->filter(fn($a) => $correctIds->contains($a->id))->count();
            return ['stage' => $stage, 'total' => $total, 'completed' => $completed];
        })->filter(fn($s) => $s['total'] > 0)->values();

        $chapterStats = $course->chapters->map(function ($chapter) use ($submissions) {
            $total      = $chapter->activities->count();
            $correctIds = $submissions->where('is_correct', true)->pluck('activity_id');
            $completed  = $chapter->activities->filter(fn($a) => $correctIds->contains($a->id))->count();
            return [
                'title'     => $chapter->title,
                'total'     => $total,
                'completed' => $completed,
                'percent'   => $total > 0 ? round($completed / $total * 100) : 0,
            ];
        });

        return view('pages.hasil-belajar.show', compact('course', 'stats', 'stageStats', 'chapterStats'));
    }

    public function downloadPdf(Course $course)
    {
        $data     = $this->prepareShowData($course);
        $identity = $this->buildIdentity($course);
        $identity['tgl_terakhir'] = $data['submissions']->max(fn($s) => $s->updated_at)?->format('d M Y, H:i') ?? '-';

        $chapterStats = $course->chapters->map(function ($chapter) use ($data) {
            $total      = $chapter->activities->count();
            $correctIds = $data['submissions']->where('is_correct', true)->pluck('activity_id');
            $completed  = $chapter->activities->filter(fn($a) => $correctIds->contains($a->id))->count();
            return [
                'title'     => $chapter->title,
                'total'     => $total,
                'completed' => $completed,
                'percent'   => $total > 0 ? round($completed / $total * 100) : 0,
            ];
        });

        $pdf = Pdf::loadView('pdf.hasil-belajar', array_merge($data, [
            'identity'     => $identity,
            'chapterStats' => $chapterStats,
        ]))->setPaper('a4', 'portrait');

        return $pdf->download($this->buildFilename($course, 'pdf'));
    }

    public function downloadExcel(Course $course)
    {
        $data     = $this->prepareShowData($course);
        $identity = $this->buildIdentity($course);
        $identity['tgl_terakhir'] = $data['submissions']->max(fn($s) => $s->updated_at)?->format('d M Y, H:i') ?? '-';

        return Excel::download(
            new HasilBelajarExport($course, $data['submissions'], $identity),
            $this->buildFilename($course, 'xlsx')
        );
    }

    private function buildFilename(Course $course, string $ext): string
    {
        $user    = Auth::user();
        $profile = $user->profile()->with('kelas')->first();

        $nis     = str($profile?->nim ?? 'nonim')->slug();
        $nama    = str($profile?->full_name ?? $user->username)->slug();
        $kelas   = str($profile?->kelas?->name ?? $course->kelas?->name ?? 'umum')->slug();
        $materi  = str($course->title)->slug();

        $chapters  = $course->chapters;
        $submateri = $chapters->count() === 1
            ? '_' . str($chapters->first()->title)->slug()
            : '';

        return "{$nis}_{$nama}_{$kelas}_{$materi}{$submateri}.{$ext}";
    }

    private function buildIdentity(Course $course): array
    {
        $user    = Auth::user();
        $profile = $user->profile()->with('kelas')->first();

        return [
            'nama'         => $profile?->full_name ?? $user->username,
            'nis'          => $profile?->nim ?? '-',
            'kelas'        => $profile?->kelas?->name ?? $course->kelas?->name ?? 'Umum',
            'sekolah'      => 'SMKN 4 Bandung',
            'tgl_cetak'    => now()->format('d M Y, H:i'),
            'tgl_terakhir' => null, // diisi dari submissions
        ];
    }

    private function prepareShowData(Course $course): array
    {
        $user = Auth::user();

        abort_unless(
            $course->enrollments()->where('user_id', $user->id)->exists(),
            403
        );

        $course->load(['kelas.school', 'chapters' => function ($q) {
            $q->orderBy('order')->with(['activities' => fn($q) => $q->orderBy('order')]);
        }]);

        $activityIds = $course->chapters->flatMap(fn($ch) => $ch->activities->pluck('id'));

        $submissions = $user->submissions()
            ->whereIn('activity_id', $activityIds)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->keyBy('activity_id');

        $course->chapters->each(function ($chapter) use ($submissions) {
            $chapter->activities->each(function ($activity) use ($submissions) {
                $activity->my_submission = $submissions->get($activity->id);
            });
        });

        $allActivities = $course->chapters->flatMap(fn($ch) => $ch->activities);
        $total         = $allActivities->count();
        $correctIds    = $submissions->where('is_correct', true)->pluck('activity_id')->unique();
        $completed     = $correctIds->count();

        $stats = [
            'total'     => $total,
            'completed' => $completed,
            'percent'   => $total > 0 ? round($completed / $total * 100) : 0,
            'avg_score' => $submissions->whereNotNull('score')->isNotEmpty()
                ? round($submissions->whereNotNull('score')->avg('score'))
                : null,
        ];

        return compact('course', 'stats', 'submissions');
    }
}
