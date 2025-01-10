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
        public readonly ?string $description = null,
        public readonly string $level,
        public readonly string $grade,
        public readonly TeacherDTO|array|null $teacher,
        public readonly ?ScheduleDTO $schedule
    ) {
        //
    }

    public static function fromModel($model): array
    {
        return [
            'id' => $model->id,
            'course' => $model->course,
            'description' => $model->description,
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
        return new self(
            $model->id,
            $model->course,
            $model->description,
            $level,
            $grade,
            $teacher,
            $schedule
        );
    }
}
