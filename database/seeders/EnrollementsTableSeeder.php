<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Enrollement;
use App\Models\Level;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class EnrollementsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // vaciamos la tabla
        Enrollement::truncate();
        Student::truncate();

        $faker = Faker::create();
        $academic_year = AcademicYear::latest()->first();
        $levels = Level::all();

        $s = 0;
        foreach ($levels as $level) {
            foreach ($level->grades as $grade) {
                for ($i = 0; $i < 5; $i++) {
                    # code...
                    $od = (int) date("Y") * 10000 + $s;
                    $code = 'ST' . $od;
                    $dni = (string)$faker->randomNumber(8, true);
                    $student = Student::create([
                        'role' => 'ROLE_STUDENT'
                    ]);
                    $student->user()->create([
                        'name' => $faker->name,
                        'first_name' => $faker->firstName,
                        'second_name' => $faker->lastName,
                        'birth_date' => $faker->date(),
                        'address' => $faker->address,
                        'dni' => $dni,
                        'email' => $faker->email,
                        'password' => $code . $dni,
                        'code' => $code,
                    ]);
                    Enrollement::create([
                        'academic_year_id' => $academic_year->id,
                        'student_id' => $student->id,
                        'grade_level_id' => $grade->pivot->id,
                    ]);
                    $s++;
                }
            }
        }
    }
}
