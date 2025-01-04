<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeachersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $courses = Course::all();

        foreach ($courses as $course) {
            $teacher = Teacher::inRandomOrder()->first();
            $course->teacher_id = $teacher->id;
            $course->save();
        }
    }
}
