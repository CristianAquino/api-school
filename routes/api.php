<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollementController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\QualificationController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Middleware\AcademicYearValidatorMiddleware;
use App\Http\Middleware\AuthenticateWithCookie;
use App\Http\Middleware\CourseValidatorMiddleware;
use App\Http\Middleware\GradeValidatorMiddleware;
use App\Http\Middleware\JWTMiddleware;
use App\Http\Middleware\LevelValidatorMiddleware;
use App\Http\Middleware\LoginValidatorMiddleware;
use App\Http\Middleware\QualificationValidatorMiddleware;
use App\Http\Middleware\ScheduleValidatorMiddleware;
use App\Http\Middleware\UserValidatorMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->middleware(LoginValidatorMiddleware::class);

Route::middleware([
    AuthenticateWithCookie::class,
    JWTMiddleware::class
])->group(function () {
    // routes for academic_years
    Route::get(
        'academic_years',
        [AcademicYearController::class, 'index']
    );
    Route::post(
        'academic_years',
        [AcademicYearController::class, 'store']
    )->middleware(AcademicYearValidatorMiddleware::class);
    Route::get(
        'academic_years/last_year',
        [AcademicYearController::class, 'lastYear']
    );
    Route::get(
        'academic_years/soft_list',
        [AcademicYearController::class, 'softList']
    );
    Route::get(
        'academic_years/{academicYear}',
        [AcademicYearController::class, 'show']
    );
    Route::put(
        'academic_years/{academicYear}',
        [AcademicYearController::class, 'update']
    )->middleware(AcademicYearValidatorMiddleware::class);
    Route::delete(
        'academic_years/soft_destroy/{academicYear}',
        [AcademicYearController::class, 'softDestroy']
    );
    Route::post(
        'academic_years/restore/{academicYear}',
        [AcademicYearController::class, 'restore']
    );
    Route::delete(
        'academic_years/destroy/{academicYear}',
        [AcademicYearController::class, 'destroy']
    );
    // routes for levels
    Route::get(
        'levels',
        [LevelController::class, 'index']
    );
    Route::post(
        'levels',
        [LevelController::class, 'store']
    )->middleware(LevelValidatorMiddleware::class);
    Route::get(
        'levels/soft_list',
        [LevelController::class, 'softList']
    );
    Route::get(
        'levels/{level}',
        [LevelController::class, 'show']
    );
    Route::put(
        'levels/{level}',
        [LevelController::class, 'update']
    )->middleware(LevelValidatorMiddleware::class);
    Route::delete(
        'levels/soft_destroy/{level}',
        [LevelController::class, 'softDestroy']
    );
    Route::post(
        'levels/restore/{level}',
        [LevelController::class, 'restore']
    );
    Route::delete(
        'levels/destroy/{level}',
        [LevelController::class, 'destroy']
    );
    // routes for grades
    Route::get(
        'grades',
        [GradeController::class, 'index']
    );
    Route::get(
        'grades/soft_list',
        [GradeController::class, 'softList']
    );
    Route::put(
        'grades/{grade}',
        [GradeController::class, 'update']
    )->middleware(GradeValidatorMiddleware::class);
    Route::delete(
        'grades/{grade}',
        [GradeController::class, 'destroy']
    );
    Route::delete(
        'grades/soft_destroy/{grade}',
        [GradeController::class, 'softDestroy']
    );
    Route::delete(
        'grades/destroy/{grade}',
        [GradeController::class, 'destroy']
    );
    Route::post(
        'grades/restore/{grade}',
        [GradeController::class, 'restore']
    );
    Route::post(
        'levels/{level}/grades',
        [GradeController::class, 'store']
    )->middleware(GradeValidatorMiddleware::class);
    Route::get(
        'levels/{level}/grades/{grade}',
        [GradeController::class, 'show']
    );
    // routes for courses
    Route::get(
        'courses',
        [CourseController::class, 'index']
    );
    Route::get(
        'courses/soft_list',
        [CourseController::class, 'softList']
    );
    Route::get(
        'courses/{course}',
        [CourseController::class, 'show']
    );
    Route::put(
        'courses/{course}',
        [CourseController::class, 'update']
    )->middleware(CourseValidatorMiddleware::class);;
    Route::delete(
        'courses/soft_destroy/{course}',
        [CourseController::class, 'softDestroy']
    );
    Route::post(
        'courses/restore/{course}',
        [CourseController::class, 'restore']
    );
    Route::delete(
        'courses/destroy/{course}',
        [CourseController::class, 'destroy']
    );
    Route::post(
        'levels/{level}/grades/{grade}/courses',
        [CourseController::class, 'store']
    )->middleware(CourseValidatorMiddleware::class);
    // routes for schedules
    Route::get(
        'schedules',
        [ScheduleController::class, 'index']
    );
    Route::post(
        'schedules',
        [ScheduleController::class, 'store']
    )->middleware(ScheduleValidatorMiddleware::class);
    Route::get(
        'schedules/soft_list',
        [ScheduleController::class, 'softList']
    );
    Route::get(
        'schedules/print',
        [ScheduleController::class, 'printSchedule']
    );
    Route::get(
        'schedules/{schedule}',
        [ScheduleController::class, 'show']
    );
    Route::put(
        'schedules/{schedule}',
        [ScheduleController::class, 'update']
    )->middleware(ScheduleValidatorMiddleware::class);
    Route::delete(
        'schedules/soft_destroy/{schedule}',
        [ScheduleController::class, 'softDestroy']
    );
    Route::post(
        'schedules/restore/{schedule}',
        [ScheduleController::class, 'restore']
    );
    Route::delete(
        'schedules/destroy/{schedule}',
        [ScheduleController::class, 'destroy']
    );
    // routes for teachers
    Route::get(
        'teachers',
        [TeacherController::class, 'index']
    );
    Route::post(
        'teachers',
        [TeacherController::class, 'store']
    )->middleware(UserValidatorMiddleware::class);
    Route::get(
        'teachers/me',
        [TeacherController::class, 'me']
    );
    Route::get(
        'teachers/soft_list',
        [TeacherController::class, 'softList']
    );
    Route::get(
        'teachers/{teacher}',
        [TeacherController::class, 'show']
    );
    Route::put(
        'teachers/{teacher}',
        [TeacherController::class, 'update']
    )->middleware(UserValidatorMiddleware::class);
    Route::delete(
        'teachers/soft_destroy/{teacher}',
        [TeacherController::class, 'softDestroy']
    );
    Route::post(
        'teachers/restore/{teacher}',
        [TeacherController::class, 'restore']
    );
    Route::delete(
        'teachers/destroy/{teacher}',
        [TeacherController::class, 'destroy']
    );
    Route::post(
        'teachers/{teacher}/courses/{course}',
        [TeacherController::class, 'assignCourse']
    );
    // routes for enrollements
    Route::get(
        'enrollements',
        [EnrollementController::class, 'index']
    );
    Route::get(
        'enrollements/soft_list',
        [EnrollementController::class, 'softList']
    );
    Route::get(
        'enrollements/print',
        [EnrollementController::class, 'printEnrollement']
    );
    Route::get(
        'enrollements/{enrollement}',
        [EnrollementController::class, 'show']
    );
    Route::delete(
        'enrollements/soft_destroy/{enrollement}',
        [EnrollementController::class, 'softDestroy']
    );
    Route::post(
        'enrollements/restore/{enrollement}',
        [EnrollementController::class, 'restore']
    );
    Route::delete(
        'enrollements/destroy/{enrollement}',
        [EnrollementController::class, 'destroy']
    );
    Route::post(
        'enrollements/academic_years/{academicYear}/grade_level/{gradeLevel}',
        [EnrollementController::class, 'newEnrollement']
    )->middleware(UserValidatorMiddleware::class);
    Route::post(
        'enrollements/academic_years/{academicYear}/grade_level/{gradeLevel}/student/{student}',
        [EnrollementController::class, 'currentEnrollement']
    );
    // routes for students
    Route::get(
        'students',
        [StudentController::class, 'index']
    );
    Route::get(
        'students/me',
        [StudentController::class, 'me']
    );
    Route::get(
        'students/soft_list',
        [StudentController::class, 'softList']
    );
    Route::get(
        'students/{student}',
        [StudentController::class, 'show']
    );
    Route::put(
        'students/{student}',
        [StudentController::class, 'update']
    )->middleware(UserValidatorMiddleware::class);
    Route::delete(
        'students/soft_destroy/{student}',
        [StudentController::class, 'softDestroy']
    );
    Route::post(
        'students/restore/{student}',
        [StudentController::class, 'restore']
    );
    Route::delete(
        'students/destroy/{student}',
        [StudentController::class, 'destroy']
    );
    // routes for admin
    Route::get(
        'admins',
        [AdminController::class, 'index']
    );
    Route::post(
        'admins',
        [AdminController::class, 'store']
    )->middleware(UserValidatorMiddleware::class);
    Route::get(
        'admins/soft_list',
        [AdminController::class, 'softList']
    );
    Route::get(
        'admins/me',
        [AdminController::class, 'me']
    );
    Route::get(
        'admins/{admin}',
        [AdminController::class, 'show']
    );
    Route::put(
        'admins/{admin}',
        [AdminController::class, 'update']
    )->middleware(UserValidatorMiddleware::class);
    Route::delete(
        'admins/soft_destroy/{admin}',
        [AdminController::class, 'softDestroy']
    );
    Route::post(
        'admins/restore/{admin}',
        [AdminController::class, 'restore']
    );
    Route::delete(
        'admins/destroy/{admin}',
        [AdminController::class, 'destroy']
    );
    // routes for qualifications
    Route::get(
        'students/{student}/courses/{course}/qualifications',
        [QualificationController::class, 'show']
    );
    Route::post(
        'students/{student}/courses/{course}/qualifications',
        [QualificationController::class, 'store']
    )->middleware(QualificationValidatorMiddleware::class);
    Route::get(
        'qualifications/print',
        [QualificationController::class, 'printQualification']
    );
    // logout
    Route::post('logout', [AuthController::class, 'logout']);
});
