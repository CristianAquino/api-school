<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Grade;
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
        $faker = Faker::create();
        $grades = Grade::all();
        $num_courses = 5;
        foreach ($grades as $grade) {
            for ($i = 0; $i < $num_courses; $i++) {
                Course::create([
                    'course' => $faker->word() . ' ' . $faker->word(),
                    'description' => $faker->sentence(),
                    'grade_id' => $grade->id,
                ]);
            }
        }
    }
}
