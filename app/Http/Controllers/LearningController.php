<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\AiInteractionLog;
use App\Models\Chapter;
use App\Models\MaterialCompletion;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LearningController extends Controller
{
    // =========================================================================
    // STAGE GATE — kunci navigasi hingga tahap diselesaikan
    // Untuk development/debugging: ganti true → false untuk menonaktifkan kunci
    // =========================================================================
    private bool $stageGateEnabled = false;
    // private bool $stageGateEnabled = false; // DEV: nonaktifkan kunci navigasi
    // =========================================================================

    public function material(Chapter $chapter, string $type)
    {
        $material = $chapter->lessonMaterials()->where('type', $type)->firstOrFail();
        $chapter->load(['lessonMaterials', 'activities']);

        $completedMaterialIds = $this->getCompletedMaterialIds($chapter);
        $completedActivityIds = $this->getCompletedActivityIds($chapter);
        $sidebarStageAccess   = $this->getSidebarStageAccess($chapter, $completedActivityIds);

        return view('learning.material', compact('chapter', 'material', 'completedMaterialIds', 'completedActivityIds', 'sidebarStageAccess'));
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
        $sidebarStageAccess   = $this->getSidebarStageAccess($chapter, $completedActivityIds);

        return view('learning.summary', compact('chapter', 'materials', 'completedMaterialIds', 'completedActivityIds', 'sidebarStageAccess'));
    }

    public function activity(Chapter $chapter, Activity $activity)
    {
        if ($activity->chapter_id !== $chapter->id) {
            abort(404);
        }

        $chapter->load(['lessonMaterials', 'activities']);
        $completedMaterialIds = $this->getCompletedMaterialIds($chapter);
        $completedActivityIds = $this->getCompletedActivityIds($chapter);
        $sidebarStageAccess   = $this->getSidebarStageAccess($chapter, $completedActivityIds);

        $canProceedWithinStage = !$this->stageGateEnabled
            || in_array($activity->id, $completedActivityIds);

        $canProceedToNextStage = !$this->stageGateEnabled
            || $chapter->activities
                ->where('stage', $activity->stage)
                ->every(fn($a) => in_array($a->id, $completedActivityIds));

        $submission = Submission::where('user_id', Auth::id())
            ->where('activity_id', $activity->id)
            ->with('teacherReview')
            ->orderBy('attempt', 'desc')
            ->first();

        $teacherReview = $submission?->teacherReview;

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

        // Investigate/Modify/Make: navigasi lintas semua level — tapi pisahkan
        // aktivitas yang punya level dari yang tidak punya level.
        //
        // Masalah sebelumnya: aktivitas berlevel dan tanpa-level dicampur dalam
        // satu daftar siblings → atoms jadi "ada 2" karena soal null-level ikut terhitung.
        //
        // Fix: jika activity punya level → siblings hanya sesama berlevel
        //       jika activity tanpa level → siblings hanya sesama tanpa level
        if (in_array($activity->stage, ['investigate', 'modify', 'make'])) {
            $siblings = $chapter->activities()
                ->where('stage', $activity->stage)
                ->when(
                    $activity->level !== null,
                    fn($q) => $q->whereNotNull('level'),  // berlevel: lintas semua level
                    fn($q) => $q->whereNull('level'),     // tanpa level: sesama tanpa level
                )
                ->orderBy('order')
                ->get();
        } else {
            // Predict/Run: navigasi dalam level yang sama
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

        // Load riwayat chat (type='chat') dari database untuk restore tampilan saat halaman dimuat
        $chatLogs = AiInteractionLog::where('user_id', Auth::id())
            ->where('activity_id', $activity->id)
            ->where('type', 'chat')
            ->orderBy('created_at')
            ->get(['prompt_sent', 'response_received']);

        $sandboxTables = [];
        if ($activity->sandbox_database_id) {
            $activity->load('sandboxDatabase.sandboxTables');
            if ($activity->sandboxDatabase) {
                foreach ($activity->sandboxDatabase->sandboxTables as $table) {
                    try {
                        $columns = DB::connection('sandbox')->select("DESCRIBE `{$table->table_name}`");
                        $sandboxTables[$table->display_name] = [
                            'real_name' => $table->table_name,
                            'columns' => array_map(fn($col) => [
                                'name' => $col->Field,
                                'type' => $col->Type,
                                'key' => $col->Key,
                            ], $columns)
                        ];
                    } catch (\Exception $e) {
                        // Tabel fisik belum dibuat di sandbox, atau ada masalah koneksi.
                        // Halaman tetap bisa dimuat; tabel ini tidak muncul di panel DB.
                        Log::warning('LearningController: Gagal DESCRIBE tabel sandbox', [
                            'table' => $table->table_name,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }
        // ---------------------------------------------------

        // Tentukan view berdasarkan stage
        $viewMap = [
            'predict' => 'learning.stages.predict',
            'run' => 'learning.stages.run',
            'investigate' => 'learning.stages.investigate',
            'modify' => 'learning.stages.modify',
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
            'sidebarStageAccess',
            'canProceedWithinStage',
            'canProceedToNextStage',
            'prevActivity',
            'nextActivity',
            'currentNumber',
            'totalSiblings',
            'sandboxTables',
            'chatLogs',
            'teacherReview',
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

    /**
     * Hitung stage mana saja yang boleh diakses dari sidebar.
     * Urutan: predict → run → investigate → modify → make
     * Sebuah stage terbuka hanya jika SEMUA aktivitas stage sebelumnya sudah is_correct.
     */
    private function getSidebarStageAccess(Chapter $chapter, array $completedActivityIds): array
    {
        if (!$this->stageGateEnabled) {
            return array_fill_keys(['predict', 'run', 'investigate', 'modify', 'make'], true);
        }

        $stageOrder = ['predict', 'run', 'investigate', 'modify', 'make'];
        $access = [];
        $previousStageCompleted = true; // predict selalu bisa diakses

        foreach ($stageOrder as $stage) {
            $access[$stage] = $previousStageCompleted;

            $stageActivities = $chapter->activities->where('stage', $stage);
            $previousStageCompleted = $stageActivities->isNotEmpty()
                && $stageActivities->every(fn($a) => in_array($a->id, $completedActivityIds));
        }

        return $access;
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
