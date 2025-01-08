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
        public readonly string $level,
        public readonly array $courses = [],
    ) {
        //
    }

    public static function fromModel($model): array
    {
        return [
            'id' => $model->id,
            'grade' => $model->grade,
        ];
    }

    public static function fromCollection($collections): array
    {
        return array_map(function ($collection) {
            return self::fromModel($collection);
        }, $collections->all());
    }

    public static function fromModelWithRelation($grade, $level, $courses): self
    {
        $courses = CourseDTO::fromCollection($courses);

        return new self(
            $grade->id,
            $grade->grade,
            $level->level,
            $courses
        );
    }
}
