<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\SandboxDatabase;

class ActivityController extends Controller
{
    public function create(Course $course, Chapter $chapter, Request $request)
    {
        $stage = $request->query('stage', 'predict');
        $level = $request->query('level');
        $sandboxDatabases = SandboxDatabase::orderBy('name')->get();
        $view = match($stage) {
            'predict'     => 'admin.activities.create-predict',
            'run'         => 'admin.activities.create-run',
            'investigate' => 'admin.activities.create-investigate',
            'modify'      => 'admin.activities.create-modify',
            'make'        => 'admin.activities.create-make',
            default       => 'admin.activities.create',
        };
        return view($view, compact('course', 'chapter', 'stage', 'level', 'sandboxDatabases'));
    }

    public function store(Request $request, Course $course, Chapter $chapter)
    {
        $rules = [
            'stage'              => 'required|in:predict,run,investigate,modify,make',
            'question_text'      => 'required|string',
            'description'        => 'nullable|string',
            'code_snippet'       => 'nullable|string',
            'editor_default_code'=> 'nullable|string',
            'reference_sql'      => 'nullable|string',
            'expected_output'    => 'nullable|json',
            'level'              => 'nullable|string',
            'sandbox_database_id'=> 'nullable|exists:sandbox_databases,id',
            'kkm'                => 'nullable|integer|min:0|max:100',
        ];

        $validated = $request->validate($rules);
        $validated['kkm'] = $validated['kkm'] ?? 70;

        // Auto-assign level untuk modify/make: Level 1, 2, 3, dst
        if (in_array($validated['stage'], ['modify', 'make'])) {
            $count = $chapter->activities()->where('stage', $validated['stage'])->count();
            $validated['level'] = (string)($count + 1);
        }

        // Auto-assign order
        $maxOrder = $chapter->activities()->max('order') ?? 0;
        $validated['order'] = $maxOrder + 1;

        $chapter->activities()->create($validated);

        return redirect()
            ->route('admin.chapters.content', [$course, $chapter])
            ->with('success', 'Aktivitas ' . ucfirst($validated['stage']) . ' berhasil ditambahkan.');
    }

    public function edit(Course $course, Chapter $chapter, Activity $activity)
    {
        $sandboxDatabases = SandboxDatabase::orderBy('name')->get();
        $view = match($activity->stage) {
            'predict'     => 'admin.activities.edit-predict',
            'run'         => 'admin.activities.edit-run',
            'investigate' => 'admin.activities.edit-investigate',
            'modify'      => 'admin.activities.edit-modify',
            'make'        => 'admin.activities.edit-make',
            default       => 'admin.activities.edit',
        };
        return view($view, compact('course', 'chapter', 'activity', 'sandboxDatabases'));
    }

    public function update(Request $request, Course $course, Chapter $chapter, Activity $activity)
    {
        $rules = [
            'stage'              => 'required|in:predict,run,investigate,modify,make',
            'question_text'      => 'required|string',
            'description'        => 'nullable|string',
            'code_snippet'       => 'nullable|string',
            'editor_default_code'=> 'nullable|string',
            'reference_sql'      => 'nullable|string',
            'expected_output'    => 'nullable|json',
            'level'              => 'nullable|string',
            'sandbox_database_id'=> 'nullable|exists:sandbox_databases,id',
            'kkm'                => 'nullable|integer|min:0|max:100',
        ];

        $validated = $request->validate($rules);
        $validated['kkm'] = $validated['kkm'] ?? 70;

        $activity->update($validated);

        return redirect()
            ->route('admin.chapters.content', [$course, $chapter])
            ->with('success', 'Aktivitas berhasil diperbarui.');
    }

    public function destroy(Course $course, Chapter $chapter, Activity $activity)
    {
        $activity->delete();

        return redirect()
            ->route('admin.chapters.content', [$course, $chapter])
            ->with('success', 'Aktivitas berhasil dihapus.');
    }
}
