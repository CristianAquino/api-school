<?php

namespace App\DTOs;

class AcademicYearDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $year,
        public readonly string $start_date,
        public readonly string $end_date
    ) {
        //
    }

    public static function fromBase($model): self
    {
        return new self(
            $model->id,
            $model->year,
            $model->start_date,
            $model->end_date,
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
            return self::fromBase($collection);
        }, $collections);
    }

    // public static function fromCollection($collections): array
    // {
    //     return array_map(function ($collection) {
    //         return self::base($collection);
    //     }, $collections->all());
    // }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'year' => $this->year,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date
        ];
    }
}
