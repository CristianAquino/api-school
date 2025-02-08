<?php

namespace App\DTOs;

use App\Models\GradeLevel;

class StudentDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function fromModel($model): array
    {
        return UserDTO::fromPartialModel($model);
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
            return self::fromModel($collection);
        }, $collections);
    }

    public static function fromModelWithRelation($model): array
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
            'phone' => $user->phone,
            'birth_date' => $user->birth_date,
            'address' => $user->address,
            'email' => $user->email,
            'dni' => $user->dni,
            'code' => $user->code,
            'academic_year' => $model->academic_year->year,
            'level' => $level,
            'grade' => $grade,
            'courses' => $courses
        ];
    }
}
