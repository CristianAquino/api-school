<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CourseScheduleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $courses = Course::all();
        $schedules = Schedule::all();
        $faker = Faker::create();

        foreach ($courses as $course) {
            $randomSchedules = $schedules->random(1)->pluck('id');
            $course->schedules()->attach($randomSchedules, [
                'day' => $faker->randomElement(Schedule::DAYS),
            ]);
        }
    }
}
