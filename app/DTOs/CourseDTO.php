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
        public readonly string $grade,
        public readonly string $level,
        public readonly ?TeacherDTO $teacher,
        public readonly ?array $schedules = []
    ) {
        //
    }

    public static function fromModel($model): array
    {
        // $gradeLevel = GradeLevel::find($model->grade_level_id);
        // $grade = $gradeLevel->grade->grade;
        // $level = $gradeLevel->level->level;
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
            $teacher = TeacherDTO::fromModel($model->teacher->user);
        } else {
            $teacher = null;
        }
        $schedules = ScheduleDTO::fromCollection($model->schedules);
        $gradeLevel = GradeLevel::find($model->grade_level_id);
        $grade = $gradeLevel->grade->grade;
        $level = $gradeLevel->level->level;
        return new self(
            $model->id,
            $model->course,
            $model->description,
            $grade,
            $level,
            $teacher,
            $schedules
        );
    }

    public static function fromCollectionWithRelation($collections): array
    {
        return array_map(function ($collection) {
            return self::fromModelWithRelation($collection);
        }, $collections->all());
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'course' => $this->course,
            'description' => $this->description,
        ];
    }
}
