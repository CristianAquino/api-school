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

    public static function fromModel($model): self
    {
        $grades = GradeDTO::fromCollection($model->grades);

        return new self(
            $model->id,
            $model->level,
            $grades
        );
    }

    public static function fromNotRelationModel($models): array
    {
        return [
            'id' => $models->id,
            'level' => $models->level,
        ];
    }

    public static function fromNotRelationCollection($collections): array
    {
        return array_map(function ($collection) {
            return self::fromNotRelationModel($collection);
        }, $collections->all());
    }
}
