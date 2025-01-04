<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // vaciamos la tabla
        Schedule::truncate();
        $courses = Course::all();
        $faker = Faker::create();
        foreach ($courses as $course) {
            $startTime = $faker->numberBetween(8, 12);
            $start_time = sprintf('%02d:00:00', $startTime);
            $end_time = sprintf('%02d:00:00', $startTime + 1);
            Schedule::create([
                'start_time' => $start_time,
                'end_time' => $end_time,
                'day' => $faker->randomElement(Schedule::DAYS),
                'course_id' => $course->id
            ]);
        }
    }
}
