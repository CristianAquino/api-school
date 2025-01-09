<?php

namespace Database\Seeders;

use App\Models\CourseSchedule;
use App\Models\Schedule;
use Illuminate\Database\Seeder;


class SchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // vaciamos la tabla
        CourseSchedule::truncate();
        Schedule::truncate();

        $schedules = [
            [
                "start_time" => "08:00:00",
                "end_time" => "09:00:00",
            ],
            [
                "start_time" => "09:00:00",
                "end_time" => "10:00:00",
            ],
            [
                "start_time" => "10:00:00",
                "end_time" => "11:00:00",
            ],
            [
                "start_time" => "11:00:00",
                "end_time" => "12:00:00",
            ],
            [
                "start_time" => "12:00:00",
                "end_time" => "13:00:00",
            ],
        ];

        foreach ($schedules as $schedule) {
            # code...
            Schedule::create([
                'start_time' => $schedule["start_time"],
                'end_time' => $schedule["end_time"],
            ]);
        }
    }
}
