<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Level;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // vaciamos la tabla
        Course::truncate();

        $levels = Level::all();
        $faker = Faker::create();
        $num_courses = 5;

        foreach ($levels as $level) {
            foreach ($level->grades as $grade) {
                for ($i = 0; $i < $num_courses; $i++) {
                    Course::create([
                        'course' => $faker->word() . ' ' . $faker->word(),
                        'description' => $faker->sentence(),
                        'grade_level_id' => $grade->pivot->id,
                    ]);
                }
            }
        }
    }
}
