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
        if ($chapter->course_id !== $course->id) {
            abort(404);
        }

        $chapter->load(['lessonMaterials', 'activities']);

        $completedActivityIds = Submission::where('user_id', Auth::id())
            ->whereIn('activity_id', $chapter->activities->pluck('id'))
            ->where('is_correct', true)
            ->pluck('activity_id')
            ->toArray();

        $totalActivities = $chapter->activities->count();
        $completedCount = count(array_unique($completedActivityIds));
        $progress = $totalActivities > 0
            ? round(($completedCount / $totalActivities) * 100)
            : 0;

        return view('chapters.show', compact('course', 'chapter', 'completedActivityIds', 'progress'));
    }
}