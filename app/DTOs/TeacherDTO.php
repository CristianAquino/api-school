<?php

namespace App\DTOs;

use App\Models\Grade;
use App\Models\Level;

class TeacherDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $first_name,
        public readonly string $second_name,
        public readonly ?string $phone,
        public readonly ?string $birth_date,
        public readonly string $address,
        public readonly string $email,
        public readonly string $dni,
        public readonly string $code,
        public readonly array $courses,
    ) {
        //

    }
    public static function fromModel($model): array
    {
        $user = UserDTO::fromModel($model->user);
        return [
            'id' => $model->id,
            'names' => $user->name,
            'first_name' => $user->first_name,
            'second_name' => $user->second_name,
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
        $user = UserDTO::fromModel($model->user);
        $courses = [];

        foreach ($model->courses as $course) {
            $sch = ScheduleDTO::fromModelWithRelation($course->schedules[0]);
            $co = CourseDTO::fromModel($course);
            $g = Grade::where('id', $course->gradelevel->grade_id)->first();
            $l = Level::where('id', $course->gradelevel->level_id)->first();

            $co['grade'] = $g->grade;
            $co['level'] = $l->level;
            $co['schedule'] = $sch;
            $courses[] = $co;
        }

        return new self(
            $model->id,
            $user->name,
            $user->first_name,
            $user->second_name,
            $user->phone,
            $user->birth_date,
            $user->address,
            $user->email,
            $user->dni,
            $user->code,
            $courses
        );
    }
}
