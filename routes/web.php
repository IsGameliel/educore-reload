<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AdminController, StudentController, LecturerController, VcController, RegistrarController,
    BursarController, HomeController, CourseRegistrationController, CourseController,
    FacultyController, DepartmentController, ClassScheduleController, StudentScheduleController,
    CourseMaterialController, TestController, StudentManagementController, StaffManagementController,
    CustomProfileController
};

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Authenticated Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Home/Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('dashboard');

    // Student Routes
    Route::prefix('student')->name('student.')->group(function () {

        // Course Registration
        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/registration', [CourseRegistrationController::class, 'showRegistrationForm'])->name('registration');
            Route::post('/register', [CourseRegistrationController::class, 'registerForCourses'])->name('register');
            Route::get('/{semester}', [CourseRegistrationController::class, 'getRegisteredCourses'])->name('registered');
            Route::post('/withdraw', [CourseRegistrationController::class, 'withdrawFromCourse'])->name('withdraw');
            Route::post('/queue', [CourseRegistrationController::class, 'addCourseToQueue'])->name('queue');
            Route::get('/download/pdf', [CourseRegistrationController::class, 'downloadCoursesPDF'])->name('download.pdf');
            Route::get('/download/excel', [CourseRegistrationController::class, 'downloadCoursesExcel'])->name('download.excel');
        });

        // User Profile
        Route::get('/user/profile', [CustomProfileController::class, 'show'])->name('profile.show');

        // Schedule
        Route::get('schedule', [StudentScheduleController::class, 'index'])->name('schedule');

        // Course Materials
        Route::get('/course-materials', [StudentController::class, 'CourseMaterial'])->name('course-materials');

        // Tests
        Route::prefix('tests')->name('tests.')->group(function () {
            Route::get('/', [TestController::class, 'index'])->name('index');
            Route::get('/{testId}/start/{questionIndex?}', [TestController::class, 'startTest'])->name('start');
            Route::post('/{testId}/start/{questionIndex?}', [TestController::class, 'storeAnswer'])->name('storeAnswer');
            Route::post('/{testId}/submit', [TestController::class, 'submitTest'])->name('submit');
        });
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {

        // Course Management
        Route::resource('courses', CourseController::class);
        Route::get('courses/{course}/prerequisites', [CourseController::class, 'showPrerequisites'])->name('courses.prerequisites');
        Route::post('courses/{course}/prerequisites', [CourseController::class, 'assignPrerequisites'])->name('courses.assignPrerequisites');

        // Faculty, Department, and Class Schedule Management
        Route::resources([
            'faculties' => FacultyController::class,
            'departments' => DepartmentController::class,
            'class-schedules' => ClassScheduleController::class,
        ]);

        // Course Materials
        Route::prefix('course-materials')->name('course-materials.')->group(function () {
            Route::get('/', [CourseMaterialController::class, 'index'])->name('index');
            Route::get('/create', [CourseMaterialController::class, 'create'])->name('create');
            Route::post('/', [CourseMaterialController::class, 'store'])->name('store');
            Route::get('/{id}', [CourseMaterialController::class, 'show']);
            Route::get('/{id}/download', [CourseMaterialController::class, 'download'])->name('download');
            Route::delete('/{id}', [CourseMaterialController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/edit', [CourseMaterialController::class, 'edit'])->name('edit');
            Route::put('/{id}', [CourseMaterialController::class, 'update'])->name('update');
        });

        // Test Management
        Route::prefix('tests')->name('tests.')->group(function () {
            Route::get('/', [TestController::class, 'adminIndex'])->name('index');
            Route::get('/create', [TestController::class, 'create'])->name('create');
            Route::post('/', [TestController::class, 'store'])->name('store');
            Route::get('/{testId}/questions', [TestController::class, 'manageQuestions'])->name('questions');
            Route::post('/{testId}/questions', [TestController::class, 'storeQuestions'])->name('questions.store');
            Route::get('/{testId}/responses', [TestController::class, 'viewResponses'])->name('responses');
        });

        // Student and Staff Management
        Route::resources([
            '/students' => StudentManagementController::class,
            '/staffs' => StaffManagementController::class,
        ]);
    });
});
