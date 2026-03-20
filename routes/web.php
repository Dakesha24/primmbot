<?php

//NAMESPACE IMPORT USER
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

//NAMESPACE IMPORT ADMIN
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\ChapterController as AdminChapterController;
use App\Http\Controllers\Admin\ChapterContentController as AdminChapterContentController;
use App\Http\Controllers\Admin\LessonMaterialController as AdminLessonMaterialController;
use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\SandboxDatabaseController as AdminSandboxController;
use App\Http\Controllers\Admin\SandboxTableController as AdminSandboxTableController;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');

// Google OAuth
Route::get('/auth/google/redirect', [\App\Http\Controllers\Auth\GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'callback'])->name('google.callback');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['profile.complete'])->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Kelas (Courses)
        Route::get('/kelas', [\App\Http\Controllers\CourseController::class, 'index'])->name('courses.index');
        Route::get('/kelas/{course}', [\App\Http\Controllers\CourseController::class, 'show'])->name('courses.show');

        // Learning (langsung masuk LKPD)
        Route::prefix('/belajar/{chapter}')->name('learning.')->group(function () {
            Route::get('/materi/{type}', [LearningController::class, 'material'])->name('material');
            Route::get('/ringkasan', [LearningController::class, 'summary'])->name('summary');
            Route::get('/aktivitas/{activity}', [LearningController::class, 'activity'])->name('activity');
            Route::post('/complete-material', [LearningController::class, 'completeMaterial'])->name('completeMaterial');
        });

        // API
        Route::post('/api/sql/run', [\App\Http\Controllers\Api\SqlRunnerController::class, 'run'])->name('api.sql.run');
        Route::get('/api/sql/tables', [\App\Http\Controllers\Api\SqlRunnerController::class, 'tables'])->name('api.sql.tables');
        Route::post('/api/submission/check', [\App\Http\Controllers\Api\SubmissionController::class, 'check'])->name('api.submission.check');
        Route::post('/api/submission/submit', [\App\Http\Controllers\Api\SubmissionController::class, 'submit'])->name('api.submission.submit');
        Route::post('/api/submission/progress', [\App\Http\Controllers\Api\SubmissionController::class, 'checkProgress'])->name('api.submission.progress');
    });
});

//ADMIN ROUTES
Route::prefix('admin')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Courses
        Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
        Route::post('/courses', [AdminCourseController::class, 'store'])->name('courses.store');
        Route::put('/courses/{course}', [AdminCourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');

        // Chapters (nested under course)
        Route::get('/courses/{course}/chapters', [AdminChapterController::class, 'index'])->name('chapters.index');
        Route::post('/courses/{course}/chapters', [AdminChapterController::class, 'store'])->name('chapters.store');
        Route::put('/courses/{course}/chapters/{chapter}', [AdminChapterController::class, 'update'])->name('chapters.update');
        Route::delete('/courses/{course}/chapters/{chapter}', [AdminChapterController::class, 'destroy'])->name('chapters.destroy');

        // Chapter Content (overview)
        Route::get('/courses/{course}/chapters/{chapter}/content', [AdminChapterContentController::class, 'index'])->name('chapters.content');

        // Lesson Materials
        Route::get('/courses/{course}/chapters/{chapter}/materials/create', [AdminLessonMaterialController::class, 'create'])->name('materials.create');
        Route::post('/courses/{course}/chapters/{chapter}/materials', [AdminLessonMaterialController::class, 'store'])->name('materials.store');
        Route::get('/courses/{course}/chapters/{chapter}/materials/{material}/edit', [AdminLessonMaterialController::class, 'edit'])->name('materials.edit');
        Route::put('/courses/{course}/chapters/{chapter}/materials/{material}', [AdminLessonMaterialController::class, 'update'])->name('materials.update');
        Route::delete('/courses/{course}/chapters/{chapter}/materials/{material}', [AdminLessonMaterialController::class, 'destroy'])->name('materials.destroy');
        // Image upload
        Route::post('/upload-image', [AdminLessonMaterialController::class, 'uploadImage'])->name('upload.image');

        // Activities
        Route::get('/courses/{course}/chapters/{chapter}/activities/create', [AdminActivityController::class, 'create'])->name('activities.create');
        Route::post('/courses/{course}/chapters/{chapter}/activities', [AdminActivityController::class, 'store'])->name('activities.store');
        Route::get('/courses/{course}/chapters/{chapter}/activities/{activity}/edit', [AdminActivityController::class, 'edit'])->name('activities.edit');
        Route::put('/courses/{course}/chapters/{chapter}/activities/{activity}', [AdminActivityController::class, 'update'])->name('activities.update');
        Route::delete('/courses/{course}/chapters/{chapter}/activities/{activity}', [AdminActivityController::class, 'destroy'])->name('activities.destroy');



        // Students
        Route::get('/students', [AdminStudentController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [AdminStudentController::class, 'show'])->name('students.show');
        Route::put('/students/{student}', [AdminStudentController::class, 'update'])->name('students.update');
        Route::patch('/students/{student}/toggle-active', [AdminStudentController::class, 'toggleActive'])->name('students.toggleActive');

        // Sandbox Databases
        Route::get('/sandbox', [AdminSandboxController::class, 'index'])->name('sandbox.index');
        Route::post('/sandbox', [AdminSandboxController::class, 'store'])->name('sandbox.store');
        Route::get('/sandbox/{sandbox}', [AdminSandboxController::class, 'show'])->name('sandbox.show');
        Route::put('/sandbox/{sandbox}', [AdminSandboxController::class, 'update'])->name('sandbox.update');
        Route::delete('/sandbox/{sandbox}', [AdminSandboxController::class, 'destroy'])->name('sandbox.destroy');


        // Sandbox Tables
        Route::get('/sandbox/{sandbox}/tables/create', [AdminSandboxTableController::class, 'create'])->name('sandbox.table.create');
        Route::post('/sandbox/{sandbox}/tables', [AdminSandboxTableController::class, 'store'])->name('sandbox.table.store');
        Route::get('/sandbox/{sandbox}/tables/{table}', [AdminSandboxTableController::class, 'show'])->name('sandbox.table.show');
        Route::post('/sandbox/{sandbox}/tables/{table}/insert', [AdminSandboxTableController::class, 'insertRow'])->name('sandbox.table.insert');
        Route::delete('/sandbox/{sandbox}/tables/{table}/delete-row', [AdminSandboxTableController::class, 'deleteRow'])->name('sandbox.table.deleteRow');
        Route::delete('/sandbox/{sandbox}/tables/{table}', [AdminSandboxTableController::class, 'destroy'])->name('sandbox.table.destroy');
        Route::put('/sandbox/{sandbox}/tables/{table}/update-row', [AdminSandboxTableController::class, 'updateRow'])->name('sandbox.table.updateRow');
        // Sandbox Table Structure
        Route::get('/sandbox/{sandbox}/tables/{table}/structure', [AdminSandboxTableController::class, 'editStructure'])->name('sandbox.table.structure');
        Route::post('/sandbox/{sandbox}/tables/{table}/add-column', [AdminSandboxTableController::class, 'addColumn'])->name('sandbox.table.addColumn');
        Route::delete('/sandbox/{sandbox}/tables/{table}/drop-column', [AdminSandboxTableController::class, 'dropColumn'])->name('sandbox.table.dropColumn');
        Route::put('/sandbox/{sandbox}/tables/{table}/modify-column', [AdminSandboxTableController::class, 'modifyColumn'])->name('sandbox.table.modifyColumn');

        Route::get('/sandbox/{sandbox}/preview', [AdminSandboxController::class, 'previewApi'])->name('sandbox.preview');
    });

require __DIR__ . '/auth.php';
