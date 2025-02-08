<?php

namespace App\DTOs;

use App\Models\GradeLevel;

class EnrollementDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function fromBaseModel($model): array
    {
        $user = UserDTO::fromBaseModel($model->student->user);
        $query = GradeLevel::find($model->grade_level_id);
        $grade = $query->grade->grade;
        $level = $query->level->level;
        $courses = [];

        foreach ($query->courses as $course) {
            if (!is_null($course->teacher)) {
                $teacher = TeacherDTO::fromModel($course->teacher);
            }
            $cour = CourseDTO::fromBaseModel($course);
            $courses[] = array_merge(
                (array)$cour,
                ["teacher" => $teacher ?? null]
            );
        }

        return [
            'id' => $model->id,
            'names' => $user->name,
            'first_name' => $user->first_name,
            'second_name' => $user->second_name,
            'academic_year' => $model->academic_year->year,
            'level' => $level,
            'grade' => $grade,
            'code' => $user->code,
            'courses' => $courses
        ];
    }

    public static function fromPartialModel($model): array
    {
        $student = UserDTO::fromPartialModel($model->student);

        return [
            "id" => $model->id,
            "academic_year" => $model->academic_year->year,
            "grade" => $model->gradeLevel->grade->grade,
            "level" => $model->gradeLevel->level->level,
            "student" => $student
        ];
    }

    public static function fromPagination($model): array
    {
        return [
            'data' => self::fromPaginationCollection($model->items()),
            'pagination' => PaginationDTO::base($model)
        ];
    }

    public static function fromPaginationCollection($collections): array
    {
        return array_map(function ($collection) {
            return self::fromPartialModel($collection);
        }, $collections);
    }

    public static function fromCollection($collections): array
    {
        return array_map(function ($collection) {
            return self::fromBaseModel($collection);
        }, $collections->all());
    }
}