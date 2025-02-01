<?php

namespace App\DTOs;

use App\Models\GradeLevel;

class TeacherDTO
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
        $courses = [];

        foreach ($model->courses as $course) {
            $sch = ScheduleDTO::fromModelWithRelation($course->schedules[0]);
            $co = (array)CourseDTO::fromBaseModel($course);
            $gl = GradeLevel::where('id', $course->grade_level_id)
                ->first();

            $co['grade'] = $gl->grade->grade;
            $co['level'] = $gl->level->level;
            $co['schedule'] = $sch;
            $courses[] = $co;
        }
        return array_merge(
            ['id' => $model->id],
            (array)$user,
            ['courses' => $courses]
        );
    }
}