<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Chapter;
use App\Models\MaterialCompletion;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningController extends Controller
{
    public function material(Chapter $chapter, string $type)
    {
        $material = $chapter->lessonMaterials()->where('type', $type)->firstOrFail();
        $chapter->load(['lessonMaterials', 'activities']);

        $completedMaterialIds = $this->getCompletedMaterialIds($chapter);
        $completedActivityIds = $this->getCompletedActivityIds($chapter);

        return view('learning.material', compact('chapter', 'material', 'completedMaterialIds', 'completedActivityIds'));
    }

    public function summary(Chapter $chapter)
    {
        $materials = $chapter->lessonMaterials()
            ->where('type', 'ringkasan_materi')
            ->orderBy('order')
            ->get();

        $chapter->load(['lessonMaterials', 'activities']);
        $completedMaterialIds = $this->getCompletedMaterialIds($chapter);
        $completedActivityIds = $this->getCompletedActivityIds($chapter);

        return view('learning.summary', compact('chapter', 'materials', 'completedMaterialIds', 'completedActivityIds'));
    }

    public function activity(Chapter $chapter, Activity $activity)
    {
        if ($activity->chapter_id !== $chapter->id) {
            abort(404);
        }

        $chapter->load(['lessonMaterials', 'activities']);
        $completedMaterialIds = $this->getCompletedMaterialIds($chapter);
        $completedActivityIds = $this->getCompletedActivityIds($chapter);

        $submission = Submission::where('user_id', Auth::id())
            ->where('activity_id', $activity->id)
            ->latest()
            ->first();

        $predictSubmission = null;
        if ($activity->stage === 'run') {
            $predictActivity = $chapter->activities()->where('stage', 'predict')->first();
            if ($predictActivity) {
                $predictSubmission = Submission::where('user_id', Auth::id())
                    ->where('activity_id', $predictActivity->id)
                    ->latest()
                    ->first();
            }
        }

        // Investigate: navigasi lintas semua level. Lainnya: dalam level yang sama.
        if (in_array($activity->stage, ['investigate', 'modified', 'make'])) {
            $siblings = $chapter->activities()
                ->where('stage', $activity->stage)
                ->orderBy('order')
                ->get();
        } else {
            $siblings = $chapter->activities()
                ->where('stage', $activity->stage)
                ->when($activity->level, fn($q) => $q->where('level', $activity->level))
                ->orderBy('order')
                ->get();
        }

        $currentIndex = $siblings->search(fn($item) => $item->id === $activity->id);
        $prevActivity = $currentIndex > 0 ? $siblings[$currentIndex - 1] : null;
        $nextActivity = $currentIndex < $siblings->count() - 1 ? $siblings[$currentIndex + 1] : null;
        $currentNumber = $currentIndex + 1;
        $totalSiblings = $siblings->count();

        // Tentukan view berdasarkan stage
        $viewMap = [
            'predict' => 'learning.stages.predict',
            'run' => 'learning.stages.run',
            'investigate' => 'learning.stages.investigate',
            'modified' => 'learning.stages.modified',
            'make' => 'learning.stages.make',
        ];

        $view = $viewMap[$activity->stage] ?? 'learning.stages.predict';

        return view($view, compact(
            'chapter',
            'activity',
            'submission',
            'predictSubmission',
            'completedMaterialIds',
            'completedActivityIds',
            'prevActivity',
            'nextActivity',
            'currentNumber',
            'totalSiblings'
        ));
    }

    // Mark material as completed saat klik Selanjutnya
    public function completeMaterial(Request $request, Chapter $chapter)
    {
        $request->validate([
            'lesson_material_id' => ['required', 'exists:lesson_materials,id'],
        ]);

        MaterialCompletion::firstOrCreate([
            'user_id' => Auth::id(),
            'lesson_material_id' => $request->lesson_material_id,
        ]);

        return response()->json(['success' => true]);
    }

    private function getCompletedMaterialIds(Chapter $chapter): array
    {
        return MaterialCompletion::where('user_id', Auth::id())
            ->whereIn('lesson_material_id', $chapter->lessonMaterials->pluck('id'))
            ->pluck('lesson_material_id')
            ->toArray();
    }

    private function getCompletedActivityIds(Chapter $chapter): array
    {
        return Submission::where('user_id', Auth::id())
            ->whereIn('activity_id', $chapter->activities->pluck('id'))
            ->where('is_correct', true)
            ->pluck('activity_id')
            ->unique()
            ->toArray();
    }
}
