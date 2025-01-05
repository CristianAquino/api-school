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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
});
// routes for academic_years
Route::get('/academic_years', [AcademicYearController::class, 'index']);
Route::post('/academic_years', [AcademicYearController::class, 'store']);
Route::get('/academic_years/last_year', [AcademicYearController::class, 'lastYear']);
Route::get('/academic_years/{academicYear}', [AcademicYearController::class, 'show']);
Route::put('/academic_years/{academicYear}', [AcademicYearController::class, 'update']);
Route::delete('/academic_years/{academicYear}', [AcademicYearController::class, 'destroy']);
// routes for levels
Route::get('/levels', [LevelController::class, 'index']);
Route::post('/levels', [LevelController::class, 'store']);
Route::get('/levels/{level}', [LevelController::class, 'show']);
Route::put('/levels/{level}', [LevelController::class, 'update']);
Route::delete('/levels/{level}', [LevelController::class, 'destroy']);
// routes for grades
Route::get('/levels/{level}/grades', [GradeController::class, 'index']);
Route::post('/levels/{level}/grades', [GradeController::class, 'store']);
Route::get('/levels/{level}/grades/{grade}', [GradeController::class, 'show']);
Route::put('/levels/{level}/grades/{grade}', [GradeController::class, 'update']);
Route::delete('/levels/{level}/grades/{grade}', [GradeController::class, 'destroy']);
// routes for courses
Route::get('/grades/{grade}/courses', [CourseController::class, 'index']);
Route::post('/grades/{grade}/courses', [CourseController::class, 'store']);
Route::get('/grades/{grade}/courses/{course}', [CourseController::class, 'show']);
Route::put('/grades/{grade}/courses/{course}', [CourseController::class, 'update']);
Route::delete('grades/{grade}/courses/{course}', [CourseController::class, 'destroy']);
// routes for schedules
Route::get('/courses/{course}/schedules', [ScheduleController::class, 'index']);
Route::post('/courses/{course}/schedules', [ScheduleController::class, 'store']);
Route::get('/courses/{course}/schedules/{schedule}', [ScheduleController::class, 'show']);
Route::put('/courses/{course}/schedules/{schedule}', [ScheduleController::class, 'update']);
Route::delete('/courses/{course}/schedules/{schedule}', [ScheduleController::class, 'destroy']);
// routes for teachers
Route::get('/teachers', [TeacherController::class, 'index']);
Route::post('/teachers', [TeacherController::class, 'store']);
Route::get('/teachers/{teacher}', [TeacherController::class, 'show']);
Route::put('/teachers/{teacher}', [TeacherController::class, 'update']);
Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy']);
Route::post('/teachers/{teacher}/courses/{course}', [TeacherController::class, 'assignCourse']);
// routes for enrollements
Route::get('/enrollements', [EnrollementController::class, 'index']);
Route::get('/enrollements/{enrollement}', [EnrollementController::class, 'show']);
Route::post('/enrollements/academic_years/{academicYear}/grades/{grade}', [EnrollementController::class, 'store']);
// routes for students
Route::get('/students', [StudentController::class, 'index']);
Route::get('/students/{student}', [StudentController::class, 'show']);
Route::put('/students/{student}', [StudentController::class, 'update']);
// routes for admin
Route::get('/admins', [AdminController::class, 'index']);
Route::post('/admins', [AdminController::class, 'store']);
Route::get('/admins/{admin}', [AdminController::class, 'show']);
Route::put('/admins/{admin}', [AdminController::class, 'update']);
Route::delete('/admins/{admin}', [AdminController::class, 'destroy']);
// routes for qualifications
Route::get('/students/{student}/courses/{course}/qualifications', [QualificationController::class, 'show']);
Route::post('/students/{student}/courses/{course}/qualifications', [QualificationController::class, 'store']);
