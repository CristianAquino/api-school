<?php

namespace App\DTOs;

class EnrollementDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $academic_year,
        public readonly string $grade,
        public readonly string $level,
        public readonly StudentDTO|array $student,
    ) {
        //
    }

    public static function fromModel($model): self
    {
        // dd($model->student);
        // return [
        //     'id' => $model->id,
        //     'academic_year' => $model->academic_year->year,
        //     'grade' => $model->gradeLevel->grade->grade,
        //     'level' => $model->gradeLevel->level->level,
        //     'student' => TeacherDTO::fromModel($model->student),
        // ];
        $student = TeacherDTO::fromModel($model->student);
        return new self(
            $model->id,
            $model->academic_year->year,
            $model->gradeLevel->grade->grade,
            $model->gradeLevel->level->level,
            $student
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
            return self::fromModel($collection);
        }, $collections);
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
            'academic_year' => $this->academic_year,
            'student' => $this->student,
            'grade' => $this->grade,
            'level' => $this->level
        ];
    }
}
