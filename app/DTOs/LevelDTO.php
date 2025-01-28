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
        public readonly array $grades
    ) {
        //
    }

    public static function fromBase($model, $grades): self
    {
        return new self(
            $model->id,
            $model->level,
            $grades
        );
    }

    public static function fromPagination($model): array
    {
        return [
            'data' => self::fromPaginationCollection($model->items()),
            'pagination' => PaginationDTO::base($model)
        ];
    }

    public static function fromModel($model): array
    {
        return [
            'id' => $model->id,
            'level' => $model->level,
        ];
    }

    public static function fromPaginationCollection($collections): array
    {
        return array_map(function ($collection) {
            return self::fromModel($collection);
        }, $collections);
    }

    public static function fromModelWithRelation($model): self
    {
        $grades = GradeDTO::fromCollection($model->grades);
        return self::fromBase($model, $grades);
    }
}
