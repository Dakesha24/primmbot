<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $kelas = $request->query('kelas');
        $sort = $request->query('sort', 'terbaru');
        $search = $request->query('search');

        $students = User::where('role', 'student')
            ->with('profile.kelas.school')
            ->when($kelas, function ($q) use ($kelas) {
                $q->whereHas('profile', fn($p) => $p->where('kelas_id', $kelas));
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('profile', fn($p) => $p->where('full_name', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%"));
                });
            });

        // Sorting
        if ($sort === 'abjad') {
            $students = $students->join('profiles', 'users.id', '=', 'profiles.user_id')
                ->orderBy('profiles.full_name', 'asc')
                ->select('users.*');
        } elseif ($sort === 'terlama') {
            $students = $students->orderBy('created_at', 'asc');
        } else {
            $students = $students->orderBy('created_at', 'desc');
        }

        $students = $students->get();

        // Progress
        $courses = Course::withCount('activities')->get();
        $students->each(function ($student) use ($courses) {
            $correctIds = $student->submissions()
                ->where('is_correct', true)
                ->pluck('activity_id')
                ->unique();

            $totalActivities = $courses->sum('activities_count');
            $student->progress_percent = $totalActivities > 0
                ? round(($correctIds->count() / $totalActivities) * 100)
                : 0;
        });

        $kelasList = Kelas::with(['school', 'tahunAjaran'])->orderBy('school_id')->orderBy('name')->get();

        return view('admin.students.index', compact('students', 'kelasList', 'kelas', 'sort', 'search'));
    }

    public function show(User $student)
    {
        $student->load('profile.kelas.school');

        $courses = Course::with(['chapters.activities'])->orderBy('order')->get();

        $correctIds = $student->submissions()
            ->where('is_correct', true)
            ->pluck('activity_id')
            ->unique();

        $courseProgress = $courses->map(function ($course) use ($correctIds) {
            $totalActivities = 0;
            $completed = 0;

            foreach ($course->chapters as $chapter) {
                foreach ($chapter->activities as $activity) {
                    $totalActivities++;
                    if ($correctIds->contains($activity->id)) {
                        $completed++;
                    }
                }
            }

            return (object) [
                'title' => $course->title,
                'completed' => $completed,
                'total' => $totalActivities,
                'percent' => $totalActivities > 0 ? round(($completed / $totalActivities) * 100) : 0,
            ];
        });

        $totalSubmissions = $student->submissions()->count();
        $correctSubmissions = $student->submissions()->where('is_correct', true)->count();

        return view('admin.students.show', compact('student', 'courseProgress', 'totalSubmissions', 'correctSubmissions'));
    }

    public function update(Request $request, User $student)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'nim'       => 'nullable|string|max:50',
            'kelas_id'  => 'nullable|exists:kelas,id',
            'gender'    => 'nullable|in:Laki-laki,Perempuan',
        ]);

        $student->profile->update($request->only('full_name', 'nim', 'kelas_id', 'gender'));

        return redirect()->route('admin.students.index', $request->only('kelas', 'sort', 'search'))
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function updatePassword(Request $request, User $student)
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $student->update(['password' => Hash::make($request->new_password)]);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Password siswa berhasil diubah.');
    }

    public function toggleActive(User $student)
    {
        $student->update(['is_active' => !$student->is_active]);

        $status = $student->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Siswa {$student->name} berhasil {$status}.");
    }
}
