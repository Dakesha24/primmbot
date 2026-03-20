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
        $sandboxDatabases = SandboxDatabase::orderBy('name')->get();
        return view('admin.activities.create', compact('course', 'chapter', 'stage', 'sandboxDatabases'));
    }

    public function store(Request $request, Course $course, Chapter $chapter)
    {
        $rules = [
            'stage' => 'required|in:predict,run,investigate,modified,make',
            'question_text' => 'required|string',
            'order' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'code_snippet' => 'nullable|string',
            'editor_default_code' => 'nullable|string',
            'expected_output' => 'nullable|json',
            'level' => 'nullable|string',
            'sandbox_database_id' => 'nullable|exists:sandbox_databases,id',
        ];

        $validated = $request->validate($rules);

        $chapter->activities()->create($validated);

        return redirect()
            ->route('admin.chapters.content', [$course, $chapter])
            ->with('success', 'Aktivitas ' . ucfirst($validated['stage']) . ' berhasil ditambahkan.');
    }

    public function edit(Course $course, Chapter $chapter, Activity $activity)
    {
        $sandboxDatabases = SandboxDatabase::orderBy('name')->get();
        return view('admin.activities.edit', compact('course', 'chapter', 'activity', 'sandboxDatabases'));
    }

    public function update(Request $request, Course $course, Chapter $chapter, Activity $activity)
    {
        $rules = [
            'stage' => 'required|in:predict,run,investigate,modified,make',
            'question_text' => 'required|string',
            'order' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'code_snippet' => 'nullable|string',
            'editor_default_code' => 'nullable|string',
            'expected_output' => 'nullable|json',
            'level' => 'nullable|string',
            'sandbox_database_id' => 'nullable|exists:sandbox_databases,id',
        ];

        $validated = $request->validate($rules);

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
