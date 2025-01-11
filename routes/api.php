<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollementController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\QualificationController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Middleware\AcademicYearValidatorMiddleware;
use App\Http\Middleware\CourseValidatorMiddleware;
use App\Http\Middleware\GradeValidatorMiddleware;
use App\Http\Middleware\LevelValidatorMiddleware;
use App\Http\Middleware\QualificationValidatorMiddleware;
use App\Http\Middleware\ScheduleValidatorMiddleware;
use App\Http\Middleware\TeacherValidatorMiddleware;
use App\Http\Middleware\UserValidatorMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
});

// routes for academic_years
Route::get('/academic_years', [AcademicYearController::class, 'index']);
Route::post('/academic_years', [AcademicYearController::class, 'store'])->middleware(AcademicYearValidatorMiddleware::class);
Route::get('/academic_years/last_year', [AcademicYearController::class, 'lastYear']);
Route::get('/academic_years/{academicYear}', [AcademicYearController::class, 'show']);
Route::put('/academic_years/{academicYear}', [AcademicYearController::class, 'update'])->middleware(AcademicYearValidatorMiddleware::class);
Route::delete('/academic_years/{academicYear}', [AcademicYearController::class, 'destroy']);

// routes for levels
Route::get('/levels', [LevelController::class, 'index']);
Route::post('/levels', [LevelController::class, 'store'])->middleware(LevelValidatorMiddleware::class);
Route::get('/levels/{level}', [LevelController::class, 'show']);
Route::put('/levels/{level}', [LevelController::class, 'update'])->middleware(LevelValidatorMiddleware::class);
Route::delete('/levels/{level}', [LevelController::class, 'destroy']);

// routes for grades
Route::get('/grades', [GradeController::class, 'index']);
Route::put('/grades/{grade}', [GradeController::class, 'update'])->middleware(GradeValidatorMiddleware::class);
Route::delete('/grades/{grade}', [GradeController::class, 'destroy']);
Route::post('/levels/{level}/grades', [GradeController::class, 'store'])->middleware(GradeValidatorMiddleware::class);
Route::get('/levels/{level}/grades/{grade}', [GradeController::class, 'show']);
Route::delete('/levels/{level}/grades/{grade}', [GradeController::class, 'detach']);

// routes for courses
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{course}', [CourseController::class, 'show']);
Route::put('/courses/{course}', [CourseController::class, 'update'])->middleware(CourseValidatorMiddleware::class);;
Route::delete('/courses/{course}', [CourseController::class, 'destroy']);
Route::post('/levels/{level}/grades/{grade}/courses', [CourseController::class, 'store'])->middleware(CourseValidatorMiddleware::class);

// routes for schedules
Route::get('/schedules', [ScheduleController::class, 'index']);
Route::post('/schedules', [ScheduleController::class, 'store'])->middleware(ScheduleValidatorMiddleware::class);
Route::get('/schedules/{schedule}', [ScheduleController::class, 'show']);
Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->middleware(ScheduleValidatorMiddleware::class);
Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy']);

// routes for teachers
Route::get('/teachers', [TeacherController::class, 'index']);
Route::post('/teachers', [TeacherController::class, 'store'])->middleware(TeacherValidatorMiddleware::class);
Route::get('/teachers/{teacher}', [TeacherController::class, 'show']);
Route::put('/teachers/{teacher}', [TeacherController::class, 'update']);
Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy']);
Route::post('/teachers/{teacher}/courses/{course}', [TeacherController::class, 'assignCourse']);

// routes for enrollements
Route::get('/enrollements', [EnrollementController::class, 'index']);
Route::get('/enrollements/{enrollement}', [EnrollementController::class, 'show']);
Route::delete('/enrollements/{enrollement}', [EnrollementController::class, 'destroy']);
Route::post('/enrollements/academic_years/{academicYear}/grade_level/{gradeLevel}', [EnrollementController::class, 'store'])->middleware(UserValidatorMiddleware::class);

// routes for students
Route::get('/students', [StudentController::class, 'index']);
Route::get('/students/{student}', [StudentController::class, 'show']);
Route::put('/students/{student}', [StudentController::class, 'update'])->middleware(UserValidatorMiddleware::class);

// routes for admin
Route::get('/admins', [AdminController::class, 'index']);
Route::post('/admins', [AdminController::class, 'store']);
Route::get('/admins/{admin}', [AdminController::class, 'show']);
Route::put('/admins/{admin}', [AdminController::class, 'update']);
Route::delete('/admins/{admin}', [AdminController::class, 'destroy']);

// routes for qualifications
Route::get('/students/{student}/courses/{course}/qualifications', [QualificationController::class, 'show']);
Route::post('/students/{student}/courses/{course}/qualifications', [QualificationController::class, 'store'])->middleware(QualificationValidatorMiddleware::class);
