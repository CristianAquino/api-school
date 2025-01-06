<?php

namespace App\DTOs;

class AcademicYearDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int $id,
        public string $year,
        public string $start_date,
        public string $end_date
    ) {
        //
    }

    public static function fromModel($model): self
    {
        return new self(
            $model->id,
            $model->year,
            $model->start_date,
            $model->end_date,
        );
    }

    public static function fromCollection($collections): array
    {
        return array_map(function ($collection) {
            return self::fromModel($collection);
        }, $collections->all());
    }

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
