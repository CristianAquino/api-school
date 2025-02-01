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
        $user = UserDTO::fromBaseModel($model->user);

        return [
            'id' => $model->id,
            'names' => $user->name,
            'first_name' => $user->first_name,
            'second_name' => $user->second_name,
            'code' => $user->code
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
            return self::fromModel($collection);
        }, $collections);
    }

    // public static function fromCollection($collections): array
    // {
    //     return array_map(function ($collection) {
    //         return self::fromModel($collection);
    //     }, $collections->all());
    // }

    public static function fromModelWithRelation($model): array
    {
        $user = UserDTO::fromBaseModel($model->user);
        $lastEnrollement = $model->enrollements()->latest("academic_year_id")->first();
        $dl = $lastEnrollement->grade_level_id;
        $query = GradeLevel::find($dl);
        $grade = $query->grade->grade;
        $level = $query->level->level;
        $cs = $query->courses;
        $courses = [];

        foreach ($cs as $course) {
            $co = CourseDTO::fromBaseModel($course);
            $courses[] = $co;
        }

        return [
            'id' => $model->id,
            'names' => $user->name,
            'first_name' => $user->first_name,
            'second_name' => $user->second_name,
            'academic_year' => $lastEnrollement->academic_year->year,
            'level' => $level,
            'grade' => $grade,
            'code' => $user->code,
            'courses' => $courses
        ];
    }


    public static function fromPDFModel($model): array
    {
        $user = UserDTO::fromBaseModel($model->user);
        $lastEnrollement = $model->enrollements()->latest("academic_year_id")->first();
        $dl = $lastEnrollement->grade_level_id;
        $query = GradeLevel::find($dl);
        $grade = $query->grade->grade;
        $level = $query->level->level;
        $cs = $query->courses;
        $courses = [];

        foreach ($cs as $course) {
            $co = CourseDTO::fromModelTeacherPDf($course);
            $courses[] = $co;
        }

        return [
            'id' => $model->id,
            'names' => $user->name,
            'first_name' => $user->first_name,
            'second_name' => $user->second_name,
            'academic_year' => $lastEnrollement->academic_year->year,
            'level' => $level,
            'grade' => $grade,
            'code' => $user->code,
            'courses' => $courses
        ];
    }
}
