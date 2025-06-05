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
        // Course Registration (unchanged)
        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/registration', [CourseRegistrationController::class, 'showRegistrationForm'])->name('registration');
            Route::post('/register', [CourseRegistrationController::class, 'registerForCourses'])->name('register');
            Route::get('/{semester}', [CourseRegistrationController::class, 'getRegisteredCourses'])->name('registered');
            Route::post('/withdraw', [CourseRegistrationController::class, 'withdrawFromCourse'])->name('withdraw');
            Route::post('/queue', [CourseRegistrationController::class, 'addCourseToQueue'])->name('queue');
            Route::get('/download/pdf', [CourseRegistrationController::class, 'downloadCoursesPDF'])->name('download.pdf');
            Route::get('/download/excel', [CourseRegistrationController::class, 'downloadCoursesExcel'])->name('download.excel');
        });

        // User Profile (unchanged)
        Route::get('/user/profile', [CustomProfileController::class, 'show'])->name('profile.show');

        // Schedule (unchanged)
        Route::get('schedule', [StudentScheduleController::class, 'index'])->name('schedule');

        // Course Materials (unchanged)
        Route::get('/course-materials', [StudentController::class, 'CourseMaterial'])->name('course-materials');

        // Tests
        Route::prefix('tests')->name('tests.')->middleware('prevent.retake')->group(function () {
            Route::get('/', [TestController::class, 'index'])->name('index');
            Route::get('/{testId}/{questionIndex?}', [TestController::class, 'startTest'])->name('start');
            Route::post('/{testId}/submit', [TestController::class, 'submitTest'])->name('submit');
            Route::post('/{testId}/{questionIndex?}', [TestController::class, 'storeAnswer'])->name('storeAnswer');
        });
    });

    // Admin Routes (unchanged, included for completeness)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/faculty/import', [FacultyController::class, 'ShowImportForm'])->name('faculties.import.form');
        Route::post('/faculty/import', [FacultyController::class, 'import'])->name('faculties.import');
        Route::get('/departments/import', [DepartmentController::class, 'showImportForm'])->name('departments.import.form');
        Route::post('/departments/import', [DepartmentController::class, 'import'])->name('departments.import');
        Route::get('/courses/import', [CourseController::class, 'showImportForm'])->name('courses.import.form');
        Route::post('/courses/import', [CourseController::class, 'import'])->name('courses.import');
        Route::resource('courses', CourseController::class);
        Route::get('courses/{course}/prerequisites', [CourseController::class, 'showPrerequisites'])->name('courses.prerequisites');
        Route::post('courses/{course}/prerequisites', [CourseController::class, 'assignPrerequisites'])->name('courses.assignPrerequisites');
        Route::resources([
            'faculties' => FacultyController::class,
            'departments' => DepartmentController::class,
            'class-schedules' => ClassScheduleController::class,
        ]);
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
        Route::prefix('tests')->name('tests.')->group(function () {
            Route::get('/', [TestController::class, 'adminIndex'])->name('index');
            Route::get('/create', [TestController::class, 'create'])->name('create');
            Route::post('/', [TestController::class, 'store'])->name('store');
            Route::get('/{testId}/edit', [TestController::class, 'edit'])->name('edit');
            Route::put('/{testId}', [TestController::class, 'update'])->name('update');
            Route::get('/{testId}/questions', [TestController::class, 'manageQuestions'])->name('questions');
            Route::post('/{testId}/questions', [TestController::class, 'storeQuestions'])->name('questions.store');
            Route::get('/{testId}/questions/{questionId}/edit', [TestController::class, 'editQuestion'])->name('questions.edit');
            Route::put('/{testId}/questions/{questionId}', [TestController::class, 'updateQuestion'])->name('questions.update');
            Route::get('/{testId}/responses', [TestController::class, 'viewResponses'])->name('responses');
            Route::delete('/{testId}/questions/{questionId}', [TestController::class, 'deleteQuestion'])->name('questions.delete');
        });
        Route::resources([
            '/students' => StudentManagementController::class,
            '/staffs' => StaffManagementController::class,
        ]);
    });
});
