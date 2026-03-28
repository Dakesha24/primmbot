<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\TeacherReview;
use Illuminate\Http\Request;

class TeacherReviewController extends Controller
{
    public function store(Request $request, Submission $submission)
    {
        $data = $request->validate([
            'score'    => 'nullable|integer|min:0|max:100',
            'feedback' => 'nullable|string|max:2000',
        ]);

        TeacherReview::create([
            'submission_id' => $submission->id,
            'teacher_id'    => auth()->id(),
            'score'         => $data['score'] ?? null,
            'feedback'      => $data['feedback'] ?? null,
        ]);

        $this->updateSubmission($submission, $data['score'] ?? null);

        return back()->with('review_saved', $submission->id);
    }

    public function update(Request $request, TeacherReview $review)
    {
        $data = $request->validate([
            'score'    => 'nullable|integer|min:0|max:100',
            'feedback' => 'nullable|string|max:2000',
        ]);

        $review->update([
            'score'    => $data['score'] ?? null,
            'feedback' => $data['feedback'] ?? null,
        ]);

        $this->updateSubmission($review->submission, $data['score'] ?? null);

        return back()->with('review_saved', $review->submission_id);
    }

    private function updateSubmission(Submission $submission, ?int $score): void
    {
        if ($score === null) {
            return;
        }

        // Hanya update is_correct — submissions.score tetap menyimpan skor AI asli
        $submission->update([
            'is_correct' => $score >= $submission->activity->kkm,
        ]);
    }
}
