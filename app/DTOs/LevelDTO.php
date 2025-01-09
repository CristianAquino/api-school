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

    public static function fromModel($model): array
    {
        return [
            'id' => $model->id,
            'level' => $model->level,
        ];
    }

    public static function fromCollection($collections): array
    {
        return array_map(function ($collection) {
            return self::fromModel($collection);
        }, $collections->all());
    }

    public static function fromModelWithRelation($model): self
    {
        $grades = GradeDTO::fromCollection($model->grades);

        return new self(
            $model->id,
            $model->level,
            $grades
        );
    }
}
