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
use App\Http\Controllers\Admin\SchoolController as AdminSchoolController;
use App\Http\Controllers\Admin\TahunAjaranController as AdminTahunAjaranController;
use App\Http\Controllers\Admin\KelasController as AdminKelasController;

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
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['profile.complete'])->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Kelas (Courses)
        Route::get('/kelas', [\App\Http\Controllers\CourseController::class, 'index'])->name('courses.index');
        Route::get('/kelas/{course}', [\App\Http\Controllers\CourseController::class, 'show'])->name('courses.show');
        Route::post('/kelas/{course}/enroll', [\App\Http\Controllers\CourseController::class, 'enroll'])->name('courses.enroll');

        // Hasil Belajar
        Route::get('/hasil-belajar', [\App\Http\Controllers\HasilBelajarController::class, 'index'])->name('hasil-belajar.index');
        Route::get('/hasil-belajar/{course}', [\App\Http\Controllers\HasilBelajarController::class, 'show'])->name('hasil-belajar.show');
        Route::get('/hasil-belajar/{course}/pdf', [\App\Http\Controllers\HasilBelajarController::class, 'downloadPdf'])->name('hasil-belajar.pdf');
        Route::get('/hasil-belajar/{course}/excel', [\App\Http\Controllers\HasilBelajarController::class, 'downloadExcel'])->name('hasil-belajar.excel');

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
        Route::post('/api/submission/submit', [\App\Http\Controllers\Api\SubmissionController::class, 'submit'])->name('api.submission.submit');
        Route::post('/api/submission/progress', [\App\Http\Controllers\Api\SubmissionController::class, 'checkProgress'])->name('api.submission.progress');
        Route::post('/api/chat', [\App\Http\Controllers\Api\ChatController::class, 'chat'])->name('api.chat');
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



        // Hasil Kelas
        Route::get('/hasil-kelas', [\App\Http\Controllers\Admin\HasilKelasController::class, 'index'])->name('hasil-kelas.index');
        Route::get('/hasil-kelas/{course}', [\App\Http\Controllers\Admin\HasilKelasController::class, 'show'])->name('hasil-kelas.show');
        Route::get('/hasil-kelas/{course}/siswa/{student}', [\App\Http\Controllers\Admin\HasilKelasController::class, 'student'])->name('hasil-kelas.student');
        Route::get('/hasil-kelas/{course}/siswa/{student}/pdf', [\App\Http\Controllers\Admin\HasilKelasController::class, 'downloadPdf'])->name('hasil-kelas.student.pdf');
        Route::get('/hasil-kelas/{course}/siswa/{student}/excel', [\App\Http\Controllers\Admin\HasilKelasController::class, 'downloadExcel'])->name('hasil-kelas.student.excel');
        Route::get('/hasil-kelas/{course}/siswa/{student}/chat', [\App\Http\Controllers\Admin\HasilKelasController::class, 'downloadChat'])->name('hasil-kelas.student.chat');

        // Teacher Reviews
        Route::post('/reviews/{submission}', [\App\Http\Controllers\Admin\TeacherReviewController::class, 'store'])->name('reviews.store');
        Route::put('/reviews/{review}', [\App\Http\Controllers\Admin\TeacherReviewController::class, 'update'])->name('reviews.update');

        // Sekolah
        Route::get('/schools', [AdminSchoolController::class, 'index'])->name('schools.index');
        Route::post('/schools', [AdminSchoolController::class, 'store'])->name('schools.store');
        Route::put('/schools/{school}', [AdminSchoolController::class, 'update'])->name('schools.update');
        Route::delete('/schools/{school}', [AdminSchoolController::class, 'destroy'])->name('schools.destroy');

        // Tahun Ajaran
        Route::get('/tahun-ajaran', [AdminTahunAjaranController::class, 'index'])->name('tahun-ajaran.index');
        Route::post('/tahun-ajaran', [AdminTahunAjaranController::class, 'store'])->name('tahun-ajaran.store');
        Route::put('/tahun-ajaran/{tahunAjaran}', [AdminTahunAjaranController::class, 'update'])->name('tahun-ajaran.update');
        Route::delete('/tahun-ajaran/{tahunAjaran}', [AdminTahunAjaranController::class, 'destroy'])->name('tahun-ajaran.destroy');
        Route::patch('/tahun-ajaran/{tahunAjaran}/activate', [AdminTahunAjaranController::class, 'toggleActive'])->name('tahun-ajaran.activate');

        // Kelas
        Route::get('/kelas', [AdminKelasController::class, 'index'])->name('kelas.index');
        Route::post('/kelas', [AdminKelasController::class, 'store'])->name('kelas.store');
        Route::put('/kelas/{kela}', [AdminKelasController::class, 'update'])->name('kelas.update');
        Route::delete('/kelas/{kela}', [AdminKelasController::class, 'destroy'])->name('kelas.destroy');

        // Students
        Route::get('/students', [AdminStudentController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [AdminStudentController::class, 'show'])->name('students.show');
        Route::put('/students/{student}', [AdminStudentController::class, 'update'])->name('students.update');
        Route::patch('/students/{student}/toggle-active', [AdminStudentController::class, 'toggleActive'])->name('students.toggleActive');
        Route::patch('/students/{student}/password', [AdminStudentController::class, 'updatePassword'])->name('students.updatePassword');

        // AI Monitor
        Route::get('/ai-monitor', [\App\Http\Controllers\Admin\AiMonitorController::class, 'index'])->name('ai-monitor.index');

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
        Route::get('/sandbox/{sandbox}/schema', [AdminSandboxController::class, 'schemaApi'])->name('sandbox.schema');
    });

require __DIR__ . '/auth.php';
