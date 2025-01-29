<?php

namespace App\DTOs;

class GradeDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $grade,
    ) {
        //
    }

    public static function fromBaseModel($model): self
    {
        return new self(
            $model->id,
            $model->grade
        );
    }

    public static function fromPaginationCollection($collections): array
    {
        return array_map(function ($collection) {
            return self::fromBaseModel($collection);
        }, $collections);
    }


    public static function fromPagination($model): array
    {
        return [
            'data' => self::fromPaginationCollection($model->items()),
            'pagination' => PaginationDTO::base($model)
        ];
    }

    public static function fromModelWithRelation($grade, $level, $courses): array
    {
        $courses = CourseDTO::fromCollection($courses);

        return [
            'id' => $grade->id,
            'grade' => $grade->grade,
            'level' => $level->level,
            'courses' => $courses
        ];
    }
}
