<?php

namespace App\DTOs;

class LevelDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $level,
    ) {
        //
    }

    public static function fromBaseModel($model): self
    {
        return new self(
            $model->id,
            $model->level
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

    public static function fromModelWithRelation($model): array
    {
        $grades = GradeDTO::fromModelCollection($model->grades);

        return [
            'id' => $model->id,
            'level' => $model->level,
            'grades' => $grades
        ];
    }
}