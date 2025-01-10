<?php

namespace App\DTOs;

use App\Models\Grade;
use App\Models\GradeLevel;
use App\Models\Level;

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
        $user = UserDTO::fromModel($model->user);
        return [
            'id' => $model->id,
            'names' => $user->name,
            'first_name' => $user->first_name,
            'second_name' => $user->second_name,
        ];
    }

    public static function fromCollection($collections): array
    {
        return array_map(function ($collection) {
            return self::fromModel($collection);
        }, $collections->all());
    }

    public static function fromModelWithRelation($model): array
    {
        $user = UserDTO::fromModel($model->user);
        $lastEnrollement = $model->enrollements()->latest("academic_year_id")->first();
        $dl = $lastEnrollement->grade_level_id;
        $query = GradeLevel::find($dl);
        $grade = $query->grade->grade;
        $level = $query->level->level;
        $cs = $query->courses;
        $courses = [];

        foreach ($cs as $course) {
            $co = CourseDTO::fromModel($course);
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
            'code_student' => $model->code_student,
            'course' => $courses

        ];

        // return new self(
        //     $model->id,
        //     $user->name,
        //     $user->first_name,
        //     $user->second_name,
        //     $user->phone,
        //     $user->birth_date,
        //     $user->address,
        //     $user->email,
        //     $user->dni,
        //     $model->code_teacher,
        //     $courses
        // );
    }
}
