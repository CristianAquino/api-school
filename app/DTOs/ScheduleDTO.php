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

    public static function fromModelWithRelation($model): self
    {
        $day = $model->pivot->day;
        return new self(
            $model->id,
            $model->start_time,
            $model->end_time,
            $day
        );
    }

    public static function fromCollectionWithRelation($model, $collections): array
    {
        $courses =  array_map(function ($collection) {
            $course = CourseDTO::fromBaseModel($collection);
            if ($collection->teacher) {
                $teacher = TeacherDTO::fromModel($collection->teacher);
            } else {
                $teacher = null;
            }
            $day = $collection->pivot->day;

            return array_merge(
                (array)$course,
                ["day" => $day],
                ["teacher" => $teacher]
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
