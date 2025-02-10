<?php

namespace App\DTOs;

use App\Models\Course;
use App\Models\Qualification;

class QualificationDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly int $id,
        public readonly int $number_note,
        public readonly string $letter_note,
        public readonly float $avg,
    ) {
        //

    }
    public static function fromBaseModel($model): self
    {
        return new self(
            $model->id,
            $model->number_note,
            $model->letter_note,
            $model->avg,
        );
    }

    public static function fromCollection($collections): array
    {
        return array_map(function ($collection) {
            return self::fromBaseModel($collection);
        }, $collections->all());
    }

    public static function fromPrintModel($model): array
    {
        $user = $model->user;
        $enro = $model->enrollements;
        $enrollements = [];
        foreach ($enro as $enrollement) {
            $academic_year = $enrollement->academic_year->year;
            $grade = $enrollement->gradeLevel->grade->grade;
            $level = $enrollement->gradeLevel->level->level;
            $cour = Course::where('grade_level_id', $enrollement->gradeLevel->id)->get();
            $courses = [];
            foreach ($cour as $course) {
                $notes = Qualification::where('student_id', $enrollement->student_id)->where('course_id', $course->id)->get();
                $courses[] = [
                    'id' => $course->id,
                    'course' => $course->course,
                    'description' => $course->description,
                    'notes' => self::fromCollection($notes),
                ];
            }
            $enrollements[] = [
                'academic_year' => $academic_year,
                'grade' => $grade,
                'level' => $level,
                'courses' => $courses
            ];
        }
        return [
            'id' => $model->id,
            'names' => $user->name,
            'first_name' => $user->first_name,
            'second_name' => $user->second_name,
            'code' => $user->code,
            'enrollements' => $enrollements,
        ];
    }
}
