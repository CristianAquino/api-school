<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Enrollement;
use App\Models\Grade;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

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
        $grades = Grade::all();

        $password = Hash::make('12345678');
        $s = 0;

        foreach ($grades as $grade) {
            for ($i = 0; $i < 5; $i++) {
                # code...
                $code = (int) date("Y") * 10000 + $s;
                $student = Student::create([
                    'code_student' => 'ST' . $code,
                    'role' => 'ROLE_STUDENT'
                ]);
                $student->user()->create([
                    'name' => $faker->name,
                    'first_name' => $faker->firstName,
                    'second_name' => $faker->lastName,
                    'birth_date' => $faker->date(),
                    'address' => $faker->address,
                    'dni' => (string)$faker->randomNumber(8),
                    'email' => $faker->email,
                    'password' => $password,
                ]);
                Enrollement::create([
                    'academic_year_id' => $academic_year->id,
                    'student_id' => $student->id,
                    'grade_id' => $grade->id,
                ]);
                $s++;
            }
        }
    }
}
