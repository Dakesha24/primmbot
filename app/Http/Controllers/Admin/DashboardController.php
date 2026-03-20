<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Submission;
use App\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_courses' => Course::count(),
            'total_activities' => Activity::count(),
            'total_submissions' => Submission::count(),
            'correct_submissions' => Submission::where('is_correct', true)->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}