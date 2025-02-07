<?php

namespace App\DTOs;

class ScheduleDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $start_time,
        public readonly string $end_time,
    ) {
        //

    }

    public static function fromBaseModel($model): self
    {
        return new self(
            $model->id,
            $model->start_time,
            $model->end_time
        );
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
            return self::fromBaseModel($collection);
        }, $collections);
    }

    public static function fromCollection($collections): array
    {
        return array_map(function ($collection) {
            return self::fromBaseModel($collection);
        }, $collections->all());
    }

    public static function fromModelWithRelation($model): array
    {
        $day = $model->pivot->day;
        return [
            "id" => $model->id,
            "start_time" => $model->start_time,
            "end_time" => $model->end_time,
            "day" => $day
        ];
    }

    public static function fromCollectionWithRelation($model): array
    {
        $collections = $model->courses;
        $courses =  array_map(function ($collection) {
            $course = CourseDTO::fromBaseModel($collection);
            if (!is_null($collection->teacher)) {
                $teacher = TeacherDTO::fromModel($collection->teacher);
            }
            $day = $collection->pivot->day;

            return array_merge(
                (array)$course,
                ["day" => $day],
                ["teacher" => $teacher ?? null]
            );
        }, $collections->all());

        return [
            'id' => $model->id,
            'start_time' => $model->start_time,
            'end_time' => $model->end_time,
            'courses' => $courses
        ];
    }
}
