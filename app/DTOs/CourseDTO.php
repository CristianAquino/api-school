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
        // public readonly string $level,
        // public readonly string $grade,
        // public readonly TeacherDTO|array|null $teacher,
        // public readonly ?ScheduleDTO $schedule
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

    public static function fromCollection($collections): array
    {
        return array_map(function ($collection) {
            return self::fromBaseModel($collection);
        }, $collections->all());
    }

    public static function fromModelWithRelation($model): array
    {
        if ($model->teacher) {
            $teacher = TeacherDTO::fromModel($model->teacher);
        } else {
            $teacher = null;
        }
        if (count($model->schedules) > 0) {
            $schedule = ScheduleDTO::fromModelWithRelation($model->schedules[0]);
        } else {
            $schedule = null;
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
            'teacher' => $teacher,
            'schedule' => $schedule
        ];
    }
}
