<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\VcController;
use App\Http\Controllers\RegistrarController;
use App\Http\Controllers\BursarController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ClassScheduleController;
use App\Http\Controllers\StudentScheduleController;


Route::get('/', function () {
    return view('welcome');
});

// Group routes that require authentication
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('dashboard');

    // Course Registration Routes
    Route::prefix('student')->name('student.')->group(function () {
        // Show registration form (if needed)
        Route::get('/courses/registration', [CourseRegistrationController::class, 'showRegistrationForm'])->name('courses.registration');

        // Register for courses
        Route::post('/courses/register', [CourseRegistrationController::class, 'registerForCourses'])->name('courses.register');

        // View registered courses for a specific semester
        // Route::get('/courses/summary', [CourseRegistrationController::class, 'showRegisteredCourses'])->name('courses.summary');
        Route::get('/courses/{semester}', [CourseRegistrationController::class, 'getRegisteredCourses'])->name('courses.registered');

        // Withdraw from a course
        Route::post('/courses/withdraw', [CourseRegistrationController::class, 'withdrawFromCourse'])->name('courses.withdraw');

        // Add course to queue (if using a queue system)
        Route::post('/courses/queue', [CourseRegistrationController::class, 'addCourseToQueue'])->name('courses.queue');

        Route::get('/courses/download/pdf', [CourseRegistrationController::class, 'downloadCoursesPDF'])->name('courses.download.pdf');
        Route::get('/courses/download/excel', [CourseRegistrationController::class, 'downloadCoursesExcel'])->name('courses.download.excel');

        Route::get('/user/profile', [\App\Http\Controllers\CustomProfileController::class, 'show'])
            ->name('profile.show');
        Route::get('schedule', [StudentScheduleController::class, 'index'])->name('schedule');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('courses', CourseController::class);
        Route::get('courses/{course}/prerequisites', [CourseController::class, 'showPrerequisites'])->name('courses.prerequisites');
        Route::post('courses/{course}/prerequisites', [CourseController::class, 'assignPrerequisites'])->name('courses.assignPrerequisites');

        Route::resource('faculties', FacultyController::class);
        Route::resource('departments', DepartmentController::class);

        Route::resource('class-schedules', ClassScheduleController::class);
    });
});
