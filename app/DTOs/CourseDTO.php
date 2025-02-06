<?php

namespace App\DTOs;

use App\Models\GradeLevel;

class CourseDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $course,
        public readonly ?string $description,
    ) {
        //
    }

    public static function fromBaseModel($model): self
    {
        return new self(
            $model->id,
            $model->course,
            $model->description,
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

    public static function fromModelCollection($collections): array
    {
        return array_map(function ($collection) {
            return self::fromBaseModel($collection);
        }, $collections->all());
    }

    public static function fromModelWithRelation($model): array
    {
        if (!is_null($model->teacher)) {
            $teacher = TeacherDTO::fromModel($model->teacher);
        }
        if (count($model->schedules) > 0) {
            $schedule = ScheduleDTO::fromModelWithRelation($model->schedules[0]);
        }

        $gradeLevel = GradeLevel::find($model->grade_level_id);
        $grade = $gradeLevel->grade->grade;
        $level = $gradeLevel->level->level;

        return [
            'id' => $model->id,
            'course' => $model->course,
            'description' => $model->description,
            'level' => $level,
            'grade' => $grade,
            'teacher' => $teacher ?? null,
            'schedule' => $schedule ?? null
        ];
    }

    public static function fromModelTeacherPDf($model)
    {
        if (!is_null($model->teacher)) {
            $teacher = TeacherDTO::fromModel($model->teacher);
        }

        return collect(
            self::fromBaseModel($model),
            ['teacher' => $teacher ?? null]
        );
    }
}